<?php
/**
 * Mostra les escriptures i lectures per a les diferents activitats segons la forquilla actual
 */
class activitats extends stat_base {
	function nom () {
		//return get_string ('activitat_activitats','admupc');
		return "Registre de les activitats";
	}
	
	function descripcio () {
		//return get_string ('activitat_activitats_desc','admupc');
		return "Aquest indicador conté les lectures i escriptures que es realitzen " .
				"mensualment sobre les activitats dels cursos.";
	}
	
	/**
	 * tracta el formulari. S'executa abans de printar res per pantalla
	 * @return Boolean (fals si retorna error, true si be)
	 */
	function tractament () {
		
	}
	
	/**
	 * mostra el contingut de la estadística
	 * @param: dades de la selecció de temps
	 */
	function contingut ($forq) {
		global $CFG, $USER;
		
		
		
		if(!isset($USER->envid) || $USER->envid == -1){
			print_error('chooseenv','service');			
		}
		
		//llegenda
		//x-dades (quad,campus,assig) (format [Compacte, Ampliat])
		$mes_fi=date('n',$forq->fi);
		$day_fi=date('t',$forq->fi);
		$any_fi=date('Y',$forq->fi);
		$mes_inici=date('n',$forq->inici);
		$any_inici=date('Y',$forq->inici);
		
		/**
		 * Agregat mensual. Per tant forçem el principi i la fi de mes especificat
		 */
		$forq->inici = mktime(0,0,0,$mes_inici,1,$any_inici);
		$forq->fi = mktime(23,59,59,$mes_fi,$day_fi,$any_fi);
				
	
		//mirem els filtres
		$filtres = array();
		if (optional_param('fetform')) {
			$moduls = array('assignment', 'calendar', 'chat', 'choice', 'forum', 'glossary', 'journal', 
		'label', 'lesson', 'message', 'quiz', 'resource', 'scorm', 'survey', 'wiki', 'workshop', 'data', 'blog', 'lams', 'grade');
			$checks = array();
			foreach ($moduls as $mod) {
				if (optional_param('v'.$mod)) $filtres[] = $mod;
			}
		}
		
		$filtre='';
		if (optional_param('fetform') and count($filtres)<count($moduls)) {
			$filtre = " AND module IN ('".implode("','",$filtres)."') ";
		}
		
		//Inici Foreach
        $enviroments = grouped_entorns($USER->envid);
        $total = count($enviroments) > 1;
        $data = array();
		foreach ($enviroments as $envID => $enviroment) {
			//Actualitzem l'entorn
			$USER->envid = $enviroment->id;
			if(isset($enviroment->nom))
				$USER->entorn_nom = $enviroment->nom;
				
			$query = "SELECT module,sum('read') as rd, sum('write') as wr FROM {$CFG->prefix}analytics_{$USER->envid}_mod " .
					"WHERE time_start>={$forq->inici} AND time_end<={$forq->fi} $filtre" .
					"GROUP BY module ORDER BY module";
			$head = array('module','rd','wr');
			if($total) array_unshift($head, 'env');
					
			
			//executem la query
			//ens connectem a la base de dades local
			
			$result = get_recordset_sql($query);
			$courses = array();
			while ($res = recordset_fetch ($result)) {
				//posem la data a la primera posicio
				$row = array();
				if ($head[0]=='data') $row[] = date("Y/m/d", $forq->inici);
				if (in_array('env',$head)) {
					$envName = '';
					$envName = get_field('analytics', 'nom_client', 'id', $USER->envid);
					$envName = str_replace("(","<br/>(", $envName);
					$row[] = $envName;
				}
				//mirem si posem el quadrimestre
				
				$row[] = get_string($res->module,'service');
				$row[] = $res->rd;
				$row[] = $res->wr;
				
				$data[] = $row;
			}
			recordset_close($result);
			//End Foreach
        }
        
        if($total) $USER->envid = 'total';
        if($total) $USER->entorn_nom = get_string('groupedstats', 'service');
        
                
		//passem a montar els resultats en taula
		obre_taula();
		
		$cap = array();
		foreach ($head as $h) {
			$cap[] = get_string($h,'service');
		}
		afegeix_capcalera($cap);
		
		foreach ($data as $row) {
			//mirem si la posem o no
			$posa = true;
			foreach ($row as $field) {
				if (!$field) {
					if ($field!==0 && $field!=='0') $posa = false;
				}
			}
			if ($posa) {
				afegeix_fila($row);
			}
		}
		
		tanca_taula();
	}
	
	/**
	 * algunes estadistiques nomes es mostren a vegades
	 * @return boolean o int (0=invisible, 1=visible, 2=visible pero no lincable)
	 */
	function visible () {
		return true;
	}
	
	/**
	 * If is gestor only show Month/Year
	 *
	 * @return Boolean
	 */
	function mostra_dia(){
		return false;
	}
	
	/**
	 * retorna el formulari associat a l'indicador
	 */
	function form () {
		$url = get_current_stat_url();
		echo '<form action="'.$url.'" method="post">';
		$mods = array('forum','glossary','label','lesson','quiz','wiki',
			'assignment','calendar','chat','choice','data','grade','journal',
			'resource','scorm','survey','workshop');
		$checks = array();
		foreach ($mods as $mod) {
			$checks[$mod] = (optional_param('v'.$mod) || !optional_param('fetform'))?'checked="checked"':'';
		}
		
		$num = 1;
		echo '<table border="0"><tr>';
		foreach ($mods as $mod) {
			echo '<td><input type="checkbox" name="v'.$mod.'" '.$checks[$mod].'/>'.get_string($mod,'service').'</td>';				
			if ($num%6==0) echo '</tr><tr>';
			$num++;
		}
		
		
		while ($num%6!=0) {
			echo '<td>&nbsp;</td>';
			$num++;
		}
	
		echo '</tr></table>';
		echo '<input type="hidden" name="fetform" value="si"/><br/>';
		
				//boto per seleccionar/desseleccionar tot
		echo '<script language="JavaScript" type="text/javascript">' .
				'function borra(valor){
					for(var i=0;i<'.sizeof($mods).';i++){' .
					//'alert(document.forms[0].elements[i].name);'.
					'document.forms[0].elements[i].checked = valor;' .
					'}' .
					'document.reload();'.
					'return false;'.
				'}' .
				'</script>';
		echo 'Selecciona: <a href="javascript:borra(1)">Totes les activitats</a>&nbsp;&nbsp;<a href="javascript:borra(0)">Cap activitat</a>';
	echo '&nbsp;&nbsp;<input type="submit" value="Filtra"/>';
		echo '</form>';
	}
	
}
?>

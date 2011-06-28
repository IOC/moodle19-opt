<?php
/**
 * Mostra les escriptures i lectures per a les diferents activitats segons la forquilla actual
 */
class accessos extends stat_base {
	function nom () {
		//return get_string ('activitat_activitats','admupc');
		return "Registre d'accessos";
	}
	
	function descripcio () {
		//return get_string ('activitat_activitats_desc','admupc');
		//return "Aquest indicador mostra els usuaris no únics al curs de qualsevol tipus d'usuari";
		$miss = "Aquest indicador mostra els usuaris que han accedit a les assignatures.
			Diferenciats per les visites totals i visites úniques que corresponen a usuaris únics.<br/>
			Això significa que no hi ha repetits en el nombre d'usuaris que entren en un curs durant un tot un dia"; 
		return $miss;
	}
	
	/**
	 * mostra el contingut de la estadística
	 * @param: dades de la selecció de temps
	 */
	function contingut ($forq) {
		
		global $CFG,$USER;
		
		if(!isset($USER->envid) || $USER->envid == -1){
			print_error('chooseenv','service');
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
			
			$query = "SELECT sum(logins) as vs, sum(logins_unicos) as vsu, sum(guest_logins) as glog FROM {$CFG->prefix}analytics_{$USER->envid}_site " .
						"WHERE time_start>={$forq->inici} AND time_end<={$forq->fi} ";
			$head = array('vs','vsu','glog');
			if($total) array_unshift($head, 'env');
			
					
			//aquestes consultes van sobre la base de dades local
			
			$result = get_recordset_sql($query);
			$courses = array();
			
			while ($res = recordset_fetch ($result)) {
				//echo 'entro al bucle';
				//print_object($res);
				//posem la data a la primera posicio
				$row = array();
				if ($head[0]=='data') $row[] = date("Y/m/d", $forq->inici);
				if (in_array('env',$head)) {
					$envName = '';
					$envName = get_field('analytics', 'nom_client', 'id', $USER->envid);
					$envName = str_replace("(","<br/>(", $envName);
					$row[] = $envName;
				}
				$row[] = $res->vs;
				$row[] = $res->vsu;
				$row[] = $res->glog;
				
				$data[] = $row;
			}
			recordset_close($result);
			//End Foreach
	    }
	    if($total) $USER->envid = 'total';
	    if($total) $USER->entorn_nom = get_string('groupedstats', 'service');

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
		return isadmin();
	}
	
}
?>

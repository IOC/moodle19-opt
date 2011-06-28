<?php


/*---------------- formulari ----------------------*/

//----- CLASSE PER A GENERAR FORMULARIS AMB EL QUICKFORMS ------

require_once($CFG->libdir.'/formslib.php');

class form_service extends moodleform {

    function definition() {
        global $COURSE;
        $mform    =& $this->_form;
     }
    
    function estat_serveis(){
		global $CFG;
		$row = array();
		//$semImage = '';
		$rows = array();
    	$mform    =& $this->_form;
    	//per cada servei busquem les seves dades:
   		
   		//controlarem la ubicació de les diferents entrades de clients
     	//a la posició 0 sempre tindrem la configuració del sistema base
     	$i = 0;
   		$servers = entorns_server();
   		//Para luego hacerlo por serverid
   		if($servers){
			//Por ahora asi
			foreach ($servers as $serverId => $serverData) {
			   $aux = clone($serverData);
			   $aux->semImage = imatge_semafor($serverData->semafor);
			   if(!isset($aux->smoodle)) $aux->smoodle = 0;
			   $rows[$i] = array ($aux->nom,$aux->semImage,'<span style="color:grey">'.$aux->smoodle.'</span>');
			   $i++;
			}
		}
        //Resto de elementos
        $entorns = entorns_disponibles();
        if($entorns){
			foreach ($entorns as $e) {
				//preparem la imatge segons semafor
				$semImage = imatge_semafor($e->semafor);
				
				//anem a buscar les sessions de moodle que es troben a la taula concreta
				//$select ="select * from {$CFG->prefix}analytics_{$e->nom}_sessions where id = (select max(id) from {$CFG->prefix}analytics_{$e->nom}_sessions)";
				$select ="select * from {$CFG->prefix}analytics_{$e->id}_sessions where id = (select max(id) from {$CFG->prefix}analytics_{$e->id}_sessions)";
				$sessions = get_record_sql($select); 
				//en la primera instalació no existeix $sessions->smoodle 
				if(!isset($sessions->smoodle)) $sessions->smoodle =0;
				$aux->smoodle += $sessions->smoodle; 
				$row = array ('<a href="index.php?select='.$e->id.'">'.$e->nom_client.'</a>',$semImage,$sessions->smoodle);
				
				$rows[$i]=$row;
				$i ++;
			}
		}
    	//Actualizar el numero de sessiones.
        $rows[0] = array ($aux->nom,$aux->semImage,'<span style="color:grey">'.$aux->smoodle.'</span>');
        
    	$table_consult = new stdClass;
    	$table_consult->head= array (get_string('instance', 'indicadors'),get_string('semafor', 'indicadors'),get_string('smoodle', 'indicadors'));
    	$table_consult->wrap = array ('nowrap','');
		$table_consult->align = array ('center','center','center','center','center','center');
		$table_consult->data = $rows;
		$table_consult->width = '80%';
		print_table($table_consult);
    }
    
    
     
     
    function basic(){
    	$mform    =& $this->_form;
		$mform->addElement('submit','nou','Crea un nou entorn','align="center"');
    }
    
    function &get_mform () {
    	return $this->_form;
    }
    
    /**
     * posa els botons de guardar i cancel·lar al formulari
     */
	function add_action_buttons() {
		$mform =& $this->_form;
		$buttonarray = array();
    	$buttonarray[] = &$mform->createElement('submit', 'guardar', 'Guardar');
    	$buttonarray[] = &$mform->createElement('cancel','cancel', 'Cancel·lar');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->setType('buttonar', PARAM_RAW);
        $mform->closeHeaderBefore('buttonar');
    }
    
    
    
    
    /**
     * afegeix un botó de modificar
     */
    function add_modify_button () {
    	$mform =& $this->_form;
		$buttonarray = array();
    		$buttonarray[] = &$mform->createElement('submit', 'update', 'Modificar');
    		$buttonarray[] = &$mform->createElement('cancel','cancel', 'Acceptar');
        	$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        	$mform->setType('buttonar', PARAM_RAW);
        	$mform->closeHeaderBefore('buttonar');
    }
    
    
/**
     * afegeix un botó d'acceptar per no fer cap acció sobre l'element
     */
    function add_accept_button () {
    	$mform =& $this->_form;
    	$mform->closeHeaderBefore('cancel');
    	$mform->addElement('submit','cancel','Acceptar');
    }
    
    /*
     * Afegeig un botó per redireccionar a l'assignació de rols
     */
	function add_assignacio_rols_button () {
		$mform =& $this->_form;
		$id = required_param('select');
		echo $id;
		echo('<a href="usuaris.php?afegir='.$id.'">Afegir usuaris</a>');
    }
    
    function camps_entorn($info=null,$bloquejat= false,$modificacio=false){
    		
    	global $CFG;
    	
    	$mform =& $this->_form;
    	
    	if(!$info){
    		
    		$info = new stdClass;
    		$info->nom = '';
    		$info->nom_client ='';
    		$info->usuaris_concurrents='';
    		$info->permissibilitat = '';
    		$info->observacions = '';
    		$info->id = '';
    		//$info->dbtype = 'postgres';
    		$info->dbtype = $CFG->dbtype;
    		$info->dbhost = '';
    		$info->dbuser = '';
    		$info->dbpass = '';
    		$info->dataroot = $CFG->dataroot;
    		
    		$patpostgres = '/.*postgres.*/';
			$patmysql = '/.*mysql.*/';
			$patoracle = '/.*oci.*/';
			if(isset($CFG->dbport)) $info->dbport = $CFG->dbport;
			else if(preg_match($patpostgres,$CFG->dbtype)) $info->dbport ='5432';
			else if(preg_match($patmysql,$CFG->dbtype)) $info->dbport ='3306';
			else if(preg_match($patoracle,$CFG->dbtype)) $info->dbport ='1521';
			else $info->dbport ='';

    		$info->dbname = '';
    		(isset($CFG->dbpersist)&& $CFG->dbpersist)?$info->dbpersist = 'true':$info->dbpersist='false';
    		(isset($CFG->prefix))?$info->prefix = $CFG->prefix:$info->prefix = 'mdl_';
    	}
    	
    	    	
     	//preparem la imatge segons semafor
     	if(!isset($info->semafor)) $info->semafor=0;
    	$semImage = imatge_semafor($info->semafor);
    	
    	//si es el per defecte cal modificar uns altres camps
    	if($info->nom_client == 'SERVER' || $info->nom == 'ADMIN_SERVER') {
    		//dades client
    		$mform->addElement('header', 'dades_server', ucwords(get_string('dadesservei','service')));
    		$max = get_field('config','value','name','maxpostgres');
    		$critic = get_field('config','value','name','limitcritic');
    		$warning = get_field('config','value','name','limitwarning');
    		$mform->addElement('hidden','nom',$info->nom);
    		if(!$bloquejat ){ 
    			$estat = 'static';
    			$mform->addElement('hidden','id_un',$info->id);
    			$mform->addElement($estat,'maxpostgres','maxpostgres',$max);
    			$mform->addElement($estat,'limitcritic','limitcritic',$critic);
    			$mform->addElement($estat,'limitwarning','limitwarning',$warning);
    		}else{
    			$estat = 'text';
    			$mform->addElement('hidden','id_dos',$info->id);
    			$mform->addElement($estat,'maxpostgres','maxpostgres','value='.$max);
    			$mform->addElement($estat,'limitcritic','limitcritic','value='.$critic);
    			$mform->addElement($estat,'limitwarning','limitwarning','value='.$warning);
    		}
    		
    	}else{
    		//dades client
    		$mform->addElement('header', 'dades_client', ucwords(get_string('dadesclient','service')));
	    	if(!$bloquejat ){ 
	    		$estat = 'static';
	    		$mform->addElement($estat,'nom_client',get_string('nomclient','service'),$info->nom_client);
	    		$mform->addElement($estat,'usuaris_concurrents',get_string('usuconcurrents','service'),$info->usuaris_concurrents);
	    		$mform->addElement($estat,'permissibilitat',get_string('permisibilitat','service'),$info->permissibilitat);
	    		//$mform->addElement($estat,'semafor','Semàfor',$info->semafor);
	    		$mform->addElement('static','semafor',get_string('semafor','service'), $semImage);
	    		$mform->addElement($estat,'observacions',get_string('observacions','service'),$info->observacions);
	    	}else{
	    		$estat = 'text';
	    		if($modificacio)
	    			$mform->addElement('static','nom_client',get_string('nomclient','service'),$info->nom_client);
	    		else
	    			$mform->addElement('text','nom_client',get_string('nomclient','service'),'value='.$info->nom_client);
	    		$mform->addElement($estat,'usuaris_concurrents',get_string('usuconcurrents','service'),'value='.$info->usuaris_concurrents);
	    		$mform->addElement($estat,'permissibilitat',get_string('permisibilitat','service'),'value='.$info->permissibilitat);
	    		//$mform->addElement('static','semafor','Semàfor',$info->semafor);
	    		$mform->addElement('static','semafor',get_string('semafor','service'), $semImage);
	    		$mform->addElement('textarea','observacions',get_string('observacions','service'), array('rows'=>6, 'cols'=>60));
	    		$mform->setDefault('observacions',$info->observacions);
	 			
	    	}
	    	
	    	//dades conexio
	    	$mform->addElement('header', 'dades_connexio', ucwords(get_string('dadesconexio','service')));
	    	if(!$bloquejat){ 
	    		$estat = 'static';
	    		
	    		//$mform->addElement('hidden','id_mod',$info->id);
	    		$mform->addElement('hidden','id_un',$info->id); 
	    		$mform->addElement($estat,'nom',get_string('nomentorn','service'),$info->nom);
	    		$mform->addElement($estat,'dbtype',get_string('tipusbd','service'), $info->dbtype);
	   			$mform->addElement($estat,'dbhost',get_string('hostbd','service'), $info->dbhost);
	   			$mform->addElement($estat,'dbuser',get_string('usuaribd','service'), $info->dbuser);
	   			//TIPO PASSWORD: $mform->addElement($estat,'dbpass',get_string('pswbd','service'), $info->dbpass);
	   			$mform->addElement($estat,'dbport',get_string('portbd','service'), $info->dbport);
	   			$mform->addElement($estat,'dbname',get_string('nombd','service'), $info->dbname);
	   			$mform->addElement($estat,'dbpersist',get_string('persistenciabd','service'), $info->dbpersist);
	   			$mform->addElement($estat,'prefix',get_string('prefix','service'),$info->prefix);
	   			$mform->addElement($estat,'dataroot',get_string('dataroot','analytics'),$info->dataroot);
	   		}
	    	else{
	    		$estat = 'text';
	    		$mform->addElement('hidden','id_dos',$info->id);
	    		if($modificacio)
	    			$mform->addElement('static','nom',get_string('nomentorn','service'),$info->nom);
	    		else	
	    			$mform->addElement('text','nom',get_string('nomentorn','service'),'value='.$info->nom);
	    			
	    		
	   			$mform->addElement($estat,'dbtype',get_string('tipusbd','service'), 'value='.$info->dbtype);
	   			$mform->addElement($estat,'dbhost',get_string('hostbd','service'), 'value='.$info->dbhost);
	   			$mform->addElement($estat,'dbuser',get_string('usuaribd','service'), 'value='.$info->dbuser);
	   			$mform->addElement('passwordunmask','dbpass',get_string('pswbd','service'), 'value='.$info->dbpass);
	   			$mform->addElement($estat,'dbport',get_string('portbd','service'), 'value='.$info->dbport);
	   			$mform->addElement($estat,'dbname',get_string('nombd','service'), 'value='.$info->dbname);
	   			$mform->addElement($estat,'dbpersist',get_string('persistenciabd','service'), 'value='.$info->dbpersist);
	   			$mform->addElement($estat,'prefix',get_string('prefix','service'),'value='.$info->prefix);
	   			$mform->addElement($estat,'dataroot',get_string('dataroot','analytics'),'value='.$info->dataroot);
	    	}
	    	
    	}
    }
    

    function is_cancelled(){
    	$mform =& $this->_form;
        if ($mform->isSubmitted()){
        	if (optional_param('cancel', 0, PARAM_RAW)){
            	return true;
            }
        }
        return false;
    }

    /*
     * indica si s'ha premut el boto per salvar les dades
     */
    function salvar(){
    	$mform =& $this->_form;
        if ($mform->isSubmitted()){
        	if (optional_param('guardar', 0, PARAM_RAW)){
            	return true;
            }
        }
        return false;
    	
    }
    
	/*
     * indica si s'ha premut el boto per salvar les dades
     */
    function modificar(){
    	$mform =& $this->_form;
        if ($mform->isSubmitted()){
        	if (optional_param('update', 0, PARAM_RAW)){
            	return true;
            }
        }
        return false;
    	
    }
    
    
    /*
     * guarda l'objecte que li pasen per parametre a la bd
     * 
     * 
     */
    function save($dades){
    	$connexio = new stdClass;
    	if(!$dades){
    		return false;
    	}
    	else{
    		
    		
    		
    		//si tenim dades del SERVER hem de guardar a la config
    		//sino és el normal
    		$server = false;
    		if(isset($dades->maxpostgres)){
    			$server = true;
    			if(!set_field('config','value',$dades['maxpostgres'],'name','maxpostgres')){
    				return false;
    			}
    		}
    		if(isset($dades->limitcritic)){
    			$server = true;
    			if(!set_field('config','value',$dades['limitcritic'],'name','limitcritic')){
    				return false;
    			}
    		}
    		if(isset($dades->limitwarning)){
    			$server = true;
    			if(!set_field('config','value',$dades['limitwarning'],'name','limitwarning')){
    				return false;
    			}
    		}
    		
    		if(!$server){
    			//muntem els dos vectors amb dades 
	    		foreach($dades as $nom=>$dada){
	    			$connexio->$nom=$dada;
	    		}
	    		    		//si tenim id entorn es que es una modificació
	    		//print_object($connexio);
	    		if(isset($connexio->id)){
	    			if(!update_record('analytics',addslashes_object($connexio))){
	    				return false;
	    			}
	    		}else{
	    		
	    			if(!$connexio->nom || $connexio->nom ==''){
	    				return false;
	    			}
	    			
	    			//comprovem que no hi hagi un entorn amb el mateix nom
	    			if(get_record('analytics','nom',$connexio->nom)){
	    				return false;
	    			}
	    			
	    			
	    			//creem les taules auxiliars per aquesta nova connexio
	    			$resultatId = insert_record ('analytics',addslashes_object($connexio));
	    		
	    			//print_object($resultat);
	    			if ($resultatId){
		    			//$resultat = crea_taules($dades['nom'],$dades['dbtype']);
		    			$resultat = crea_taules($resultatId,$dades['dbtype']);
		    			
		    	
		    			if(!$resultat){
		    				//borrem les taules
		    				borra_taules($resultat);
		    				return false;
		    			}
		    			//$resultat = insert_record ('analytics',addslashes_object($connexio));
		    		
		    			/*if(!$resultat){
		    				//borrem les taules
		    				borra_taules($dades['nom']);
		    				return false;
		    			}*/
		    			$dades['id']=$resultatId;
	    			}	
	    		}
    		
    		
    			
    		}
    	}
    	return $dades;
    }
    
/*
     * obte les dades d'un registre concret
     * 
     */
    function dades_entorn($id){
    	
    	if(!$id){
    		return false;
    	}
    	$resultat = new stdClass;
    	$resultat = get_record('analytics','id',$id);
    	if(!$resultat){
    		return false;
    	}
    	else{
    		return $resultat;
    	}
    		
    }
    
    function get_dades(){    	
    	$mform = $this->get_mform();
    	$fromform = $mform->_submitValues;
    	unset($fromform['sesskey']); // we do not need to return sesskey
        unset($fromform['_qf__'.$this->_formname]);   // we do not need the submission marker too
        unset($fromform['MAX_FILE_SIZE']);
    	return $fromform;
    }
    
    
    function is_validated(){
    	return true;
    }
} // fi de la classe

?>

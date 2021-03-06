<?php
global $CFG;
$ADM->modul = 'service';
$ADM->site = get_record_sql("SELECT * FROM {$CFG->prefix}course WHERE id = 1");

$parts = explode('/',$CFG->dirroot);
$ADM->nominstancia = $parts[count($parts)-1];
require_once ($CFG->libdir.'/ddllib.php');

/**
 * Nova funció que ens retorna els entorns que té accessibles un usuari determinat
 * @author judit.nieto
 * @param unknown_type $configure
 */

function entorns_disponibles(){
    $sort = "nom_client ASC";
    return get_records('analytics','','', $sort);	
}

function entorns_server(){
    $sort = "nom ASC";
    return get_records('analytics_backends','','', $sort);	
}

/*
 * es connecta a lentorn que li passen per parametre
 * 
 * 
 */
function preparar_entorn($id){
	global $ADM;
	if(!$id){
    	return false;
    }
    $resultat = new stdClass;
    $resultat = get_record('analytics','id',$id);
    if(!$resultat){
    	return false;
    }
    //guardem en la global el que necessitem per consultar	
	$ADM->entorn_actual=$resultat;
	return $resultat;
}


function entorn_acutal(){
	global $ADM;
	return $ADM->entorn_actual->nom;
	
}


function nom_entorn($id){
	if(!$id){
    	return false;
    }
    $resultat = new stdClass;
    $resultat = get_record('analytics','id',$id);
    if(!$resultat){
    	return false;
    }

    return $resultat->nom;
}


/**
 * funcio que connecta al servidor gauss
 * NOTA: usa $ADM->gauss
 */
function generic_connect() {
	global $ADM,$CFG;
	//incloim l'adodb
	require_once("$CFG->libdir/adodb/adodb.inc.php");
	//ocultem els errors
	//error_reporting(0);
	if(!isset($ADM->generichandler)||!$ADM->generichandler){
		$ADM->generichandler = &ADONewConnection($ADM->entorn_actual->dbtype);
		//connectem
		if (!isset($ADM->entorn_actual->dbpersist) or !empty($ADM->entorn_actual->dbpersist)) {
		    $ADM->dbconnectedgeneric = $ADM->generichandler->PConnect($ADM->entorn_actual->dbhost,$ADM->entorn_actual->dbuser,
		    										$ADM->entorn_actual->dbpass,$ADM->entorn_actual->dbname);
		} else {
		    $ADM->dbconnectedgeneric = $ADM->generichandler->Connect($ADM->entorn_actual->dbhost,$ADM->entorn_actual->dbuser,
		    										$ADM->entorn_actual->dbpass,$ADM->entorn_actual->dbname);
		}
		if (!$ADM->dbconnectedgeneric) {
			return false;
		}
		return true;
	}
	return true;
}


function generic_close(){
	global $ADM;
	if($ADM->generichandler && $ADM->dbconnectedgeneric){
		$tancat = $ADM->generichandler->Close();
		if($tancat){
			unset($ADM->generichandler);
		}else{
			return false;
		}
	}
	return true;
}

/**
 * Realitza una query (select, update, delete) a Saurus
 *
 * @param String la query
 * @return resultat de la query
 */
function generic_query($sql){
	global $CFG,$ADM;
	
	if (!isset($ADM->dbconnectedgeneric) || !$ADM->dbconnectedgeneric) {
		generic_connect();
	}
	if (!$rs = $ADM->generichandler->Execute($sql)) {
		if (isset($CFG->debug) and $CFG->debug > 7) {
		    notify($ADM->generichandler->ErrorMsg()."<br /><br />$sql");
		}
		return false;
	}
	return $rs;
}


/**
 * realitza una query i retorna el primer resultat
 *
 * @param $query
 */
function generic_query_fetch_cris ($query) {
	$rs = generic_query($query);
	if (!$rs) return false;
	$res = generic_fetch($rs);
	pg_free_result($rs);
	return $res;
}

/**
 * realitza una query i retorna el primer resultat
 *
 * @param $query
 */
function generic_query_fetch ($query) {
	$rs = generic_query($query);
	if (!$rs){
		return false;
	}
	$res = generic_fetch($rs);
//	pg_free_result($rs);
	return $res;
}


/**
 * Fa un fetching d'un recordset
 *
 * @param recordset $rs
 * @return o false o l'object del record
 */
function generic_fetch($rs){
	if(!$obj = $rs->FetchNextObj()){
		return false;
	}
	//print_object($obj);
	return $obj;
}


/**
 * retorna el prefix de les taules de Saurus
 *
 * @return String
 */
function generic_prefix () {
	global $ADM;
	return $ADM->entorn_actual->prefix;
}


/*
 * encapsula la funcio count_records_select
 * 
 */

function generic_count_records_select($table,$where){

	$query = "SELECT COUNT(*) AS quants FROM ".$table.' WHERE '.$where;
	//print_object($query);
	$resultat = generic_query_fetch($query);
	//print_object($resultat);
	
	return $resultat->quants;	
}

function crea_taules($nom,$familia){
	global $CFG;
	//
	//
    // Core Analytics tables for Instance
    //
  
    $result = true;
    
    //New Table Indicadors Site UPC   
    $table = new XMLDBTable("analytics_{$nom}_site");
    $table->comment = 'Information about Indicadors of Site';
        // fields
    $f = $table->addFieldInfo('id',                 XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $f->comment = 'Unique Host ID';
    $f = $table->addFieldInfo('logins',        XMLDB_TYPE_INTEGER,  '8', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
    $f = $table->addFieldInfo('logins_unicos', XMLDB_TYPE_INTEGER,  '8', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
    $f = $table->addFieldInfo('guest_logins',  XMLDB_TYPE_INTEGER,  '8', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
    $f = $table->addFieldInfo('uploads',       XMLDB_TYPE_INTEGER,  '8', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
    $f = $table->addFieldInfo('max',           XMLDB_TYPE_INTEGER,  '8', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
    $f = $table->addFieldInfo('time_max',           XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 1);
    $f = $table->addFieldInfo('time_start',           XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 1);
    $f = $table->addFieldInfo('time_end',           XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 1);
    // PK and indexes
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Add Index
    $table->addIndexInfo('time_start', XMLDB_INDEX_NOTUNIQUE, array('time_start'));
    $table->addIndexInfo('time_end', XMLDB_INDEX_NOTUNIQUE, array('time_end'));
    // Create the table
    $result = $result && create_table($table, true, false);
	
	//New Table Indicadors Assig UPC    
    $table = new XMLDBTable("analytics_{$nom}_assig");
    $table->comment = 'Information about Indicadors of Assig';
        // fields
    $f = $table->addFieldInfo('id',                 XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $f->comment = 'Unique Host ID';
    $f = $table->addFieldInfo('categoryid',        XMLDB_TYPE_INTEGER,  '8', null,
                                  null, null, null, null, 0);
    $f = $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER,  '8', null,
                                  null, null, null, null, 0);
    $f = $table->addFieldInfo('visites',  XMLDB_TYPE_INTEGER,  '8', null,
                                  null, null, null, null, 0);
    $f = $table->addFieldInfo('visites_uniques',       XMLDB_TYPE_INTEGER,  '8', null,
                                  null, null, null, null, 0);
    $f = $table->addFieldInfo('guest',           XMLDB_TYPE_INTEGER,  '8', null,
                                  null, null, null, null, 0);
    $f = $table->addFieldInfo('max',           XMLDB_TYPE_INTEGER,  '8', null,
                                  null, null, null, null, 0);
    $f = $table->addFieldInfo('time_max',           XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 1);
    $f = $table->addFieldInfo('time_start',           XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 1);
    $f = $table->addFieldInfo('time_end',           XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 1);
    // PK and indexes
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Add Index
    $table->addIndexInfo('time_start', XMLDB_INDEX_NOTUNIQUE, array('time_start'));
    $table->addIndexInfo('time_end', XMLDB_INDEX_NOTUNIQUE, array('time_end'));
    // Create the table
    $result = $result && create_table($table, true, false);
	
	//New Table Indicadors Mod UPC  
    
    $table = new XMLDBTable("analytics_{$nom}_mod");
    $table->comment = 'Information about Indicadors of Modules';
        // fields
    $f = $table->addFieldInfo('id',                 XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $f->comment = 'Unique Host ID';
    $f = $table->addFieldInfo('categoryid',        XMLDB_TYPE_INTEGER,  '8', null,
                                  null, null, null, null, 0);
    $f = $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER,  '8', null,
                                  null, null, null, null, 0);
    $f = $table->addFieldInfo('module',  XMLDB_TYPE_CHAR,  '255', null,
                                  XMLDB_NOTNULL, null, null, null, 0);
    $f = $table->addFieldInfo('read',       XMLDB_TYPE_INTEGER,  '8', null,
                                  null, null, null, null, 0);
    $f = $table->addFieldInfo('write',           XMLDB_TYPE_INTEGER,  '8', null,
                                  null, null, null, null, 0);
    $f = $table->addFieldInfo('time_start',           XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 1);
    $f = $table->addFieldInfo('time_end',           XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 1);
    // PK and indexes
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Add Index
    $table->addIndexInfo('time_start', XMLDB_INDEX_NOTUNIQUE, array('time_start'));
    $table->addIndexInfo('time_end', XMLDB_INDEX_NOTUNIQUE, array('time_end'));
    // Create the table
    //$result = $result && create_table($table, true, false);
    $result = $result && create_table($table, true, false);

    //New Table Indicadors Sessions  
    
    $table = new XMLDBTable("analytics_{$nom}_sessions");
    $table->comment = 'Information about Indicadors of Modules';
        // fields
    $f = $table->addFieldInfo('id',       XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $f->comment = 'Unique Host ID';
    $f = $table->addFieldInfo('smoodle',  XMLDB_TYPE_INTEGER,  '8', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
    $f = $table->addFieldInfo('spostgres', XMLDB_TYPE_INTEGER,  '8', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
	
	$f = $table->addFieldInfo('time_executed',  XMLDB_TYPE_DATETIME,  null, null,
	    	                      XMLDB_NOTNULL, null, null, null, null);
	                                  
    // PK and indexes
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    // Add Index
    $table->addIndexInfo('time_executed', XMLDB_INDEX_NOTUNIQUE, array('time_executed'));
    // Create the table
    //$result = $result && create_table($table, true, false);
    $result = $result && create_table($table, true, false);
    return $result;
}


function borra_taules($nom){
	global $CFG;
	drop_table(new XMLDBTable("analytics_{$nom}_site"));
    drop_table(new XMLDBTable("analytics_{$nom}_assig"));
    drop_table(new XMLDBTable("analytics_{$nom}_mod"));
    drop_table(new XMLDBTable("analytics_{$nom}_sessions"));
}

//---------Admin_upc
//---------misc.lib
/**
 * monta la navegació del print header de 1.9 (objecte) a partir del de 1.6 (string)
 * @param string $navigation navegació en string
 * @return array
 */
function nav_upc ($navigation) {
	global $CFG;
	
    if (!is_array($navigation)) {
        $ar = explode('->', $navigation);
        $navigation = array();

        foreach ($ar as $a) {
            if (strpos($a, '</a>') === false) {
                $navigation[] = array('name' => $a, 'link' => '', 'type' => 'activity');
            } else {
                if (preg_match('/<a.*href="([^"]*)">(.*)<\/a>/', $a, $matches)) {
                    $navigation[] = array('name' => $matches[2], 'link' => $matches[1], 'type' => 'activity');
                }
            }
        }
    }
    return build_navigation($navigation);
}
//--------gestor.lib (Aquesta s'ha d'eliminar)
/**
 * retorna tots els campus d'un gestor
 * NOTA: carrega $ADM->campus amb el nom dels campus accessibles
 * @param $gestor=false: un objecte user_gestor
 * @param $names=true: si se li passa false, retornarà només el campus campus per comptes de campus=>nomcampus
 * @return false o un array associatiu campus => nomcampus
 */
function get_campus_gestor($gestor=false,$names=true) {
	//global $options, $dirgestor, $campus, $modul;
	global $ADM;
	//si no ens passem gestor, suposem que és l'actual
	if (!$gestor && isset($ADM->gestor)) {
		$gestor = $ADM->gestor;
	}
	if ($gestor==false) return false;
	
	$options = array();
	//$idres = array();
	for ($i=0;$i<count($gestor->idue);$i++) {
		//si ens demanen nomes els noms, els posem
		if (!$names) {
			//$idres[$gestor->campusids[$gestor->idue[$i]]] = $gestor->idue[$i];
			$options[] = $gestor->idue[$i];
		} else {
			$options[$gestor->idue[$i]] = get_string("campus", $ADM->modul)." ".strtoupper($gestor->idue[$i]);
		}
	}
	$dirgestor =  $gestor->nomperfil;
	if (!isset($ADM->campus) || $ADM->campus=="") {
		$ADM->campus = key($options);
	}
	//if ($ids) return $idres;
	return $options;
}

/**
 * mira si l'usuari actual és un gestor. Si ho és, retorna una estrcutura user_gestor:
 *   user_gestor->id: id de l'usuari mirat (el parámetre passat)
 *   user_gestor->nomperfil: un string que identifica el perfil ("admin"...)
 *   user_gestor->iduser: id de l'usuari gestor
 *   user_gestor->idue: un array amb tots els centres que gestiona
 * NOTA: també carrega el resultat a la global $ADM->gestor
 * @param $id=false: id de l'usuari (l'actual si false)
 * @return false o un user_gestor
 */
function isgestor($iduser=false) {
	global $ADM,$USER;
	if (!$iduser) {
		$iduser = $USER->id;
	}
	$gestor = false;
	if(isset($ADM->gestor))
	return $ADM->gestor;
	
	// Si es admin cargamos todas las ue's
	if (isadmin($iduser)) {
		$res = get_records_sql("select * from {$CFG->prefix}campus_upc ORDER BY campus");
		$gestor = new stdClass;
    	$gestor->id = $iduser;
    	$gestor->nomperfil = "admin";
    	$gestor->iduser = $iduser;
        $i=0;
    	foreach ($res as $value) {
    		$gestor->idue[$i] = $value->campus;
     		$i++;
     	}
	} else {	
		$i=0;
		//Agafem tots els campus		
		$res = get_records_sql("select * from {$CFG->prefix}campus_upc ORDER BY campus");
		foreach ($res as $value) {
     	    //per a cada campus mirem si és el gestor
     		$query = "select * from mdl_perfils_gestor_upc where idue='".$value->campus."'";
     		$res2 = get_records_sql($query);
     		if ($res2) {
	     		foreach ($res2 as $res3) {		
	     			if ($res3->iduser==$iduser) { //si és gestor del centre....
	     			if ($i==0) {  	     			
		    			$gestor = new stdClass;
		    			$gestor->id = $res3->id;
	    				$gestor->nomperfil = $res3->nom;
		    			$gestor->iduser = $res3->iduser;
	     			}	     		
	     			$gestor->idue[$i] = $res3->idue;
	     			//guardem els ids dels campus
	     			$gestor->campusids[$res3->idue] = $value->id;
		     		$i++;
	     			}
				}
     		}     		
     	}
	}
	$ADM->gestor = $gestor;
	return $gestor;     	
}
//--------mant.lib (Aquesta s'ha d'eliminar)
/**
 * retorna els ids dels rols de professor
 * 
 * @param bool $nomesedit=false: només retorna els rols dels professors editors
 * 
 * @return array d'ids (primer el no editor i després l'editor o només l'editor)
 */
function get_rols_profes ($nomesedit=false) {
	global $ADM;
	if (!$nomesedit) {
		if (!isset($ADM->rolsprofes)) $ADM->rolsprofes = array();
		if (!empty($ADM->rolsprofes)) return $ADM->rolsprofes;
		$rec = get_record ('role','shortname','teacher');
		if ($rec) $ADM->rolsprofes[] = $rec->id;
		$rec = get_record ('role','shortname','editingteacher');
		if ($rec) $ADM->rolsprofes[] = $rec->id;
		//$rec = get_record ('role_capabilities','contextid',1,'capability','moodle/legacy:teacher');
		//if ($rec) $ADM->rolsprofes[] = $rec->roleid;
		//$rec = get_record ('role_capabilities','contextid',1,'capability','moodle/legacy:editingteacher');
		//if ($rec) $ADM->rolsprofes[] = $rec->roleid;
		return $ADM->rolsprofes;
	} else {
		if (!isset($ADM->rolsprofesedit)) $ADM->rolsprofesedit = array();
		if (!empty($ADM->rolsprofesedit)) return $ADM->rolsprofesedit;
		//$rec = get_record ('role_capabilities','contextid',1,'capability','moodle/legacy:editingteacher');
		//if ($rec) $ADM->rolsprofesedit[] = $rec->roleid;
		$rec = get_record ('role','shortname','editingteacher');
		if ($rec) $ADM->rolsprofesedit[] = $rec->id;
		return $ADM->rolsprofesedit;
	}
}

/**
 * retorna l'id del rol editor de coordinador
 * 
 * @return integer
 */
function get_rol_coord () {
	global $ADM;
	if (isset($ADM->rolcoord)) return $ADM->rolcoord;
	$rec = get_record ('role','shortname','coord');
	if (!$rec) return false;
	$ADM->coord = $rec->id;
	return $ADM->rolcoord;
}
//--------wget.lib 
class exectophp {
   /**
     * Run Application in background
     *
     * @param    unknown_type $Command
     * @param    unknown_type $Priority
     * @return    PID
     */
   function backgroundtophp($Command, $Priority = 0){
      
       //$PID = shell_exec("$Command > /dev/null");
       	$txt = "$Command > /dev/null &";

		if (!isset($this->show) || $this->show) echo "\n".$txt.'<br/>';
		$PID = shell_exec($txt);
		return($PID);
   }
   /**
   * Check if the Application running !
   *
   * @param    unknown_type $PID
   * @return    boolen
   */
   function is_runningtophp($PID){
       exec("ps $PID", $ProcessState);
       return(count($ProcessState) >= 2);
   }
   /**
   * Kill Application PID
   *
   * @param  unknown_type $PID
   * @return boolen
   */
   function killtophp($PID){
       if(exec::is_running($PID)){
           exec("kill -KILL $PID");
           return true;
       }else return false;
   }
   
};

function run_script($url){
	global $CFG;
    run_online_url('http://localhost/'.basename($CFG->wwwroot).'/'.$url,false); 	
}
/**
 * executa una url
 * 
 * @param $url: la url a executar
 * @param $show=true: mostra la comanda
 */
 function run_online_url ($url,$show=true) {
 	global $CFG,$ADM;
		//mirem si tenim el nom de la carpeta
		$proces= new exectophp();
		//$txt = "wget -q -T 0 -O /dev/null http://localhost/mod/admin_upc/carregues/prova_cron.php";
		//$txt = "wget -q -T 0 -O /dev/null $url";
		$txt = "curl -s $url > /dev/null";
		$proces->show = $show;
		$proces->backgroundtophp($txt);
 }
//----Extra
	function imatge_semafor($estat = 0){
    	$imatge = "";
    	//$imatge = print_semafor_estat($estat.".png", 119, 49, true);
    	$imatge = print_semafor_estat($estat.".png", 46, 18, true);
    	return $imatge;
    		
    }
	/**
	 * Print a png image.
	 *
	 * @param string $url ?
	 * @param int $sizex ?
	 * @param int $sizey ?
	 * @param boolean $return ?
	 * @param string $parameters ?
	 * @todo Finish documenting this function
	 */
	function print_semafor_estat($url, $sizex, $sizey, $return, $parameters='alt=""') {
	    global $CFG;
	    static $recentIE;
		$output = '';
		
	    if (!isset($recentIE)) {
	        $recentIE = check_browser_version('MSIE', '5.0');
	    }
	
	    if ($recentIE) {  // work around the HORRIBLE bug IE has with alpha transparencies
	        $output .= '<img src="'. $CFG->pixpath .'/spacer.gif" width="'. $sizex .'" height="'. $sizey .'"'.
	                   ' class="png" style="width: '. $sizex .'px; height: '. $sizey .'px; '.
	                   ' filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.
	                   "'$url', sizingMethod='scale') ".
	                   ' '. $parameters .' />';
	    } else {
	        $output .= '<img src="'. $url .'" style="width: '. $sizex .'px; height: '. $sizey .'px; '. $parameters .' />';
	    }
	
	    if ($return) {
	        return $output;
	    } else {
	        echo $output;
	    }
	}
	
	
	//----- FUNCIONS GRAFIQUES ------

	/**
	 * Mostra una barra de notificació amb un missatge
	 * 
	 * @param String $msg: el missatge
	 * @param boolean return=false: si de li passa true retorna l'string
	 * @param boolean $closeable=true: si pot tancar-se o no
	 * @return res o un String
	 */
	function mostrar_barra ($msg,$return=false,$closeable=true) {
		global $ADM,$CFG;
		/*if (!isset($ADM->barres)) $ADM->barres = 0;
		$ADM->barres++;
		
		//posem el botó de tancar
		$msg = "<span style=\"float:right;cursor:pointer;padding-right:5px;\" ".
			"onClick=\"document.getElementById('barra$ADM->barres').style.display = 'none'\">".
			'<img src="'.$CFG->wwwroot.'/pix/t/delete.gif"/>'.
			"</span>".$msg;
		
		//$output.= print_box($msg.' '.$box,'box userinfobox','barra'.$ADM->boxes,$return);
		$classes = 'box userinfobox';
	    $output = print_box_start($classes, 'barra'.$ADM->barres, true);*/
		$output = obre_barra(true,$closeable);
	    $output.= stripslashes_safe($msg);
	    $output.= tanca_barra(true);
	    //$output.= print_box_end(true);
	
	    if ($return) {
	        return $output;
	    } else {
	        echo $output;
	    }
	}
	
	/**
	 * obre una barra de missatge
	 * 
	 * @param boolean $return=false: si cal retorna un string o s'imprimeix
	 * @param boolean $closeable=true: si pot tancar-se o no
	 * @return string o res
	 */
	function obre_barra ($return=false,$closeable=true) {
		global $ADM,$CFG;
		if (!isset($ADM->barres)) $ADM->barres = 0;
		$ADM->barres++;
		$classes = 'box userinfobox';
	    $output = print_box_start($classes, 'barra'.$ADM->barres, true);
	    if ($closeable) {
		    $output.= "<span style=\"float:right;cursor:pointer;padding-right:5px;\" ".
				"onClick=\"document.getElementById('barra$ADM->barres').style.display = 'none'\">".
				'<img src="'.$CFG->wwwroot.'/pix/t/delete.gif"/>'.
				"</span>";
	    }
	    
		if ($return) {
	        return $output;
	    } else {
	        echo $output;
	    }
	}
	
	/**
	 * tanca una barra de missatge
	 * 
	 * @param boolean $return=false: si cal retorna un string o s'imprimeix
	 * @return string o res
	 */
	function tanca_barra($return=false) {
		return print_box_end($return);
}
	
function getReadSelect($module){
		switch ($module) {
			case 'forum':
				return "(action = 'view discussion' OR action = 'view forum')";
				break;
			case 'glossary':
				return "(action = 'view' OR action = 'view entry')";
				break;
			case 'journal':
				return "(action = 'view' OR action = 'view responses')";
				break;
			case 'lesson':
				return "(action = 'view' OR action = 'start')";
				break;
			case 'message':
				return "action = 'read'";
				break;
			case 'quiz':
				return "(action = 'view' OR action = 'start attempt' OR action = 'attempt')";
				break;
			case 'survey':
				return "action = 'view form'";
				break;
			case 'wiki':
				return "(action = 'diff' OR action = 'edit' OR action = 'info')";
				break;
			case 'workshop':
				return "(action = 'view' OR action = 'submit' OR action = 'assessments' OR action = 'assess')";
				break;
			case 'blog':
				return "action = 'blog view'";
				break;
			/*Casos amb registres iguals*/
			/*case 'assignment':
			case 'calendar':
			case 'chat':
			case 'choice':
			case 'internalmail':
			case 'label':
			case 'resource':
			case 'scorm':
			case 'data':
			case 'lams':
			case 'grade':*/
			default:
				return "action = 'view'";
				break;
		}
	}

	function getWriteSelect($module){
		switch ($module) {
			case 'forum':
				return "action = 'add%'";
				break;
			case 'glossary':
				return "(action = 'add' OR action = 'add entry' OR action = 'add comment')";
				break;
			case 'journal':
				return "(action = 'add' OR action = 'add entry')";
				break;
			case 'message':
				return "action = 'write'";
				break;
			case 'blog':
				return "action = 'blog add'";
				break;
			case 'grade':
				return "(action = 'import' OR action = 'edit')";
				break;
			/*Casos amb registres iguals
			case 'assignment':
			case 'calendar':
			case 'chat':
			case 'choice':
			case 'internalmail':
			case 'label':
			case 'resource':
			case 'scorm':
			case 'data':
			case 'lams':
			case 'lesson':
			case 'quiz':
			case 'survey':
			case 'wiki':
			case 'workshop':*/
			default:
				return "action = 'add'";
				break;
		}
	}
	
	
?>

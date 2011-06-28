<?PHP // $Id: index.php,v 1.1 2003/09/30 02:45:19 moodler Exp $


    require_once("../../../config.php");
    include_once "../lib.php";
	require_login();
    if (!isadmin()) {
    	print_error('chooseenv','service');
    }

    
    
/// Get all required strings
	$strindicadorss = get_string("modulenameplural", "indicadors");
	$title = get_string("rendiment",'indicadors');
    $nav_array = build_navigation(array(array('name'=>$title, 'link'=>null, 'type'=>'misc')));
    print_header($ADM->site->shortname.": ". $strindicadorss,$ADM->site->fullname,$nav_array);
   
    print_heading($title);                              
                                                          
/// Print the list of instances (your module will probably extend this)
	echo'<br />';
 	echo'<br />';
 	echo'<br />';
 	


 	
 	//print_object ($CFG);
 	//$row = array('<a href="./consult_log/index.php?id=1&entorn='.$entorn.'">'.get_string('logins', 'indicadors').'</a>');
 	$row = array('<a href="./consult_log/index.php">'.get_string('logins', 'indicadors').'</a>');
 	$rows[]=$row;
 	
 	//23/01/08: Nuevas consultas de usuarios no logados
 	//$row = array('<a href="./consult_loginko/index.php?id=1&entorn='.$entorn.'">'.get_string('loginsko', 'indicadors').'</a>');
 	$row = array('<a href="./consult_loginko/index.php">'.get_string('loginsko', 'indicadors').'</a>');
 	$rows[]=$row;
 	//$row = array('<a href="./consult_loginuserko/index.php?id=1&entorn='.$entorn.'">'.get_string('loginsuserko', 'indicadors').'</a>');
 	$row = array('<a href="./consult_loginuserko/index.php">'.get_string('loginsuserko', 'indicadors').'</a>');
	//23/01/08: Nuevas consultas de usuarios no logados -->FIN
 	
 	$rows[]=$row;
 	//$row = array('<a href="'.$CFG->wwwroot.'/mod/admin_upc/rendiment/consult_conc/index.php?id=1">'.get_string('concurrents', 'indicadors') .'</a>');
 	//$rows[]=$row;
 	//$row = array('<a href="./consult_actius/index.php?id=1&entorn='.$entorn.'">'.get_string('actius', 'indicadors') .'</a>');
 	$row = array('<a href="./consult_actius/index.php">'.get_string('actius', 'indicadors') .'</a>');
 	$rows[]=$row;
 	//$row = array('<a href="'.$CFG->wwwroot.'/mod/admin_upc/rendiment/consult_activ/view.php?id=1&option=dia">'.get_string('activitats', 'indicadors').'</a>');
 	//$rows[]=$row;
 	//$row = array('<a href="http://stan.upc.es/sessions/">Consulta log d\'Activitat</a>');
 	//$row = array('<a href="./consult_log_activ/view.php?id=1&entorn='.$entorn.'">Consulta log d\'Activitat</a>');
 	$row = array('<a href="./consult_log_activ/view.php">Consulta log d\'Activitat</a>');
 	$rows[]=$row;
 	//Nuevo Indicador Diario de Actividades. Sólo permite ver por dia. Es el complemento a los indicadores por Mes de actividades.
 	//$row = array('<a href="./consult_diari_activ/index.php?id=1&entorn='.$entorn.'">Consulta Indicadors diaris d\'Activitat</a>');
 	$row = array('<a href="./consult_diari_activ/index.php">Consulta Indicadors diaris d\'Activitat</a>');
 	$rows[]=$row;
 	//nou indicador sessions moodle de cada instancia de la infraestrucutrfa
 	//$row = array('<a href="./consult_log_sessions/view.php?id=1&entorn='.$entorn.'">Consulta Sessions Moodle de les instàncies</a>');
 	$row = array('<a href="./consult_log_sessions/view.php">Consulta Sessions Moodle de les instàncies</a>');
 	$rows[]=$row;
 	//Analytics 2011
 	$table->wrap = array ('nowrap');
	$table->align = array ('center');
	$table->data = $rows;
	$table->width = '80%';
	print_table($table);

    print_footer();

?>

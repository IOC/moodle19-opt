<?php
/**
 * Created on 26/04/2007
 * Modified on 06/05/2011
 * 
 * @author david.castro, pau.ferrer-ocana
 * @package analytics
 * 
 */

require_once ('../../../config.php');
require_once ('../lib.php');
set_time_limit(0);

//recollim l'entorn que cal carregar
$entornid = optional_param('entorn');
$entorn = preparar_entorn($entornid);

//Record de dates.
//Timestamp de ayer
$yesterday = time() - (24*60*60);

$any = date ("Y", $yesterday); //2007
$mes = date ("n", $yesterday); //4 -->Abril
$dia = date ("j", $yesterday); //1-31

$time_start = mktime(0,0,0,$mes,$dia,$any);
$time_end = mktime(23,59,59,$mes,$dia,$any);

echo date("d/m/Y H:i:s", time()).' - Calculant site: '.date("d/m/Y H:i:s", $time_start).' a '.date("d/m/Y H:i:s", $time_end)."<br/>\n";

//conexio generica a la taula que toqui
if(generic_connect()){
	$prefix = $entorn->prefix;
	
	//Record d'informacio.
	$logins = 0;
	$logins_unicos = 0;
	$guest_logins = 0;
	$uploads = 0;
	$max = 0;
	$time_max = mktime(0,0,0,$mes,$dia,$any);
	
	//Informacio a emmagatzamar
	//aixo va sobre la local i volen anar contra la que ens connectem
	
	//Informacio del guest
	$query = "SELECT id FROM {$prefix}user WHERE username='guest'";
	$guest = generic_query_fetch ($query);
	$guestid = $guest->id;
	
	//Càlcul de logins, únics i no únics
	$query = "SELECT count(distinct userid) as visites_uniques, count(*) as visites FROM {$prefix}log WHERE time BETWEEN $time_start AND $time_end AND module = 'user' AND action = 'login' AND userid <> $guestid";
	$visits = generic_query_fetch ($query);
	
	$logins = $visits->visites;
	$logins_unicos = $visits->visites_uniques;
		
	$guest_logins = generic_count_records_select($prefix.'log',"time BETWEEN $time_start AND $time_end AND module = 'user' AND action = 'login' AND userid = $guestid");
	$uploads = generic_count_records_select($prefix.'log',"time BETWEEN $time_start AND $time_end AND module = 'upload' AND action = 'upload'");
	
	for ($i = 0; $i < 24; $i++) {
		$start = mktime($i,0,0,$mes,$dia,$any);
		$end   = mktime($i,59,59,$mes,$dia,$any);
		
		$count = generic_count_records_select($prefix.'log', "time BETWEEN $start AND $end AND module = 'user' AND action = 'login'");
		if($count > $max){
			$max = $count;
			$time_max = $start;
		}
	}
	
	//Insetem la informacio.
	$row = new Stdclass();	
	$row->uploads = $uploads;
	$row->logins = $logins;
	$row->logins_unicos = $logins_unicos;
	$row->guest_logins = $guest_logins;
	$row->max = $max;
	$row->time_max = $time_max;
	$row->time_start = $time_start;
	$row->time_end = $time_end;

	if (insert_record("analytics_{$entornid}_site", $row)){
		echo 'Inserit'."<br/>\n";
	}else{
		echo 'No s\'ha inserit'."<br/>\n";	
	}
	
	//tanquem la conexio fent una nova funcio de close generic
	generic_close();
	
	echo date("d/m/Y H:i:s", time()).' - Fi'."<br/>\n";
	
}else{
	print_error('connectionproblem','analytics');	
}
?>

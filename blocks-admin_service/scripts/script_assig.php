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
@raise_memory_limit('256M');

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

echo date("d/m/Y H:i:s", time()).' - Calculant assig: '.date("d/m/Y H:i:s", $time_start).' a '.date("d/m/Y H:i:s", $time_end)."<br/>\n";

//conexio generica a la taula que toqui
if(generic_connect()){
	$prefix = $entorn->prefix;
	
	//Record d'informacio.
	$visites = 0;
	$visites_uniques = 0;
	$num_guests = 0;
	$time_max = mktime(0,0,0,$mes,$dia,$any);

	//Agafen els identificadors dels cursos diferents en el periode temps
	$query= "SELECT course FROM {$prefix}log WHERE time BETWEEN $time_start AND $time_end GROUP BY course";
	$courses = generic_query($query);
	
	//Informacio del guest
	$query = "SELECT id FROM {$prefix}user WHERE username='guest'";
	$guest = generic_query_fetch ($query);
	$guestid = $guest->id;

	//Tractem tots el cursos
	while ($course = generic_fetch($courses)){
		$courseid = $course->course;
		
		//Saltem els cursos de no assignatures.
		if ($courseid == 0 || $courseid == 1) continue;
		//Informacio del curs. Preparem la informacio rellevant
		
		$query = "SELECT category FROM {$prefix}course WHERE id ={$courseid}";
		$categoryid = generic_query_fetch($query);
		
		$category = $categoryid->category;
		
		//Calculem la informacio rellevant.
		//Usuaris registrats 
		//Dos en un, visites usuaris registrats i visites usuaris registrats uniques!!
		$query = "SELECT count(distinct userid) as visites_uniques, count(*) as visites FROM {$prefix}log WHERE time BETWEEN $time_start AND $time_end AND module = 'course' AND course = $courseid AND action LIKE 'view%' AND userid <> $guestid";
		$visits = generic_query_fetch ($query);
		
		$visites = $visits->visites;
		$visites_uniques = $visits->visites_uniques;
		
		//Usuaris guests
		$num_guests = generic_count_records_select($prefix.'log',"time BETWEEN $time_start AND $time_end AND module = 'course' AND course = $courseid AND action LIKE 'view%' AND userid = $guestid");
		//Maxim d'usuaris per hora
		$max_per_hour = 0;
		for ($i = 0; $i < 24; $i++) {
			//echo '<br/>la i:'.$i.' it <br/>';
			$start = mktime($i,0,0,$mes,$dia,$any);
			$end = mktime($i,59,59,$mes,$dia,$any);
			$count = generic_count_records_select($prefix.'log', "time BETWEEN $start AND $end AND module = 'course' AND course = $courseid AND action LIKE 'view%'");
			if($count > $max_per_hour){
				$max_per_hour = $count;
				$time_max = $start;
			}
		}
		
		if ($visites != 0 || $visites_uniques != 0 || $num_guests != 0){
			//Insetem la informacio.
			$row = new Stdclass();
			$row->categoryid = $category;
			$row->courseid = $courseid;
			$row->visites = $visites;
			$row->visites_uniques = $visites_uniques;
			$row->guest = $num_guests;
			$row->max = $max_per_hour;
			$row->time_max = $time_max;
			$row->time_start = $time_start;
			$row->time_end = $time_end;
                                  
			if (insert_record("analytics_{$entornid}_assig", $row)){
				echo 'Inserit'."<br/>\n";
			}else{
				echo 'No s\'ha inserit'."<br/>\n";	
			}
		}
	}
	
	//final de la conexio
	//tanquem la conexio fent una nova funcio de close generic
	generic_close();
	
	echo date("d/m/Y H:i:s", time()).' - Fi'."<br/>\n";
}else{
	print_error('connectionproblem','analytics');	
}


 
 
?>

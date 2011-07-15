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
@raise_memory_limit('1024');

//recollim l'entorn que cal carregar
$entornid = optional_param('entorn');
$entorn = preparar_entorn($entornid);

//Record de dates.
//Timestamp de ayer
$first_day_of_last_month = mktime(1,0,0,date("n")-1, 1,date ("Y"));

$any = date ("Y", $first_day_of_last_month); 
$mes = date ("n", $first_day_of_last_month);
$days_in_month = date ("t", mktime(0,0,0,$mes,1,$any));

//First day of the month
$time_start = mktime(0,0,0,$mes,1,$any);
//Last day of the month
$time_end = mktime(23,59,59,$mes,$days_in_month,$any);

echo date("d/m/Y H:i:s", time()).' - Calculant mod: '.date("d/m/Y H:i:s", $time_start).' a '.date("d/m/Y H:i:s", $time_end)."<br/>\n";

//Agafen els identificadors dels cursos diferents en el periode temps

//conexio generica a la taula que toqui
if(generic_connect()){
	$prefix = $entorn->prefix;
	
	$query= "SELECT course FROM {$prefix}log WHERE time BETWEEN $time_start AND $time_end GROUP BY course";
	$courses = generic_query($query);
	
	//Tractem tots el cursos
	while ($course = generic_fetch($courses)){
		$courseid = $course->course;
		
		//Saltem els cursos de no assignatures.
		if ($courseid == 0 || $courseid == 1) continue;
		
		//Calculem la informacio rellevant.
		$query = "SELECT category FROM {$prefix}course WHERE id ={$courseid}";
		$categoryid = generic_query_fetch($query);
		
		$category = $categoryid->category;
		
		//Moduls actius
		$moduls = array('calendar','grade');
        if ($allmods = get_records('modules') ) {
			foreach ($allmods as $mod) {
				$moduls[] = $mod->name;
			}
		}
		
		//Informacio dels moduls.
		foreach ($moduls as $mod) {
			$read_sel = getReadSelect($mod);
			$write_sel = getWriteSelect($mod);
			
			$read = generic_count_records_select("{$prefix}log", "time BETWEEN $time_start AND $time_end AND module = '$mod' AND $read_sel AND course=$courseid");
			$write = generic_count_records_select("{$prefix}log", "time BETWEEN $time_start AND $time_end AND module = '$mod' AND $write_sel AND course=$courseid");
			
			if ($read != 0 || $write != 0){
				//Insetem la informacio.
				$row = new Stdclass();
				$row->read = $read;
				$row->write = $write;
				$row->module = $mod;
				$row->categoryid = $category;
				$row->courseid = $courseid;
				$row->time_start = $time_start;
				$row->time_end = $time_end;
				
				if (insert_record("analytics_{$entornid}_mod", $row)){
					echo 'Inserit'."<br/>\n";
				}else{
					echo 'No s\'ha inserit'."<br/>\n";	
				}
			}
		}
	}
	generic_close();
	
	echo date("d/m/Y H:i:s", time()).' - Fi'."<br/>\n";
}else{
	print_error('connectionproblem','analytics');	
}

?>

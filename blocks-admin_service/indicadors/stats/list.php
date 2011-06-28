<?php
if (!isset($CFG->dirroot)) die ('Esteu fora del sistema.');

//requires
$ruta_stat = $CFG->dirroot.'/blocks/admin_service/indicadors/stats/';

require_once ($ruta_stat.'stat_base.class.php');
require_once ($ruta_stat.'activitats.class.php');
require_once ($ruta_stat.'accessos.class.php');

//llista dels stats que es presentaran
$ADM->stats = array();

$ADM->stats[] = 'stat_base';
$ADM->stats[] = '<hr>';
$ADM->stats[] = 'activitats';
$ADM->stats[] = '<hr>';
$ADM->stats[] = 'accessos';
?>

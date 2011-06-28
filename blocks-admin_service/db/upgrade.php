<?php  //$Id: upgrade.php,v 1.1 2006/10/26 16:33:49 stronk7 Exp $

// This file keeps track of upgrades to 
// the activity_modules block
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_block_admin_service_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }

    
    if ($result && $oldversion < 2011040501) {

    /// Define field timemodified to be added to forum_queue
    //    $table = new XMLDBTable('analytics_sessions');
    //    $field = new XMLDBField('time_executed');
	//	$field->setAttributes(XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, null, null, null);
	//	$table->addIndexInfo('time_executed', XMLDB_INDEX_NOTUNIQUE, array('time_executed'));
    /// Launch add field timemodified
    //    $result = $result && add_field($table, $field);
    }
    
    return $result;
}

?>

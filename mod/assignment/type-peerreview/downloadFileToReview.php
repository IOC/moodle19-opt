<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Peer Review submission download script
 *
 * @package    contrib
 * @subpackage assignment_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once("../../../../config.php");
global $CFG, $USER;
require_once($CFG->dirroot."/mod/assignment/lib.php");

// Get course ID and assignment ID
$id   = optional_param('id', 0, PARAM_INT);          // Course module ID
$a    = optional_param('a', 0, PARAM_INT);           // Assignment ID

if ($id) {
    if (! $cm = get_coursemodule_from_id('assignment', $id)) {
        error("Course Module ID was incorrect");
    }
    if (! $assignment = get_record("assignment", "id", $cm->instance)) {
        error("assignment ID was incorrect");
    }
    if (! $course = get_record("course", "id", $assignment->course)) {
        error("Course is misconfigured");
    }
} else {
    if (!$assignment = get_record("assignment", "id", $a)) {
        error("Course module is incorrect");
    }
    if (! $course = get_record("course", "id", $assignment->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("assignment", $assignment->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
}

// Check user is logged in and capable of submitting
require_login($course->id, false, $cm);
require_capability('mod/assignment:submit', get_context_instance(CONTEXT_MODULE, $cm->id));
if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', 'error');
}

/// Load up the required assignment code
require('assignment.class.php');
$assignmentclass = 'assignment_peerreview';
$assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm, $course);

// Determine which file to send
if($reviewsToDownload = get_records_select('assignment_review','assignment=\''.$a.'\' and reviewer=\''.$USER->id.'\'ORDER BY id ASC')) {
    $reviewsToDownload = array_values($reviewsToDownload);
    while(count($reviewsToDownload)>0 && $reviewsToDownload[0]->complete==1) {
        array_shift($reviewsToDownload);
    }
    if(count($reviewsToDownload)!=0) {
    
        // Set the file status to downloaded
        $reviewsToDownload[0]->downloaded = 1;
        $reviewsToDownload[0]->timedownloaded = time();
        $reviewsToDownload[0]->timemodified = $reviewsToDownload[0]->timedownloaded;
        update_record('assignment_review',$reviewsToDownload[0]);

        // Send the file, force download
        require_once($CFG->libdir.'/filelib.php');
        $filearea = $CFG->dataroot.'/'.$assignmentinstance->file_area_name($reviewsToDownload[0]->reviewee);
        $files = get_directory_list($filearea, '', false);
        send_file($filearea.'/'.$files[0], assignment_peerreview::FILE_PREFIX.(2-count($reviewsToDownload)+1).'.'.$assignmentinstance->assignment->fileextension,60,0,false,true);

    }
    else {
        error(get_string('reviewscomplete','assignment_peerreview'));
    }
}
else {
    error(get_string('reviewsnotallocated','assignment_peerreview'));
}

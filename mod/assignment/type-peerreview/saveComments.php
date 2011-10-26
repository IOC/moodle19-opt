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
 * Peer Review "pre-saved" comment saving script
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
print_header();

// Get course ID and assignment ID
$id   = optional_param('id', 0, PARAM_INT);          // Course module ID
$a    = optional_param('a', 0, PARAM_INT);           // Assignment ID
$comments = optional_param('comments',NULL,PARAM_RAW);

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
require_capability('mod/assignment:grade', get_context_instance(CONTEXT_MODULE, $cm->id));
if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', 'error');
}

if(set_field('assignment_peerreview','savedcomments',$comments,'assignment',$assignment->id)) {
    print_heading(get_string('commentssaved','assignment_peerreview'),'center',1);
}
else {
    notify(get_string('unabletosavecomments','assignment_peerreview'));
}
require('assignment.class.php');
$assignmentclass = 'assignment_peerreview';
$assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm, $course);

echo '<p align="center"><a href="#null" onclick="window.close();">'.get_string('close','assignment_peerreview').'</a></p>';
close_window(1);

$assignmentinstance->view_footer();

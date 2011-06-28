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
 * Peer Review mark saving script
 *
 * @package    contrib
 * @subpackage assignment_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once("../../../../config.php");
global $SESSION, $CFG, $USER;
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot."/mod/assignment/lib.php");
print_header();

// Get course ID and assignment ID
$id     = optional_param('id', 0, PARAM_INT);          // Course module ID
$a      = optional_param('a', 0, PARAM_INT);           // Assignment ID
$userid = required_param('userid');
$grade  = required_param('mark');

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

/// Load up the required assignment code
require('assignment.class.php');
$assignmentclass = 'assignment_peerreview';
$assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm, $course);

$grading_info = grade_get_grades($course->id, 'mod', 'assignment', $assignment->id, $userid);

if (!$grading_info->items[0]->grades[$userid]->locked &&
!$grading_info->items[0]->grades[$userid]->overridden &&
$submission = $assignmentinstance->set_grade($userid,$grade)
) {

echo '<script>';
if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['finalgrade'])) {
    echo 'if(opener.document.getElementById(\'gvalue'.$userid.'\')) {'."\n";
    echo '    opener.document.getElementById("g'.$userid.'").innerHTML="'.$assignmentinstance->display_grade($grade)."\";\n";
    echo "}\n";
}		
echo '</script>';
echo '<div align="center">';
print_heading(get_string('gradeset','assignment_peerreview'),1);
echo $submission->mailed?get_string('emailsent','assignment_peerreview'):get_string('emailnotsent','assignment_peerreview');
}
else {
print_heading(get_string('unsabletoset','assignment_peerreview'),1);
}
echo '<p><a href="#null" onclick="window.close();">'.get_string('close','assignment_peerreview').'</a></p>';
echo '</div>';
close_window(1);

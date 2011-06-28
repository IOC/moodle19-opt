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
 * Allows a student to view a peer's online text submission and records that it has been downloaded
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
$view = required_param('view',PARAM_TEXT);

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

/// Load up the required assignment code
require('assignment.class.php');
$assignmentclass = 'assignment_peerreview';
$assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm, $course);

// Check user is logged in and capable of submitting
require_login($course->id, false, $cm);
if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', 'error');
}

if($view=='peerreview' || $view=='selfview') {
    require_capability('mod/assignment:submit', get_context_instance(CONTEXT_MODULE, $cm->id));
    if($view=='peerreview') {
        // Determine which text to send
        if($reviewsToDownload = get_records_select('assignment_review','assignment=\''.$a.'\' and reviewer=\''.$USER->id.'\'ORDER BY id ASC')) {
            $reviewsToDownload = array_values($reviewsToDownload);
            while(count($reviewsToDownload)>0 && $reviewsToDownload[0]->complete==1) {
                array_shift($reviewsToDownload);
            }
            if(count($reviewsToDownload)!=0) {

                // Set the file status to downloaded
                set_field('assignment_review','downloaded','1','id',$reviewsToDownload[0]->id);
                set_field('assignment_review','timemodified',time(),'id',$reviewsToDownload[0]->id);

                // Send the submission text
                print_header(get_string('reviewnumber','assignment_peerreview',2-count($reviewsToDownload)+1));
                print_heading(get_string('reviewnumber','assignment_peerreview',2-count($reviewsToDownload)+1),"center");
                if ($submission = $assignmentinstance->get_submission($reviewsToDownload[0]->reviewee)) {
                    print_heading(get_string('keepopenwhilereviewing','assignment_peerreview'),"center",3);
                    print_simple_box(format_text(stripslashes($submission->data1), PARAM_CLEAN), 'center', '100%');
                } else {
                    print_simple_box(get_string('emptysubmission', 'assignment'), 'center', '100%');
                }
                print_footer('none');
                
            }
            else {
                error(get_string('reviewscomplete','assignment_peerreview'));
            }
        }
        else {
            error(get_string('reviewsnotallocated','assignment_peerreview'));
        }
    }
    else if($view=='selfview') {
        print_header(get_string('yoursubmission','assignment_peerreview'));
        print_heading(get_string('yoursubmission','assignment_peerreview'),"center");
        if ($submission = $assignmentinstance->get_submission($USER->id)) {
            print_simple_box(format_text(stripslashes($submission->data1), PARAM_CLEAN), 'center', '100%');
        } else {
            print_simple_box(get_string('emptysubmission', 'assignment'), 'center', '100%');
        }
        print_footer('none');
    }
}
else if($view=='moderation') {
    require_capability('mod/assignment:grade', get_context_instance(CONTEXT_MODULE, $cm->id));
    $userid = optional_param('userid',null,PARAM_TEXT);
    $user = get_record('user', 'id', $userid);
    print_header(fullname($user));
    print_heading(fullname($user));
    if ($submission = $assignmentinstance->get_submission($userid)) {
        print_simple_box(format_text(stripslashes($submission->data1), PARAM_CLEAN), 'center', '100%');
    } else {
        print_simple_box(get_string('emptysubmission', 'assignment'), 'center', '100%');
    }
    print_footer('none');
}

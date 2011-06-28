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
 * Peer Review resubmission page
 *
 * @package    contrib
 * @subpackage assignment_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once("../../../../config.php");
global $CFG;
require_once($CFG->dirroot."/mod/assignment/lib.php");

// Get course ID and assignment ID
$id     = optional_param('id', 0, PARAM_INT);          // Course module ID
$a      = optional_param('a', 0, PARAM_INT);           // Assignment ID
$userid = required_param('userid', PARAM_INT);         // User ID

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

// Get the student info
$student = get_record('user','id',$userid);

// Header
$navigation = build_navigation($assignmentinstance->strsubmissions, $assignmentinstance->cm);
print_header_simple(format_string($assignmentinstance->assignment->name,true), "", $navigation,
        '', '', true, update_module_button($assignmentinstance->cm->id, $assignmentinstance->course->id, $assignmentinstance->strassignment), navmenu($assignmentinstance->course, $assignmentinstance->cm));
print_heading(get_string('resubmission','assignment_peerreview').": ".fullname($student),1);

if(optional_param('save',NULL,PARAM_TEXT)!=NULL) {

    if (isset($assignmentinstance->assignment->var3) && $assignmentinstance->assignment->var3==assignment_peerreview::ONLINE_TEXT) {
        $submission = $assignmentinstance->get_submission($userid);
        $submission->timemodified = time();
        $submission->data1 = required_param('text',PARAM_CLEANHTML);
        if (update_record('assignment_submissions', $submission)) {
            add_to_log($assignmentinstance->course->id, 'assignment', 'upload', 
                    'view.php?a='.$assignmentinstance->assignment->id, $assignmentinstance->assignment->id, $assignmentinstance->cm->id);
            notify(get_string('resubmissionsuccessful','assignment_peerreview'),'notifysuccess');
        }
        else {
            notify(get_string("uploadnotregistered", "assignment", $newfile_name) );
        }
    }
    else {
        // Process the resubmission
        $dir = $assignmentinstance->file_area_name($userid);
        require_once($CFG->dirroot.'/lib/uploadlib.php');
        $um = new upload_manager('newfile',true,false,$assignmentinstance->course,false,$assignmentinstance->assignment->maxbytes);
        if($um->preprocess_files()) {
        
            //Check the file extension
            $submittedFilename = $um->get_original_filename();
            $extension = $assignmentinstance->assignment->fileextension;
            if(strtolower(substr($submittedFilename,strlen($submittedFilename)-strlen($extension))) != $extension) {
                notify(get_string("incorrectfileextension","assignment_peerreview",$extension));
            }
            
            // Save the new file and delete the old	
            else if ($um->save_files($dir)) {
                $newfile_name = $um->get_new_filename();
                $um->config->silent = true;
                $um->delete_other_files($dir,$dir.'/'.$newfile_name);
                $submission = $assignmentinstance->get_submission($userid);
                if (set_field('assignment_submissions','timemodified', time(), 'id',$submission->id)) {
                    add_to_log($assignmentinstance->course->id, 'assignment', 'upload', 
                            'view.php?a='.$assignmentinstance->assignment->id, $assignmentinstance->assignment->id, $assignmentinstance->cm->id);
                    notify(get_string('resubmissionsuccessful','assignment_peerreview'),'notifysuccess');
                }
                else {
                    notify(get_string("uploadnotregistered", "assignment", $newfile_name) );
                }
            }
        }
    }
    print_continue($CFG->wwwroot.'/mod/assignment/submissions.php?id='.$assignmentinstance->cm->id);
}
else {
    if (isset($assignmentinstance->assignment->var3) && $assignmentinstance->assignment->var3==assignment_peerreview::ONLINE_TEXT) {
        notify(get_string("resubmissionwarning","assignment_peerreview"));
        $mform = new mod_assignment_peerreview_edit_form($CFG->wwwroot.'/mod/assignment/type/peerreview/resubmit.php',array('id'=>$assignmentinstance->cm->id,'a'=>$assignmentinstance->assignment->id,'userid'=>$userid));
        $mform->display();
    }
    else {
    
        // Show form for resubmission
        notify(get_string("resubmissionwarning","assignment_peerreview"));
        require_once($CFG->libdir.'/filelib.php');
        $icon = mimeinfo('icon', 'xxx.'.$assignmentinstance->assignment->fileextension);
        $type = mimeinfo('type', 'xxx.'.$assignmentinstance->assignment->fileextension);
        $struploadafile = get_string("uploada","assignment_peerreview") . "&nbsp;" .
                          "<img align=\"middle\" src=\"".$CFG->pixpath."/f/".$icon."\" class=\"icon\" alt=\"".$icon."\" />" .
                          "<strong>" . $type . "</strong>&nbsp;" .
                          get_string("file","assignment_peerreview") . "&nbsp;" .
                          get_string("witha","assignment_peerreview") . "&nbsp;<strong>." .
                          $assignmentinstance->assignment->fileextension . "</strong>&nbsp;" .
                          get_string("extension","assignment_peerreview");
        $strmaxsize = get_string("maxsize", "", display_size($assignmentinstance->assignment->maxbytes));

        echo '<div style="text-align:center">';
        echo '<form enctype="multipart/form-data" method="post" '.
             "action=\"$CFG->wwwroot/mod/assignment/type/peerreview/resubmit.php\">";
        echo '<fieldset class="invisiblefieldset">';
        echo "<p>$struploadafile ($strmaxsize)</p>";
        echo '<input type="hidden" name="id" value="'.$assignmentinstance->cm->id.'" />';
        echo '<input type="hidden" name="a" value="'.$assignmentinstance->assignment->id.'" />';
        echo '<input type="hidden" name="userid" value="'.$userid.'" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        require_once($CFG->libdir.'/uploadlib.php');
        upload_print_form_fragment(1,array('newfile'),false,null,0,$assignmentinstance->assignment->maxbytes,false);
        echo '<input type="submit" name="save" value="'.get_string('uploadthisfile').'" />';
        echo '</fieldset>';
        echo '</form>';
        echo '</div>';
    }
}

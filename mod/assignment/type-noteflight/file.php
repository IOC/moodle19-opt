<?php  // $Id: file.php,v 1.6 2006/08/31 08:51:09 toyomoyo Exp $
/*
 * Copyright (c) 2008, 2009 Noteflight LLC
 *
 * This file is part of The Noteflight Learning Edition plugin for Moodle.
 *
 *    The Noteflight Learning Edition plugin for Moodle is free software:
 *    you can redistribute it and/or modify it under the terms of the
 *    GNU General Public License as published by the Free Software Foundation,
 *    either version 2 of the License, or (at your option) any later version.
 *
 *    The Noteflight Learning Edition plugin for Moodle
 *    is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with The Noteflight Learning Edition plugin for Moodle.
 *    If not, see <http://www.gnu.org/licenses/>.
 */

    require("../../../../config.php");
    require("../../lib.php");
    require("assignment.class.php");

    $id     = required_param('id', PARAM_INT);      // Course Module ID
    $userid = required_param('userid', PARAM_INT);  // User ID

    if (! $cm = get_coursemodule_from_id('assignment', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $assignment = get_record("assignment", "id", $cm->instance)) {
        error("Assignment ID was incorrect");
    }

    if (! $course = get_record("course", "id", $assignment->course)) {
        error("Course is misconfigured");
    }

    if (! $user = get_record("user", "id", $userid)) {
        error("User is misconfigured");
    }

    require_login($course->id, false, $cm);

    if (($USER->id != $user->id) && !has_capability('mod/assignment:grade', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        error("You can not view this assignment");
    }

    if ($assignment->assignmenttype != 'noteflight') {
        error("Incorrect assignment type");
    }

    $assignmentinstance = new assignment_noteflight($cm->id, $assignment, $cm, $course);

    if ($submission = $assignmentinstance->get_submission($user->id)) {
        print_header(fullname($user,true).': '.$assignment->name);

        print_simple_box_start('center', '', '', '', 'generalbox', 'dates');

        // Look for links that appear to be Noteflight scores, and append the student's SSO id
        // to them so that we will view the student's copy.
        //
        // TODO: match the description's original link text and reproduce it here.

        preg_match_all('#((?:https?)://[^\s\'"<>()]+)#S', $assignment->description, $matches, PREG_SET_ORDER);
        $i = 1;
        foreach ($matches as $m)
        {
          if (preg_match('#/scores/view/[0-9a-f]+$#', $m[0]))
          {
            echo '<a href="' . $m[0] . '?copy_author_sso_user_id=' . $userid . '"' .
              ' target="_new">View student copy of score ' . ($i++) . '</a><br/>';
          }
        }
        echo '<table>';
        if ($assignment->timedue) {
            echo '<tr><td class="c0">'.get_string('duedate','assignment').':</td>';
            echo '    <td class="c1">'.userdate($assignment->timedue).'</td></tr>';
        }
        echo '<tr><td class="c0">'.get_string('lastedited').':</td>';
        echo '    <td class="c1">'.userdate($submission->timemodified);
        /// Decide what to count
            if ($CFG->assignment_itemstocount == ASSIGNMENT_COUNT_WORDS) {
                echo ' ('.get_string('numwords', '', count_words(format_text($submission->data1, $submission->data2))).')</td></tr>';
            } else if ($CFG->assignment_itemstocount == ASSIGNMENT_COUNT_LETTERS) {
                echo ' ('.get_string('numletters', '', count_letters(format_text($submission->data1, $submission->data2))).')</td></tr>';
            }
        echo '</table>';
        print_simple_box_end();

        print_simple_box(format_text($submission->data1, $submission->data2), 'center', '100%');
        close_window_button();
        print_footer('none');
    } else {
        print_string('emptysubmission', 'assignment');
    }

?>

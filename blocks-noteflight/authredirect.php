<?php
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

// This handler performs the SSO signature on an incoming URI, making use of
// global contexts to guess the user role.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/blocks/noteflight/sso_signing_utils.php');
require_login();

$original_uri = required_param('original_uri');

// make use of Moodle user ID and username as-is
$sso_user_id = $USER->id;
$sso_username = $USER->username;

// Assume user is student, but do some context permission checks to see
// if they are an admin or an instructor.
//
$sso_user_role = 'student';
$context = get_context_instance(CONTEXT_SYSTEM);
if (has_capability('moodle/course:manageactivities', $context) ||
      has_capability('moodle/site:manageblocks', $context)) {
    $sso_user_role = 'admin';
}
else {
    $courses = get_courses('all', 'c.sortorder ASC', 'c.id');
    if ($courses) {
        foreach ($courses as $course) {
          $context = get_context_instance(CONTEXT_COURSE, $course->id);
          if (has_capability('moodle/course:manageactivities', $context)) {
              $sso_user_role = 'instructor';
              break;
          }
        }
    }
}

// sign the URI and redirect to it.
$signed_uri = create_sso_url($original_uri,
                             $sso_user_id,
                             $sso_user_role,
                             $sso_username,
                             $CFG->block_noteflight_secret,
                             120);

redirect ($signed_uri);
?>
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
 * Peer Review version informatin
 *
 * @package    contrib
 * @subpackage assignment_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

$plugin->version = 2011061400; // YYYYMMDDnn for when the plugin was created

// Version required: Moodle 1.9.7 for Assignment type fix (see http://tracker.moodle.org/browse/MDL-16796)

$plugin->requires = 2007101570; // Look in <moodleroot>/mod/assignment/version.php for the minimum version allowed here
 
// For earlier versions...
//  - Simple fix: lower the version above and add the following line to /mod/lang/assignment.php
//
//    $string['typepeerreview'] = 'Peer Review';
//
//  - Generic fix: follow bug fix above to generic solution for pre-1.9.7 versions

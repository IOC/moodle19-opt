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

// This block does very little beyond provide a link to Noteflight.  All the SSO action is
// located in authredirect.php.

class block_noteflight extends block_base {
    function init() {
        $this->version = 2009021700;
        $this->title = get_string('noteflight', 'block_noteflight');
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function get_content() {

        global $CFG, $USER;

        if (!isloggedin()) {
            return false;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;

        $this->content->text   = '<a href="' . $CFG->block_noteflight_linkurl . '">' .
                                 $CFG->block_noteflight_linktext . '</a>';
        $this->content->footer = '';

        return $this->content;
    }
}

?>

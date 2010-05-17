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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');

/**
 * Plugin for no authentication.
 */
class auth_plugin_noteflight extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_noteflight() {
        $this->authtype = 'noteflight';
        $this->config = get_config('auth/noteflight');
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        return false;
    }

    /**
     * Hook for overriding behavior of logout page.
     * This method is called from login/logout.php page for all enabled auth plugins.
     */
    function logoutpage_hook() {
        global $USER, $CFG;     // use $USER->auth to find the plugin used for login
        global $redirect; // can be used to override redirect after logout

        $redirect = $CFG->block_noteflight_linkurl . '/logout';
    }
}

?>

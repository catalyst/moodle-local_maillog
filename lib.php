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
 * Mail log plugin by Catalyst IT.
 * Update Moodle user bounce counts and delete mail message
 * Delete old bounce messages
 * Expunge
 *
 * @package local_maillog
 * @author  Sumaiya Javed <sumaiya.javed@catalyst.net.nz>
 * @copyright 2013 onwards Catalyst IT Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Records the date of the last update to queuemails admin setting.
 *
 * @return void
 */
function local_maillog_notify() {
    // Add time when the queuemails setting is updated.
    set_config('queuemailsdate', time(), 'local_maillog');
}
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
 * Maillog upgrade library.
 *
 * @package    local_maillog
 * @copyright  2020 Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_maillog_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 20201106000) {

    // Define field originscript to be added to mail_log.
    $table = new xmldb_table('mail_log');
    $field = new xmldb_field('originscript', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'queuestatus');

    // Conditionally launch add field originscript.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Maillog savepoint reached.
    upgrade_plugin_savepoint(true, 20201106000, 'local', 'maillog');
    }

    return true;
}

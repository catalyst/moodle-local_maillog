<?php
/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright 2022 Catalyst IT
 * @author Dan Marsden
 * @package local_maillog
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_local_csp_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2022090900) {
        $table = new xmldb_table('mail_log');
        $field = new xmldb_field('attachment_list', XMLDB_TYPE_TEXT, null, null, null, null, null, 'returnmsg');

        // Conditionally launch add field and set the default to LEGACY for existing seminars
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        upgrade_plugin_savepoint(true, 2022090900, 'local', 'maillog');
    }
}
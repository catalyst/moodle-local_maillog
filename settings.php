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
 * Settings for Maillog plugin.
 *
 * @package   local_maillog
 * @author    Eugene Venter <eugene@catalyst.net.nz>
 * @copyright 2013 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) { // needs this condition or there is error on login page
    $settings = new admin_settingpage('local_maillog', 'Mail log');
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configcheckbox('local_maillog/logmails', get_string('logmails', 'local_maillog'), get_string('configlogmails', 'local_maillog'), 1));

    $settings->add(new admin_setting_configcheckbox('local_maillog/queuemails', get_string('queuemails', 'local_maillog'), get_string('configqueuemails', 'local_maillog', $CFG->wwwroot.'/local/maillog/mailqueue.php'), 0));


    $daysoptions = range(1, 30);
    $daysoptions = array_combine(array_values($daysoptions), $daysoptions);  // fix index
    $settings->add(new admin_setting_configselect('local_maillog/maxdays',
        new lang_string('maxdays', 'local_maillog'), new lang_string('maxdaysinfo', 'local_maillog'), 7, $daysoptions));

    // Add log report to reports.
    $ADMIN->add('reports', new admin_externalpage('maillogreport',
        get_string('pluginname', 'local_maillog'), new moodle_url('/local/maillog/maillogreport.php')));
}
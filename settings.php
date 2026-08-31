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
 * Settings for Mail log.
 *
 * @package    local_maillog
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\setting\part\category;
use core\setting\part\page;
use core\setting\type\checkbox;
use core\setting\type\select;

defined('MOODLE_INTERNAL') || die;

// Needs this condition or there is error on login page.
if ($hassiteconfig) {
    $ADMIN->add('localplugins', new category('local_maillog_settings', new lang_string('pluginname', 'local_maillog')));
    $settingspage = new page('managelocalmaillog', new lang_string('pluginname', 'local_maillog'));

    if ($ADMIN->fulltree) {
        require_once("$CFG->dirroot/local/maillog/lib.php");

        $settingspage->add(new checkbox('local_maillog/logmails', get_string('logmails', 'local_maillog'), get_string('configlogmails', 'local_maillog'), 1));

        $setting = new checkbox(
            'local_maillog/queuemails',
            get_string('queuemails', 'local_maillog'),
            get_string('configqueuemails', 'local_maillog', $CFG->wwwroot . '/local/maillog/mailqueue.php'),
            0
        );
        $setting->set_updatedcallback('local_maillog_notify');
        $settingspage->add($setting);

        $daysoptions = range(1, 30);
        $daysoptions = array_combine(array_values($daysoptions), $daysoptions);  // fix index
        $settingspage->add(new select(
            'local_maillog/maxdays',
            new lang_string('maxdays', 'local_maillog'),
            new lang_string('maxdaysinfo', 'local_maillog'),
            7,
            $daysoptions
        ));
    }

    $ADMIN->add('localplugins', $settingspage);
}

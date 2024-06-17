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

namespace local_maillog\task;

use core\task\adhoc_task;
use local_maillog\helper;

/**
 * Mail log purge log task.
 *
 * @package   local_maillog
 * @author    Matthew Hilton <matthewhilton@catalyst-au.net>
 * @copyright 2024 Catalyst IT Australia
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class purge_log_adhoc extends adhoc_task {

    /**
     * Executes purge
     */
    public function execute() {
        // Purge old mail log entries
        $maxdays = get_config('local_maillog', 'maxdays');
        if (empty($maxdays)) {
            $maxdays = 7;
        }
        mtrace("Deleting log entries older than {$maxdays} days.");
        helper::purge($maxdays);
    }
}

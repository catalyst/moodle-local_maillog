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
 * Mail log purge log task.
 *
 * @package   local_maillog
 * @author    Eugene Venter <eugene@catalyst.net.nz>
 * @copyright 2013 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_maillog\task;

/**
 * Purge log entries older than configured timeframe.
 */
class purge_log extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('task:purgelog', 'local_maillog');
    }

    /**
     * Execute task
     */
    public function execute() {
        // Spawn adhoc task to do the purge, in case it fails it can be retried gracefully.
        $task = new purge_log_adhoc();

        // Deduplicate in case the previous one has not finished yet or one is already queued.
        \core\task\manager::queue_adhoc_task($task, true);
    }
}

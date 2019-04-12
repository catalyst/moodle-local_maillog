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
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @package local_maillog
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

    public function execute() {
        // Purge old mail log entries
        $maxdays = get_config('local_maillog', 'maxdays');
        if (empty($maxdays)) {
            $maxdays = 7;
        }
        mtrace("Deleting log entries older than {$maxdays} days.");
        \local_maillog\helper::purge($maxdays);
    }
}

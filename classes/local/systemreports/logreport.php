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

namespace local_maillog\local\systemreports;

use core_reportbuilder\system_report;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\entities\user;
use local_maillog\local\entities\maillog;

/**
 * Base class for system reports
 *
 * @package   local_maillog
 * @author    Leah Skinner <leahskinner@catalyst.net.nz>
 * @copyright 2013 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class logreport extends system_report {
    protected function initialise(): void {
        // Our main entity, it contains all of the column definitions that we need.
        $entitymain = new maillog();
        $entitymainalias = $entitymain->get_table_alias('mail_log');

        $this->set_main_table('mail_log', $entitymainalias);
        $this->add_entity($entitymain);

        // Any columns required by actions should be defined here to ensure they're always available.
        $this->add_base_fields("{$entitymainalias}.id");

        // We can join the "user" entity to our "main" entity and use the fullname column from the user entity.
        $entityuser = new user();
        $entituseralias = $entityuser->get_table_alias('user');
        $this->add_entity($entityuser->add_join(
            "LEFT JOIN {user} {$entituseralias} ON {$entituseralias}.id = {$entitymainalias}.userid"
        ));

        require_once(__DIR__ . '/../helper.php');
        $statusparam = database::generate_param_name();
        $wheresql = "$entitymainalias.queuestatus = :{$statusparam}";
        $params = [$statusparam => LOCAL_MAILLOG_STATUS_SENT];
        $this->add_base_condition_sql($wheresql, $params);

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();

        // Set if report can be downloaded.
        $this->set_downloadable(true, get_string('pluginname', 'local_maillog'));
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('moodle/site:config', \context_system::instance());
    }

    /**
     * Get the visible name of the report
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('pluginname', 'local_maillog');
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    public function add_columns(): void {
        $columns = [
            'maillog:toaddress',
            'user:fullnamewithlink',
            'maillog:fromaddress',
            'maillog:subject',
            'maillog:messagetext',
            'maillog:hasattachment',
            'maillog:timesent',
            'maillog:originscript',
            'maillog:success',
        ];

        $this->add_columns_from_entities($columns);
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $filters = [
            'maillog:toaddress',
            'user:fullname',
            'maillog:fromaddress',
            'maillog:subject',
            'maillog:messagetext',
            'maillog:timesent',
            'maillog:originscript',
            'maillog:success',
        ];

        $this->add_filters_from_entities($filters);
    }

}

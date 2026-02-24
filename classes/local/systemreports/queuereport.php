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
 * Mail queue system level report.
 *
 * @package   local_maillog
 * @author    Sasha Anastasi <sasha.anastasi@catalyst.net.nz>
 * @copyright 2026 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_maillog\local\systemreports;

use local_maillog\local\entities\mailqueue;
use core_reportbuilder\system_report;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\report\action;
use core_reportbuilder\local\helpers\database;

class queuereport extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        $context = \context_system::instance();
        require_capability('local/maillog:managequeue', $context);

        // Our main entity, it contains all of the column definitions that we need.
        $entitymain = new mailqueue();
        $entitymainalias = $entitymain->get_table_alias('mail_log');

        $this->set_main_table('mail_log', $entitymainalias);
        $this->add_entity($entitymain);

        // Base fields required for action callbacks and checkbox toggle.
        $this->add_base_fields("{$entitymainalias}.id");
        $this->set_checkbox_toggleall(static function(\stdClass $row): array {
            return [$row->id, get_string('select')];
        });

        // We can join the "user" entity to our "main" entity using standard SQL JOIN.
        $entityuser = new user();
        $entityuseralias = $entityuser->get_table_alias('user');
        $this->add_entity($entityuser
            ->add_join("LEFT JOIN {user} {$entityuseralias} ON {$entityuseralias}.id = {$entitymainalias}.userid")
        );

        require_once(__DIR__ . '/../helper.php');
        $statusparam = database::generate_param_name();
        $wheresql = "$entitymainalias.queuestatus <> :{$statusparam}";
        $params = [$statusparam => LOCAL_MAILLOG_STATUS_SENT];
        $this->add_base_condition_sql($wheresql, $params);

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();
        $this->add_actions();
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('local/maillog:managequeue', \context_system::instance());
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    public function add_columns(): void {
        $columns = [
            'user:fullnamewithlink',
            'mailqueue:fromaddress',
            'mailqueue:toaddress',
            'mailqueue:subject',
            'mailqueue:timesent'
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
            'user:fullname',
            'mailqueue:subject',
            'mailqueue:timesent'
        ];

        $this->add_filters_from_entities($filters);
    }

    /**
     * Add actions to report
     */
    protected function add_actions(): void {
        // Send action.
        $this->add_action(new action(
            new \moodle_url('/local/maillog/queueaction.php', [
                'action' => 'send',
                'logid' => ':id',
            ]),
            new \pix_icon('t/email', ''),
            ['class' => 'text-info'],
            false,
            new \lang_string('send', 'core_message')
        ));

        // Delete action.
        $this->add_action(new action(
            new \moodle_url('/local/maillog/queueaction.php', [
                'action' => 'delete',
                'logid' => ':id',
            ]),
            new \pix_icon('t/delete', ''),
            ['class' => 'text-danger'],
            false,
            new \lang_string('delete')
        ));
    }
}
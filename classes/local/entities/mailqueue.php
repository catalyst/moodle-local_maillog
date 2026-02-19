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
 * Reporting entity for Maillog queue.
 *
 * @package   local_maillog
 * @author    Sasha Anastasi <sasha.anastasi@catalyst.net.nz>
 * @copyright 2026 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_maillog\local\entities;

use core_reportbuilder\local\filters\{date, text};
use core_reportbuilder\local\report\{column, filter};
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\helpers\format;
use lang_string;

class mailqueue extends base {

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {

        $columns = $this->get_all_columns();

        foreach ($columns as $column) {
            $this->add_column($column);
        }

        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this
                ->add_filter($filter)
                ->add_condition($filter);
        }

        return $this;
    }

    /**
     * Database tables that this entity uses
     *
     * @return array
     */
    protected function get_default_tables(): array {
        return [
            'mail_log',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('mailqueue', 'local_maillog');
    }

    /**
     * Add extra columns to mail queue report.
     * @return array
     * @throws \coding_exception
     */
    protected function get_all_columns(): array {
        $maillogalias = $this->get_table_alias('mail_log');

        $columns[] = (new column(
            'fromaddress',
            new lang_string('fromaddress', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("$maillogalias.fromaddress")
            ->set_type(column::TYPE_TEXT)
            ->set_is_sortable(true);
        $columns[] = (new column(
            'toaddress',
            new lang_string('toaddress', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("$maillogalias.toaddress")
            ->set_type(column::TYPE_TEXT)
            ->set_is_sortable(true);
        $columns[] = (new column(
            'subject',
            new lang_string('subject', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("$maillogalias.subject")
            ->set_type(column::TYPE_TEXT)
            ->set_is_sortable(true);
        $columns[] = (new column(
            'timesent',
            new lang_string('timequeued', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("$maillogalias.timesent")
            ->set_type(column::TYPE_TIMESTAMP)
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $filters = [];
        $maillogalias = $this->get_table_alias('mail_log');

        // Filter on subject
        $filters[] = (new filter(
            text::class,
            'subject',
            new lang_string('subject', 'local_maillog'),
            $this->get_entity_name(),
            "{$maillogalias}.subject"
        ))
            ->add_joins($this->get_joins());

        // Filter on time sent
        $filters[] = (new filter(
            date::class,
            'timesent',
            new lang_string('timequeued', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.timesent"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}
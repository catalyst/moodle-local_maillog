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

namespace local_maillog\local\entities;

use core_reportbuilder\local\filters\{date, duration, number, text};
use core_reportbuilder\local\report\{column, filter};
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\helpers\format;
use lang_string;

/**
 * Mail log task helper
 *
 * @package   local_maillog
 * @author    Leah Skinner <leahskinner@catalyst.net.nz>
 * @copyright 2013 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class maillog extends base {

    /**
     * Database tables that this entity uses
     *
     * @return array
     */
    protected function get_default_tables(): array {
        return [
            'mail_log',
            'user',
            'course',
        ];
    }
    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('entity_maillog', 'local_maillog');
    }

    /**
     * Initialise the entity, add all user fields and all 'visible' user profile fields
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


    // ADD ALL COLUMNS FROM MAIL_LOG TABLE


    /**
     * Add extra columns to course report.
     * @return array
     * @throws \coding_exception
     */
    protected function get_all_columns(): array {
        $maillogalias = $this->get_table_alias('mail_log');

        // User ID column
        $columns[] = (new column(
            'userid',
            new lang_string('userid', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$maillogalias}.userid");

        // From object column
        $columns[] = (new column(
            'fromobj',
            new lang_string('fromobj', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.fromobj");

        // To address column
        $columns[] = (new column(
            'toaddress',
            new lang_string('toaddress', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.toaddress");
        
        // From address column
        $columns[] = (new column(
            'fromaddress',
            new lang_string('fromaddress', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.fromaddress");
        
        // Subject column
        $columns[] = (new column(
            'subject',
            new lang_string('subject', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.subject");
        
        // Message text column
        $columns[] = (new column(
            'messagetext',
            new lang_string('messagetext', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.messagetext");
        
        // Message HTML column
        $columns[] = (new column(
            'messagehtml',
            new lang_string('messagehtml', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.messagehtml");
        
        // Attachment column
        $columns[] = (new column(
            'attachment',
            new lang_string('attachment', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.attachment");
        
        // Attachname column
        $columns[] = (new column(
            'attachname',
            new lang_string('attachname', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.attachname");
        
        // Use true address column
        $columns[] = (new column(
            'usetrueaddress',
            new lang_string('usetrueaddress', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$maillogalias}.usetrueaddress");
        
        // Reply to column
        $columns[] = (new column(
            'replyto',
            new lang_string('replyto', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.replyto");
        
        // Reply to name column
        $columns[] = (new column(
            'replytoname',
            new lang_string('replytoname', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.replytoname");
        
        // Word wrap width column
        $columns[] = (new column(
            'wordwrapwidth',
            new lang_string('wordwrapwidth', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$maillogalias}.wordwrapwidth");
        
        // Time sent column
        $columns[] = (new column(
            'timesent',
            new lang_string('timesent', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$maillogalias}.timesent")
            ->add_callback([format::class, 'userdate'], get_string('strftimedatetimeshortaccurate', 'core_langconfig'));
        
        // Success column
        $columns[] = (new column(
            'success',
            new lang_string('success', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$maillogalias}.success");
        
        // Return message column
        $columns[] = (new column(
            'returnmsg',
            new lang_string('returnmsg', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.returnmsg");
        
        // Queue status column
        $columns[] = (new column(
            'queuestatus',
            new lang_string('queuestatus', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_BOOLEAN)
            ->add_field("{$maillogalias}.queuestatus");
        
        // Origin script column
        $columns[] = (new column(
            'originscript',
            new lang_string('originscript', 'local_maillog'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_is_sortable(true)
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$maillogalias}.originscript");
        
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

        // User ID filter
        $filters[] = (new filter(
            number::class,
            'userid',
            new lang_string('userid', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.userid"
        ))
            ->add_joins($this->get_joins());

        // From object filter
        $filters[] = (new filter(
            text::class,
            'fromobj',
            new lang_string('fromobj', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.fromobj"
        ))
            ->add_joins($this->get_joins());

        // To address filter
        $filters[] = (new filter(
            text::class,
            'toaddress',
            new lang_string('toaddress', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.toaddress"
        ))
            ->add_joins($this->get_joins());

        // From address filter
        $filters[] = (new filter(
            text::class,
            'fromaddress',
            new lang_string('fromaddress', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.fromaddress"
        ))
            ->add_joins($this->get_joins());

        // Subject filter
        $filters[] = (new filter(
            text::class,
            'subject',
            new lang_string('subject', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.subject"
        ))
            ->add_joins($this->get_joins());

        // Message text filter
        $filters[] = (new filter(
            text::class,
            'messagetext',
            new lang_string('messagetext', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.messagetext"
        ))
            ->add_joins($this->get_joins());
            
        // Message HTML filter
        $filters[] = (new filter(
            text::class,
            'messagehtml',
            new lang_string('messagehtml', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.messagehtml"
        ))
            ->add_joins($this->get_joins());
            
        // Attachment filter
        $filters[] = (new filter(
            text::class,
            'attachment',
            new lang_string('attachment', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.attachment"
        ))
            ->add_joins($this->get_joins());

        // Attachname filter
        $filters[] = (new filter(
            text::class,
            'attachname',
            new lang_string('attachname', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.attachname"
        ))
            ->add_joins($this->get_joins());

        // Use true address filter
        $filters[] = (new filter(
            number::class,
            'usetrueaddress',
            new lang_string('usetrueaddress', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.usetrueaddress"
        ))
            ->add_joins($this->get_joins());

        // Reply to filter
        $filters[] = (new filter(
            text::class,
            'replyto',
            new lang_string('replyto', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.replyto"
        ))
            ->add_joins($this->get_joins());

        // Reply to name filter
        $filters[] = (new filter(
            text::class,
            'replytoname',
            new lang_string('replytoname', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.replytoname"
        ))
            ->add_joins($this->get_joins());

        // Word wrap width filter
        $filters[] = (new filter(
            number::class,
            'wordwrapwidth',
            new lang_string('wordwrapwidth', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.wordwrapwidth"
        ))
            ->add_joins($this->get_joins());

        // Time sent filter
        $filters[] = (new filter(
            number::class,
            'timesent',
            new lang_string('timesent', 'local_maillog'),
            $this->get_entity_name(),
            "$maillogalias.timesent"
        ))
            ->add_joins($this->get_joins());

        // Success filter
        $filters[] = (new filter(
        number::class,
        'success',
        new lang_string('success', 'local_maillog'),
        $this->get_entity_name(),
        "$maillogalias.success"
        ))
            ->add_joins($this->get_joins());

        // Return message filter
        $filters[] = (new filter(
        text::class,
        'returnmsg',
        new lang_string('returnmsg', 'local_maillog'),
        $this->get_entity_name(),
        "$maillogalias.returnmsg"
        ))
            ->add_joins($this->get_joins());     

        // Queue status filter
        $filters[] = (new filter(
        text::class,
        'queuestatus',
        new lang_string('queuestatus', 'local_maillog'),
        $this->get_entity_name(),
        "$maillogalias.queuestatus"
        ))
            ->add_joins($this->get_joins());  

        // Origin script filter
        $filters[] = (new filter(
        text::class,
        'originscript',
        new lang_string('originscript', 'local_maillog'),
        $this->get_entity_name(),
        "$maillogalias.originscript"
        ))
            ->add_joins($this->get_joins());  
            
        return $filters;
    }

}
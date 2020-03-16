<?php

/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 - 2013 Totara Learning Solutions LTD
 * Copyright (C) 1999 onwards Martin Dougiamas
 *
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
 * @package totara
 * @subpackage maillog
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/local/maillog/classes/helper.php');  // so we can use consts.

class rb_source_maillog extends rb_base_source {
    public $base, $joinlist, $columnoptions, $filteroptions;
    public $contentoptions, $paramoptions, $defaultcolumns;
    public $defaultfilters, $requiredcolumns, $sourcetitle;

    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        if ($groupid instanceof rb_global_restriction_set) {
            throw new coding_exception('Wrong parameter orders detected during report source instantiation.');
        }
        // Remember the active global restriction set.
        $this->globalrestrictionset = $globalrestrictionset;

        // Apply global user restrictions.
        $this->add_global_report_restriction_join('base', 'userid');

        $this->base = '{mail_log}';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();
        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();
        $this->requiredcolumns = $this->define_requiredcolumns();
        $this->sourcetitle = get_string('sourcetitle', 'rb_source_maillog');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_maillog');
        $this->usedcomponents[] = 'local_maillog';

        parent::__construct();
    }


    /**
     * Global report restrictions are implemented in this source.
     * @return boolean
     */
    public function global_restrictions_supported() {
        return true;
    }


    //
    //
    // Methods for defining contents of source
    //
    //

    protected function define_joinlist() {

        $joinlist = array();

        // include some standard joins
        $this->add_core_user_tables($joinlist, 'base', 'userid');

        return $joinlist;
    }

    protected function define_columnoptions() {
        $columnoptions = array(
            new rb_column_option(
                'maillog',
                'toaddress',
                get_string('toaddress', 'rb_source_maillog'),
                "base.toaddress",
                array('displayfunc' => 'user_email_unobscured')
            ),
            new rb_column_option(
                'maillog',
                'fromaddress',
                get_string('fromaddress', 'rb_source_maillog'),
                "base.fromaddress",
                array('displayfunc' => 'user_email_unobscured')
            ),
            new rb_column_option(
                'maillog',
                'subject',
                get_string('subject', 'rb_source_maillog'),
                "base.subject",
                array('displayfunc' => 'format_string')
            ),
            new rb_column_option(
                'maillog',
                'messagetext',
                get_string('messagetext', 'rb_source_maillog'),
                "base.messagetext",
                array('displayfunc' => 'plaintext')
            ),
            new rb_column_option(
                'maillog',
                'attachname',
                get_string('attachname', 'rb_source_maillog'),
                "base.attachname",
                array('displayfunc' => 'format_string')
            ),
            new rb_column_option(
                'maillog',
                'timesent',
                get_string('timesent', 'rb_source_maillog'),
                "base.timesent",
                array('displayfunc' => 'nice_datetime')
            ),
            new rb_column_option(
                'maillog',
                'success',
                get_string('success', 'rb_source_maillog'),
                'base.success',
                array('displayfunc' => 'yes_or_no')
            ),
            new rb_column_option(
                'maillog',
                'returnmsg',
                get_string('returnmsg', 'rb_source_maillog'),
                'base.returnmsg',
                array('displayfunc' => 'plaintext')
            ),
            new rb_column_option(
                'maillog',
                'queuestatus',
                get_string('queuestatus', 'rb_source_maillog'),
                'base.queuestatus',
                array('displayfunc' => 'maillog_queuestatus')
            ),
            new rb_column_option(
                'maillog',
                'checkbox',
                get_string('select', 'rb_source_maillog'),
                'base.id',
                array('displayfunc' => 'maillog_checkbox',
                      'noexport' => true,
                      'nosort' => true)
            ),
        );

        // include some standard columns
        $this->add_core_user_columns($columnoptions);

        return $columnoptions;
    }

    protected function define_filteroptions() {
        $filteroptions = array(
            new rb_filter_option(
                'maillog',              // type
                'fromaddress',         // value
                get_string('fromaddress', 'rb_source_maillog'), // label
                'text'              // filtertype
            ),
            new rb_filter_option(
                'maillog',              // type
                'toaddress',         // value
                get_string('toaddress', 'rb_source_maillog'), // label
                'text'              // filtertype
            ),
            new rb_filter_option(
                'maillog',              // type
                'subject',         // value
                get_string('subject', 'rb_source_maillog'), // label
                'text'              // filtertype
            ),
            new rb_filter_option(
                'maillog',              // type
                'messagetext',         // value
                get_string('messagetext', 'rb_source_maillog'), // label
                'text'              // filtertype
            ),
            new rb_filter_option(
                'maillog',              // type
                'attachname',         // value
                get_string('attachname', 'rb_source_maillog'), // label
                'text'              // filtertype
            ),
            new rb_filter_option(
                'maillog',              // type
                'timesent',     // value
                get_string('timesent', 'rb_source_maillog'), // label
                'date'              // filtertype
            ),
            new rb_filter_option(
                'maillog',        // type
                'success',        // value
                get_string('success', 'rb_source_maillog'), // label
                'select',           // filtertype
                array(
                    'selectfunc' => 'success_yesno',
                    'attributes' => rb_filter_option::select_width_limiter(),
                )
            ),
            new rb_filter_option(
                'maillog',              // type
                'returnmsg',         // value
                get_string('returnmsg', 'rb_source_maillog'), // label
                'text'              // filtertype
            ),
            new rb_filter_option(
                'maillog',              // type
                'queuestatus',     // value
                get_string('queuestatus', 'rb_source_maillog'), // label
                'select',              // filtertype
                array(
                    'selectfunc' => 'maillog_queuestatus',
                    'attributes' => rb_filter_option::select_width_limiter(),
                )
            ),

        );

        // include some standard filters
        $this->add_core_user_filters($filteroptions);

        return $filteroptions;
    }

    protected function define_contentoptions() {
        $contentoptions = array();

        return $contentoptions;
    }

    protected function define_paramoptions() {

        $paramoptions = array(
            new rb_param_option(
                'queuestatus',
                'base.queuestatus'
            ),
        );
        return $paramoptions;
    }

    protected function define_defaultcolumns() {
        $defaultcolumns = array(
            array(
                'type' => 'user',
                'value' => 'username',
            ),
            array(
                'type' => 'maillog',
                'value' => 'toaddress',
            ),
            array(
                'type' => 'maillog',
                'value' => 'fromaddress',
            ),
            array(
                'type' => 'maillog',
                'value' => 'subject',
            ),
            array(
                'type' => 'maillog',
                'value' => 'messagetext',
            ),
            array(
                'type' => 'maillog',
                'value' => 'timesent',
            ),
            array(
                'type' => 'maillog',
                'value' => 'success',
            ),
            array(
                'type' => 'maillog',
                'value' => 'returnmsg',
            ),
        );

        return $defaultcolumns;
    }

    protected function define_defaultfilters() {
        $defaultfilters = array(
            array(
                'type' => 'user',
                'value' => 'username',
                'advanced' => 0,
            ),
            array(
                'type' => 'maillog',
                'value' => 'fromaddress',
                'advanced' => 0,
            ),
            array(
                'type' => 'maillog',
                'value' => 'toaddress',
                'advanced' => 0,
            ),
            array(
                'type' => 'maillog',
                'value' => 'subject',
                'advanced' => 0,
            ),
            array(
                'type' => 'maillog',
                'value' => 'messagetext',
                'advanced' => 0,
            ),
            array(
                'type' => 'maillog',
                'value' => 'attachname',
                'advanced' => 0,
            ),
            array(
                'type' => 'maillog',
                'value' => 'timesent',
                'advanced' => 0,
            ),
            array(
                'type' => 'maillog',
                'value' => 'success',
                'advanced' => 1,
            ),
            array(
                'type' => 'maillog',
                'value' => 'returnmsg',
                'advanced' => 1,
            ),
        );

        return $defaultfilters;
    }

    protected function define_requiredcolumns() {
        $requiredcolumns = array(
            /*
            // array of rb_column objects, e.g:
            new rb_column(
                '',         // type
                '',         // value
                '',         // heading
                '',         // field
                array()     // options
            )
            */
        );
        return $requiredcolumns;
    }

    //
    //
    // Source specific filter display methods
    //
    //
    function rb_filter_success_yesno() {
        return array(
            1 => get_string('yes'),
            0 => get_string('no')
        );
    }

    function rb_filter_maillog_queuestatus() {
        return array(
            LOCAL_MAILLOG_STATUS_QUEUED => get_string('queued', 'rb_source_maillog'),
            LOCAL_MAILLOG_STATUS_PENDINGSEND => get_string('pendingsend', 'rb_source_maillog')
        );
    }

} // end of rb_source_maillog class

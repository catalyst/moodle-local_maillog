<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 Catalyst IT
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
 * @package local
 * @subpackage maillog
 */

global $CFG;
require_once($CFG->dirroot.'/totara/reportbuilder/classes/rb/content/base.php');
require_once($CFG->dirroot.'/local/maillog/classes/helper.php');  // so we can use the consts.

class rb_local_maillog_mailqueue_embedded extends rb_base_embedded {

    public $url, $source, $fullname, $filters, $columns;
    public $contentmode, $contentsettings, $embeddedparams;
    public $hidden, $accessmode, $accesssettings, $shortname;
    public $defaultsortcolumn, $defaultsortorder;

    public function __construct($data) {
        $url = new moodle_url('/local/maillog/mailqueue.php', $data);
        $this->url = $url->out_as_local_url();
        $this->source = 'maillog';
        $this->defaultsortcolumn = 'timesent';
        $this->shortname = 'local_maillog_mailqueue';
        $this->fullname = get_string('mailqueue', 'local_maillog');
        $this->columns = array(
            array(
                'type' => 'user',
                'value' => 'namelink',
                'heading' => get_string('name', 'rb_source_user'),
            ),
            array(
                'type' => 'maillog',
                'value' => 'fromaddress',
                'heading' => get_string('fromaddress', 'rb_source_maillog'),
            ),
            array(
                'type' => 'maillog',
                'value' => 'toaddress',
                'heading' => get_string('toaddress', 'rb_source_maillog'),
            ),
            array(
                'type' => 'maillog',
                'value' => 'subject',
                'heading' => get_string('subject', 'rb_source_maillog'),
            ),
            array(
                'type' => 'maillog',
                'value' => 'timesent',
                'heading' => get_string('timequeued', 'rb_source_maillog'),
            ),
            array(
                'type' => 'maillog',
                'value' => 'checkbox',
                'heading' => get_string('select', 'rb_source_maillog'),
            ),
        );

        // no filters
        $this->filters = array(
            array(
                'type' => 'user',
                'value' => 'fullname',
                'advanced' => 0,
            ),
            array(
                'type' => 'maillog',
                'value' => 'subject',
                'advanced' => 0,
            ),
            array(
                'type' => 'maillog',
                'value' => 'timesent',
                'advanced' => 0,
            ),
        );

        // only show queued items in the log
        $this->embeddedparams = array('queuestatus' => LOCAL_MAILLOG_STATUS_QUEUED);

        parent::__construct($data);
    }

    /**
     * Check if the user is capable of accessing this report.
     * We use $reportfor instead of $USER->id and $report->get_param_value() instead of getting params
     * some other way so that the embedded report will be compatible with the scheduler (in the future).
     *
     * @param int $reportfor userid of the user that this report is being generated for
     * @param reportbuilder $report the report object - can use get_param_value to get params
     * @return boolean true if the user can access this report
     */
    public function is_capable($reportfor, $report) {
        return true;
    }
}

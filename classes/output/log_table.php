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
 * Mail log queue/log.
 *
 * @package   local_maillog
 * @author    Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright 2020 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_maillog\output;

class log_table extends \table_sql {
    public function __construct($uniqueid, \moodle_url $url, $perpage = 100) {
        parent::__construct($uniqueid);

        $this->set_attribute('class', 'generaltable generalbox');
        $cols = [
            'to',
            'subject',
            'content',
            'hasattachment',
            'timesent',
            'originscript',
            'status',
        ];

        $this->define_columns($cols);
        $this->define_headers(array(
                get_string('to'),
                get_string('subject', 'hub'),
                get_string('content'),
                get_string('hasattachment', 'local_maillog'),
                get_string('timesent', 'local_maillog'),
                get_string('originscript', 'local_maillog'),
                get_string('status'),
            )
        );
        $this->pagesize = $perpage;
        $systemcontext = \context_system::instance();
        $this->context = $systemcontext;
        $this->collapsible(false);
        $this->sortable(false);
        $this->pageable(true);
        $this->is_downloadable(true);
        $this->define_baseurl($url);
    }

    public function col_to($row) {
        return $row->toaddress;
    }

    public function col_content($row) {
        if (!empty($row->messagehtml)) {
            if ($this->is_downloading()) {
                return $row->messagehtml;
            } else {
                return shorten_text($row->messagehtml, 100);
            }
        } else {
            if ($this->is_downloading()) {
                return $row->messagetext;
            }
            return shorten_text($row->messagetext, 100);
        }
    }

    public function col_hasattachment($row) {
        return !empty($row->attachname) ? get_string('yes') : get_string('no');
    }

    public function col_timesent($row) {
        return userdate($row->timesent, get_string('strftimedatetimeshort', 'langconfig'));
    }

    public function col_status($row) {
        return $row->success ? get_string('sent', 'local_maillog') : get_string('failed', 'local_maillog');
    }

    public function col_originscript($row) {
        return $row->originscript ?? 'Unknown';
    }
}

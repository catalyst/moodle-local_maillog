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

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('maillogreport');

$strheading = get_string('pluginname', 'local_maillog');

$perpage = 100;
$table = new \local_maillog\output\log_table('logtable', $PAGE->url);
// Handle table download.
$download = optional_param('download', '', PARAM_ALPHA);
$table->is_downloading($download, 'maillog');

if (!$table->is_downloading()) {
    $PAGE->set_title($strheading);
    $PAGE->set_heading($strheading);
    echo $OUTPUT->header();
}

// Setup the SQL query and display
$fields = 'userid, subject, messagetext, messagehtml, attachname, timesent, success';
$from = '{mail_log}';
$where = 'queuestatus = 0';
$table->set_sql($fields, $from, $where);
$table->out($perpage, false);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}

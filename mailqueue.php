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
 * @author    Eugene Venter <eugene@catalyst.net.nz>
 * @copyright 2013 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

require_login();

$context = context_system::instance();

require_capability('local/maillog:managequeue', $context);

$sid = optional_param('sid', '0', PARAM_INT);
$format = optional_param('format', '',PARAM_TEXT); //export format
$debug = optional_param('debug', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url('/local/maillog/mailqueue.php');
$PAGE->set_pagelayout('admin');

$strheading = get_string('mailqueue', 'local_maillog');

///
/// Display the page
///
$PAGE->navbar->add(get_string('pluginname', 'local_maillog'), new moodle_url('/admin/settings.php', array('section' => 'local_maillog')));
$PAGE->navbar->add($strheading);

$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

echo $OUTPUT->header();
echo $OUTPUT->heading($strheading, 1);



echo $OUTPUT->footer();

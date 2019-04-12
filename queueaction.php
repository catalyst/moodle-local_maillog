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
 *
 * @package    local_maillog
 * @copyright  2015 Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

$context = context_system::instance();

require_login();
require_capability('local/maillog:managequeue', $context);

// determine queue action
if (optional_param('send', '', PARAM_ALPHANUM)) {
    $action = 'send';
} else if (optional_param('delete', '', PARAM_ALPHANUM)) {
    $action = 'delete';
} else {
    print_error('error:unknownaction', 'local_maillog');
}

$logids = required_param_array('local_maillog_items', PARAM_INT);
if (empty($logids)) {
    print_error('error:noitemsselected', 'local_maillog');
}

$PAGE->set_context($context);
$PAGE->set_url('/local/maillog/queueaction.php');
$PAGE->navbar->add(get_string('pluginname', 'local_maillog'), new moodle_url('/admin/settings.php', array('section' => 'local_maillog')));
$PAGE->navbar->add(get_string('mailqueue', 'local_maillog'), new moodle_url('/local/maillog/mailqueue.php', array('section' => 'local_maillog')));

$returnurl = $CFG->wwwroot.'/local/maillog/mailqueue.php';

$confirmurl = $PAGE->url;
foreach ($logids as $logid) {
    $confirmurl->params(array("local_maillog_items[{$logid}]" => $logid));
}

if ($action == 'delete') {
    $confirm = optional_param('confirm', false, PARAM_BOOL);
    if (!$confirm) {
        echo $OUTPUT->header();
        $confirmurl->params(array('delete' => 1, 'confirm' => 1, 'sesskey' => sesskey()));
        echo $OUTPUT->confirm(get_string('confirmqueuedelete', 'local_maillog'), $confirmurl, $returnurl);
        echo $OUTPUT->footer();
        die();
    }

    require_sesskey();

    \local_maillog\helper::delete($logids);

    totara_set_notification(get_string('queueitemsdeleted', 'local_maillog'), $returnurl, array('class' => 'notifysuccess'));
} else if ($action == 'send') {
    $confirm = optional_param('confirm', false, PARAM_BOOL);
    if (!$confirm) {
        echo $OUTPUT->header();
        $confirmurl->params(array('send' => 1, 'confirm' => 1, 'sesskey' => sesskey()));
        echo $OUTPUT->confirm(get_string('confirmqueuesend', 'local_maillog'), $confirmurl, $returnurl);
        echo $OUTPUT->footer();
        die();
    }

    require_sesskey();

    \local_maillog\helper::schedule_send($logids);

    totara_set_notification(get_string('queueitemsscheduled', 'local_maillog'), $returnurl, array('class' => 'notifysuccess'));
}


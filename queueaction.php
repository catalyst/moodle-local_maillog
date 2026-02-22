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
 * Process queue actions.
 *
 * @package   local_maillog
 * @author    Eugene Venter <eugene@catalyst.net.nz>
 *            Sasha Anastasi <sasha.anastasi@catalyst.net.nz>
 * @copyright 2026 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

$context = context_system::instance();

require_login();
require_capability('local/maillog:managequeue', $context);

$PAGE->set_context($context);
$PAGE->set_url('/local/maillog/queueaction.php');
$PAGE->navbar->add(get_string('pluginname', 'local_maillog'), new moodle_url('/admin/settings.php', array('section' => 'local_maillog')));
$PAGE->navbar->add(get_string('mailqueue', 'local_maillog'), new moodle_url('/local/maillog/mailqueue.php', array('section' => 'local_maillog')));

$returnurl = $CFG->wwwroot.'/local/maillog/mailqueue.php';
$confirmurl = $PAGE->url;

$action = required_param('action', PARAM_ALPHANUM);
switch($action) {
    case 'delete':
        $logid = required_param('logid', PARAM_ALPHANUM);
        $confirmurl->params(array('logid' => $logid));
        $confirm = optional_param('confirm', false, PARAM_BOOL);
        if (!$confirm) {
            echo $OUTPUT->header();
            $confirmurl->params(array('action' => 'delete', 'confirm' => 1, 'sesskey' => sesskey()));
            echo $OUTPUT->confirm(get_string('confirmdelete', 'local_maillog'), $confirmurl, $returnurl);
            echo $OUTPUT->footer();
            die();
        }
        require_sesskey();
        \local_maillog\local\helper::delete([$logid]);
        redirect($returnurl, get_string('queueitemdeleted', 'local_maillog'));
    case 'send':
        $logid = required_param('logid', PARAM_ALPHANUM);
        $confirmurl->params(array('logid' => $logid));
        $confirm = optional_param('confirm', false, PARAM_BOOL);
        if (!$confirm) {
            echo $OUTPUT->header();
            $confirmurl->params(array('action' => 'send', 'confirm' => 1, 'sesskey' => sesskey()));
            echo $OUTPUT->confirm(get_string('confirmsend', 'local_maillog'), $confirmurl, $returnurl);
            echo $OUTPUT->footer();
            die();
        }
        require_sesskey();
        \local_maillog\local\helper::schedule_send([$logid]);
        redirect($returnurl, get_string('queueitemscheduled', 'local_maillog'));
    default:
        throw new \moodle_exception('error:unknownaction', 'local_maillog');
}

<?php

/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Catalyst IT
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

/**
 * Displays collaborative features for the current user
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/totara/reportbuilder/lib.php');

// Initialise jquery requirements.
require_once($CFG->dirroot.'/totara/core/js/lib/setup.php');

require_login();

$context = context_system::instance();

require_capability('local/maillog:managequeue', $context);


$sid = optional_param('sid', '0', PARAM_INT);
$format = optional_param('format', '',PARAM_TEXT); //export format
$debug = optional_param('debug', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url('/local/maillog/mailqueue.php');
$PAGE->set_pagelayout('noblocks');

$strheading = get_string('mailqueue', 'local_maillog');

$data = array(
    'queued' => 1,
);
$rb_config = new rb_config();
$rb_config->set_sid($sid)
    ->set_nocache(false)
    ->set_embeddata($data);

if (!$report = reportbuilder::create_embedded('local_maillog_mailqueue', $rb_config)) {
    print_error('error:couldnotgenerateembeddedreport', 'totara_reportbuilder');
}

if ($debug) {
    $report->debug($debug);
}

$logurl = $PAGE->url->out_as_local_url();
if ($format!='') {
    $report->export_data($format);
    exit;
}

\totara_reportbuilder\event\report_viewed::create_from_report($report)->trigger();

$report->include_js();

$jsmodule = array(
    'name' => 'local_maillog_mailqueue',
    'fullpath' => '/local/maillog/mailqueue.js',
    'requires' => array('json')
);
$PAGE->requires->strings_for_js(array('all', 'none'), 'moodle');
$PAGE->requires->js_init_call('M.local_maillog_mailqueue.init', array(), false, $jsmodule);

///
/// Display the page
///
$PAGE->navbar->add(get_string('pluginname', 'local_maillog'), new moodle_url('/admin/settings.php', array('section' => 'local_maillog')));
$PAGE->navbar->add($strheading);

$PAGE->set_title($strheading);
$PAGE->set_button($report->edit_button());
$PAGE->set_heading($strheading);

$output = $PAGE->get_renderer('totara_reportbuilder');

echo $output->header();
echo $output->heading($strheading, 1);
#echo html_writer::tag('p', html_writer::link("{$CFG->wwwroot}/my/", "<< " . get_string('mylearning', 'totara_core')));

$countfiltered = $report->get_filtered_count();
$countall = 0;
if($report->can_display_total_count()) {
    $countall = $report->get_full_count();
}

// Display heading including filtering stats.
if ($countfiltered == $countall) {
    echo $output->heading(get_string('recordsall', 'local_maillog', $countall));
} else {
    $a = new stdClass();
    $a->countfiltered = $countfiltered;
    $a->countall = $countall;
    echo $output->heading(get_string('recordsshown', 'local_maillog', $a));
}

echo $output->print_description($report->description, $report->_id);

$report->display_search();
$report->display_sidebar_search();


echo html_writer::start_tag('form', array('id' => 'local_maillog_mailqueue_frm', 'name' => 'local_maillog_mailqueue_frm',
        'action' => new moodle_url('/local/maillog/queueaction.php'),  'method' => 'post'));
$report->display_table();
if ($countfiltered > 0) {
    $out = $output->box_start('generalbox', 'local_maillog_mailqueue_actions');
    $out .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'returnto', 'value' => $FULLME));
    $out .= html_writer::start_tag('center');
    $tab = new html_table();
    $tab->align = array('left', 'left');
    $tab->size = array('80%');
    $tab->attributes = array('class', 'fullwidth');
    $deletelink = html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'delete', 'id' => 'maillog-queue-delete',
            'disabled' => 'true', 'value' => get_string('delete', 'local_maillog'), 'style' => 'display:none;')) .
            html_writer::tag('noscript', get_string('noscript', 'local_maillog'));
    $sendlink = html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'send', 'id' => 'maillog-queue-send',
            'disabled' => 'true', 'value' => get_string('send', 'local_maillog'), 'style' => 'display:none;')) .
            html_writer::tag('noscript', get_string('noscript', 'local_maillog'));
    $tab->data[]  = new html_table_row(array(get_string('withselected', 'local_maillog'), $deletelink, $sendlink));
    $out .= html_writer::table($tab);
    $out .= html_writer::end_tag('center');
    $out .= $output->box_end();
    print $out;
}
print html_writer::end_tag('form');

// Export button.
$output->export_select($report, $sid);

echo $output->footer();

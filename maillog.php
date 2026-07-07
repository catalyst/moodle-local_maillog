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
 * Task log.
 *
 * @package    admin
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

use core_reportbuilder\system_report_factory;
use local_maillog\local\systemreports\logreport;

require_login();
$context = \context_system::instance();
require_capability('local/maillog:viewmaillog', $context);

$PAGE->set_context($context);
$PAGE->set_url(new \moodle_url('/local/maillog/maillog.php'));
$PAGE->set_pagelayout('admin');

$strheading = get_string('maillog', 'local_maillog');

$PAGE->navbar->add($strheading);

$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

echo $OUTPUT->header();
$report = system_report_factory::create(logreport::class, $context);

echo $report->output();
echo $OUTPUT->footer();

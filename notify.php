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
 * Mail log plugin by Catalyst IT.
 * Notifies if the setting of queuemails is switched on.
 *
 * @package local_maillog
 * @author  Sumaiya Javed <sumaiya.javed@catalyst.net.nz>
 * @copyright 2013 onwards Catalyst IT Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_UPGRADE_CHECK', true);
require(__DIR__ . '/../../config.php');
header("Content-Type: text/plain");
header('Pragma: no-cache');
header('Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate, proxy-revalidate');
header('Expires: Tue, 04 Sep 2012 05:32:29 GMT');
$format = '%b %d %H:%M:%S';
$now = userdate(time(), $format);
$queuemails = get_config('local_maillog', 'queuemails');
if ($queuemails) {
    $queuemailsdate = get_config('local_maillog', 'queuemailsdate');
    $a = new stdClass();
    $a->hourspassed = round((time() - $queuemailsdate) / 3600);
    $a->now = $now;
    printf (get_string('critical', 'local_maillog', $a));

} else {
    printf ("OK");
}





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
 * Mail log strings
 *
 * @package   local_maillog
 * @author    Eugene Venter <eugene@catalyst.net.nz>
 * @copyright 2013 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Mail log';
$string['type_maillog'] = 'Mail log';
$string['entity_maillog'] = 'Mail log';
$string['maillog'] = 'Mail log';
$string['mailqueue'] = 'Mail Queue';
$string['logmails'] = 'Log emails';
$string['configlogmails'] = 'Log all sent emails';
$string['queuemails'] = 'Queue emails';
$string['configqueuemails'] = 'Rather than sending out emails, queue emails for approval prior to sending out. <b>NOTE:</b> You must have logging enabled too, if you want to use this feature.<br><b><a href="{$a}">View queue here</a></b>';
$string['maxdays'] = 'Max days to keep emails in log/queue';
$string['maxdaysinfo'] = 'Any mail log entries older than the configured amount of days will be purged.';
$string['maillog:viewmaillog'] = 'View mail log';
$string['maillog:managequeue'] = 'Manage queue';
$string['recordsall'] = '{$a} record(s) shown';
$string['recordsshown'] = '{$a->countfiltered} of {$a->countall} records shown';
$string['task:purgelog'] = 'Queue adhoc purge of mail log';
$string['task:sendscheduled'] = 'Send scheduled emails';
$string['fromaddress'] = 'From address';
$string['usetrueaddress'] = 'Use true address';
$string['replytoaddress'] = 'Reply-to address';
$string['replytoname'] = 'Reply-to name';
$string['fromobj'] = 'Sender (json object)';
$string['toaddress'] = 'To address';
$string['subject'] = 'Subject';
$string['hasattachment'] = 'Has attachment';
$string['attachname'] = 'Attachment filename';
$string['timesent'] = 'Time sent';
$string['timequeued'] = 'Time queued';
$string['strftimerecentfullish'] = '%a, %d %b %Y, %H:%M';
$string['originscript'] = 'Originating script';
$string['wordwrapwidth'] = 'Word wrap width';
$string['returnmsg'] = 'Return message';
$string['pendingsend'] = 'Pending send';
$string['queued'] = 'Queued';
$string['failed'] = 'Failed';
$string['sendselected'] = 'Send selected';
$string['confirmqueuedelete'] = 'Are you sure you want to remove these items from the queue?';
$string['confirmqueuesend'] = 'Are you sure you want to send these queued items?';
$string['confirmdelete'] = 'Are you sure you want to remove this item from the queue?';
$string['confirmsend'] = 'Are you sure you want to send this queued item?';
$string['queueitemsdeleted'] = 'Queue items will be deleted on next cron run.';
$string['queueitemsscheduled'] = 'Queue items scheduled to be sent on next cron run.';
$string['queueitemdeleted'] = 'Queue item will be deleted on next cron run.';
$string['queueitemscheduled'] = 'Queue item scheduled to be sent on next cron run.';
$string['error:unknownaction'] = 'Unknown action';
$string['noscript'] = 'You need to have Javascript enabled in order to use this feature';
$string['privacy:metadata:mail_log:userid'] = 'The id that the email was sent to.';
$string['privacy:metadata:mail_log:toaddress'] = 'The email address that the email was sent to.';
$string['privacy:metadata:mail_log'] = 'Stores information on sent emails from the system.';
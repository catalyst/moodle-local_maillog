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
 * Mail log task helper
 *
 * @package   local_maillog
 * @author    Eugene Venter <eugene@catalyst.net.nz>
 * @copyright 2013 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace local_maillog;

defined('MOODLE_INTERNAL') or die();

define('LOCAL_MAILLOG_STATUS_QUEUED', 1);
define('LOCAL_MAILLOG_STATUS_PENDINGSEND', 2);


class helper {

	/**
	 * Purges emails logged older than $maxdays
	 * @param int $maxdays the number of days old a log should be before being purged
	 * @param int $maxiterations the number of blocks of 1k records to delete as a fail safe against infinite loops.
	 */
	static function purge($maxdays, $maxiterations = 1000) {
		global $DB;

		$olderthantime = time() - $maxdays * DAYSECS;

		// We can straight up delete records without attachments. This is very efficient.
		$DB->delete_records_select('mail_log', 'timesent < ? AND attachment IS NULL', [$olderthantime]);

		// The remaining have attachments which need to also be deleted.
		// Delete these in chunks of 1k, as to not exceed the query parameter limit.
		// Do this until there are no more records to delete (or ran out of iterations as a fail safe);
		for ($i = 0; $i < $maxiterations; $i++) {
			$records = $DB->get_records_select('mail_log', 'timesent < ?', [$olderthantime], '', 'id', 0, 1000);
			$ids = array_column($records, 'id');

			// Done - exit early.
			if (empty($ids)) {
				break;
			}
			\local_maillog\helper::delete($ids);
		}
	}

	static function log_mail($success, $msg, $user, $from, $subject, $messagetext, $messagehtml, $attachment, $attachname, $usetrueaddress, $replyto, $replytoname, $wordwrapwidth, $queuestatus=0) {
		global $CFG, $DB;

		if (!\get_config('local_maillog', 'logmails') || PHPUNIT_TEST)  {
			return true;
		}

		// Find originating script. It will be the layer with the first email_to_user call.
		$stack = debug_backtrace(false);
		$origin = $stack[0];

		foreach ($stack as $depth => $call) {
			if ($call['function'] == 'message_send') {
				$origin = $call;
			}
		}

		$originscript = gethostname() . ':' . str_replace($CFG->dirroot . '/', '', $origin['file']) . ':' . $origin['line'];

		$transaction = $DB->start_delegated_transaction();

		$todb = new \stdClass();
		$todb->userid = $user->id;
		$todb->fromobj = json_encode($from);
		$todb->toaddress = substr($user->email, 0, 100);
		$from = is_string($from) ? $from : $from->email;
		$todb->fromaddress = substr($from, 0, 100);
		$todb->subject = $subject;
		$todb->messagetext = $messagetext;
		$todb->messagehtml = $messagehtml;
		$todb->attachment = $attachment;
		$todb->attachname = $attachname;
		$todb->usetrueaddress = $usetrueaddress ?  : 0;
		$todb->replyto = substr($replyto, 0, 100);
		$todb->replytoname = substr($replytoname, 0, 100);
		$todb->wordwrapwidth = (int) $wordwrapwidth;
		$todb->timesent = time();
		$todb->success = $success ? 1 : 0;
		$todb->returnmsg = substr($msg, 0, 255);
		$todb->queuestatus = $queuestatus;
		$todb->originscript = $originscript;

		$newrecordid = $DB->insert_record('mail_log', $todb);

		if (!empty($todb->attachment) && $queuestatus == LOCAL_MAILLOG_STATUS_QUEUED) {  // only save full attachment for queued items
			// Copy attachment to a safe location, so we can access it later
			$todb = new \stdClass();
			$todb->id = $newrecordid;
			$todb->attachment = \local_maillog\helper::copy_attachment($newrecordid, $attachment, $attachname);

			$DB->update_record('mail_log', $todb);
		}

		$transaction->allow_commit();
	}

	/**
	 * Delete the logs with the given ids
	 * Note the size of the $ids param must not exceed the maximum db query parameter limit.
	 * @param array $ids
	 */
	static function delete($ids) {
		global $DB;

		$context = \context_system::instance();

		list($sqlin, $params) = $DB->get_in_or_equal($ids);
		$sql = "SELECT * FROM {mail_log}
			WHERE id {$sqlin}";
		$logitems = $DB->get_records_sql($sql, $params);

		// Delete any attachment files
		$fs = \get_file_storage();
		foreach ($logitems as $item) {
			if (empty($item->attachment)) {
				continue;
			}
			$fs->delete_area_files($context->id, 'local_maillog', 'queuefiles', $item->id);
		}

		// Delete log records
		$sql = "DELETE FROM {mail_log}
			WHERE id {$sqlin}";
		return $DB->execute($sql, $params);
	}

	static function schedule_send($ids) {
		global $DB;

		list($sqlin, $params) = $DB->get_in_or_equal($ids);
		$sql = "UPDATE {mail_log}
			SET queuestatus = ?, returnmsg = ?
			WHERE id {$sqlin}";
		$params = array_merge(array(LOCAL_MAILLOG_STATUS_PENDINGSEND, get_string('pendingsend', 'local_maillog')), $params);

		return $DB->execute($sql, $params);
	}

	static function send_scheduled() {
		global $DB;

		$context = \context_system::instance();
		$fs = \get_file_storage();
		$rs = $DB->get_recordset('mail_log', array('queuestatus' => LOCAL_MAILLOG_STATUS_PENDINGSEND));

		$count = 0;
		if ($rs->valid()) {
			foreach ($rs as $mail) {
				// If totara, use totara calls, else moodle user object.
				if (class_exists('totara_core\totara_user')) {
					$user = \totara_core\totara_user::get_user($mail->userid);
					if ($mail->userid == \totara_core\totara_user::EXTERNAL_USER) {
						$user->email = $mail->toaddress;
						$user->firstname = $mail->toaddress;
					}
				} else {
					$user = \core_user::get_user($mail->userid);
				}

				if (!empty($mail->attachment)) {
					$attachmentfile = $fs->get_file($context->id, 'local_maillog', 'queuefiles', $mail->id, "/{$mail->id}/", $mail->attachname);
					if (!empty($attachmentfile)) {
						$mail->attachment = $attachmentfile->copy_content_to_temp('maillog', "{$mail->id}_");  // do this so we can get an absolute path to the file
					}
				}
				$from = json_decode($mail->fromobj);
				$from->queueapproved = true;

				\email_to_user(
					$user,
					$from,
					$mail->subject,
					$mail->messagetext,
					$mail->messagehtml,
					$mail->attachment,
					$mail->attachname,
					$mail->usetrueaddress,
					$mail->replyto,
					$mail->replytoname,
					$mail->wordwrapwidth,
				);
				if (!empty($mail->attachment)) {
					unlink($mail->attachment);
				}
				\local_maillog\helper::delete(array($mail->id));
				$count++;
			}
		}

		$rs->close();

		return $count;
	}

	static function copy_attachment($logid, $attachment, $attachname) {
		global $CFG;

		$now = time();

		// The bit below is copied from the attachment functionality in email_to_user()
		$attachmentpath = $attachment;
		// Before doing the comparison, make sure that the paths are correct (Windows uses slashes in the other direction).
		$attachpath = str_replace('\\', '/', $attachmentpath);
		// Make sure both variables are normalised before comparing.
		$temppath = str_replace('\\', '/', $CFG->tempdir);
		// If the attachment is a full path to a file in the tempdir, use it as is,
		// otherwise assume it is a relative path from the dataroot (for backwards compatibility reasons).
		if (strpos($attachpath, $temppath) !== 0) {
			$attachmentpath = $CFG->dataroot . '/' . $attachmentpath;
		}

		$context = \context_system::instance();
		$fs = \get_file_storage();
		$filerecord = array('contextid' => $context->id, 'component' => 'local_maillog', 'filearea' => 'queuefiles',
				'itemid' => $logid, 'filepath' => "/{$logid}/", 'filename' => $attachname,
				'timecreated' => $now, 'timemodified' => $now);

		clearstatcache(); // ensure we grab the latest details for the attachment
		$newfile = $fs->create_file_from_pathname($filerecord, $attachmentpath);

		return $newfile->get_filepath().$newfile->get_filename();
	}
}

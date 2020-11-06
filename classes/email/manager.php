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
 * Version details.
 *
 * @package tool_emailtester
 * @copyright 2020 Peter Burnett <peterburnett@catalyst-au.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_maillog\email;

defined('MOODLE_INTERNAL') || die();

use core\email;

class manager extends email\manager {

    protected $queueapproved = false;

    protected function mail_validity_checks(): ?bool {
        global $CFG;

        // Check if this message is queue approved. This is set in the $from object.
        if (!empty($this->from->queueapproved)) {
            $this->queueapproved = $this->from->queueapproved;
            // If this email has been queue approved, remove the flag from the $from.
            if ($this->queueapproved) {
                unset($this->from->queueapproved);
            }
        }

        // Maillog checks.
        // Deleted user.
        $failed = false;
        if (!empty($this->user->deleted)) {
            $msg = 'User is deleted';
            $failed = true;
        }

        if (!empty($CFG->noemailever)) {
            $msg = 'noemailever config set';
            $failed = true;
        }

        if ((isset($this->user->auth) && $this->user->auth=='nologin') or
            (isset($this->user->suspended) && $this->user->suspended)) {
            $msg = 'Suspended user';
            $failed = true;
        }

        if (!validate_email($this->user->email)) {
            $msg = 'Invalid email';
            $failed = true;
        }

        if (over_bounce_threshold($this->user)) {
            $msg = 'Over bounce threshold';
            $failed = true;
        }

        if (substr($this->user->email, -8) == '.invalid') {
            $msg = 'Invalid domain for email';
            $failed = true;
        }

        if ($failed) {
            \local_maillog\helper::log_mail(
                false,
                $msg,
                $this->user, $this->from,
                $this->subject,
                $this->messagetext,
                $this->messagehtml,
                $this->attachment, $this->attachname,
                $this->usetrueaddress, $this->replyto,
                $this->replytoname,
                $this->wordwrapwidth
            );
        }

        // Mail queueing.
        if (get_config('local_maillog', 'queuemails') && !$failed) {
            if (!$this->queueapproved) {
                // Queue email for sending later and return
                \local_maillog\helper::log_mail(
                    true,
                    'Email queued',
                    $this->user,
                    $this->from,
                    $this->subject,
                    $this->messagetext,
                    $this->messagehtml, $this->attachment,
                    $this->attachname, $this->usetrueaddress,
                    $this->replyto,
                    $this->replytoname,
                    $this->wordwrapwidth,
                    LOCAL_MAILLOG_STATUS_QUEUED
                );

                return true;
            }
        }

        // Otherwise, standard function.
        return parent::mail_validity_checks();
    }

    protected function mail_send(\moodle_phpmailer $mail): bool {
        $sent = parent::mail_send($mail);
        if ($sent) {
            \local_maillog\helper::log_mail(
                true,
                '',
                $this->user,
                $mail->From,
                $this->subject,
                $this->messagetext,
                $this->messagehtml,
                $this->attachment,
                $this->attachname,
                $this->usetrueaddress,
                $this->replyto,
                $this->replytoname,
                $this->wordwrapwidth
            );
        } else {
            \local_maillog\helper::log_mail(
                false,
                'mail->Send() returned false',
                $this->user,
                $mail->From,
                $this->subject,
                $this->messagetext,
                $this->messagehtml,
                $this->attachment,
                $this->attachname,
                $this->usetrueaddress,
                $this->replyto,
                $this->replytoname,
                $this->wordwrapwidth
            );
        }

        return $sent;
    }
}
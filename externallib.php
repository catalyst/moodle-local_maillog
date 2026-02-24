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
 * External mailqueue API
 *
 * @package   local_maillog
 * @category  external
 * @author    Sasha Anastasi <sasha.anastasi@catalyst.net.nz>
 * @copyright 2026 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_external\external_api;
use core_external\external_format_value;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_external\util;
use local_maillog\local\helper as local_helper;

defined('MOODLE_INTERNAL') || die();

class local_maillog_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function send_messages_parameters() {
        return new external_function_parameters(
            array(
                'messageids' => new external_multiple_structure(new external_value(PARAM_INT, 'message IDs')),
            )
        );
    }

    /**
     * Send messages
     *
     * @param array $messageids
     * @return null
     * @since Moodle 2.5
     */
    public static function send_messages($messageids) {
        require_capability('local/maillog:managequeue', \context_system::instance());

        $params = self::validate_parameters(self::send_messages_parameters(), array('messageids' => $messageids));

        // Validate params.
        foreach ($params['messageids'] as $messageid) {
            $logid = validate_param($messageid, PARAM_INT);
            local_helper::schedule_send([$logid]);
        }
        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 2.5
     */
    public static function send_messages_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_messages_parameters() {
        return new external_function_parameters(
            array(
                'messageids' => new external_multiple_structure(new external_value(PARAM_INT, 'message IDs')),
            )
        );
    }

    /**
     * Delete messages
     *
     * @param array $messageids
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_messages($messageids) {
        require_capability('local/maillog:managequeue', \context_system::instance());

        $params = self::validate_parameters(self::delete_messages_parameters(), array('messageids' => $messageids));

        // Validate params.
        foreach ($params['messageids'] as $messageid) {
            $logid = validate_param($messageid, PARAM_INT);
            local_helper::delete([$logid]);
        }
        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_messages_returns() {
        return null;
    }
}
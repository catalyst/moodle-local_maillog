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
 * Maillog external functions and service definitions.
 *
 * @package   local_maillog
 * @category  webservice
 * @author    Sasha Anastasi <sasha.anastasi@catalyst.net.nz>
 * @copyright 2026 onwards Catalyst IT Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$functions = [
    'local_maillog_send_messages' => [
        'classname'   => 'local_maillog_external',
        'methodname'   => 'send_messages',
        'description' => 'Schedule queued messages to be sent.',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'local_maillog_delete_messages' => [
        'classname'   => 'local_maillog_external',
        'methodname'   => 'delete_messages',
        'description' => 'Schedule queued messages to be deleted.',
        'type'        => 'write',
        'ajax'        => true,
    ],
];
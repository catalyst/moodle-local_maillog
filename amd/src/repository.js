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
 * Module to handle mailqueue AJAX requests.
 *
 * @module     local_maillog/repository
 * @copyright  2026 Catalyst IT Ltd
 * @author     Sasha Anastasi <sasha.anastasi@catalyst.net.nz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Send multiple messages.
 *
 * @param {Number[]} messageids
 * @return {Promise}
 */
export const sendMessages = messageids => {
    const request = {
        methodname: 'local_maillog_send_messages',
        args: {messageids},
    };

    return Ajax.call([request])[0];
};

/**
 * Delete multiple messages.
 *
 * @param {Number[]} messageids
 * @return {Promise}
 */
export const deleteMessages = messageids => {
    const request = {
        methodname: 'local_maillog_delete_messages',
        args: {messageids},
    };

    return Ajax.call([request])[0];
};
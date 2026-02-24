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
 * Mail queue bulk actions
 *
 * @module     local_maillog/queueaction
 * @copyright  2026 Catalyst IT Ltd
 * @author     Sasha Anastasi <sasha.anastasi@catalyst.net.nz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {dispatchEvent} from 'core/event_dispatcher';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import {getString} from 'core/str';
import {add as addToast} from 'core/toast';
import {deleteMessages, sendMessages} from 'local_maillog/repository';
import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';

const SELECTORS = {
    CHECKBOXES: '[data-togglegroup="report-select-all"][data-toggle="target"]:checked',
    SENDBUTTON: '[data-action="queue-send-selected"]',
    DELETEBUTTON: '[data-action="queue-delete-selected"]',
};

/**
 * Initialise module.
 */
export const init = () => {

    prefetchStrings('local_maillog', [
        'confirmqueuedelete',
        'queueitemsdeleted',
        'confirmqueuesend',
        'queueitemsscheduled',
        'sendselected',
    ]);

    prefetchStrings('core', [
        'deleteselected',
    ]);

    registerEventListeners();
};

/**
 * Register event listeners.
 */
export const registerEventListeners = () => {

    document.addEventListener('click', event => {

        // Send multiple queued messages.
        const messageSendMultiple = event.target.closest(SELECTORS.SENDBUTTON);
        if (messageSendMultiple) {
            event.preventDefault();

            const reportElement = document.querySelector(reportSelectors.regions.report);
            const messageSendChecked = reportElement.querySelectorAll(SELECTORS.CHECKBOXES);
            if (messageSendChecked.length === 0) {
                return;
            }

            Notification.saveCancelPromise(
                getString('sendselected', 'local_maillog'),
                getString('confirmqueuesend', 'local_maillog'),
                getString('send', 'message'),
                {triggerElement: messageSendMultiple}
            ).then(() => {
                const pendingPromise = new Pending('local_maillog/messages:send');
                const sendMessageIds = [...messageSendChecked].map(check => check.value);

                // eslint-disable-next-line promise/no-nesting
                return sendMessages(sendMessageIds)
                    .then(() => addToast(getString('queueitemsscheduled', 'local_maillog')))
                    .then(() => {
                        dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                        return pendingPromise.resolve();
                    })
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }

        // Delete multiple queued messages.
        const messageDeleteMultiple = event.target.closest(SELECTORS.DELETEBUTTON);
        if (messageDeleteMultiple) {
            event.preventDefault();

            const reportElement = document.querySelector(reportSelectors.regions.report);
            const messageDeleteChecked = reportElement.querySelectorAll(SELECTORS.CHECKBOXES);
            if (messageDeleteChecked.length === 0) {
                return;
            }

            Notification.saveCancelPromise(
                getString('deleteselected', 'core'),
                getString('confirmqueuedelete', 'local_maillog'),
                getString('delete', 'core'),
                {triggerElement: messageDeleteMultiple}
            ).then(() => {
                const pendingPromise = new Pending('local_maillog/messages:delete');
                const deleteMessageIds = [...messageDeleteChecked].map(check => check.value);

                // eslint-disable-next-line promise/no-nesting
                return deleteMessages(deleteMessageIds)
                    .then(() => addToast(getString('queueitemsdeleted', 'local_maillog')))
                    .then(() => {
                        dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                        return pendingPromise.resolve();
                    })
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }
    });
};
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
 * @package local_maillog
 */
M.local_maillog_mailqueue = M.local_maillog_mailqueue || {

    Y:null,
    // optional php params and defaults defined here, args passed to init method
    // below will override these values
    config: {},

    /**
     * module initialisation method called by php js_init_call()
     *
     * @param object    YUI instance
     * @param string    args supplied in JSON format
     */
    init: function(Y, args){
        // save a reference to the Y instance (all of its dependencies included)
        this.Y = Y;
        // if defined, parse args into this module's config object
        if (args) {
            var jargs = Y.JSON.parse(args);
            for (var a in jargs) {
                if (Y.Object.owns(jargs, a)) {
                    this.config[a] = jargs[a];
                }
            }
        }

        this.init_select_all_none_checkbox();
        this.init_action_buttons();
    },

    /**
     * Initialise action buttons and toggles
     */
    init_action_buttons: function() {
        $('#local_maillog_mailqueue_actions input').css('display', 'block');
        $('#local_maillog_mailqueue input[type=checkbox]').bind('click', function() {
            if ($('form#local_maillog_mailqueue_frm input[type=checkbox]:checked').length) {
                $('#local_maillog_mailqueue_actions input').attr('disabled', false);
            } else {
                $('#local_maillog_mailqueue_actions input').attr('disabled', true);
            }
        });
    },

    init_select_all_none_checkbox: function(){
        $('th.maillog_checkbox').html('<div id="maillog_selects"><a id="all" href="#">'+M.util.get_string('all', 'moodle')+
                                        '</a>/<a id="none" href="#">'+M.util.get_string('none', 'moodle')+'</a></div>');
        function jqCheckAll(flag) {
           if (flag === false) {
              $("form#local_maillog_mailqueue_frm [type='checkbox']").prop('checked', false);
              if ($('form#local_maillog_mailqueue_frm input[type=checkbox]:checked').length) {
                  $('#local_maillog_mailqueue_actions input').attr('disabled', false);
              } else {
                  $('#local_maillog_mailqueue_actions input').attr('disabled', true);
              }
           } else {
              $("form#local_maillog_mailqueue_frm [type='checkbox']").prop('checked', true);
              if ($('form#local_maillog_mailqueue_frm input[type=checkbox]:checked').length) {
                  $('#local_maillog_mailqueue_actions input').attr('disabled', false);
              } else {
                  $('#local_maillog_mailqueue_actions input').attr('disabled', true);
              }
           }
        }
        $('#maillog_selects #all').click(function() {jqCheckAll(true); return false;});
        $('#maillog_selects #none').click(function() {jqCheckAll(false); return false;});
    },
};

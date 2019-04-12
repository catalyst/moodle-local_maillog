<?php
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) { // needs this condition or there is error on login page
    $settings = new admin_settingpage('local_maillog', 'Mail log');
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configcheckbox('local_maillog/logmails', get_string('logmails', 'local_maillog'), get_string('configlogmails', 'local_maillog'), 1));

    $settings->add(new admin_setting_configcheckbox('local_maillog/queuemails', get_string('queuemails', 'local_maillog'), get_string('configqueuemails', 'local_maillog', $CFG->wwwroot.'/local/maillog/mailqueue.php'), 0));


    $daysoptions = range(1, 30);
    $daysoptions = array_combine(array_values($daysoptions), $daysoptions);  // fix index
    $settings->add(new admin_setting_configselect('local_maillog/maxdays',
        new lang_string('maxdays', 'local_maillog'), new lang_string('maxdaysinfo', 'local_maillog'), 7, $daysoptions));
}


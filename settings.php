<?php
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) { // needs this condition or there is error on login page

    $ADMIN->add('localplugins', new admin_category('local_maillog_settings', new lang_string('pluginname', 'local_maillog')));
    $settingspage = new admin_settingpage('managelocalmaillog', new lang_string('pluginname', 'local_maillog'));

    if ($ADMIN->fulltree) {
        require_once "$CFG->dirroot/local/maillog/lib.php";

        $settingspage->add(new admin_setting_configcheckbox('local_maillog/logmails', get_string('logmails', 'local_maillog'), get_string('configlogmails', 'local_maillog'), 1));

        $setting = new admin_setting_configcheckbox('local_maillog/queuemails',
            get_string('queuemails', 'local_maillog'), get_string('configqueuemails', 'local_maillog', $CFG->wwwroot.'/local/maillog/mailqueue.php'), 0);
        $setting->set_updatedcallback('local_maillog_notify');
        $settingspage->add($setting);

        $daysoptions = range(1, 30);
        $daysoptions = array_combine(array_values($daysoptions), $daysoptions);  // fix index
        $settingspage->add(new admin_setting_configselect('local_maillog/maxdays',
            new lang_string('maxdays', 'local_maillog'), new lang_string('maxdaysinfo', 'local_maillog'), 7, $daysoptions));
    }

    $ADMIN->add('localplugins', $settingspage);

}
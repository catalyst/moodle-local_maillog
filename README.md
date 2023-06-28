Totara Mail log plugin by Catalyst IT
===

This is for version T17

This plugin requires some changes to core code.
```sh
patch -ruN server/lib/moodlelib.php < server/local/maillog/patches/moodlelib.patch
patch -ruN server/totara/reportbuilder/tests/column_test.php < server/local/maillog/patches/column_test.patch
mv server/local/maillog/reportbuilder/embedded/rb_local_maillog_mailqueue_embedded.php server/totara/reportbuilder/embedded/rb_local_maillog_mailqueue_embedded.php
```

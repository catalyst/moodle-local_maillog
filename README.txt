Totara Mail log plugin by Catalyst IT

This plugin requires some changes to core code.
* lib/moodlelib.php (see patch file in patches/moodlelib.patch)
* totara/reportbuilder/tests/column_test.php  (see patch file in patches/column_test.patch)
* Embedded reportbuilder report file needs to be moved from:
   /local/maillog/reportbuilder/embedded/rb_local_maillog_mailqueue_embedded.php
   to: 
   /totara/reportbuilder/embedded/rb_local_maillog_mailqueue_embedded.php

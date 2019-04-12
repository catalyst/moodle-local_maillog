Mail log plugin by Catalyst IT

This plugin requires a changes to the core email_to_user function so that it will function - you will need to
apply the patch file in patches/moodlelib.php against your moodle install first.

* lib/moodlelib.php (see patch file in patches/moodlelib.patch)

PLEASE NOTE:
The Moodle version of this plugin doesn't currently provide a report that exposes the maillog data - you will need to view the data directly in the database until a report is developed.

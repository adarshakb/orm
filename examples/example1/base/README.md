# Base - ORM Extended classes

----

Admin.php 		- ORM Extension for admin table.
Posts.php 		- ORM Extension for the posts table.
include_top.php - Starts session, includes missing classes.

Note: 	Notice how foreign keys are handled in Posts.php
		through the use of `protected $UserObject;`
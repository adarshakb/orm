# Example 1

A simple example demonstrating the use of the ORM.

db.sql 		- Database Schema
Admin.php 	- ORM Extension for admin table
Posts.php 	- ORM Extension for the posts table

Note: 	Notice how foreign keys are handled in Posts.php
		through the use of `protected $UserObject;`
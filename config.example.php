<?php
/**
 * Example configuration for the ORM library.
 *
 * Copy to config.php (or set equivalent environment variables) and
 * adjust values to your environment.
 */

define("HOST", getenv("ORM_HOST") ?: "localhost");
define("DB", getenv("ORM_DB") ?: "orm_demo");
define("NORMAL_USER", getenv("ORM_NORMAL_USER") ?: "orm_user");
define("NORMAL_PASS", getenv("ORM_NORMAL_PASS") ?: "");
define("ADMIN_USER", getenv("ORM_ADMIN_USER") ?: "orm_admin");
define("ADMIN_PASS", getenv("ORM_ADMIN_PASS") ?: "");

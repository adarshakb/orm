<?php
require_once 'config.php';

/**
 * Main database utility class.
 *
 * Backward-compatible static API with optional connection injection for testing.
 */
final class Database {

    private static $db = DB;
    private static $host = HOST;
    private static $username = NORMAL_USER;
    private static $password = NORMAL_PASS;

    private static $dbConn = null; // Holds the database connection
    private static $queryCatch = array(); // Holds the prepared statement cache
    private static $HTMLconfig = null; // Optional HTML purifier config

    private function __construct() {
    }

    public static function setHTMLConfig($c) {
        self::$HTMLconfig = $c;
    }

    public static function getHTMLConfig() {
        return self::$HTMLconfig;
    }

    /**
     * Allows injecting a PDO connection (useful for tests).
     */
    public static function setConnection(PDO $pdo) {
        self::$dbConn = $pdo;
        self::$queryCatch = array();
    }

    /**
     * Resets connection and statement cache.
     */
    public static function resetConnection() {
        self::$dbConn = null;
        self::$queryCatch = array();
    }

    public static function changeToAdmin() {
        self::$username = ADMIN_USER;
        self::$password = ADMIN_PASS;
        self::resetConnection();
    }

    public static function changeToUser() {
        self::$username = NORMAL_USER;
        self::$password = NORMAL_PASS;
        self::resetConnection();
    }

    private static function connect() {
        if (self::$dbConn === null) {
            $dsnFromEnv = getenv('ORM_DSN');
            if ($dsnFromEnv !== false && strlen($dsnFromEnv) > 0) {
                $connUrl = $dsnFromEnv;
            } else {
                $connUrl = 'mysql:host=' . self::$host . ';dbname=' . self::$db . ';charset=utf8mb4';
            }

            self::$dbConn = new PDO(
                $connUrl,
                self::$username,
                self::$password,
                array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        }
    }

    private static function prepare($sql) {
        if (isset(self::$queryCatch[$sql]) && is_object(self::$queryCatch[$sql])) {
            return self::$queryCatch[$sql];
        }
        $stmt = self::$dbConn->prepare($sql);
        self::$queryCatch[$sql] = $stmt;
        return $stmt;
    }

    private static function executeWithArgs($stmt, $args) {
        $params = array_slice($args, 1);
        return $stmt->execute($params);
    }

    public static function query($sql) {
        self::connect();
        $stmt = self::prepare($sql);
        $args = func_get_args();
        self::executeWithArgs($stmt, $args);
        return $stmt;
    }

    public static function updateQuery($sql) {
        self::connect();
        $stmt = self::prepare($sql);
        $args = func_get_args();
        return self::executeWithArgs($stmt, $args);
    }

    public static function getLastInsertId() {
        return intval(self::$dbConn->lastInsertId());
    }
}

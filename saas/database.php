<?php

class Database
{
    private static $dbName = 'n2a33d5_erp';
    private static $dbHost = 'localhost';
    private static $dbUsername = 'n2a33d5_hasepm';
    private static $dbUserPassword = '#23hawlast';
    
    private static $cont = null;
    
    public function __construct() {
        die('Init function is not allowed');
    }
    
    public static function connect()
    {
        if (null == self::$cont) {
            // 2. Define your specific options
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throws exceptions on errors
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Returns arrays indexed by column name
                PDO::ATTR_EMULATE_PREPARES   => false,                 // Uses real prepared statements
                PDO::MYSQL_ATTR_FOUND_ROWS   => true                   // Returns count of affected rows even if unchanged
            ];

            try {
                $dsn = "mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName . ";charset=utf8mb4";
                self::$cont = new PDO($dsn, self::$dbUsername, self::$dbUserPassword, $options);
            } catch (PDOException $e) {
                // Log the error for you, but die with a generic message for the user
                error_log("Connection Failed: " . $e->getMessage());
                die("Internal Server Error: Database connection could not be established."); 
            }
        }
        return self::$cont;
    }
    
    public static function disconnect()
    {
        self::$cont = null;
    }
}
?>
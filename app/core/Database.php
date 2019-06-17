<?php

 class Database {
     private static $db=null;


    private function __construct() {
    }

    public static function getDB() {
        if (is_null(self::$db)) {
            self::$db=new PDO("mysql:host=localhost;dbname=file_manager","YOU HAVE TO SPECIFY","YOU HAVE TO SPECIFY");
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$db;
    }



}
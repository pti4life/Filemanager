<?php

 class Database {
     private static $db=null;


    private function __construct() {
    }

    public static function getDB() {
        if (is_null(self::$db)) {
            self::$db=new PDO("mysql:host=localhost;dbname=file_manager","pti4life","65482321");
            //self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->exec("set names utf8");
        }
        return self::$db;
    }



}
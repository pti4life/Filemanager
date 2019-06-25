<?php

 class Database {
     private static $db=null;


    private function __construct() {
    }

    public static function getDB() {
        if (is_null(self::$db)) {
            self::$db=new PDO("mysql:host=localhost;dbname=file_manager","PLEASE TYPE DB USERNAME","PLEASE TYPE DB PASSWORD");
            //self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->exec("set names utf8");
        }
        return self::$db;
    }



}
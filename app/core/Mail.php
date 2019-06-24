<?php

use PHPMailer\PHPMailer;


class Mail {
    private static $omg=null;


    private function __construct() {
    }

    public static function getMail() {
        if (is_null(self::$omg)) {
            self::$omg = new PHPMailer(true);
        }
        return self::$omg;
    }



}
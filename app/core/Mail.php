<?php

use PHPMailer\PHPMailer;


class Mail {
    private static $instance=null;


    private function __construct() {
    }

    public static function getMail() {
        if (is_null(self::$instance)) {
            self::$instance = new PHPMailer(true);
        }
        return self::$instance;
    }



}
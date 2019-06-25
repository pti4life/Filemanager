<?php


//Class for most used operations with User
class UserOperations  {

    private static $db;

    private function __construct() {
    }


    public static function isValidPassword($password) {
        if (strlen($password) < 5) {
            return 0;
        }
        return 1;
    }

    public static function isValidEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 0;
        }
        return 1;
    }

    public static function isValidUsername($username) {
        if (preg_match('/[^\p{L}0-9\p{L}a-z\p{L}A-Z\p{L}\s]/u',$username) or strlen(trim($username))<3) {
            return 0;
        }
        return 1;
    }

    public static function isValidName($name) {
        if (preg_match('/[^\p{L}a-z\p{L}A-Z\p{L}\s]/u',$name) or strlen(trim($name))<3) {
            return 0;
        }
        return 1;
    }

    public static function checkUser($username) {
        self::initializer();
        $stmt = self::$db->prepare("SELECT * FROM users WHERE user_uname=:username");
        $stmt->execute(["username" => $username]);
        $exsist = $stmt->rowCount();

        if ($exsist == 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function checkEmail($email) {
        self::initializer();
        $stmt = self::$db->prepare("SELECT * FROM users WHERE user_email=:email");
        $stmt->execute(["email" =>$email ]);
        $exsist = $stmt->rowCount();

        if ($exsist == 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function authUser($username,$password) {
        self::initializer();
        $stmt=self::$db->prepare("SELECT user_password FROM users WHERE user_uname=:username");
        $stmt->execute(["username"=>$username]);
        $select=$stmt->fetch(PDO::FETCH_ASSOC);
        if ($select) {
            if (password_verify($password,$select["user_password"])) {
                return 0;
            } else {
                return 2;
            }

        } else {
            return 1;
        }

    }

    private static function initializer() {
        if (!isset(self::$db)) {
            self::$db=Database::getDB();
        }
    }






}

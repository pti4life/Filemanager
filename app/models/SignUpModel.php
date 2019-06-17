<?php

class SignUpModel extends Model {

    public function __construct() {
        parent::__construct();
    }


    //return 1 Invalid name
    //return 2 Invalid username.
    //return 3 Invalid email
    //return 4. Invalid password
    //return 5 email exists.
    //return 6 username exists.
    //return 7 Dadatabase exception
    //empty errorarray succes signup
    public function SignUp($name, $email, $username, $password) {
        $errors=$this->isValidUser($name, $email, $username, $password);
        if (empty($errors)) {
            if (!UserOperations::checkUser($username)) {
                if (!UserOperations::checkEmail($email)) {
                    $password=password_hash($password, PASSWORD_BCRYPT);
                    try {
                        $stmt = $this->db->prepare('INSERT INTO users(user_uname,user_password,user_email,user_name)
                                                                    VALUES(:username,:password,:email,:user_name)');
                        $stmt->execute(["username" => $username, "password" => $password, "email"=>$email, "user_name"=>$name]);
                    } catch (PDOException $ex) {
                        echo "PDO EX: ".$ex."<br/>";
                        array_push($errors, 7);
                        return $errors;
                    }

                } else {
                    array_push($errors, 5);
                    return $errors;
                }

            } else {
                array_push($errors, 6);
                return $errors;
            }

        } else {
            return $errors;
        }
    }


    //1: Nem megfelelő név
    //2. Nem megfelelő felhasználónév
    //3. Nem megfelelő emailcím
    //4. Nem megfelelő a jelszó
    public function isValidUser($name, $email, $username, $password) {
        $errorArray=[];
        if (!UserOperations::isValidName($name)) {
            array_push($errorArray, 1);
        }

        if (!UserOperations::isValidUsername($username)) {
            array_push($errorArray, 2);
        }

        if (!UserOperations::isValidEmail($email)) {
            array_push($errorArray, 3);
        }

        if (!UserOperations::isValidPassword($password)) {
            array_push($errorArray, 4);
        }

        return $errorArray;

    }


}
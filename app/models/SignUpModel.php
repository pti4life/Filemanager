<?php

class SignUpModel extends Model {

    public function __construct() {
        parent::__construct();
    }


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
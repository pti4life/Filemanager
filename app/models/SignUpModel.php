<?php

class SignUpModel extends Model
{
    private $errorArray = [];


    public function __construct()
    {
        parent::__construct();
    }


    public function SignUp($name, $email, $username, $password)
    {
        //return 6 if username exists.
        //return 5 if email exists.
        $this->isValidUser($name, $email, $username, $password);
        if (empty($this->errorArray)) {
            if (!$this->checkUser($username)) {
                if (!$this->checkEmail($email)) {
                    $password=password_hash($password, PASSWORD_BCRYPT);
                    try {
                        $stmt = $this->db->prepare('INSERT INTO users(user_uname,user_password,user_email,user_name)
                                                                    VALUES(:username,:password,:email,:user_name)');
                        $stmt->execute(["username" => $username, "password" => $password, "email"=>$email, "user_name"=>$name]);
                    } catch (PDOException $ex) {
                        echo "PDO EX: ".$ex."<br/>";
                        array_push($this->errorArray, 7);
                        return;
                    }

                } else {
                    array_push($this->errorArray, 5);
                    return $this->errorArray;
                }

            } else {
                array_push($this->errorArray, 6);
                return $this->errorArray;
            }

        } else {
            return $this->errorArray;
        }


    }

    public function isValidUser($name, $email, $username, $password)
    {
        if (strlen($name) < 3) {
            array_push($this->errorArray, 1);
        }

        if (strlen($username) < 5) {
            array_push($this->errorArray, 2);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($this->errorArray, 3);
        }

        if (strlen($password) < 5) {
            array_push($this->errorArray, 4);
        }

    }


    //TODO: !!!!!!!!!!!!!!!!!CODE DUPLICATION WITH lOGINMODEL.PHP!!!!!!!!!!!!!!!!!!!!
    private function checkUser($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_uname=:username");
        $stmt->execute(["username" => $username]);
        $exsist = $stmt->rowCount();

        if ($exsist == 0) {
            return false;
        } else {
            return true;
        }
    }

    private function checkEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_email=:email");
        $stmt->execute(["email" =>$email ]);
        $exsist = $stmt->rowCount();

        if ($exsist == 0) {
            return false;
        } else {
            return true;
        }
    }

}
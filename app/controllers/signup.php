<?php

class SignUp extends Controller {


    function __construct() {

        $this->setModel("signupmodel");
    }


    public function index($errors=[]) {
        $this->view("signupview",$errors);
    }

    public function signup() {
        $username=$_POST["username"];
        $password=$_POST["password"];
        $email=$_POST["email"];
        $name=$_POST["name"];
        $errorArray=[];
        if(!strlen($name)==0 and !strlen($username)==0 and !strlen($password)==0 and !strlen($email)==0 ) {
            $array=$this->model->SignUp($name,$email,$username,$password);
            if (empty($array)) {
                call_user_func_array(["signup","index"],array(["message"=>"Sikeres regisztáció!"]));
            } else {
                foreach ($array as $item) {
                    echo "ARRAY ITEMS: ".$item."<br/>";
                    switch ($item) {
                        case 1:
                            $errorArray["nameErr"]="Nem megfelelő név";
                            break;
                        case 2:
                            $errorArray["unameErr"]="Nem megfelelő felhasználónév.";
                            break;
                        case 3:
                            $errorArray["emailErr"]="Nem megfelelő emailcím.";
                            break;
                        case 4:
                            $errorArray["passwordErr"]="Nem megfelelő jelszó.";
                            break;
                        case 5:
                            $errorArray["emailErr"]="Ez az email cím létezik.";
                            break;
                        case 6:
                            $errorArray["unameErr"]="Ez a felhasználónév már létezik.";
                            break;
                        case 7:
                            $errorArray["message"]="Ismeretlen hiba!";
                            break;
                    }
                }
                call_user_func_array(["signup","index"],array($errorArray));
            }

        } else {
            call_user_func_array(["signup","index"],[["message"=>"Minden mezőt kötelező kitölteni!"]]);
        }

    }


}
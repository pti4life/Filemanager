<?php
class Login extends Controller {

    public function __construct() {
        Session::init();

        if(Session::get("loggedin")) {
            header("location:..\\filestorage");
            Session::destroy();
            exit;
        }
        $this->setModel("Loginmodel");
    }

    public function index($parameter=[]) {
        //echo "Login index called";
        $this->view("loginview",$parameter);

    }

    public function loginSubmit() {
        $username=$_POST["username"];
        $password=$_POST["password"];
        if(!(strlen($username)==0 or strlen($password)==0)) {
            $status=$this->model->login($username,$password);
            echo "statis: ".$status;
            switch ($status) {
                case 0:
                    Session::init();
                    Session::set("loggedin",true);
                    Session::set('user_name', $username);
                    echo "BEJELENTKEZÉS..";
                    header("Location: ..\\filestorage");
                    break;
                case 1:
                    call_user_func_array(["login","index"],[["message"=>"Nem található ilyen fehasználó!"]]);
                    break;
                case 2:
                    call_user_func_array(["login","index"],[["message"=>"Hibás jelszót adott meg!"]]);
                    break;
            }

        } else {
            call_user_func_array(["login","index"],[["message"=>"Minden mezőt ki kell tölteni!"]]);
        }
    }

    public function test() {
        //echo "kolbi";
    }
}
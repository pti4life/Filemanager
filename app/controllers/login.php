<?php
class Login extends Controller {
    private $model;


    public function __construct() {
        Session::init();

        if(Session::get("loggedin")) {
            header("location:..\public\\filestorage");
            exit;
        }
    }

    public function index($parameter=[]) {
        //echo "Login index called";
        $this->view("loginview",$parameter);

    }

    public function loginSubmit() {
        $this->model=$this->getModel("Loginmodel");
        if(isset($_POST["username"]) and isset($_POST["password"])) {
            $username=$_POST["username"];
            $password=$_POST["password"];

            $status=$this->model->authUser($username,$password);
            switch ($status) {
                case 0:
                    Session::init();
                    Session::set("loggedin",true);
                    Session::set('user_name', $username);
                    header("Location: ..\\filestorage");
                    break;
                case 1:
                    call_user_func_array(["login","index"],[["message"=>"Nem található ilyen fehasználó!"]]);
                    break;
                case 2:
                    call_user_func_array(["login","index"],[["message"=>"Hibás jelszót adott meg!"]]);
                    break;
            }

        }
    }

    public function test() {
        //echo "kolbi";
    }
}
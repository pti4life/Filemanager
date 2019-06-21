<?php

class App {

    protected $controller="home";
    protected $method ="index";
    protected $parameters=[];


    function __construct() {
        $url=$this->processURL();
        if (class_exists($url[0])) {
            //echo "class exists";
            $this->controller=$url[0];
            unset($url[0]);
        } else {
            $this->controller="errorpage";
            unset($url[0]);
        }

        $this->controller=new $this->controller();

        if(isset($url[1])) {
            if (method_exists($this->controller,$url[1])) {
                $this->method=$url[1];
                unset($url[1]);
            } else {
                //metho is setted but doesnt exists;
            }
        }

        $this->parameters=$url? array_values($url):[];

        //debug messages
        //echo(var_dump($this->controller));
        //echo("method: ". $this->method."<br/>");
        //echo "PARAMETERS: ";
        //print_r($this->parameters)."<br/>";
        call_user_func_array([$this->controller,$this->method],$this->parameters);
    }


    public function processURL() {
        if(isset($_GET["url"])) {
            //echo "URL: ".$_GET["url"]."<br/>";
            //echo "VALIDATED URL: ";
            //print_r(explode("/",filter_var(rtrim($_GET["url"],"/"),FILTER_SANITIZE_URL)));
            //echo "<br/>";
            return explode("/",filter_var(rtrim($_GET["url"],"/"),FILTER_SANITIZE_URL));

        }
    }

}
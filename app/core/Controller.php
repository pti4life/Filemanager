<?php

$msg=[];

abstract class Controller {

    protected function view($name,$param=[]) {

        $GLOBALS["msg"]=$param;
        //echo "GLOBAL MSG: ";
        //print_r($GLOBALS["msg"]);
        //echo "<br/>";
        Session::init();
        autoload_views("headerview");
        autoload_views("$name");
        autoload_views("footerview");
    }

    protected function getModel($name) {
        return new $name();

    }





}
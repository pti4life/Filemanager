<?php

class Errorpage extends Controller {
    function __construct() {
    }

    public function index($param=["message"=>"Az oldal nem található!"]) {
        $this->view("errorview",$param);
    }
}
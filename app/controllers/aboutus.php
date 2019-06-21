<?php

class AboutUs extends Controller {


    public function index() {
        $this->view("aboutusview");
        $file=file_get_contents("../app/files/hunhar13/38.txt");
    }
}
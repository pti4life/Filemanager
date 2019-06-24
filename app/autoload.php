
<?php
function autoload($className) {
    $directorys = array(
        '..\\app\\controllers\\',
        '..\\app\\models\\',
        '..\\app\\core\\',
        '..\\app\\core\\PHPMailer\\'
    );

    foreach ($directorys as $directory) {
        $filename = $directory . $className . ".php";
        //echo "filename: ".$filename."<br/>";
        if (file_exists($filename)) {
            //echo "autoloaded: ".$filename."<br/>";
            require_once $filename;
            return;
        }
    }

}


spl_autoload_register("autoload");
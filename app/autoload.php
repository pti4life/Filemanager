
<?php
function autoload($className) {
    $directorys = array(
        '..\\app\\controllers\\',
        '..\\app\\models\\',
        '..\\app\\core\\',
    );

    $filename="";
    foreach ($directorys as $directory) {
        $filename = strtolower($directory) . $className . ".php";
        if (file_exists($filename)) {
            //echo "autoloaded: ".$filename."<br/>";
            require_once $filename;
            return;
        }
    }

}


spl_autoload_register("autoload");
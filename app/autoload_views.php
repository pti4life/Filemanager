<?php
function autoload_views($className) {
    $directorys = array(
        '..\\app\\views\\',
        '..\\app\\views\\webcontent\\'
    );

    $filename="";
    foreach ($directorys as $directory) {
        $filename = strtolower($directory) . $className . ".html";
        if (file_exists($filename)) {
            //echo "autoloaded: ".$filename."<br/>";
            require_once $filename;
            return;
        }
    }

}


spl_autoload_register("autoload_views");
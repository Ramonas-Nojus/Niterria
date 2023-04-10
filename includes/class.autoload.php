<?php 

spl_autoload_register("myAutoloader");

function myAutoloader($className){

    $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    if(strpos($url, "admin") !== false){
        $path = "../classes/";
    } else {
        $path = "classes/";
    } 

    $extension = ".class.php";
    require_once $path.$className.$extension;

}
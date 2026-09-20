<?php

spl_autoload_register(function($class){

    $prefix = 'Stripe\\';


    if(strpos($class,$prefix)!==0){
        return;
    }


    $class = str_replace(
        $prefix,
        '',
        $class
    );


    $class = str_replace(
        '\\',
        '/',
        $class
    );


    $file = __DIR__ .
    '/stripe/lib/' .
    $class .
    '.php';


    if(file_exists($file)){

        require_once $file;

    }

});
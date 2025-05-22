<?php


class OurLogger{
    private static $instance;

    protected function __construct(){

    }

    public static function GetInstance(){
        if(OurLogger::$instance == null){
            OurLogger::$instance = require_once ("Utils/Logging.php");
        }
        return OurLogger::$instance;
    }
};

?>
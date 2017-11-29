<?php
if(!defined('INIT')){
    exit(0);
}
class View {
    public static function render(){
        if(preg_match("/^\/api*/",$_SERVER[REQUEST_URI])){
            //pass through for api
            Controller::render();
            return;
        }
        if(preg_match("/^\/import$/",$_SERVER[REQUEST_URI])){
            //pass through for importer
            require_once "import.php";
            return;
        }
        require_once "home.php";
    }
}
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
        require_once "home.php";
    }
}
<?php
if(!defined('INIT')){
    exit(0);
}
require_once "credentials.php";

class Controller {
    private static $db;

    public static function render(){
        self::$db = new Db();
        if(preg_match('/\/api\/kid*/',$_SERVER[REQUEST_URI])&&$_SERVER['REQUEST_METHOD']==='GET'){
            Controller::get_kid();
        } elseif(preg_match('/\/api\/auth*/',$_SERVER[REQUEST_URI])&&$_SERVER['REQUEST_METHOD']==='GET'){
            Controller::get_auth();
        } elseif(preg_match('/\/api\/login*/',$_SERVER[REQUEST_URI])&&$_SERVER['REQUEST_METHOD']==='POST'){
            Controller::login();
        } elseif(preg_match('/\/api\/logout*/',$_SERVER[REQUEST_URI])&&$_SERVER['REQUEST_METHOD']==='GET'){
            Controller::logout();
        } elseif(preg_match('/\/api\/club*/',$_SERVER[REQUEST_URI])&&$_SERVER['REQUEST_METHOD']==='GET'){
            Controller::get_club();
        } elseif(preg_match('/\/api\/save*/',$_SERVER[REQUEST_URI])&&$_SERVER['REQUEST_METHOD']==='POST'){
            Controller::save_query();
        } elseif(preg_match('/\/api\/queries*/',$_SERVER[REQUEST_URI])&&$_SERVER['REQUEST_METHOD']==='GET'){
            Controller::get_past_queries();
        } elseif(preg_match('/\/api\/connected*/',$_SERVER[REQUEST_URI])&&$_SERVER['REQUEST_METHOD']==='GET'){
            Controller::get_connected();
        } else {   
            Controller::default_route();
        }
    }
    private static function default_route(){
        header('Content-Type: application/json');
        ob_start();
        echo json_encode(array());
        echo ob_get_clean();
    }
    private static function get_kid(){
        header('Content-Type: application/json');
        if(!isset($_SESSION['logged_in'])) {
            echo json_encode(array("status"=>false));
            return;
        }
        ob_start();
        if(isset($_GET['email'])){
            echo json_encode(self::$db->get_kid($_GET['email']));
        } else {
            echo json_encode(array("status"=>false));
        }
        echo ob_get_clean();
    }
    private static function get_club(){
        header('Content-Type: application/json');
        if(!isset($_SESSION['logged_in'])) {
            echo json_encode(array("status"=>false));
            return;
        }
        ob_start();
        if(isset($_GET['name'])){
            echo json_encode(self::$db->get_club($_GET['name']));
        } else {
            echo json_encode(array("status"=>false));
        }
        echo ob_get_clean();
    }
    private static function get_connected(){
        header('Content-Type: application/json');
        if(!isset($_SESSION['logged_in'])) {
            echo json_encode(array("status"=>false));
            return;
        }
        ob_start();
        if(isset($_GET['email1'])&&isset($_GET['email2'])){
            echo json_encode(self::$db->get_connected($_GET['email1'],$_GET['email2']));
        } else {
            echo json_encode(array("status"=>false));
        }
        echo ob_get_clean();
    }
    private static function get_past_queries(){
        header('Content-Type: application/json');
        if(!isset($_SESSION['logged_in'])) {
            echo json_encode(array("status"=>false));
            return;
        }
        ob_start();
        echo json_encode(self::$db->get_past_queries());
        echo ob_get_clean();
    }
    private static function save_query(){
        header('Content-Type: application/json');
        if(!isset($_SESSION['logged_in'])) {
            echo json_encode(array("status"=>false));
            return;
        }
        ob_start();
        $input = json_decode(file_get_contents("php://input"));
        echo json_encode(self::$db->save_query($input->name,$input->value));
        echo ob_get_clean();
    }
    private static function get_auth(){
        header('Content-Type: application/json');
        ob_start();
        if(isset($_SESSION['logged_in'])){
            echo json_encode(array("status"=>true));
        } else {
            echo json_encode(array("status"=>false));
        }
        echo ob_get_clean();
    }
    private static function login(){
        global $login_password, $login_user;
        $correct_pass = false;
        $input = json_decode(file_get_contents("php://input"));
        if(strcmp($input->user,$login_user)===0&&strcmp($input->password,$login_password)===0){
            $_SESSION['logged_in'] = true;
            $correct_pass = true;
        }
        header('Content-Type: application/json');
        ob_start();
        if(isset($_SESSION['logged_in'])&&$correct_pass){
            echo json_encode(array("status"=>true));
        } else {
            echo json_encode(array("status"=>false));
        }
        echo ob_get_clean();
    }
    private static function logout(){
        session_unset();
        session_destroy();
        header('Content-Type: application/json');
        ob_start();
        echo json_encode(array());
        echo ob_get_clean();
    }
}
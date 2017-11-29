<?php
if(!defined('INIT')){
    exit(0);
}
require_once "credentials.php";

class Controller {
    private static $db;

    public static function render(){
        self::$db = new Db();
        switch($_SERVER[REQUEST_URI]){
            case '/api/get-child':
                Controller::get_child();
                break;
            case '/api/login':
                Controller::login();
                break;
            case '/api/logout':
                Controller::logout();
                break;
            case '/api/import':
                Controller::import();
                break;
            default:
                Controller::default_route();
                break;
        }
    }
    private static function default_route(){
        header('Content-Type: application/json');
        ob_start();
        echo "{}";
        echo json_encode(ob_get_clean());
    }
    private static function import(){
        header('Content-Type: application/json');
        ob_start();
        //echo self::$db->import();
        echo json_encode(ob_get_clean());
    }
    private static function get_child(){
        header('Content-Type: application/json');
        ob_start();
        echo $_SESSION['logged_in'];
        echo json_encode(ob_get_clean());
    }
    private static function login(){
        global $login_password, $login_user;
        $input = json_decode(file_get_contents("php://input"));
        if(strcmp($input->user,$login_user)===0&&strcmp($input->password,$login_password)===0){
            $_SESSION['logged_in'] = true;
        }
        header('Content-Type: application/json');
        ob_start();
        if(isset($_SESSION['logged_in'])){
            echo '{"status":true}';
        } else {
            echo '{"status":false}';
        }
        echo json_encode(ob_get_clean());
    }
    private static function logout(){
        session_unset();
        session_destroy();
        header('Content-Type: application/json');
        ob_start();
        echo "{}";
        echo json_encode(ob_get_clean());
    }
}
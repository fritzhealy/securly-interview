<?php //securly interview 
if(!defined('INIT')){
    exit(0);
}
require_once "credentials.php";
class Db {
    private $pdo;
    
    function __construct(){
        global $host, $dbname, $user, $pass;
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname",$user,$pass);
            if(!defined('DEBUG')||DEBUG === "false"){
                $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            } else {
                $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
    function __destruct(){
        $this->pdo=null;
    }

    public function import(){
        
    }

    public function get_child(){

    }
}

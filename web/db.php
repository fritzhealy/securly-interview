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
            $this->init_tables();
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
    function __destruct(){
        $this->pdo=null;
    }
    private function init_tables(){
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS school
            (id INT NOT NULL AUTO_INCREMENT, 
            name VARCHAR(120) NOT NULL, 
            PRIMARY KEY (id),
            UNIQUE INDEX (name));");
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS club
            (id INT NOT NULL AUTO_INCREMENT, 
            name VARCHAR(120) NOT NULL, 
            PRIMARY KEY (id),
            UNIQUE INDEX (name));");
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS kid
            (id INT NOT NULL AUTO_INCREMENT, 
            name VARCHAR(120) NOT NULL,
            email VARCHAR(120) NOT NULL, 
            PRIMARY KEY (id),
            UNIQUE INDEX (name, email));");
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS schoolKidLink
            (schoolId INT NOT NULL , 
            kidId INT NOT NULL,
            UNIQUE INDEX (schoolId, kidId));");
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS clubKidLink
            (clubId INT NOT NULL , 
            kidId INT NOT NULL,
            UNIQUE INDEX (clubId, kidId));");
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS schoolClubLink
            (schoolId INT NOT NULL , 
            clubId INT NOT NULL,
            UNIQUE INDEX (schoolId, clubId));");
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS queries
            (name VARCHAR(50) NOT NULL , 
            value VARCHAR(100) NOT NULL,
            UNIQUE INDEX (name, value));");
        } catch (PDOException $e){
            //add error handling later
        }
    }
    public function import($name){
        if($file = fopen($name,"r")){
            try{
                $school = '';
                $club = '';
                $kid_email = '';
                $kid_name = '';
                $school_stmt = $this->pdo->prepare("INSERT INTO school(name) VALUES(:name) ON DUPLICATE KEY UPDATE name=:name");
                $school_stmt->bindParam(':name',$school,PDO::PARAM_STR);
                $club_stmt = $this->pdo->prepare("INSERT INTO club(name) VALUES(:name) ON DUPLICATE KEY UPDATE name=:name");
                $club_stmt->bindParam(':name',$club,PDO::PARAM_STR);
                $kid_stmt = $this->pdo->prepare("INSERT INTO kid(name,email) VALUES(:name, :email) ON DUPLICATE KEY UPDATE name=:name");
                $kid_stmt->bindParam(':name',$kid_name,PDO::PARAM_STR);
                $kid_stmt->bindParam(':email',$kid_email,PDO::PARAM_STR);
                $school_kid_stmt = $this->pdo->prepare("INSERT INTO schoolKidLink(schoolId, kidId) VALUES(
                    (SELECT id FROM school WHERE name=:school),
                    (SELECT id FROM kid WHERE email=:email)) 
                    ON DUPLICATE KEY UPDATE schoolId=(SELECT id FROM school WHERE name=:school)");
                $school_kid_stmt->bindParam(':school',$school,PDO::PARAM_STR);
                $school_kid_stmt->bindParam(':email',$kid_email,PDO::PARAM_STR);
                $club_kid_stmt = $this->pdo->prepare("INSERT INTO clubKidLink(clubId, kidId) VALUES(
                    (SELECT id FROM club WHERE name=:club),
                    (SELECT id FROM kid WHERE email=:email))
                    ON DUPLICATE KEY UPDATE clubId=(SELECT id FROM club WHERE name=:club)");
                $club_kid_stmt->bindParam(':club',$club,PDO::PARAM_STR);
                $club_kid_stmt->bindParam(':email',$kid_email,PDO::PARAM_STR);
                $school_club_stmt = $this->pdo->prepare("INSERT INTO schoolClubLink(schoolId, clubId) VALUES(
                    (SELECT id FROM school WHERE name=:school),
                    (SELECT id FROM club WHERE name=:club))
                    ON DUPLICATE KEY UPDATE schoolId=(SELECT id FROM school WHERE name=:school)");
                $school_club_stmt->bindParam(':school',$school,PDO::PARAM_STR);
                $school_club_stmt->bindParam(':club',$club,PDO::PARAM_STR);
                $i=0;
                while(($line = fgetcsv($file))!==false){
                    if($i===0){
                        $i++;
                        continue;
                    }
                    $school = $line[0];
                    $club = $line[1];
                    $kid_name = $line[2];
                    $kid_email = $line[3];
                    $school_stmt->execute();
                    $club_stmt->execute();
                    $kid_stmt->execute();
                    $school_kid_stmt->execute();
                    $club_kid_stmt->execute();
                    $school_club_stmt->execute();
                }
            } catch(PDOException $e){
                echo $e;
            }
            return "Succes, imported everyone!";
        } else {
            return "Import failed, couldn't open file";
        }
    }

    public function get_kid($email){
        $return = array();
        try {
            $stmt = $this->pdo->prepare("SELECT c.name FROM club as c 
            INNER JOIN clubKidLink as ck ON ck.clubId=c.id 
            INNER JOIN kid as k ON ck.kidId = k.id WHERE k.email=?");
            $stmt->execute(array($email));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $return['clubs'] = $rows;
            
            $stmt = $this->pdo->prepare("SELECT s.name FROM school as s 
            INNER JOIN schoolKidLink as sk ON sk.schoolId=s.id 
            INNER JOIN kid as k ON sk.kidId = k.id WHERE k.email=?");
            $stmt->execute(array($email));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $return['schools'] = $rows;
        } catch(PDOException $e){

        }
        return $return;
    }
    public function get_club($name){
        $return = array();
        try {
            $stmt = $this->pdo->prepare("SELECT k.name FROM club as c 
            INNER JOIN clubKidLink as ck ON ck.clubId=c.id 
            INNER JOIN kid as k ON ck.kidId = k.id WHERE c.name=?");
            $stmt->execute(array($name));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $return['kids'] = $rows;
            
            $stmt = $this->pdo->prepare("SELECT s.name FROM school as s 
            INNER JOIN schoolClubLink as sc ON sc.schoolId=s.id 
            INNER JOIN club as c ON c.id = sc.clubId WHERE c.name=?");
            $stmt->execute(array($name));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $return['schools'] = $rows;
        } catch(PDOException $e){
        
        }
        return $return;
    }
    public function get_past_queries(){
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM queries;");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("queries"=>$rows);
        } catch(PDOException $e){
            
        }
        return array("status"=>false);
    }
    public function save_query($name, $value){
        try {
            $stmt = $this->pdo->prepare("INSERT INTO queries(name, value) 
            VALUES(?,?) ON DUPLICATE KEY UPDATE name=?;");
            $stmt->execute(array($name, $value, $name));
            if($stmt->fetchColumn()>0){
                return array("status"=>true);
            }
        } catch(PDOException $e){
            
        }
        return array("status"=>false);
    }
}

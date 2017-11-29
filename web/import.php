<?php 
if(!defined('INIT')){
    exit(0);
}
if(isset($_FILES['file'])){
    if(in_array(
        pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION), 
        array('csv'))){
        move_uploaded_file($_FILES['file']['tmp_name'],'uploads/temp.csv');
        //need to add security check
        $db = new Db();
        $message = $db->import("uploads/temp.csv");
    } else {
        $message = "incorrect file type";
    }
}
?>
<!doctype html>
<html>
    <head>
        <title>Securly App Importer</title>
    </head>
    <body>
        <h1>Importer</h1>
        <h2><?php if($message) echo $message;?></h2>
        <form method="POST" action="/import" enctype="multipart/form-data">
            <input type="file" name="file">
            <input type="submit" value="submit">
        </form>
    </body>
</html>
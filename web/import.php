<?php if(isset($_FILES['file'])){
    move_uploaded_file($_FILES['file']['tmp_name'],'uploads/temp.csv');
    //need to add security check
    $db = new Db();
    $message = $db->import("uploads/temp.csv");
}
?>
<h1>Importer</h1>
<h2><?php if($message) echo $message;?></h2>
<form method="POST" action="" enctype="multipart/form-data">
<input type="file" name="file">
<input type="submit" value="submit">
</form>
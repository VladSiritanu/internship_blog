<?php

//Check  if there is any post request,which submit
//was sent,if the required fields are not empty
if($_SERVER['REQUEST_METHOD']=='POST'
    && $_POST['submit']=='Save Entry'
    && !empty($_POST['title'])
    && !empty($_POST['entry']))
{
    //Include database credentials and connect to it
    include_once 'db.inc.php';
    try{
        $db = new PDO(DB_INFO,DB_USER,DB_PASS);
    }catch(PDOException $e)
    {
        echo 'Connection failed : ', $e->getMessage();
    }
//Save the entry into the database
    $sql = "INSERT INTO entries (entry_title, entry_text) VALUES(?,?)";
    $stmt = $db->prepare($sql);
    $stmt->execute(array($_POST['title'],$_POST['entry']));
    $stmt->closeCursor();

//Get the ID of the entry we just saved
    $id_obj = $db->query("SELECT LAST_INSERT_ID()");
    $id = $id_obj->fetch();
    $id_obj->closeCursor();

//Send the user to the new entry
    header('Location: ../?id='.$id[0]);
    exit;
}
//If any of the condition aren't met, send the user
//to the mai page
else
{
    header('Location: ../');
    exit;
}

?>
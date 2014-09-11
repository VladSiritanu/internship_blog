<?php
//Include the function so ypu can create a URL
include_once 'function.inc.php';

//Check  if there is any post request,which submit
//was sent,if the required fields are not empty
if($_SERVER['REQUEST_METHOD']=='POST'
    && $_POST['submit']=='Save Entry'
    && !empty($_POST['page'])
    && !empty($_POST['title'])
    && !empty($_POST['entry']))
{
    //Create a URL to save in the database
    $url = makeURL($_POST['title']);

    //Include database credentials and connect to it
    include_once 'db.inc.php';
    try{
        $db = new PDO(DB_INFO,DB_USER,DB_PASS);
    }catch(PDOException $e)
    {
        echo 'Connection failed : ', $e->getMessage();
    }
    //Edit an existing entry
    if(!empty($_POST['id']))
    {
        $sql = "UPDATE entries
                SET entry_title=?, entry_text=?, url=?
                WHERE entry_id=?
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(
            array(
                $_POST['title'],
                $_POST['entry'],
                $url,
                $_POST['id']
            )
        );
        $stmt->closeCursor();
        $page = htmlentities(strip_tags($_POST['page']));
        header('Location: /internship_blog/' . $page . '/'. $url);
    }


    //Create a new entry
    else{
    //Save the entry into the database
    $sql = "INSERT INTO entries (page,entry_title, entry_text,url) VALUES(?,?,?,?)";
    $stmt = $db->prepare($sql);
    $stmt->execute(array($_POST['page'],$_POST['title'],$_POST['entry'],$url));
    $stmt->closeCursor();

    // Sanitize the page information dor use in the  success URL
    $page = htmlentities(strip_tags($_POST['page']));


//Send the user to the new entry
    header('Location: /internship_blog/' . $page . '/'. $url);
    }
}

//If any of the condition aren't met, send the user
//to the mai page
else
{
    header('Location: ../');
    exit;
}

?>
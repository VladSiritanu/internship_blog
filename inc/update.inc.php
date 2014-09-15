<?php

session_start();

//Include the function so ypu can create a URL
include_once 'function.inc.php';

//Include the image handling class
include_once 'image.inc.php';
// Check  if there is any post request,which submit was sent,if the required
// fields are not empty.
if($_SERVER['REQUEST_METHOD']=='POST'
    && $_POST['submit']=='Save Entry'
    && !empty($_POST['page'])
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
        exit;
    }
    $query = "Select AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'internship_blog' AND TABLE_NAME = 'entries'";
    $result = $db->query($query);
    $row = $result->fetch();
    $id = $row['AUTO_INCREMENT'];

    //Create a URL to save in the database
    $url = makeURL($_POST['title'],$id);

    if(!empty($_FILES['image']['tmp_name']))
    {
        try
        {
            //Instantiate the class and set  a save path
            $img = new ImageHandler("/internship_blog/images/",array(800,600) );

            //Process the file adn store the returned path
            $img_path = $img->processUploadedImage($_FILES['image']);
        }
        catch(Exception $e)
        {
            //If an error occurred, output your custom error  message
            die( $e->getMessage());
        }
    }
    else
    {
        //Avoids a notice if no image was uploaded
        $img_path == NULL;
    }



    //Edit an existing entry
    if(!empty($_POST['id']))
    {
        $url = makeURL($_POST['title'],$_POST['id']);
        $sql = "UPDATE entries
                SET entry_title=?,image=?, entry_text=?, url=?
                WHERE entry_id=?
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(
            array(
                $_POST['title'],
                $img_path,
                nl2br(trim($_POST['entry'])),
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
    $sql = "INSERT INTO entries (page,entry_title,image, entry_text,url) VALUES(?,?,?,?,?)";
    $stmt = $db->prepare($sql);
    $stmt->execute(
        array($_POST['page'],
            $_POST['title'],
            $img_path,
            nl2br(trim($_POST['entry'])),
            $url
        )
    );
    $stmt->closeCursor();

    // Sanitize the page information dor use in the  success URL
    $page = htmlentities(strip_tags($_POST['page']));


//Send the user to the new entry
    header('Location: /internship_blog/' . $page . '/'. $url);
    }
}

//If a comment is being posted, handle it here
elseif($_SERVER['REQUEST_METHOD'] == 'POST'
    && $_POST['submit'] == 'Post Comment')
{

    //Include and instantiate the Comment class
    include_once 'comment.inc.php';
    $comment = new Comments();

    //Save the comment
    $comment->saveComment($_POST);

    //If available, store the entry where the user came from
    if(isset($_SERVER['HTTP_REFERER']))
     {
            $loc = $_SERVER['HTTP_REFERER'];
     }
     else
     {
            $loc = '../';
     }
    //Send the user back to the entry
    header('Location: ' . $loc);
    exit;


}

    //If the delete ling is clicked on a comment, confirm it here
    elseif($_GET['action'] == 'comment_delete')
    {
        //Include and instantiate the Comments class
        include_once 'comment.inc.php';
        $comments = new Comments();
        echo $comments->confirmDelete($_GET['id']);
        exit;
    }
    //If the confirmDelete()  form was submitted, handle it here
    elseif($_SERVER['REQUEST_METHOD'] == 'POST'
    && $_POST['action'] == 'comment_delete')
    {
        //IF set, store the entry form which we came
        $loc = isset($_POST['url']) ? $_POST['url'] : '../';

        //If the user clicked "Yes", continue with deletion
        if($_POST['confirm'] == "Yes")
        {
            //Include and instantiate the Comments class
            include_once 'comment.inc.php';
            $comments = new Comments();

            //Delete the comment and return to the entry
            if($comments->deleteComment($_POST['id']))
            {
                header('Location: ' . $loc);
                exit;
            }

            //If deleting fails, output an error message
            else{
                exit('Could not delete the comment.');
            }

        }

        //If user clicked "No", do nothing adn return to the entry
        else{
            header('Location: ' . $loc);
            exit;
        }
    }

    //if a user is trying to log in, check it here
    else if ($_SERVER['REQUEST_METHOD'] == "POST"
            && $_POST['action'] == 'login'
            && !empty($_POST['username'])
            && !empty($_POST['password']))
    {
        //Include database credentials and connect to the database
        include_once 'db.inc.php';
        $db = new PDO(DB_INFO,DB_USER,DB_PASS);
        $sql = "SELECT COUNT(*) AS num_users
                FROM admin
                WHERE username=?
                AND password=SHA1(?)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(
            $_POST['username'],
            $_POST['password']
        ));
        $response = $stmt->fetch();
        if($response['num_users'] > 0)
        {
            $_SESSION['loggedin'] =1;
        }
        else
        {
            $_SESSION['loggedin'] = NULL;
        }
        header('Location: /internship_blog/');
    }

    //If an admin is being created, save it here
    else if($_SERVER['REQUEST_METHOD'] == 'POST'
        && $_POST['action'] == 'create_user'
        && !empty($_POST['username'])
        && !empty($_POST['password']))
    {
        //Include database credentials and connect to the database
        include_once 'db.inc.php';
        $db = new PDO(DB_INFO,DB_USER,DB_PASS);
        $sql = "INSERT INTO admin (username, password)
                VALUES (?,SHA1(?))";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(
            $_POST['username'],
            $_POST['password']
        ));
        header('Location: /internship_blog');
        exit;
    }

    //If the user has chosen to log out, process it here
    else if($_GET['action'] == 'logout')
    {
        session_destroy();
        header('Location: ../');
        exit;
    }
//If any of the condition aren't met, send the user
//to the main page
else
{
    unset($_SESSION['c_name'], $_SESSION['c_email'],
    $_SESSION['c_comment'], $_SESSION['error']);
    header('Location: ../');
    exit;
}

?>
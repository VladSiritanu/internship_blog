<?php

    session_start();

    //Include the necessary files
    include_once 'inc/function.inc.php';
    include_once 'inc/db.inc.php';

    //Open a database connection
    $db =  new PDO(DB_INFO,DB_USER,DB_PASS);

    //Figure out what page is being requested(default is blog)
    //Perform basic sanitization on the variable as well
    if(isset($_GET['page']))
    {
        $page= htmlentities(strip_tags($_GET['page']));
    }
    else
{
        $page = 'blog';
    }

    //Determine if an entry URL was passed
    $url = (isset($_GET['url'])) ? $_GET['url'] : NULL;

    //Load the entries
    $entries = retrieveEntries($db,$page,$url);
    //Get the fulldisp and remove it from the  array
    $fulldisp = array_pop($entries);

    //Sanitize the entry data
    $entries = sanitizeData($entries);

?>

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0  Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type"
          content="text/html;charset=utf-8"/>
    <link rel="stylesheet" href="/internship_blog/css/default.css" type="text/css"/>
    <link rel="alternate" type="application/rss+xml"
            title="My First Blog - RSS 2.0"
            href="/internship_blog/feeds/rss.php"/>
    <title>Blog</title>
</head>

<body>
    <h1 >Simple Blog Application</h1>

    <ul id="menu">
        <li><a href="/internship_blog/blog/">Blog</a> </li>
        <li><a href="/internship_blog/about/">About</a> </li>
    </ul>

    <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1): ?>
        <p id="control_panel">
            You are logged in!
            <a href="/internship_blog/inc/update.inc.php?action=logout">Log Out!</a>
        </p>
    <?php endif; ?>
    <div id="entries">
        <?php
            //If the full display flag is set, show the entry
            if ($fulldisp==1)
            {
                //get the URL if one wasn't passed
                $url = (isset($url)) ? $url : $entries['url'];
                if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1){
                //Build the admin lings
                $admin = adminlinks($page,$url);
                }
                else
                {
                    $admin = array('edit' =>NULL, 'delete' =>NULL);
                }

                //Format the image if one exists
                if(isset($entries['image'])){
                $img = formatImage($entries['image'], $entries['entry_title']);
                }
                else
                {
                    $img = NULL;
                }

                if($page == 'blog')
                {
                    //Load the comment object
                    include_once 'inc/comment.inc.php';
                    $comments = new Comments();
                    $comment_disp = $comments->showComments($entries['entry_id']);
                    $comment_form = $comments->showCommentForm($entries['entry_id']);
                }
                else
                {
                    $comment_form = NULL;
                }
             ?>
                <h2 align="center"><?php echo $entries['entry_title']?></h2>
                <p><?php echo $img, $entries['entry_text'] ?></p>
                <p>
                    <?php echo $admin['edit'] ?>
                    <?php if($page=='blog') echo $admin['delete'] ?>
                </p>
                <?php if($page=='blog'): ?>
                    <p class="backlink">
                        <a href="./">Back to Latest Entries</a>
                    </p>
                <h3> Comments for This Entry</h3>

                <?php echo $comment_disp, $comment_form; endif; ?>
        <?php
            } //End of if statement
            //If the full display flag is 0, format linked entry titles
            else
            {
                //loop through each entry
                foreach($entries as $entry)
                {

                    ?>
            <p>
                <a href="/internship_blog/<?php echo $entry['page'] ?>/<?php echo $entry['url'] ?>" >
                    <?php echo $entry['entry_title'] ?>
                </a>
            </p>

        <?php
                }// End of the foreach loop
            }//end of the else
        ?>

        <p class="backlink">
            <?php if($page=='blog'
                    && isset($_SESSION['loggedin'])
                    && $_SESSION['loggedin'] == 1): ?>
                <a href="/internship_blog/admin/<?php echo $page ?>">
                    Post a new Entry
                </a>
            <?php endif; ?>
        </p>
        <p>
            <a href="/internship_blog/feeds/rss.php">
                Subscribe via RSS!
                </a>
        </p>

    </div>

</body>

</html>
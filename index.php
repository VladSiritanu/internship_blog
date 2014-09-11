<?php

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
    <link rel="stylesheet" href="css/default.css" type="text/css"/>
    <title>Blog</title>
</head>

<body>
    <h1>Simple Blog Application</h1>

    <ul id="menu">
        <li><a href="/internship_blog/blog/">Blog</a> </li>
        <li><a href="/internship_blog/about/">About</a> </li>
    </ul>

    <div id="entries">
        <?php
            //If the full display flag is set, show the entry
            if($fulldisp==1)
            {
                //get the URL if one wasn't passed
                $url = (isset($url)) ? $url : $entries['url'];
             ?>
                <h2><?php echo $entries['entry_title']?></h2>
                <p><?php echo $entries['entry_text'] ?></p>
                <?php if($page=='blog'): ?>
                    <p class="backlink">
                        <a href="./">Back to Latest Entries</a>
                    </p>
                <?php endif; ?>
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
            <?php if($page=='blog'): ?>
                <a href="/internship_blog/admin/<?php echo $page ?>">
                    Post a new Entry
                </a>
            <?php endif; ?>
        </p>

    </div>

</body>

</html>
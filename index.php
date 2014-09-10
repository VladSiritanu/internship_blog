<?php

    //Include the necessary files
    include_once 'inc/function.inc.php';
    include_once 'inc/db.inc.php';

    //Open a database connection
    $db =  new PDO(DB_INFO,DB_USER,DB_PASS);

    //Detecmine if an entry ID was passed in the URL
    $id = (isset($_GET['id'])) ? (int) $_GET['id'] : NULL;

    //Load the entries
    $entries = retrieveEntries($db,$id);

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

    <div id="entries">
        <?php
            //If the full display flag is set, show the entry
            if($fulldisp==1)
            {
             ?>
                <h2><?php echo $entries['entry_title']?></h2>
                <p><?php echo $entries['entry_text'] ?></p>
                <p class="backlink">
                    <a href="./">Back to Latest Entries</a>
                </p>
        <?php
            } //End of if statement
            //If the full display flag is 0, format linked entry titles
            else
            {
                //loop through each entry
                foreach($entries as $entries)
                {
              ?>
            <p>
                <a href="?id=<?php echo $entries['id'] ?>" >
                    <?php echo $entries['title'] ?>
                </a>


            </p>

        <?php
                }// End of the foreach loop
            }//end of the else
        ?>

        <p class="backlink">
            <a href="admin.php">Post a new Entry</a>
        </p>

    </div>

</body>

</html>
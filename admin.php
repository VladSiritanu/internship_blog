<?php
if(isset($_GET['page']))
{
    $page = htmlentities(strip_tags($_GET['page']));
}
else
{
    $page = 'blog';
}
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

    <form action="../inc/update.inc.php" method="post">
        <fieldset>
            <legend>New Entry Submission</legend>
            <label>Title
                <input type="text" name="title" maxlength="150"/>
            </label>
            <label>Entry
                <textarea name="entry" rows="10" cols="45"></textarea>
            </label>
            <input type="hidden" name="page" value="<?php echo $page ?>" />
            <input type="submit" name="submit" value="Save Entry"/>
            <input type="submit" name="submit" value="Cancel"/>
        </fieldset>
    </form>
</body>

</html>


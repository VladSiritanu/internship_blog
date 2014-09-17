<?php

  session_start();

  // If the user is logged in, we can continue.
  if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1) :

    // Include the necessaty files.
    include_once 'inc/function.inc.php';
    include_once 'inc/db.inc.php';

    // Open a database connection.
    $db = new PDO(DB_INFO,DB_USER,DB_PASS);

    if(isset($_GET['page'])) {

      $page = htmlentities(strip_tags($_GET['page']));
    }
    else {

      $page = 'blog';
    }

    if (isset($_POST['action']) && $_POST['action'] == 'delete') {

      if ($_POST['submit'] == "Yes") {

        $url = htmlentities(strip_tags($_POST['url']));
        if (deleteEntry($db,$url)) {

          header("Location: /internship_blog/");
          exit;
        }
        else {

          exit("Error deleting the entry!");
        }
      }
      else {

        $url = htmlentities(strip_tags($_POST['url']));
        header("Location: /internship_blog/blog/$url");
        exit;
      }
    }

    if (isset($_GET['url'])) {

      // Do basic sanitization of the url variable.
      $url = htmlentities(strip_tags($_GET['url']));

      // Check if the comment should be deleted.
      if($page == 'delete') {

        $confirm = confirmDelete($db,$url);
      }

      // Set the legend of the form.
      $legend = "Edit This Entry";

      // Load the entry to be edited.
      $entry = retrieveEntries($db,$page,$url);

      // Save each entry field as individual variables.
      $id = $entry['entry_id'];
      $title = $entry['entry_title'];
      $entry_txt = $entry['entry_text'];
      $img = $entry['image'];
    }
    else {

      // Check if we're creating a new user.
      if ($page == 'create_user') {

        $create = createUserForm();
      }

      // Set the legend.
      $legend = "New Entry Submission";

      // Set variables to NULL is not editing.
      $id = NULL;
      $title = NULL;
      $entry_txt = NULL;
    }
    ?>

    <!DOCTYPE html
        PUBLIC "-//W3C//DTD XHTML 1.0  Strict//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
      <meta http-equiv="Content-Type"
        content="text/html;charset=utf-8"/>
      <link rel="stylesheet" href="/internship_blog/css/default.css"
            type="text/css"/>

      <title>Blog</title>
    </head>

    <body>

      <h1>Simple Blog Application</h1>

      <?php

        if ($page == 'delete') : {

          echo $confirm;
        }
        elseif ($page == 'create_user') : {

          echo $create;
        }
        else :

          ?>
          <form action="/internship_blog/inc/update.inc.php" method="post"
                enctype="multipart/form-data">
            <fieldset>
              <legend><?php echo $legend ?></legend>
              <label>Title
                <input type="text" name="title" maxlength="150"
                       value="<?php echo htmlentities($title) ?>"/>
              </label>
              <label>Image
                <input type="file" name="image" value="<?php echo $img ?>" />
              </label>
              <label>Entry
                <textarea name="entry" rows="10" cols="45">
                  <?php echo strip_tags($entry_txt,"<a>") ?>
                </textarea>
              </label>
              <input type="hidden" name="img" value="<?php echo $img?>"/>
              <input type="hidden" name="id" value="<?php echo $id ?>"/>
              <input type="hidden" name="page" value="<?php echo $page ?>" />
              <input type="submit" name="submit" value="Save Entry"/>
              <input type="submit" name="submit" value="Cancel"/>
            </fieldset>
          </form>
      <?php
        endif;
      ?>
    </body>
  </html>
<?php
  /*
   * If we get here, the user is not logged in. Display a form
   * and ask them to log in.
   */
   else:
     $page = NULL;
     if(isset($_GET['page'])){

       $page = htmlentities(strip_tags($_GET['page']));
     }
     if($page == 'register'){
       include_once 'inc/function.inc.php';
       $errors = array(
           1 => '<p class="error">
                  Password and confirm password do not match.
                  Please try again!
                  </p>'

       );

       if (isset($_SESSION['error'])) {

         $error = $errors[$_SESSION['error']];
       }
       else {

         $error = NULL;
       }
       $register = createRegisterForm($error);
       echo $register;
       exit;
     }

     $errors = array(
         1 => '<p class="error">
                  Password and username wrong.
                  Please try again!
                  </p>'

     );

     if (isset($_SESSION['error'])) {


       $error = $errors[$_SESSION['error']];
     }
     else {

       $error = NULL;
     }
     ?>
     <!DOCTYPE html
          PUBLIC "-//W3C//DTD XHTML 1.0  Strict//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
     <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

     <head>

       <meta http-equiv="Content-Type"
             content="text/html;charset=utf-8"/>

       <link rel="stylesheet" href="/internship_blog/css/default.css"
             type="text/css"/>

       <title>Please Log In!</title>
     </head>

     <body>

      <form action="/internship_blog/inc/update.inc.php"
            method="post"
            enctype="multipart/form-data">
        <fieldset>
          <legend>Please Log In To Continue</legend><?php echo $error ?>
          <label>Username
            <input type="text" name="username" maxlength="75" />
          </label>
          <label>Password
            <input type="password" name="password" maxlength="150"/>
          </label>
          <input type="hidden" name="action" value="login" />
          <input type="submit" name="submit" value="Log In" />
          <input type="submit" name="submit" value="Cancel"/>
        </fieldset>
      </form>
     </body>
     </html>
<?php
  endif;
?>

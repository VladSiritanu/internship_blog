<?php

  session_start();

  $page = null;
  if (isset($_GET['page'])) {

    $page = htmlentities(strip_tags($_GET['page']));

  }
  include_once 'db.inc.php';
  $db = new PDO(DB_INFO,DB_USER,DB_PASS);
  $sql = "SELECT  *
          FROM admin
          WHERE username=?
          AND password=SHA1(?)";
  $stmt = $db->prepare($sql);
  $stmt->execute(array(
      $_SESSION['username'],
      $_SESSION['pass']

  ));
  $response = $stmt->fetch();
  $fname = $response['first_name'];
  $surname = $response['surname'];
  $email = $response['email'];

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

  <title>Account!</title>
</head>

<body>
<h1 >Account settings</h1>

<ul id="menu">
  <li><a href="/internship_blog/inc/account.inc.php">
      User Information
    </a>
  </li>
  <li><a href="/internship_blog/inc/account.inc.php?page=ch_pass">
      Change Password
    </a>
  </li>
</ul>
<?php  if ($page == NULL):?>

<div id="entries">
  <form action="/internship_blog/inc/update.inc.php"
        method="post"
        enctype="multipart/form-data">
    <fieldset>
      <legend>User Information</legend>
      <label>Username
        <input type="text" name="username" maxlength="75"
               value="<?php echo $_SESSION['username'] ?>" />
      </label>
      <label>First Name
        <input type="text" name="f_name" maxlength="150"
               value="<?php echo $fname ?>">
      </label>
      <label>Surname
        <input type="text" name="surname" maxlength="150"
             value="<?php echo $surname ?>">
      </label>
      <label>Email
        <input type="text" name="email" maxlength="150"
               value="<?php echo $email ?>">
      </label>
      <input type="hidden" name="action" value="acc_update" />
      <input type="submit" name="submit" value="Submit" />
      <input type="submit" name="submit" value="Cancel"/>
    </fieldset>
  </form>
  <?php elseif($page == 'ch_pass'): ?>
  <p id="control_panel">
    Here u can change your password!
  </p>
</div>
<?php endif; ?>
</body>
</html>
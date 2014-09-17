<?php

  function retrieveEntries ($db,$page, $url=NULL) {

    // If an entry URL was supplied, load the associated entry.
    if (isset($url)) {

      $sql = "SELECT entry_id,page,entry_title,image,entry_text,entry_date
                FROM entries
                WHERE url=?
                LIMIT 1";


      $stmt = $db->prepare($sql);
      $stmt->execute(array($url));

      // Save the returned entry array.
      $result = $stmt->fetch();

      // Set the fulldisp flag for a single entry.
      $fulldisp = 1;
    }
    // If no entry URL was supplied, load all entry titles for the page.
    else {

      // Query text.
      $sql = "SELECT entry_id,page,entry_title,entry_text,url,entry_date
              FROM entries
              WHERE page=?
              ORDER BY entry_date DESC";
      $stmt = $db->prepare($sql);
      $stmt->execute(array($page));
      $result = NULL;

      // Loop through returned results and store as an array.
      while ($row = $stmt->fetch()) {

        if ($page=='blog') {

          $result[] = $row;
          $fulldisp = 0;
        }
        else {

          $result = $row;
          $fulldisp = 1;
        }
      }


      // If no entries were returned, display a default message and set fulldisp
      // flag to display a single entry.
      if (!is_array($result)) {

        $fulldisp = 1;
        $result= array(
          'entry_title' => 'No Entries Yet',
          'entry_text' => '<a href="/internship_blog/admin.php?page=' . $page
              . '">Post an entry!</a>'
        );
      }
    }

    // Add the $fulldisp flag to the end of the array.
    array_push($result,$fulldisp);

    return $result;
  }

  function sanitizeData ($data) {

    // If $data is not an array, tun strip_tags().
    if (!is_array($data)) {

      // Remove all tags except <a> tags.

      return strip_tags($data,"<a><br>");
    }
    // If $data is an array, process each element.
    else {

      // Call sanitizeData recursively fot each array element.
      return array_map('sanitizeData',$data);
    }
  }

  function makeURL ($title,$id) {

    $patterns = array(
      '/\s+/',
      '/(?!-)\W+/'
    );
    $replacements = array('-','');

    return preg_replace($patterns,$replacements,strtolower($title)) . $id;
  }

  function adminlinks ($page, $url) {

    // Format the link to be followed for each option.
    $editURL = "/internship_blog/admin/$page/$url";
    $deleteURL = "/internship_blog/admin/delete/$url";

    // Make a hyperlink and add it to an array.
    $admin['edit'] = "<a href=\"$editURL\">edit</a>";
    $admin['delete'] = "<a href=\"$deleteURL\">delete</a>";

    return $admin;
  }

  function confirmDElete ($db, $url) {

    $entry = retrieveEntries($db, '',$url);

    return <<<FORM
<form action="/internship_blog/admin.php" method="post">
    <fieldset>
        <legend>Are You Sure?</legend>
        <p>Are you sure you want to delete the entry "$entry[entry_title]"?</p>
        <input type="submit" name="submit" value="Yes" />
        <input type="submit" name="submit" value="No" />
        <input type="hidden" name="action" value="delete" />
        <input type="hidden" name="url" value="$url" />
    </fieldset>
</form>
FORM;
  }

  function deleteEntry($db, $url) {

    $sql = "DELETE FROM entries
            WHERE url=?
            LIMIT 1";
    $stmt = $db->prepare($sql);

    return $stmt->execute(array($url));
  }

  function formatImage($img=NULL, $alt=NULL) {

    if (!empty($img)) {

      return '<img src="' . $img . '" alt="' . $alt .'" />';
    }
    else {

      return NULL;
    }
  }

  function createUserForm () {

    return <<<FORM
<form action="/internship_blog/inc/update.inc.php" method="post">
    <fieldset>
        <legend>Create a New Administrator</legend>
        <label>Username
            <input type="text" name="username" maxlength="75" />
        </label>
        <label>Password
            <input type="password" name="password" />
        </label>
        <input type="submit" name="submit" value="Create" />
        <input type="submit" name="submit" value="Cancel" />
        <input type="hidden" name="action" value="create_user" />
    </fieldset>
</form>
FORM;
  }

  function createRegisterForm ($error){

    return <<<FORM
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
  <form action="/internship_blog/inc/update.inc.php" method="post">
    <fieldset>
      <legend>Register</legend>$error
      <label>Username*
        <input type="text" name="username" maxlength="75">
      </label>
      <label>First Name
        <input type="text" name="f_name" maxlength="150">
      </label>
      <label>Surname
        <input type="text" name="surname" maxlength="150">
      </label>
      <label>Email
        <input type="text" name="email" maxlength="150">
      </label>
      <label>Password*
        <input type="password" name="password" >
      </label>
      <label>Confirm password*
        <input type="password" name="confirm_password">
      </label>
      <input type="submit" name="submit" value="Create" />
      <input type="submit" name="submit" value="Cancel" />
      <input type="hidden" name="action" value="register" />
    </fieldset>
  </form>
</body>
</html>
FORM;
  }
?>





<?php

function retrieveEntries($db,$page, $url=NULL)
{
    //If an entry URL was supplied, load the associated entry
    if(isset($url))
    {
       $sql = "SELECT entry_id,page,entry_title,image,entry_text
                FROM entries
                WHERE url=?
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute(array($url));

        //Save the returned entry array
        $result = $stmt->fetch();

        //Set the fulldisp flag for a single entry
        $fulldisp = 1;

    }

    //If no entry URL was supplied, load all entry titles for the page
    else
    {
        // Query text
        $sql = "SELECT entry_id,page,entry_title,entry_text,url
                FROM entries
                WHERE page=?
                ORDER BY entry_date DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($page));
        $result = NULL;
        // Loop through returned results and store as an array
       while($row = $stmt->fetch())
       {
           if($page=='blog')
           {
           $result[] = $row;
           $fulldisp = 0;
           }
           else
           {
               $result = $row;
               $fulldisp = 1;
           }
       }


        //IF no entries were returned, display a default
        //messafe and set fulldisp flag to display a
        //single entry
        if(!is_array($result))
        {
            $fulldisp = 1;
            $result= array(
                        'entry_title' => 'No Entries Yet',
                        'entry_text' => '<a href="/internship_blog/admin.php?page=' . $page . '">Post an entry!</a>'
            );
        }

    }

    //Add the $fulldisp flag to the end of the array
    array_push($result,$fulldisp);
    return $result;

}

function sanitizeData($data)
{
    //If $data is not an array, tun strip_tags()
    if(!is_array($data))
    {
        //Remove all tags except <a> tags
        return strip_tags($data,"<a><br>");
    }
    //If $data is an array, process each element
    else
    {
        //Call sanitizeData recursively fot each array element
        return array_map('sanitizeData',$data);
    }
}

function makeURL($title)
{
    $patterns = array(
        '/\s+/',
        '/(?!-)\W+/'
    );
    $replacements = array('-','');
    return preg_replace($patterns,$replacements,strtolower($title));

}

function adminlinks($page, $url)
{
    //Format the link to be followed for each option
    $editURL = "/internship_blog/admin/$page/$url";
    $deleteURL = "/internship_blog/admin/delete/$url";

    //Make a hyperlink and add it to an array
    $admin['edit'] = "<a href=\"$editURL\">edit</a>";
    $admin['delete'] = "<a href=\"$deleteURL\">delete</a>";

    return $admin;
}

function confirmDElete($db, $url)
{
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

function deleteEntry($db, $url)
{
    $sql = "DELETE FROM entries
            WHERE url=?
            LIMIT 1";
    $stmt = $db->prepare($sql);
    return $stmt->execute(array($url));
}

function formatImage($img=NULL, $alt=NULL)
{
    if(isset($img) and $img != "")
    {
        return '<img src="' . $img . '" alt="' . $alt .'" />';
    }
    else
    {
        return NULL;
    }
}

?>
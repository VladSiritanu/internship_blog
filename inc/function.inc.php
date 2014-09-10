<?php

function retrieveEntries($db, $id=NULL)
{
    //If an entry ID was supplied, load the associated
    //entry
    if(isset($id))
    {
       $sql = "SELECT entry_title,entry_text
                FROM entries
                WHERE entry_id=?
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($_GET['id']));

        //Save the returned entry array
        $result = $stmt->fetch();

        //Set the fulldisp flag for a single entry
        $fulldisp = 1;
    }

    //If no entry id was supplied, load all entry titles
    else
    {
        // Query text
        $sql = "SELECT entry_id,entry_title
                FROM entries
                ORDER BY entry_date DESC";
        // Loop through returned results and store as an array
        foreach($db->query($sql) as $row)
        {
            $result[] = array(
                            'id' => $row['entry_id'],
                            'title' => $row['entry_title']
            );
        }
        //Set the fulldisp falg for multiple entries
        $fulldisp = 0;

        //IF no entries were returned, display a default
        //messafe and set fulldisp flag to display a
        //single entry
        if(!is_array($result))
        {
            $fulldisp = 1;
            $result= array(
                        'title' => 'No Entries Yet',
                        'entry' => '<a href="/admin.php">Post an entrhy!</a>'
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
        return strip_tags($data,"<a>");
    }
    //If $data is an array, process each element
    else
    {
        //Call sanitizeData recursively fot each array element
        return array_map('sanitizeData',$data);
    }
}

?>
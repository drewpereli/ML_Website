<?php
    require_once('../sanitize.php');
    require_once('../connectToDB.php');
    require_once('../authenticate.php');
    require_once('../requiresLogIn.php');


    if (!empty($_POST['action'])) 
    {       
        $action = $_POST['action'];
        if ($action === "add")
        {
            $tagName = $_POST['tagName'];
            $query = "SELECT tag from tags";
            $results = $db->query($query)->FetchAll(PDO::FETCH_ASSOC);
            $tagAlreadyExists = false;
            foreach($results as $result)
            {
                $existingName = $result['tag'];
                
                if (str_replace('_', ' ', ($existingName)) == str_replace('_', ' ', ($tagName)))
                {
                    $message = "The tag $tagName already exists dumbass. See that list at the botom? Check it next time ffs.";
                    $tagAlreadyExists = true;
                }
            }
            if ($tagAlreadyExists === false)
            {
                $tagName = cleanInput($tagName);
                $tagName = str_replace(' ', '_', $tagName);
                $query = "INSERT INTO tags (tag) value ('$tagName')";
                $db->exec($query);
                $tagName = str_replace('_', ' ', $tagName);
                $message = "Succesfully added the tag '$tagName'!";
                echo "success";
            }
        }
        else if ($action === "remove")
        {
            $tagsToRemove = $_POST['tagNames'];
            $query = "DELETE FROM tags WHERE";
            foreach ($tagsToRemove as $tagName)
            {
                $query .= " tag = '$tagName' OR";
            }
            $query = trim($query, "OR");
            $stm = $db->prepare($query);
            $stm->execute();
        }
        else if ($action === "edit")
        {
            $tagToChange = $_POST['tagName'];
            $newName = $_POST['newName'];
            $query =   "UPDATE tags
                        SET tag='$newName'   
                        WHERE tag='$tagToChange'";
            $stm = $db->prepare($query);
            try{
                $stm->execute();
                echo "success";
            }
            catch (Exception $e)
            {
                echo ($e->getCode());
            }
        }
    }
    

?>
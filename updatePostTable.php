<?php
    require_once("connectToDB.php");

    $query = "SELECT * FROM tempPosts";
    $results = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    foreach($results as $result)
    {
        
    }
?>
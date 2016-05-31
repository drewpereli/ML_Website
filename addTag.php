<?php
    require_once('sanitize.php');
    require_once('connectToDB.php');
    require_once('functions.php');
    require_once('authenticate.php');
    require_once('requiresLogIn.php');
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css" />
    <script src="jQuery.js"></script>
</head>
<body>
    <h1>Add a Tag</h1>
    <hr/>
    <div id="navBar">
        <a href="index.php">Home</a>
        <a href="addPost.php">Add a Post</a>
    </div>
    <hr/>
    <div id="message">
        
    </div>
    <form action="controllers/addTag.php" method="POST" id="addTagForm">
        <label for="tagName">Tag Name: </label>
        <input type="text" name="tagName" /><br/>
        <input type="submit" name="submit" value="Submit Tag" />
        <input type="hidden" name="cameFrom" value="../addTag.php" />
    </form>
    <div id="existingTagsContainer">
        <h3>Existing Tags</h3>
        <div id="existingTagsList">
            <ul>
                <?php
                    $query = "SELECT tag FROM tags ORDER BY tag";
                    $results = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
                    foreach($results as $result)
                    {
                        $tagName = replaceUnderscores($result['tag']);
                        ?>
                        <script>
                        var tagName = "<?=$tagName;?>";
                        var li = document.createElement('li');
                        li.innerHTML = tagName;
                        $("#existingTagsList").append(li);
                        </script>
                        <?php
                    }
                ?>
            </ul>
        </div>
    </div>
</body>
</html>
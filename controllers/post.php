<?php

	require_once("../authenticate.php");
	
	if ($_POST['action'] === 'add' || $_POST['action'] === 'edit')
	{	
		$author = $_POST['author'];
		$comment_count = $_POST['comment_count'];
		$id = $_POST['id'];
		$score = $_POST['score'];
		$title = $_POST['title'];
		$time_created = $_POST['created_utc'];
		$current_time = time();

		if ($_POST['action'] === 'edit')
		{
			$q = "DELETE FROM posts WHERE id='$id'";
			$statement = $db->prepare($q);
			$statement->execute();
		}

		$query = "INSERT INTO posts (id, author, title, score, comments, created_utc, updated_utc) 
					values (?,?,?,?,?,?, UNIX_TIMESTAMP())";

		$statement = $db->prepare($query);
		$statement->execute(array($id, $author, $title, $score, $comment_count, $time_created));
		
		//Add the tags
	 	$postTagsQuery = "INSERT INTO post_tags (post, tag) values";

	 	foreach($_POST['tags'] as $tagName=>$val)
	 	{
	 		$postTagsQuery .= " ('$id', '$tagName'),";
	 	}
	 	$postTagsQuery = trim($postTagsQuery, ",");
	 	$statement = $db->prepare($postTagsQuery);
	 	$statement->execute();
	 	echo "success";
	 }
	 else if ($_POST['action'] === 'remove')
	 {
	 	$ids = $_POST['ids'];
	 	$query = "DELETE FROM posts WHERE";
	 	foreach ($ids as $id)
	 	{
	 		$query .= " id='$id' OR";
	 	}
	 	$query = trim($query, " OR");
	 	$stm = $db->prepare($query);
	 	$stm->execute();
	 	echo "success";
	 }
// ?>
<?php
	$q = 'SELECT 
            id,
            author,
            title,
            score,
            comments,
            UNIX_TIMESTAMP() - created_utc age,
            UNIX_TIMESTAMP() - updated_utc outdated
          FROM posts';

    $posts = $db->prepare($q);
    $posts->execute(array());
?>

<style>
	
</style>

	  
<?php


	function displayPosts($args)
	{
		global $db;
		$posts = $args['posts'];
		if (isset($args['narrow']))
			$narrow = true;
		else
			$narrow = false;

		?>
		  <div class="row">
		    <span class="col-md-2"></span>
		    <span class="col-md-8">
		    <?php while($post = $posts->fetch(PDO::FETCH_ASSOC)) { ?>
		      <?php include('post.php'); ?>
		    <?php } ?>
		    </span>
		    <span class="col-md-2"></span>
		  </div>
		<?php
	}
	?>
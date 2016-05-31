

<?php
	require_once("authenticate.php");
    require_once("requiresLogIn.php");
    require_once('connectToDB.php');
    require_once("utils.php");
    require_once("functions.php");
    require_once("displayPosts.php")
?>

<html>
<head>
    <script src="jQuery.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link href="style/display.css" type="text/css" rel="stylesheet" />
    <style>
    body {
        padding-top: 70px;
        padding-bottom: 20px;
        overflow-x: hidden;
    }
    .btn.tag{
      margin: 2px;
      padding: 0 4px;
    }
    .container.flex{
        display: flex;
        flex-flow: row wrap;
        justify-content: flex-start;
    }
    .container.flex .tag-cont{
        flex-basis:20%;
        text-align: center;
    }
    .left-of-post{
    	margin-top: 40px;
    }
    #confirm, #success{
    	padding: 10px;
    }
    #success {
    	margin-top: 14px;
    }
    #goBackButton{
    	margin: 10px;
    }
    #message{
        padding:25px;
        min-height: 50px;
    }
    </style>

    <script>
    	var selectedTags = [];
    	var selectedPost = {};
    </script>
</head>
<body>


<?php include("navBar.php");?>
<br/>
<div class="row">
	<?php
		if (isset($_POST['post']))
		{
			?>
				<a href="<?=$_SERVER['PHP_SELF'];?>" id="goBackButton" class="col-xs-2">
					<button type="button" class="btn-default btn-lg"><span class="glyphicon glyphicon-triangle-left"></span> Go Back
					</button>
				</a>
				<div class="col-xs-6 col-xs-offset-1 bg-success text-center invisible" id="message">
					<span class="message-container">Succesfully edited the posts.</span>
				</div>
			<?php
		}
	?>

	
</div>
<br/>

<div class="container-fluid">

	<?php $header = isset($_POST['post']) ? "Select new tags for this post" : "Select a post to edit";?>

	<h2 class="text-center"><?=$header;?></h2>
	<br/>
	<?php
		if (isset($_POST['post']))
		{
			?>
			<button type="button" class="col-xs-8 col-xs-offset-2 btn btn-default btn-lg text-center" id="edit-post-button">
			Edit Post
			</button>
			<?php
		}
	?>

</div>

<br/>
<br/>

<div class="container-fluid">
	
<?php 

	if (isset($_POST['post']))
	{
		$id = $_POST['post'];
		$q = "SELECT 
            id,
            author,
            title,
            score,
            comments,
            UNIX_TIMESTAMP() - created_utc age,
            UNIX_TIMESTAMP() - updated_utc outdated
          	FROM posts
          	WHERE id='$id'";
		$posts = $db->prepare($q);
		$posts->execute();
		$args = array("posts"=>$posts);

	}
	else
	{
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
		$posts->execute();
		$args = array("posts"=>$posts, "narrow"=>true);
	}
	
	displayPosts($args);

	if (isset($_POST["post"]))
	{
		$id = $_POST['post'];
		?><script>var postId = "<?=$id;?>";</script><?php

		$q = "SELECT tag AS name
				FROM tags
				WHERE tag <> 'Self_Post'
				AND tag <> 'Link_Post'";
		$tags = $db->prepare($q);
		$tags->execute();

		$q = "SELECT tag AS name
				FROM post_tags
				WHERE post='$id'";
		$selectedTags = $db->prepare($q);
		$selectedTags->execute();
		$selectedTags = $selectedTags->fetchAll(PDO::FETCH_ASSOC);
		foreach($selectedTags as $selectedTag)
		{
			$selectedName = $selectedTag['name'];
			?>
				<script>selectedTags.push("<?=$selectedName?>");</script>
			<?php
		}

		?>
		<div class="row" id="tag-container">
			<?php
			while ($tag = $tags->fetch(PDO::FETCH_ASSOC))
			{
				$name = $tag['name'];
				?>
				<span class="btn-container col-md-3 col-xs-12">
					<span class="btn btn-default tag tag-cont col-xs-12" data-tag="<?=$name;?>">
						<?=replaceUnderscores($name);?>
					</span>
				</span>
				<?php
			}	
			?>
		</div>
		<?php
	}

?>

</div>

</body>

<?php
if (!isset($_POST['post']))
{
	?>
	<script>
		$(".left-of-post").each(function(index, element){

			var id = $(element).attr("data-tag");
			$(document.createElement('button')).attr("type", "button")
				.html("Edit this post")
				.addClass("btn btn-default edit-button")
				.attr("data-tag", id)
				.appendTo(element);
		});
	</script>
	<?php
}
else
{
	?>
	<script>
		$(".tag").each(function(index, element){
			var dataTag = $(element).attr("data-tag");
			if (selectedTags.indexOf(dataTag) !== -1)
			{
				$(this).toggleClass('btn-primary btn-success selected');
			}
		});
	</script>
	<?php
}
?>



<script>

$(".edit-button").on("click", function(){
	var id = $(this).attr("data-tag");
	var form = $(document.createElement("form")).attr("action", "")
		.attr("method", "POST")
		.addClass("hidden")
		.appendTo("body");

	$(document.createElement("input")).attr("type", "hidden")
		.attr("name", "post").val(id).appendTo(form);

	form.submit();
});


$("#edit-post-button").on("click", function(){
	var jsonURL = "https://www.reddit.com/" + postId + ".json?jsonp=?";

	$.getJSON(jsonURL)
            .done(function(results)
            {
            	var postData = results[0].data.children[0].data;
            	var postDataArray = {
	                        submit: true,
	                        action: "edit",
	                        is_self: postData.is_self,
	                        title: postData.title,
	                        author: postData.author,
	                        score: postData.score,
	                        comment_count: postData.num_comments,
	                        id: postData.id,
	                        created_utc: postData.created_utc,
	                        tags: {}
	                    };

	            //Add the entered form data as well
                $(".selected").each(function(index, element){

                    if ($(element).hasClass("selected"))
                    {
                        var name = $(element).attr('data-tag');
                        postDataArray.tags[name] = true;
                    }
                });

                var typeTag = postData.is_self == true ? "Self_Post" : "Link_Post";
                postDataArray.tags[typeTag] = true;

                postDataArray['URL'] = "https://www.reddit.com/" + postId;

                console.log(postDataArray);

                $.ajax({
                        type: 'POST',
                        url: "controllers/post.php",
                        data: postDataArray,
                        success: function(result){
                            displayMessage();
                        }
                    });

            });
	
});


    function displayMessage(){

        $("#message").removeClass("invisible");
        $("#message").delay(1500).fadeOut(1000, function(){
            window.location = "editPost.php";
        });
    }



$(function(){
      $('.tag').on('click', function(){
        $(this).toggleClass('btn-primary btn-success selected');
      })
    })
</script>
</html>




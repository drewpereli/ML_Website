<?php
	require_once("authenticate.php");
    require_once("requiresLogIn.php");
    require_once('connectToDB.php');
    require_once("utils.php");
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
    </style>
</head>
<body>


<?php include("navBar.php");?>
<br/>
<br/>
<br/>

<div class="container-fluid">
	<div class="row">
			<span class="col-xs-8 col-xs-offset-2 btn btn-default btn-lg text-center" id="remove-post-button">Remove Selected Posts</span>
		</div>

		<div class="row" id="message-container">
			<br/>
			<div class="col-xs-8 col-xs-offset-2 bg-warning invisible" id="confirm">
				<span class="col-xs-5 text-right">Are you sure you'd like to remove these posts?</span>
				<span class="btn-container col-xs-2">
					<span class="btn btn-default btn-sm col-xs-12" id="confirm-remove-button">Yes</span>
				</span>
				<span class="btn-container col-xs-2">
					<span class="btn btn-default btn-sm col-xs-12" id="cancel-remove-button">No</span>
				</span>
			</div>
			<div class="col-xs-8 col-xs-offset-2 bg-success text-center hidden" id="success" style="display: default">
				<span class="message-container">Succesfully removed the posts.</span>
			</div>
		</div>
		<br/>
		<br/>
<?php 

	$args = array("posts"=>$posts, "narrow"=>true);
	displayPosts($args);
?>
</div>

</body>

<script>
$(".left-of-post").each(function(index, element){

	var id = $(element).attr("data-tag");
	$(document.createElement('button')).attr("type", "button")
		.html("Select for removal")
		.addClass("btn btn-default removal-button")
		.attr("data-tag", id)
		.appendTo(element);
});






$("#confirm-remove-button").on("click", function(){
	var removeIds = [];
	$(".selected").each(function(index, element){
		var removeId = $(element).attr("data-tag");
		removeIds.push(removeId);
		$("#" + removeId).parent().addClass("remove"); //Slate the post container for removal from the dom
	}); 

	var json = {
		action: "remove",
		ids: removeIds
	};


	
	$.ajax({
		type: "POST",
		url: "controllers/post.php",
		data: json
	})
	.done(function(result){
		$("#confirm").addClass("hidden");
		$("#success").removeClass("hidden");
		$(".remove").fadeOut(700);
		$("#success").delay(1000).fadeOut(1000, function(){
			$("#success").addClass("hidden");
			$("#success").attr("style", "display: default");
			$("#success").css("opacity", "1");
			$("#confirm").addClass("invisible");
			$("#confirm").removeClass("hidden");
		});
	});
	
});


$("#cancel-remove-button").on("click", function(){
    	$("#confirm").addClass("invisible");
    });


$('#remove-post-button').on('click', function(){
        $("#confirm").removeClass("invisible");
    });


$(".removal-button").on("click", function(){
	$(this).toggleClass("selected btn-primary btn-success");
});
</script>
</html>




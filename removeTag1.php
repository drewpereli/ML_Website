<?php
    require_once("authenticate.php");
    require_once("requiresLogIn.php");
    require_once('connectToDB.php');
    require_once("utils.php");
?>

<html>
<head>
	<script src="jQuery.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<style>
	.logout{
      position: relative;
    }
    .logout:hover{
      color: transparent;
    }
    .logout:hover:before{
      color: white;
      content: "Logout";
      position: absolute;
      width: 100%;
      left: 0;
      text-decoration: underline; 
    }
    body {
        padding-top: 70px;
        padding-bottom: 20px;
    }
    .panel-heading{
      font-weight: bold;
    }
    .btn-container{
      margin: 0px;
      padding: 2px;
      padding-left: 10px;
      padding-right: 10px;
    }
    .container.flex{
        display: flex;
        flex-flow: row wrap;
        justify-content: flex-start;
    }
    .container.flex .tag-cont{
        flex-basis:20%;
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
	<nav class="navbar navbar-inverse navbar-fixed-top">
	  <div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      <a class="navbar-brand" href="https://www.reddit.com/r/MensLib/" target=_blank>/r/MensLib</a>
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <ul class="nav navbar-nav">
	        <li><a href="/menslib/">Home</a></li>
	        <li><a href="addPost.php">Add posts and tags</a></li>
	        <li class="active"><a href="removeTag.php">Remove tags</a></li>
	        <!--<li><a href="addTag.php">Add tags</a></li>-->
	      </ul>
	      <ul class="nav navbar-nav navbar-right">
	        <li>
	          <form class="navbar-form navbar-left" method=POST>
	            <input type="hidden" class="form-control" name=logout>
	            <button type="submit" class="btn btn-primary logout"><?=$auth['username']?></button>
	          </form>
	        </li>
	      </ul>
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>


	<!--Meat of the thing-->
	<div class="container-fluid">
		<div class="row">
			<span class="col-xs-8 col-xs-offset-2 btn btn-default btn-lg text-center" id="remove-tag-button">Remove Selected Tags</span>
		</div>

		<div class="row" id="message-container">
			<br/>
			<div class="col-xs-8 col-xs-offset-2 bg-warning invisible" id="confirm">
				<span class="col-xs-5 text-right">Are you sure you'd like to remove these tags?</span>
				<span class="btn-container col-xs-2">
					<span class="btn btn-default btn-sm col-xs-12" id="confirm-remove-button">Yes</span>
				</span>
				<span class="btn-container col-xs-2">
					<span class="btn btn-default btn-sm col-xs-12" id="cancel-remove-button">No</span>
				</span>
			</div>
			<div class="col-xs-8 col-xs-offset-2 bg-success text-center hidden" id="success" style="display: default">
				<span class="message-container">Succesfully removed the tags.</span>
			</div>
		</div>

		<br/><br/>

		<script>var permTagNames = [];</script>
		<div class="row" id="tag-container">
            <?php
                $q = "SELECT tag from tags order by tag";
                $tags = $db->prepare($q);
                $tags->execute();
                while($tag = $tags->fetch(PDO::FETCH_ASSOC)){
            ?>
            <span class="btn-container col-xs-3">
	            <span class="btn btn-default tag tag-cont col-xs-12" data-tag="<?=$tag['tag']?>">
	                <!--<?=//str_replace('_',' ', $tag['tag'])?>-->
	                <script>permTagNames.push("<?=$tag['tag']?>");</script>
	            </span>
            </span>
            <?php } ?>
        </div>
	</div>




	<script>
	$(function(){
      $('#tag-container').on('click', '.tag', function(){
        $(this).toggleClass('btn-primary btn-success selected');
      });
    });

    $('#remove-tag-button').on('click', function(){
        $("#confirm").removeClass("invisible");
    });

    $("#confirm-remove-button").on("click", function(){
    	//Get all the selected tags
    	var selectedTags = $(".selected");
    	//Get the names
    	var tagNames = {};
    	$(selectedTags).each(function(index, tagElement){
    		var name = $(tagElement).attr("data-tag");
    		tagNames[index] = name;
    		permTagNames.splice(permTagNames.indexOf(name), 1);
    	});

    	$(selectedTags).remove();

    	var json = {
    		"action":"remove",
    		"tagNames": tagNames
    	}
    	//Send them to controller/tag.php with the action being remove
    	$.ajax({
    		type: "POST",
    		url: "controllers/tag.php",
    		data: json
    	})
    	.done(function(){
    		regenerateTagList();
    		$("#confirm").addClass("hidden");
    		$("#success").removeClass("hidden");
    		$("#success").fadeOut(2000, function(){
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


    function regenerateTagList(){
    	
    	$("#tag-container").empty();
    	$(permTagNames).each(function(index, currTagName){
    		console.log(currTagName);

    		var cont = $("span");
    		$(cont).addClass("btn-container col-xs-3");
    		$(cont).appendTo("#tag-container")[0];

    		var tagBtn = $("span");
    		$(tagBtn).addClass("btn btn-default tag tag-cont col-xs-12");
    		$(tagBtn).attr("data-tag", currTagName);
    		$(tagBtn).html(currTagName.replace("_", " "));
    		$(tagBtn).appendTo(cont);
    		
    	});
    	
    }

	</script>
</body>
</html>





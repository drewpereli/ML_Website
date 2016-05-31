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
    body {
        padding-top: 70px;
        padding-bottom: 20px;
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
	
	<?php include("navBar.php");?>

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
		
            <?php
                $q = "SELECT tag from tags order by tag";
                $tags = $db->prepare($q);
                $tags->execute();
                while($tag = $tags->fetch(PDO::FETCH_ASSOC)){
            ?>
	            	<script>permTagNames.push("<?=$tag['tag']?>");</script>
            <?php } ?>
            
        <div class="row" id="tag-container">
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
    		generateTagList();
    		$("#confirm").addClass("hidden");
    		$("#success").removeClass("hidden");
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


    function generateTagList(){
    	$("#tag-container").empty();
    	$(permTagNames).each(function(index, currTagName){
    		var cont = document.createElement("span");
    		$(cont).addClass("btn-container col-md-3 col-xs-12");
    		$(cont).appendTo("#tag-container")[0];

    		var tagBtn = document.createElement("span");
    		$(tagBtn).addClass("btn btn-default tag tag-cont col-xs-12");
    		$(tagBtn).attr("data-tag", currTagName);
    		$(tagBtn).html(currTagName.replace(/_/g, " "));
    		$(tagBtn).appendTo(cont);
    	});	
    }

    generateTagList();

	</script>
</body>
</html>





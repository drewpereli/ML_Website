
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
        text-align: center;
    }
    #changeButton{
    	margin-left: 5px;
    }
    #inputrow, #messagerow{
    	margin-top: -30px;
    	min-height: 100px;
    }
    #changeContainer, #messageContainer{
    	padding: 10px;
    }
  
    </style>
</head>
<body>

	<?php include("navBar.php");?>

	<script>var permTagNames = [];</script>
		
    <?php
        $q = "SELECT tag from tags order by tag";
        $tags = $db->prepare($q);
        $tags->execute();
        while($tag = $tags->fetch(PDO::FETCH_ASSOC)){
    ?>
        	<script>permTagNames.push("<?=$tag['tag']?>");</script>
    <?php } ?>


    <div class="container">
	    <div class="row">
	    	<h2 class="col-xs-12 text-center">Click a tag to edit it</h2>
	    </div>
	    <br/>
	    <div class="row" id="inputRow">
	    	<div class="col-xs-8 col-xs-offset-2 bg-info invisible" id="changeContainer">
	    		<label for="changeToInput" class="col-xs-5 text-right">
	    			Change<span id="tagChangeName"></span> to: 
	    		</label>
	    		<input type="text" name="changeToInput" id="changeToInput" class="col-xs-4"/>
	    		<span class="btn btn-default btn-sm text-center col-xs-2" id="changeButton">Change</span>
	    	</div>
	    </div>
	    <div class="row" id="messageRow">
	    	<div class="col-xs-8 col-xs-offset-2 text-center invisible" id="messageContainer">
	    		<span id="message"></span>
	    	</div>
	    </div>
		<div class="row" id="tag-container">
	    </div>
	</div>


	<script>

		$("#changeButton").on("click", function(){
			var newName = capitalize($("#changeToInput").val());
			if (newName === "")
				return;
			var tagToChange = $(".selected")[0];
			var tagNameToChange = $(tagToChange).attr("data-tag");
			var json = {
				"action": "edit",
				"tagName": tagNameToChange,
				"newName": newName
			};

			$.ajax({
				type: "POST",
				url: "controllers/tag.php",
				data: json
			})
			.done(function(result){
				result = result.trim();
				if (result === "success")
				{
					displayMessage("Succesfully edited the tag", false);
					$("#changeContainer").addClass("invisible");
	        		$("#tagChangeName").empty();
					permTagNames[permTagNames.indexOf(tagNameToChange)] = newName;
					permTagNames.sort();
					generateTagList();
				}
				else if (result === "23000")
				{
					displayMessage("There's already a tag with that name.", true);
				}
			})
			.fail(function(result){
				
			});
		});


		var displayingMessage = false;
		function displayMessage(message, error){
			if (displayingMessage) return;
			displayingMessage = true;
			$("#messageContainer").removeClass("invisible").html(message);
			if (error) $("#messageContainer").addClass("bg-danger");
			else $("#messageContainer").addClass("bg-success");
			$("#messageContainer").delay(1500).fadeOut(1000, function(){
				$(this).css("opacity", "1");
				$(this).attr("style", "display:default");
				$(this).addClass("invisible");
				$(this).removeClass("bg-success bg-danger");
				$(this).html("");
				displayingMessage = false;
			});
		}


		$(function(){
	      $('#tag-container').on('click', '.tag', function(){
	        $(this).toggleClass('btn-primary btn-success selected');
	        if ($(this).hasClass('selected'))
	        {
	        	var currTag = $(this)[0];
	        	//Make all the other buttons unselectable
	        	$(".tag").each(function(index, element){
	        		if (element === currTag)
	        		{
	        			return true;//which is continue in this case
	        		}
	        		$(element).attr("disabled", "disabled");
	        	});
	        	//Display the change message thing and add the tagname
	        	$("#changeContainer").removeClass("invisible");
	        	$("#tagChangeName").html(" " + $(currTag).attr("data-tag").replace(/_/g, " "));
	        }
	        else
	        {
	        	//Make all the other buttons selectable
	        	var currTag = $(this)[0];
	        	//Make all the other buttons unselectable
	        	$(".tag").each(function(index, element){
	        		if (element === currTag)
	        		{
	        			return true;//which is continue in this case
	        		}
	        		$(element).removeAttr("disabled");
	        	});

	        	$("#changeContainer").addClass("invisible");
	        	$("#tagChangeName").empty();
	        }
	      });
	    });

		function generateTagList(){
	    	$("#tag-container").empty();
	    	$(permTagNames).each(function(index, currTagName){
	    		var cont = document.createElement("span");
	    		$(cont).addClass("btn-container col-md-3 col-xs-12");
	    		$(cont).appendTo("#tag-container")[0];

	    		var tagBtn = document.createElement("button");
	    		$(tagBtn).addClass("btn btn-default tag tag-cont col-xs-12");
	    		$(tagBtn).attr("data-tag", currTagName);
	    		$(tagBtn).attr("type", "button");
	    		$(tagBtn).html(currTagName.replace(/_/g, " "));
	    		$(tagBtn).appendTo(cont);
	    	});	
	    }

	    generateTagList();
	</script>

</body>
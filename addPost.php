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
    #addTagContainer{
        border-left: 1px solid black;
    }
    #message{
        min-height: 20px;
        padding: 10px;
    }
    #messageContainer{
        padding:0px;
        min-height: 50px;
    }
    </style>
</head>
<body>


<?php include("navBar.php");?>

<div class="row"><br/></div>

<div class="container-fluid">
        <div class="row" id="messageContainer">
            <span class="col-xs-offset-2 col-xs-5 bg-success text-center invisible" id="message">
            </span>
        </div>
        <br/>
        <br/>
        <div class="row form-inline" id="postForm">
            <span class="form-group col-xs-8">
                <label for="postURL" class="col-xs-2 text-right">Post URL: </label>
                <input type="text" name="postURL" id="postURL" class="col-xs-7"/>
                <input type="button" name="submitButton" id="submitButton" value="Add Post" class="col-xs-2 col-xs-offset-1"/>
            </span>
            <span class="form-group col-xs-4" id="addTagContainer">
                <label for="newTagInput" class="col-xs-3 text-right">Add Tag: </label>
                <input type="text" name="newTagInput" id="newTagInput" class="col-xs-5"/>
                <input type="button" name="submitTag" id="submitTag" value="Add Tag" class="col-xs-3"/>
            </span>

        </div>
        <br/><br/>
        <!--Add the hidden post container for the post preview-->
        <div class="container-fluid hidden" id="dynamic_post_container">
            <div class="well post-container col-xs-8">
                <div class="row">
                    <a class="col-md-12" href="https://www.reddit.com/<?=$post['id']?>">
                        <span class="col-md-1 post-score">
                            <span id="dynamic_post_score"></span>
                        </span>
                        <span class="col-md-11 post">
                            <div class="row">
                                <span class="col-md-10 post-title" id="dynamic_post_title">
                                </span>
                                <span class="col-md-2 post-author" id="dynamic_post_author">
                                </span>
                            </div>
                            <div class="row">
                                <span class="col-md-2 post-comments">
                                    <span id="dynamic_post_comments"></span><span> comments</span>
                                </span>
                                <span class="col-md-7"></span>
                                <span class="col-md-3 post-time" id="dynamic_post_age">
                                </span>
                            </div>
                            <div class="row">
                                <!-- FLOAT ALL THE TAGS HERE! -->
                            </div>
                        </span>
                    </a>
                </div>
            </div>
        </div>
        <br/>
        <!--Add the tag options-->
        <div class="container flex tag-container col-xs-10" id="tag-container">
            <?php
                $q = "SELECT tag from tags where tag <> 'Self_Post' AND tag <> 'Link_Post' order by tag";
                $tags = $db->prepare($q);
                $tags->execute();
                while($tag = $tags->fetch(PDO::FETCH_ASSOC)){
            ?>
            <span class="btn btn-default btn-sm tag tag-cont" data-tag="<?=$tag['tag']?>">
                <?=str_replace('_',' ', $tag['tag'])?>
            </span>
            <?php } ?>
        </div>
</div>

    <script>
    var dynamicPostElements = {
                "root": $("#dynamic_post_container")[0],
                "score": $("#dynamic_post_score")[0],
                "title": $("#dynamic_post_title")[0],
                "author": $("#dynamic_post_author")[0],
                "comments": $("#dynamic_post_comments")[0],
                "age": $("#dynamic_post_age")[0]
            };
    var previewVisible = false; //Set to true if the user enters a proper URL and a preview is shown. 
    //Reset to false if they change the URL to be improper
    var testAnchor = document.createElement('a');//Used to test if the URL is valid 
    var urlTests = [];//Set of test functions. Only one has to return true for the url to be good
    urlTests.push(function(pathnameArray){//If the path is of the form /r/menslib/comments/id
        if (pathnameArray[1] !== "r") return false;
        if (pathnameArray[2] !== "menslib") return false;
        if (pathnameArray[3] !== "comments") return false;
        if (pathnameArray[4].length !== 6) return false;
        return true;
    });
    
    
    //Currently works for the forms "https://www.reddit.com/r/menslib/id/whatevershit" with our without https:// and www.
    $("#postURL").on('change keyup paste', function() {
        var url = $("#postURL").val();
        var properURL = false;
        

        //If the URL doesn't start with https://www. or http://wwww., change it so it does
        if (url.substring(0, 8) !== "https://" && url.substring(0, 7) !== "http://")
        {
            if (url.substring(0, 4) !== "www.")
            {
                url = "www.".concat(url);
            }
            url = "https://".concat(url);
        }

        testAnchor.href = url;

        if (testAnchor.hostname !== "www.reddit.com")
        {
            if (previewVisible)
                hidePreview();
            return;
        }

        var urlPathnameArray = testAnchor.pathname.toLowerCase().split("/");
        $.each(urlTests, function(index, test){
            properURL = test(urlPathnameArray); //If the test is true, return;
            return !properURL; //If the URL is proper, this will return false and break the loop.
        });

        if(properURL && !previewVisible)
        {
            var id = urlPathnameArray[4];
            showPreview(id);
            return;
        }

        if (!properURL && previewVisible)
        {
            hidePreview();
            return;
        }
    });


    function showPreview(postId){
        var jsonURL = "https://www.reddit.com/r/menslib/comments/" + postId + ".json?jsonp=?";
            $.getJSON(jsonURL).done(function(results){
                var postData = results[0].data.children[0].data;
                if (postData.subreddit !== "MensLib")
                {
                    if (previewVisible)
                        hidePreview();
                    return;
                }
                var postDataArray = {
                                    title: postData.title,
                                    author: postData.author,
                                    score: postData.score,
                                    comments: postData.num_comments,
                                    age: prettyTimeInterval(postData.created_utc)
                                    };

                $(dynamicPostElements.root).removeClass("hidden");
                $(dynamicPostElements.title).html(postDataArray.title);
                $(dynamicPostElements.author).html(postDataArray.author);
                $(dynamicPostElements.score).html(postDataArray.score);
                $(dynamicPostElements.comments).html(postDataArray.comments);
                $(dynamicPostElements.title).html(postDataArray.title);
                $(dynamicPostElements.age).html(postDataArray.age);

                previewVisible = true;
            })
            .fail(function(){
                console.log('fucked up');
            });
        
    }


    function hidePreview(){
        $(dynamicPostElements.root).addClass("hidden");
        previewVisible = false;
    }


    
    $("#submitButton").click(function submitform()
    {
        var URL = $("#postURL").val();
        if (URL === '')
        {
            displayMessage("You need to enter a URL", true);
            return false;
        }
        var jsonURL = URL + ".json?jsonp=?";
        $.getJSON(jsonURL)
            .done(function(results)
            {

                var postData = results[0].data.children[0].data;

                //If it's not a link to men's lib
                if (postData.subreddit !== "MensLib")
                {
                    displayMessage("That's not a link to /r/menslib", true);
                    return false;
                }
				
				var postDataArray = {
                                    submit: true,
                                    action: "add",
                                    is_self: postData.is_self,
		                            title: postData.title,
		                            author: postData.author,
		                            score: postData.score,
		                            comment_count: postData.num_comments,
		                            id: postData.id,
                                    created_utc: postData.created_utc,
                                    tags: {}};

                //Add the entered form data as well
                $(".selected").each(function(index, element){

                    //console.log($(element).is(':checked'));
                    if ($(element).hasClass("selected"))
                    {
                        var name = $(element).attr('data-tag');

                        postDataArray.tags[name] = true;
                    }
                });

                var typeTag = postData.is_self == true ? "Self_Post" : "Link_Post";
                postDataArray.tags[typeTag] = true;
                
                //Add the url
                postDataArray['URL'] = $("#postURL").val();
                

		        $.ajax({
                        type: 'POST',
                        url: "controllers/post.php",
                        data: postDataArray,
                        success: function(result){
                            $("#postURL").val("");
                            //Unselect all the tags
                            $(".selected").each(function(index, element)
                            {
                                $(element).toggleClass('btn-primary btn-success selected');
                            });
                            hidePreview();
                            displayMessage("Succesfully added the post.", false);
                        }
                    });

            
            
            })
            .fail(function(){
                displayMessage("That's not a link to reddit", true);
            });
    });



    $("#submitTag").click(function(){
        var tagName = capitalize($("#newTagInput").val());
        $.ajax({
            type: 'POST',
            url: "controllers/tag.php",
            data: {"tagName":tagName,
                    "action": "add"},
        })
        .done(function(result){
            result = result.trim();
            if (result==="success")
            {
                $("#newTagInput").val("");
                //Add the tag to the list
                var newTag = $(".tag")[0].cloneNode();
                $(newTag).attr("data-tag", tagName);
                $(newTag).html(tagName);
                $("#tag-container")[0].appendChild(newTag);
            }
        });
    });

    
    var displayingMessage = false;
    function displayMessage(message, error){
        if (displayingMessage) return;
        displayingMessage = true;
        $("#message").removeClass("invisible").html(message);
        if (error) $("#message").addClass("bg-danger");
        else $("#message").addClass("bg-success");
        $("#message").delay(1500).fadeOut(1000, function(){
            $(this).css("opacity", "1");
            $(this).attr("style", "display:default");
            $(this).addClass("invisible");
            $(this).removeClass("bg-success bg-danger");
            $(this).html("");
            displayingMessage = false;
        });
    }


    $(function(){
      $('.tag-container').on('click', '.tag', function(){
        $(this).toggleClass('btn-primary btn-success selected');
      })
    })
    </script>
    
</body>
</html>
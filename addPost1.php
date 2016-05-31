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
        <li class="active"><a href="addPost.php">Add posts</a></li>
        <li><a href="addTag.php">Add tags</a></li>
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
<div class="row"><br/><br/></div>

<div class="container-fluid">
        <div class="row">
            <label for="postURL" class="col-xs-1 text-right">Post URL: </label>
            <input type="text" name="postURL" id="postURL" class="col-xs-7"/>
            <input type="button" name="submitButton" id="submitButton" value="Add Post" class="col-xs-1 col-xs-offset-1"/>
        </div>
        <br/><br/>
        <!--Add the hidden post container for the post preview-->
        <div class="container-fluid hidden" id="dynamic_post_container">
            <div class="well post-container">
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
        <!--Add the tag options-->
        <div class="container flex tag-container col-xs-offset-1">
            <?php
                $q = "SELECT tag from tags order by tag";
                $tags = $db->prepare($q);
                $tags->execute();
                while($tag = $tags->fetch(PDO::FETCH_ASSOC)){
            ?>
            <span class="btn btn-default tag tag-cont" data-tag="<?=$tag['tag']?>">
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


    </script>

    <script>
        $("#submitButton").click(function submitform()
        {
            var URL = $("#postURL").val();
            if (URL === '')
            {
                document.write('You need to enter a URL.');
                return false;
            }
            var jsonURL = URL + ".json?jsonp=?";
            $.getJSON(jsonURL)
                .done(function(results)
                {
                    console.log(results);
                    var postData = results[0].data.children[0].data;

                    //If it's not a link to men's lib
                    if (postData.subreddit !== "MensLib")
                    {
                        return false;
                    }
                    
                    var postDataArray = {
                                        submit: true,
                                        is_self: postData.is_self,
                                        title: postData.title,
                                        author: postData.author,
                                        score: postData.score,
                                        comment_count: postData.num_comments,
                                        id: postData.id,
                                        created_utc: postData.created_utc,
                                        tags: {}};

                    //Add the entered form data as well
                    $(".selected").find("span").each(function(index, element){

                        //console.log($(element).is(':checked'));
                        if ($(element).hasClass("selected"))
                        {
                            var name = $(element).attr('data-tag');
                            console.log(name);
                            postDataArray.tags[name] = true;
                        }
                    });
                    
                    //Add the url
                    postDataArray['URL'] = $("#postURL").val();
                    
                    $.ajax({
                            type: 'POST',
                            url: "controllers/addPost.php",
                            data: postDataArray,
                            success: function(result){
                                $("#postForm")[0].reset();
                            }
                        });

                
                
                })
                .fail(function(){
                    console.log('failed');
                });
        });

    </script>
    <script>
    $(function(){
      $('.tag-container').on('click', '.tag', function(){
        $(this).toggleClass('btn-primary btn-success selected');
      })
    })
    </script>
    
</body>
</html>
<?php
    require_once('connectToDB.php');
    require_once('authenticate.php');
    require_once('utils.php');
    require_once("displayPosts.php");

    


    $posts;
    $selectedTags = array();
    $rowsReturned;

    if (isset($_POST['tags']))
    {
      $selectedTags = json_decode($_POST['tags'], true);
    }

    if(isset($_POST['anyOrAll']) && sizeof($selectedTags) > 0){
        if($_POST['anyOrAll'] === "any"){
            $q = 'SELECT post_tags.post as id,
                  posts.author,
                  posts.title,
                  posts.score,
                  posts.comments,
                  UNIX_TIMESTAMP() - posts.created_utc age,
                  UNIX_TIMESTAMP() - posts.updated_utc outdated
                  FROM post_tags
                  JOIN posts
                  ON post_tags.post = posts.id
                  WHERE';
            foreach($selectedTags as $tagName=>$value)
            {
                if ($tagName === 'anyOrAll'){continue;}
                $q .= " tag='$tagName' OR";
            }
            $q = trim($q, " OR");
            $posts = $db->prepare($q);
            $posts->execute();
        }
        else if ($_POST['anyOrAll'] === 'all')
        {
          $q = 'SELECT post AS id, tag
                FROM post_tags
                WHERE';
            foreach($selectedTags as $tagName=>$value)
            {
                $q .= " tag='$tagName' OR";
            }
            $q = trim($q, " OR");
            $posts = $db->prepare($q);
            $posts->execute();
            $posts = $posts->fetchAll(PDO::FETCH_ASSOC);
            //Put all the posts into an array of
            $organizedPosts = array();
            foreach ($posts as $post)
            {
                $organizedPosts[$post['id']][$post['tag']] = true;
            }
            //Now go through each post array and make sure every selected tag appears in it
            $goodIds = array();
            foreach ($organizedPosts as $id=>$tags)
            {
                $badId = false;
                foreach($selectedTags as $tagName=>$value)
                {
                    if (!isset($tags[$tagName]))//If the tag isn't included
                    {
                        $badId = true;
                        break;
                    }
                }
                if (!$badId)
                {
                    array_push($goodIds, $id);
                }
            }
            $q = 'SELECT id,
                  author,
                  title,
                  score,
                  comments,
                  UNIX_TIMESTAMP() - created_utc age,
                  UNIX_TIMESTAMP() - updated_utc outdated
                  FROM posts';

            if (sizeof($goodIds) !== 0)
            {
                $q .= ' WHERE';
                foreach($goodIds as $index=>$id)
                {
                    $q .= " id='$id' OR";
                }
            }
            else
            {
                $q .= " WHERE id='asdfdsas'";
            }

            $q = trim($q, " OR");
            $posts = $db->prepare($q);
            $posts->execute();
        }
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
      $posts->execute(array());
    }


    $rowsReturned = $posts->rowCount();
    $tags = $db->prepare('SELECT * FROM tags');
    $tags->execute();
    

    /*
    $q = 'SELECT 
            temp.id,
            temp.author,
            temp.title,
            temp.score,
            temp.comments,
            UNIX_TIMESTAMP() - temp.created_utc age,
            UNIX_TIMESTAMP() - temp.updated_utc outdated
          FROM
            (SELECT * FROM 
            posts p LEFT JOIN 
            post_tags pt ON pt.post = p.id) temp';
    $post_tags = $db->prepare($q);
    $post_tags->execute(array());
    */
?>

<html>
<head>
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
.alert-login{
  padding: 6px 35px 6px 10px;
  margin: 8px 0;
}



</style>

</head> 
<body>

<?php include("navBar.php");?>

<div class="container-fluid">
  <div class="row">
    <span class="col-md-2"></span>
    <span class="col-md-8 text-center">
      <div class="panel panel-default">
        <div class="panel-heading">Click a tag to add or remove it from the search. Searching with no tags will show all the posts.</div>
        <div class="panel-body row">
          <?php while($tag = $tags->fetch(PDO::FETCH_ASSOC)) { ?>
            <span class="tag-container col-xs-3">
              <span class="btn btn-default tag col-xs-12" data-tag="<?=$tag['tag']?>"><?=str_replace('_',' ',$tag['tag'])?></span>
            </span>
          <?php } ?>
        </div>
      </div>
    </span>
    <span class="col-md-2"></span>
  </div>

  <!--Search buttons here-->
  <div class="row">
     <div id="search-button-container" class="col-xs-offset-2 col-xs-8"> 
      <span class="col-xs-1"></span>
      <span class="btn btn-default btn-lg col-xs-4 submit-button all">Search for all selected tags</span>
      <span class="col-xs-2"></span>
      <span class="btn btn-default btn-lg col-xs-4 submit-button any">Search for any selected tags</span>
      <span class="col-xs-1"></span>
    </div>
  </div>
  <hr/>
  <br/>
  <br/>


  <?php 
    $args = array("posts"=>$posts);
    displayPosts($args);
    if ($rowsReturned === 0)
    {
      ?>
          <div class="row">
              <span class="col-xs-6 col-xs-offset-3 bg-danger text-center">
                <br/>
                <br/>
                No posts met the search criteria.
                <br/>
                <br/>
              </span>
          </div>
      <?php
    }
  ?>

</div>
<script src="jQuery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script>

$(".submit-button").on("click", function(){
    console.log("y")
    var form = $(document.createElement("form")).addClass("hidden").appendTo("body");
    $(form).attr("action", "index.php").attr("method", "POST");
    var json = "{";
    $(".selected").each(function(index, element){
        
        var tagName = $(element).attr("data-tag");
        //var input = document.createElement("input");
        //$(input).attr("type", "hidden").attr("name", tagName).val("true").appendTo(form);
        
        json += "\"" + tagName + "\": \"true\",";
    });

    json = json.substring(0, json.length - 1);  
    json += "}";


    $(document.createElement("input")).attr("type", "hidden").attr("name", "tags").val(json).appendTo(form);
    var anyOrAll = $(this).hasClass("any") ? "any" : "all";
    $(document.createElement("input")).attr("type", "hidden").attr("name", "anyOrAll").val(anyOrAll).appendTo(form);
    form.submit();
});

$(function(){
  $('.tag-container').on('click', '.tag', function(){
    $(this).toggleClass('btn-primary btn-success selected');
  })
})
</script>
</body>
</html>




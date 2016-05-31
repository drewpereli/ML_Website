<?php
    require_once('connectToDB.php');
    include('authenticate.php');
    require_once('utils.php');

    $tags = $db->prepare('SELECT * FROM tags');
    $tags->execute();

    $where = '';
    if(isset($_GET['any']) || isset($_GET['all'])){
        if(isset($_GET['all'])){
          $where = ' WHERE ';
        }
    }
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
    $posts = $db->prepare($q);
    $posts->execute(array());
?>

<html>
<head>
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
.alert-login{
  padding: 6px 35px 6px 10px;
  margin: 8px 0;
}
.post-container{
  padding: 5px;
}
.post-score{
  font-size: 125%;
  padding-top: 10px;
}
.post-title{
  font-weight:  bold;
}
.post-author, .post-time{
  text-align: right;  
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
        <li class="active"><a href="/">Home</a></li>
        <?php if ($auth['logged in']) { ?>
        <li><a href="addPost.php">Add posts and tags</a></li>
        <li><a href="removeTag.php">Remove tags</a></li>
        <!--<li><a href="addTag.php">Add tags</a></li>-->
        <?php } ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
          <?php if($auth['message']) { ?>
          <li>
            <div class="alert alert-login alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <strong>Error: </strong><?=$auth['message']?>
            </div>
          </li>
          <?php } ?>
        <li>
          <form class="navbar-form navbar-left" method=POST>
        <?php if(!$auth['logged in']) { ?>
            <div class="form-group">
              <input type="text" class="form-control" name=username placeholder="Username">
              <input type="password" class="form-control" name=password placeholder="Password">
            </div>
            <button type="submit" class="btn btn-default">Login</button>
          <?php } else { ?>
            <input type="hidden" class="form-control" name=logout>
            <button type="submit" class="btn btn-primary logout"><?=$auth['username']?></button>
          </form>
          <?php } ?>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<div class="container-fluid">
  <div class="row">
    <span class="col-md-2"></span>
    <span class="col-md-8 text-center">
      <div class="panel panel-default">
        <div class="panel-heading">Click a tag to add or remove it from the search bar. Searching with no tags in the bar will show all the posts.</div>
        <div class="panel-body tag-container">
          <?php while($tag = $tags->fetch(PDO::FETCH_ASSOC)) { ?>
            <span class="btn btn-default tag" data-tag="<?=$tag['tag']?>"><?=str_replace('_',' ',$tag['tag'])?></span>
          <?php } ?>
        </div>
      </div>
    </span>
    <span class="col-md-2"></span>
  </div>
  <div class="row">
    <span class="col-md-2"></span>
    <span class="col-md-8">
    <?php while($post = $posts->fetch(PDO::FETCH_ASSOC)) { ?>
      <?php include('post.php'); ?>
    <?php } ?>
    </span>
    <span class="col-md-2"></span>
  </div>
</div>
<script src="jQuery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script>
$(function(){
  $('.tag-container').on('click', '.tag', function(){
    $(this).toggleClass('btn-primary btn-success');
  })
})
</script>
</body>
</html>





<?php
	require_once("authenticate.php");
?>

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
	.panel-heading{
	  font-weight: bold;
	}
</style>


<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <a class="navbar-brand" href="https://www.reddit.com/r/MensLib/" target=_blank>/r/MensLib</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="<?php if($_SERVER['PHP_SELF'] === '/menslib/index.php') echo 'active'?>">
        	<a href="/menslib/">Home</a>
        </li>
        <?php if ($auth["logged in"])
        {
        	?>
	        <li class="<?php if($_SERVER['PHP_SELF'] === '/menslib/addPost.php') echo 'active'?>">
	        	<a href="addPost.php">Add posts and tags</a>
	        </li>
          <li class="<?php if($_SERVER['PHP_SELF'] === '/menslib/removePost.php') echo 'active'?>">
            <a href="removePost.php">Remove posts</a>
          </li>
          <li class="<?php if($_SERVER['PHP_SELF'] === '/menslib/editPost.php') echo 'active'?>">
            <a href="editPost.php">Edit posts</a>
          </li>
	        <li class="<?php if($_SERVER['PHP_SELF'] === '/menslib/removeTag.php') echo 'active'?>">
	        	<a href="removeTag.php">Remove tags</a>
	        </li>
	        <li class="<?php if($_SERVER['PHP_SELF'] === '/menslib/editTag.php') echo 'active'?>">
	        	<a href="editTag.php">Edit tags</a>
	        </li>
	        <?php
	    }
	    ?>
      </ul>
      <?php
        if ($_SERVER['PHP_SELF'] !== '/menslib/signUp.php')//Don't show the login form if you're in the sign up page
        {
          ?>
          <ul class="nav navbar-nav navbar-right">
              <?php if($auth['message']) { ?>
              <li>
                <div class="alert alert-login alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>Error: </strong><?php echo $auth['message']; $auth['message']=false; $_SESSION['auth']['message']=false;?>
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
                <a href="signUp.php"><button type="button" class="btn btn-default">Sign Up</button></a>
              <?php } else { ?>
                <input type="hidden" class="form-control" name=logout>
                <button type="submit" class="btn btn-primary logout"><?=$auth['username']?></button>
              </form>
              <?php } ?>
            </li>
          </ul>
          <?php
        }
        ?>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>




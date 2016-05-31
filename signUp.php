<?php
    require_once('connectToDB.php');

    $message = "";
    $class = "invisible";
    $class;             

    if (isset($_POST['submit']))
    {
        $key = $_POST['key'];
        if ($key !== 'cuckgina3000')
        {
            $message = "LELELELELELELELELELEL";
            $class = "BAD";
        }
        else
        {
            //If the super secret key was correct
            $username = $_POST['username'];
            $password = hash('sha256', ($_POST['password'] . $username));
            unset($_POST['password']);

            
            
            $query = "INSERT INTO users (username, password) values (?, ?)";
            $statement = $db->prepare($query);
            try
            {
                session_start();
                global $auth;
                $statement->execute(array($username, $password)); 
                $message = "Welcome to the cabal, $username!";
                $class = "bg-success";
                $auth['username'] = $username;
                $auth['message'] = '';
                $auth['logged in'] = true;
                $_SESSION['auth'] = $auth;
            }
            catch (Exception $e)
            {
                $message = "Looks like that username already exists.";
                $class = "bg-danger";
            }
        }
    }

?>

<html>
<head>
    <script src="jQuery.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <style>
        .sign-up-element{
            margin-top: 10px;
        }
        body{
            overflow-x: hidden;
        }
        #message{
            min-height: 30px;
            padding: 15px;
        }
    </style>


</head>
<body>

    <?php include("navBar.php");?>
    <br/><br/><br/><br/>
    <?php
    if ($class !== 'BAD')
    {
        ?>
        <div class="row"> 
            <span class="col-xs-6 col-xs-offset-3 text-center">  
                <span class="text-center <?=$class;?>" id="message"><?=$message;?></span> 
                <br/>
                <br/>
                <form id="signUpForm" action="" method="POST" class="col-xs-8">
                    <label for="username" class="col-xs-6 text-right sign-up-element">Username: </label>
                    <input type="text" name="username" class="col-xs-6 sign-up-element"/><br/>
                    <label for="password" name="password" class="col-xs-6 text-right sign-up-element">Password: </label>
                    <input type="password" name="password" class="col-xs-6 sign-up-element"/><br/>
                    <label for="key" class="col-xs-6 text-right sign-up-element">Super Secret Key: </label>
                    <input type="password" name="key" class="col-xs-6 sign-up-element"/><br/>
                    <input type="submit" name="submit" value="Sing Up!" class="col-xs-offset-2 sign-up-element"/>
                </form>
            </span>
        </div>
        <?php
    }
    else
    {
        ?>
        <h1 class="text-center"><?=$message;?></h1>
        <?php
    }
    ?>
</body>
<script>
    var success = "<?=$class;?>";
    if (success === 'bg-success')
    {
        setTimeout(function(){window.location.href = "/menslib/index.php";}, 1000);
    }
    if (success === "BAD")
    {
        setTimeout(function(){window.location.href = "http://www.betacuck.com";}, 1000);
    }
</script>       
</html>






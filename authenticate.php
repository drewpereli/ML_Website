<?php
    
    require_once('connectToDB.php');

    session_start();
    $_SESSION['auth'] = do_database_auth();
    global $auth;
    $auth = $_SESSION['auth'];

    function do_database_auth()
    {
        global $db;
        if (isset($_POST['logout']))
        {
            return array("message"=>"", "logged in"=>false);
        }
        if (isset($_POST['username']) && isset($_POST['password']))
        {            
            $username = $_POST['username'];
            $password = hash('sha256', ($_POST['password'] . $username));
            unset($_POST['password']);
            
            $query = "select username, password from users where username=?";
            $statement = $db->prepare($query);
            $statement->execute(array($username));
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            if (sizeOf($results) === 0)
                return array("message"=>'Incorrect username', "logged in"=>false);
            $correctPassword = $results[0]['password'];
            if ($correctPassword !== $password)
                return array("message"=>'Incorrect password', "auth"=>false, "logged in"=>false);
            return array('username'=>$results[0]['username'], "message"=>"", "logged in"=>true);
        }
        if(!isset($_SESSION['auth']))
            return array("message"=>"", "logged in"=>false);
        if (isset($auth))//If auth is set but post[username] and post[password] aren't set don't send a message
        {
            $returnArray = array("message"=>"", "logged in"=>$auth['logged in']);
            if (isset($auth['username']))
            {
                $newArray = array("username"=>$auth['username']);
                $returnArray = array_merge($returnArray, $newArray);
            }
            return $returnArray;
        }
        
        return $_SESSION['auth'];
     }
?>
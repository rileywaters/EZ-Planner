<!DOCTYPE html>
<?php 
    // Set up session and database connection
    session_start(); 
    require "connection.php";
?>
<html>
<head>
<title>validating...</title>
<link rel="stylesheet" type="text/css" href="ezplan_css.css">
</head>
<body>
<?php
    //User passed in variables
    $email = filter_input(INPUT_POST, 'email');
    $password = filter_input(INPUT_POST, 'password');;
	
    //Get connection
    $con = getConnection();
    if ($con == NULL) {
        header("Location: no_connection.php");
        exit;
    }
	
    //query the database to see if email in use.
    $sql = "SELECT * FROM User WHERE email = '".$email."' AND password = PASSWORD('".$password."')";
    $result = mysqli_query($con,$sql) or die(mysqli_error($con));
	
    if (mysqli_num_rows($result) == 1)
    {
        //if authorized, get the values of FirstName LastName
        while ($info = mysqli_fetch_array($result)) 
        {
            $email = stripslashes($info['email']);
            $uid = stripslashes($info['uid']);
        }
		
        //set session items for user to be used throughout website
        $_SESSION["email"] = $email;
        $_SESSION["uid"] = $uid;
                
        //set authorization cookie
        setcookie("auth", "1", time()+60*30*24, "/", "", 0);
				
        header("Location: home_page.php");
        exit;
    }
    else
    {
        echo "<h1>Sorry, something went wrong. Please check your email and password and try again.</h1>";
        echo "<a href='index.php'>Back</a>";
    }
?>
</body>
</html>
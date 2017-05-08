<!DOCTYPE html>
<html>
<title>processing...</title>
<head>
<link rel="stylesheet" type="text/css" href="ezplan_css.css">
</head>
<body>
<?php
    // Set up session and database connection
    session_start();
    require "connection.php";
    
    //User passed in variables
    $email = filter_input(INPUT_POST, 'email');
    $password = filter_input(INPUT_POST, 'password');
    $cpassword = filter_input(INPUT_POST, 'cpassword');
    $fname = filter_input(INPUT_POST, 'fname');
    $lname = filter_input(INPUT_POST, 'lname');
    $major = filter_input(INPUT_POST, 'major');
    $year = filter_input(INPUT_POST, 'year');

    //Get connection
    $con = getConnection();
    if ($con == NULL) {
        header("Location: no_connection.php");
        exit;
    }
	
	//query the database to see if email in use.
	$sql = "SELECT * FROM User WHERE email = '".$email."'";
	$result = mysqli_query($con,$sql) or die(mysqli_error($con));
	
	if (!(mysqli_num_rows($result) > 0))
	{
		if (strcmp($password,$cpassword) === 0)
		{
			//sql query
			$sql = "INSERT INTO User (fname, lname, year, umajor, email, password) ". 
				   "VALUES ('".$fname."','".$lname."',".$year.",'".$major."','".$email."'".
				   ",PASSWORD('".$password."'))";
			
			//query execution and error handling
			if($con -> query($sql) === TRUE)
			{
				//finding the userid of added user
				$sql = "SELECT uid FROM User WHERE email = '".$email."'";
				$result = mysqli_query($con,$sql) or die(mysqli_error($con));
				
				if (mysqli_num_rows($result) == 1)
				{
                                    //get the uid of the user
                                    $info = mysqli_fetch_array($result);
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
				echo "<h1>Error: ".$sql."<br/>".$con->error."</h1>";
			}
			
			$con->close;
		}
		else
		{
			echo "<h1>Your passwords do not match.</h1>";
			echo "<a href='registerpage.php'>Back</a>";
		}
	}
	else
	{
		echo "<h1>Sorry, this email has already been used.</h1>";
		echo "<a href='registerpage.php'>Back</a>";
	}
?>
</body>
</html>

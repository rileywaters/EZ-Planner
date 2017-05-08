<?php
include 'header.php';
echo '<link href="ezplan_css.css" rel="stylesheet" type="text/css">';
session_start();
require "connection.php";
$uid = $_SESSION["uid"];
$email = $_SESSION["email"];
$interest;
$fname;
$lname;
$year; //1
$degree;
$major; //compsci
//DATABASE STUFF BELOW
$conn = getConnection();
if ($conn == NULL) {
    header("Location: no_connection.php");
    exit;
}
//GET DEGREE STUFF, ADD CORRECT CREDIT REQ TO VARIABLES
$sql = "SELECT* FROM User WHERE uid= '$uid'";
$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($res) < 1) {
    header("Location: index.php"); // This user is not recognized so kick back to landing page.
    exit;
} else {
    while ($row = mysqli_fetch_array($res)) {
        $fname = stripslashes($row['fname']);
        $lname = stripslashes($row['lname']);
        $year = stripslashes($row['year']);
        $degree = stripslashes($row['umajor']);
        $email = stripslashes($row['email']);
        $uid = stripslashes($row['uid']);
        $interest = stripslashes($row['interest']);
    }
    $res->free();
}
$major = substr($degree, strpos($degree, ",") + 2);
?>

<html>
    <head>
        <title>Edit Info</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            #content{
                margin-left: 20%;
            }
            form
            {
                width: 200px;
                border-radius: 4px;
            }
            
            input, select
            {
                padding: 5px;
                margin-bottom: 10px;
                display: inline-block;
                border: 2px solid #000;
                border-radius: 4px;
            }
            
            input[type=submit]
            {
                margin-top: 10px;
                padding: 10px 20px;
                background-color: cadetblue;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 13pt;
            }
        </style>
    </head>

    <body>
        <div id="content">
            <form method="post" action="my_info_edit2.php">
                <fieldset>
                    <legend>My Info Edit</legend>
                    <label>Username:</label><br>
                    <label><?php echo $email;?></label><br><br>
                    
                    <label>First Name:</label><br>
                    <input type="text" name="fname" value="<?php echo $fname; ?>" size="25" maxlength="20">
                    <br><br>

                    <label>Last Name:</label><br>
                    <input type="text" name="lname" value="<?php echo $lname; ?>" size="25" maxlength="20">
                    <br><br>

                    <label>New Password: <b style='font-size: smaller; color: red;'>Please leave these fields blank if you do not wish to change your password</b></label><br>
                    <input type="password" name="password" value="" size="25" maxlength="20">
                    <br><br>

                    <label>Confirm New Password:</label><br>
                    <input type="password" name="cpassword" value="" size="25" maxlength="20">
                    <br><br>

                    <label>Degree:</label><br>
                    <select name="major">
                        <option value="Bachelor of Science, Major in Computer Science" selected>Bachelor of Science, Major in Computer Science</option>
                    </select><br/>
                    <a href='degree_page.php'>Click here to edit your degree info</a>
                    <br><br>

                    <label>Year:</label><br>
                    <input style="width: 100px;" type="number" min="1" max="5" name="year" value="<?php echo $year ?>"/><br/>
                    <br><br>

                    Insert tags for courses that you might be interested in seperated by ",":
                    <br>                        
                    <textarea name="interest" value="<?php $interest ?>" rows="8" cols="100"><?php echo $interest ?></textarea><br>

                    <input type="submit" value="Update" name="submit"><br>
                    <a href="home_page.php"><h5>Return to profile</h5></a>
                </fieldset>
            </form>
        </div>
    <?php
        include 'footer.php';
    ?>

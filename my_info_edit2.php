<?php

// Set up session and database connection
session_start();
require "connection.php";

//User passed in variables
$uid = $_SESSION["uid"];
$email = $_SESSION["email"];
$password = filter_input(INPUT_POST, 'password');
$cpassword = filter_input(INPUT_POST, 'cpassword');
$fname = filter_input(INPUT_POST, 'fname');
$lname = filter_input(INPUT_POST, 'lname');
$major = filter_input(INPUT_POST, 'major');
$year = filter_input(INPUT_POST, 'year');
$interest = filter_input(INPUT_POST, 'interest');
//Get connection
$con = getConnection();
if ($con == NULL) {
    header("Location: no_connection.php");
    exit;
}

//no new password or new pass
if (empty($cpassword) && empty($password)) {
    $sql = "UPDATE User SET fname ='$fname', lname = '$lname', year='$year', umajor='$major', interest='$interest'  WHERE uid='$uid'";
    if ($con->query($sql) == TRUE) {
        echo "Your info has been updated!<br>";
        echo "<body  style='background: powderblue;'><a href='home_page.php'>Click here to return to your profile</a></body>";
    } else {
        echo "error updated:" . $conn->error;
    }
    $conn->close();
} else if (strcmp($password, $cpassword) === 0) {
    $sql = "UPDATE User SET fname ='$fname', lname = '$lname', year='$year', umajor='$major', interest='$interest', password = PASSWORD('" . $password . "')  WHERE uid='$uid'";
    if ($con->query($sql) == TRUE) {
        echo "updated!<br/><a href='home_page.php'>Back</a>";
    } else {
        echo "error updated:" . $conn->error;
    }
    $conn->close();
} else {
    echo "<script> alert('Password did not match'); </script>";
    echo "<body  style='background: powderblue;'><a href='my_info_edit.php'>Back</a></body>";
}
?>


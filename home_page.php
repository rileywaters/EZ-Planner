<!DOCTYPE html>
<?php
session_start();
require "connection.php";

// Set up connection; redirect to log in if cannot connect or not logged in
if (filter_input(INPUT_COOKIE, "auth") != 1) {
    header("Location: index.php");
    exit;
}

$uid = $_SESSION["uid"];
$email = $_SESSION["email"];	
//$email = "jdoe@gmail.com"; //test
$name; //john doe
$year; //1
$degree;
$major; //compsci
$currentcred = 0; 
$requiredcred; 
$remaining = $requiredcred - $currentcred;
//DATABASE STUFF BELOW
$conn = getConnection();
if ($conn == NULL) {
    header("Location: no_connection.php");
    exit;
}
//GET DEGREE STUFF, ADD CORRECT CREDIT REQ TO VARIABLES
$sql = "SELECT * FROM User WHERE uid = $uid";
$res = mysqli_query($conn,$sql) or die(mysqli_error($conn));
if(mysqli_num_rows($res) < 1){
    echo "<h1>No rows found...</h1>";
}
else{
    while ($row = mysqli_fetch_array($res)) 
	{
		$name = stripslashes($row['fname'])." ".stripslashes($row['lname']);
		$year = stripslashes($row['year']);
		$degree = stripslashes($row['umajor']);
	}
    $res -> free();
}
$major = substr($degree,strpos($degree,",")+2);
//echo "Query:".$year.",".$name.",",$major.",".$uid; //test echo
$sql2 = "SELECT* FROM UserCourse WHERE uid = '$uid'";   
$res2 = mysqli_query($conn,$sql2) or die(mysqli_error($conn));  //get users courses
if(mysqli_num_rows($res2) < 1){
    $currentcred = 0;
}
else{
    while ($row = mysqli_fetch_array($res2)){   //counting credits based off each course in userCourse
           $cc = $row["cname"];
           $sql3 = "SELECT credits FROM Course WHERE cname = '$cc'";
           $res3 = mysqli_query($conn,$sql3) or die(mysqli_error($conn));
           if(mysqli_num_rows($res2) < 1){
                echo "<h1>No rows found...</h1>";
           }
           else{
               while ($row = mysqli_fetch_array($res3)){
                   $currentcred += $row['credits'];
               }
           }
	}
    $res2 -> free();
    $res3 -> free();
}
$sql4 = "SELECT mincredits FROM DegreeType WHERE degree = '$degree'";
$res4 = mysqli_query($conn,$sql4) or die(mysqli_error($conn));
if(mysqli_num_rows($res4) < 1){
    echo "<h1>No rows found...</h1>";
}
else{
    while($row = mysqli_fetch_array($res4)){
        $requiredcred = $row['mincredits'];
    }
    $res4 -> free();
}
?>
<html>
    <head>
        <title>Compare</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="ezplan_css.css">
        <style>
            input[type=submit] {
                margin-top: 10px;
                padding: 10px 20px;
                background-color: cadetblue;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 13pt;
            }
            table,th,tr,td{
                z-index: -10;
            }
            th{
                border-style: hidden;
            }
            tr,td{
                border-style: hidden;
                padding-left: 5px;
                padding-right: 5px;
            }
            table{
                border-style: ridge;
                border-color: powderblue;
                border-width: 5px;
            }
            #wrapper{
                margin-top: 5%;
                margin-left: 10%;
            }
            .floating-boxs{
                display: inline-table;
                margin: 10px;
                width: 500px;
                height: 400px;
                background-color: lightcyan;
            }
            #userheader{
                background-color: white;
                height: 40px;
            }
            #degreeheader{
                background-color: white;
                height: 40px;
            }
            form{
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div id="header">
            <?php include("header.php");?>
        </div>
        <div id="wrapper">
            <table id ="userinfo" class="floating-boxs">
                <th><h2>Welcome <?php echo $name;?>!</h2></th>
                <tr>
                    <td>
                        <p>You are currently registered in year <?php echo $year;?> of a <?php echo $degree;?> degree.</p>
                        <p>EZ-Plan is here to help you with scheduling and planning your degree over the course of your university career.</p>
                        <p>Select from a variety of options from the top menu bar to get started!</p>
                        <p>If you would like to update your display name or any other information regarding your account, either click on the button below or select 'My Information' from the top menu bar if you are on any other page.</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <form name="edit info" action = "my_info_edit.php">
                        <input type="submit" value ="edit info"/>
                        </form>
                    </td>
                </tr>
            </table>
            <table id="degreeinfo" class="floating-boxs">
                <th><h2>Degree Overview</h2></th>
                <tr>
                    <td>
                        <form method = "POST">
                            <select>
                                <option value = "<?php $major ?>"><?php echo $major;?></option>
                            </select>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style = "text-align:center; font-size: 115%;"> <b>Required Credits: <?php echo $requiredcred;?></b> </p>
                        <p style = "text-align:center; font-size: 115%;"> <b>Current Credits: <?php echo $currentcred;?> </b></p></br>
                        <p> To see a more detailed breakdown and customize your schedule, click the button below.</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <form name="edit schedule" action = "suggested_schedule.php">
                        <input type="submit" value ="edit schedule" style = "margin-bottom:5px"/>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <div if="footer">
            <?php include("footer.php");?>
        </div>
    </body>
</html>
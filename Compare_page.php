
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
session_start();
require "connection.php";
$conn = getConnection();
if ($conn == NULL) {
    header("Location: no_connection.php");
    exit;
}
$d1val;
$d2val;
$d3val;
$uid = $_SESSION["uid"];
$email = $_SESSION["email"];
//$email = "jdoe@gmail.com"; //test
$name; //john doe
$year; //1
$degree;
$major; //compsci
$currentcred = 0; 
$requiredcred; 

if(!isset($_POST['Compare'])){
    $sql = "SELECT* FROM User WHERE email= '$email'";
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
                    $email = stripslashes($row['email']);
                    $uid = stripslashes($row['uid']);
            }
            $res -> free();
    }
    $major = substr($degree,strpos($degree,",")+2);
?>
<!-- Show initial set page if user has not selected compare yet -->
<html>
    <head>
        <title>Compare</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="ezplan_css.css">
         <style>
             footer{
                 position:absolute;
                 bottom: 7px;
                 width: 97.8%;
             }
            .selector{
                position:absolute;
                border-style: hidden;
                width:inherit;
                top:20%;
                left:15%
            }
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
            select{
                padding:5px;
                background-color: whitesmoke;
                color: black;
                border: solid;
                border-color: cadetblue;
                border-radius: 4px;
                font-size: 20px;
            }
        </style>
    </head>
    <body>
        <?php include("header.php"); ?>
        <div id = "wrapper">
            <table class = "selector" style = "margin-left: 25px;">
                <th><h2>Select Up to 3 different degrees:</th></br>
                <tr>
                    <td>
                        <div>
                            <center>
                            <form method="post" name ="selector" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <select style = "float:left; margin-right:8px;" name ="degsel1">
                                    <option value ="null">Select a Degree</option>
                                    <option value ="cosc"><?php echo $major;?></option>
                                    <option value ="test1">Sample Degree 1</option>
                                    <option value ="test2">Sample Degree 2</option>
                                </select>
                                <select name ="degsel2" style = "clear:both;">
                                    <option value ="null">Select a Degree</option>
                                    <option value ="cosc"><?php echo $major;?></option>
                                    <option value ="test1">Sample Degree 1</option>
                                    <option value ="test2">Sample Degree 2</option>
                                </select>
                                <select name ="degsel3" style = "float:right; margin-left:5px;" >
                                    <option value ="null">Select a Degree</option>
                                    <option value ="cosc"><?php echo $major;?></option>
                                    <option value ="test1">Sample Degree 1</option>
                                    <option value ="test2">Sample Degree 2</option>
                                </select></br>
                                <input style = "margin-top:10px;" type="submit" name = "Compare" value="Compare"/>
                            </form>
                            </center>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            
       <?php include("footer.php"); ?>
    </body>
</html>
<?php }else{
    $sql = "SELECT* FROM User WHERE email= '$email'";
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
                    $email = stripslashes($row['email']);
                    $uid = stripslashes($row['uid']);
            }
            $res -> free();
    }
    /*Check selections and weed out dupes,
     * Not going to be used since we only have 1 degree
     * rest is hardcoded for now
     */
    $d1val = $_POST['degsel1'];
    $d2val = $_POST['degsel2'];
    $d3val = $_POST['degsel3'];
    if($d1val === $d2val){
        $d2val = "null";
    }
    if($d1val === $d3val){
        $d3val = "null";
    }
    if($d2val === $d3val){
        $d3val = "null";
    }
    $sql3 = "SELECT mincredits FROM DegreeType WHERE degree = '$degree'";
    $res3 = mysqli_query($conn,$sql3) or die(mysqli_error($conn));
    if(mysqli_num_rows($res3) < 1){
        echo "<h1>No rows found...</h1>"; // This should never happen or we have an inconsistant database.
    }
    else{
        while($row = mysqli_fetch_array($res3)){
            $requiredcred = $row['mincredits'];
        }
        $res3 -> free();
    }
    $sql4 = "SELECT* FROM UserCourse WHERE uid = '$uid'";   
    $res4 = mysqli_query($conn,$sql4) or die(mysqli_error($conn));  //get users courses
    if(mysqli_num_rows($res4) > 0){
        while ($row = mysqli_fetch_array($res4)){   //counting credits based off each course in userCourse
               $cc = $row["cname"];
               $sql5 = "SELECT credits FROM Course WHERE cname = '$cc'";
               $res5 = mysqli_query($conn,$sql5) or die(mysqli_error($conn));
               if(mysqli_num_rows($res5) < 1){
                    echo "<h1>No rows found...</h1>"; // This should never happen or we have an inconsistent database.
               }else{
                   while ($row = mysqli_fetch_array($res5)){
                       $currentcred += $row['credits'];
                   }
               }
        }
        $res4 -> free();
        $res5 -> free();
    }
     
?>
<html>
    <head>
        <title>Compare</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="ezplan_css.css">
         <style>
            .content{
                border-style: hidden;
                border-right: solid;
                border-width: 1px;
                padding: 15px;
                display:inline-block;
                border-collapse: collapse;
            }
            tr,td,th{
                border-style: hidden;
                
            }
            h3{
                border-bottom: solid;
                border-width: 1px;
            }
            #conttable{
                text-align: center;
                font-size:18px;
                margin-top:5%;
                margin-left:15%;
                border-style: ridge;
                border-color: powderblue;
                background-color: lightcyan;
                
            }
             #comp {
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
        <?php include("header.php"); ?>
        <div id = "wrapper">
            <h2 style = "font-size: 48px; text-align: center;">Degree Breakdowns Below<h2>
            <table id ="conttable">
                    <tr>
                    <td>
                        <table class = "content" style = "">
                            <tr>
                                <td>
                            <?php
                            switch($d1val){
                                    case "cosc":
                                        echo "<h3>$degree</h3>";
                                        echo "<p>Required Credits: ".$requiredcred."</p>";
                                        echo "<p>Current Credits: ".$currentcred."</p>";
                                        echo "<p><b>Required Courses:</b></p>";
                                        $sql2 = "SELECT* FROM CourseRequirement";
                                        $res2 = mysqli_query($conn,$sql2) or die(mysqli_error($conn));
                                        $rows_res = mysqli_fetch_all($res2,MYSQLI_ASSOC);
                                        $course_array = array();
                                        foreach($rows_res as $rows){
                                            $desc = $rows['description'];
                                            if(stristr($desc, 'elective') === false){
                                                $course_array[] = $desc;
                                            }
                                            else{
                                                $cred = $rows['credits'];
                                                $course_array[] = $cred." ".$desc." credits";
                                            }
                                        }
                                        foreach($course_array as $course){
                                            echo "<p id = 'ind'>$course</p>";
                                        }
                                        break;
                                    case "test1":
                                        include("test1.php");
                                        break;
                                    case "test2":
                                        include("test2.php");
                                        break;
                                    default:
                                        include("none.php");
                                        break;
                                }
                            ?></td>          
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table class = "content" style = "">
                            <tr>
                                <td>
                            <?php
                            switch($d2val){
                                    case "cosc":
                                        echo "<h3>$degree</h3>";
                                        echo "<p>Required Credits: ".$requiredcred."</p>";
                                        echo "<p>Current Credits: ".$currentcred."</p>";
                                        echo "<p><b>Required Courses:</b></p>";
                                        $sql2 = "SELECT* FROM CourseRequirement";
                                        $res2 = mysqli_query($conn,$sql2) or die(mysqli_error($conn));
                                        $rows_res = mysqli_fetch_all($res2,MYSQLI_ASSOC);
                                        $course_array = array();
                                        foreach($rows_res as $rows){
                                            $desc = $rows['description'];
                                            if(stristr($desc, 'elective') === false){
                                                $course_array[] = $desc;
                                            }
                                            else{
                                                $cred = $rows['credits'];
                                                $course_array[] = $cred." ".$desc." credits";
                                            }
                                        }
                                        foreach($course_array as $course){
                                           echo "<p id = 'ind'>$course</p>";
                                        }
                                        break;
                                    case "test1":
                                        include("test1.php");
                                        break;
                                    case "test2":
                                        include("test2.php");
                                        break;
                                    default:
                                        include("none.php");
                                        break;
                                }
                            ?></td>          
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table class = "content" style = "border-right: hidden;">
                            <tr>
                                <td>
                            <?php
                            switch($d3val){
                                    case "cosc":
                                        echo "<h3>$degree</h3>";
                                        echo "<p>Required Credits: ".$requiredcred."</p>";
                                        echo "<p>Current Credits: ".$currentcred."</p>";
                                        echo "<p><b>Required Courses:</b></p>";
                                        $sql2 = "SELECT* FROM CourseRequirement";
                                        $res2 = mysqli_query($conn,$sql2) or die(mysqli_error($conn));
                                        $rows_res = mysqli_fetch_all($res2,MYSQLI_ASSOC);
                                        $course_array = array();
                                        foreach($rows_res as $rows){
                                            $desc = $rows['description'];
                                            if(stristr($desc, 'elective') === false){
                                                $course_array[] = $desc;
                                            }
                                            else{
                                                $cred = $rows['credits'];
                                                $course_array[] = $cred." ".$desc." credits";
                                            }
                                        }
                                        foreach($course_array as $course){
                                            echo "<p id = 'ind'>$course</p>";
                                        }
                                        break;
                                    case "test1":
                                        include("test1.php");
                                        break;
                                    case "test2":
                                        include("test2.php");
                                        break;
                                    default:
                                        include("none.php");
                                        break;
                                }
                            ?></td>          
                            </tr>
                        </table>
                    </td>
                    </tr>
            </table>
        </div>
    <center><a id = "comp" href="Compare_page.php">Back</a></center>
    <?php include("footer.php"); ?>
    </body>
</html>

<?php
}
?>

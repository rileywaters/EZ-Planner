<!DOCTYPE html>
<?php
session_start();
require "connection.php";

$uid = $_SESSION["uid"];

//connection
$conn = getConnection();
if ($conn == NULL) {
    header("Location: no_connection.php");
    exit;
}

$data = filter_input(INPUT_GET, 'course');
if (!isset($data)) {
	header("Location: course_browser.php");
        exit;
}
// Query
$search_sql= "SELECT cname,title,credits FROM Course WHERE cname LIKE '%".$data."%' OR title LIKE '%".$data."%';"; 
$search_query = mysqli_query ($conn,$search_sql);

if(mysqli_num_rows($search_query) < 1){
    $message = "Nothing found please try again.<br/>"
             . "<a href='course_browser.php'>Back</a>";
}
else
{
    $rows = mysqli_fetch_all($search_query, MYSQLI_BOTH);
}
  
?>
<html>
    <head>
         <title>Courses</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="ezplan_css.css">
        <style>
            table{
                border-style: ridge;
                border-color: powderblue;
                border-width: 2px;
                background-color: lightcyan;
                width: 600px;
                height: 440px;
                right: 100px;
                top: 100px;
                overflow-x: auto;
                overflow-y: auto;
                margin: 0px auto;
                border-radius: 4px;
            }
            table.courselist tr td 
            {
                padding: 5px;
                margin: 5px;
                border-radius: 4px;
            }
            
            td.course 
            {
                border: 1px solid #80C1CA;
            }
            .message {
                padding: 20px;
                text-align: center;
                font-size: large;
                color: #FFF;
                background-color: #5199A3;
            }
        </style>
    </head>
    <body>
        <?php include "header.php";?>
        <div id = "wrapper">
            <?php
            // Put message at the top of the page if applicable
            if (isset($message)) {
                echo "<p class='message'>$message</p>";
                
            } else {
            ?>
                <div>
                    <p style = "text-align: center; margin-left: 25px; vertical-align: top; font-size:20px;"><b><u>Course Info</u> </b></p>
                </div>
                <table class="courselist">
            <?php 
            }
                foreach($rows as $row)
                {
                    $cname = $row["cname"];
                    $title = $row["title"];
                    $credits = $row["credits"];
            ?>
                <tr>
                    <td class="course"><?php echo $cname; ?></td>
                    <td class="course"><?php echo $title; ?></td>
                    <td class="course"><?php echo $credits; ?></td>
                </tr>
            <?php
                }
            ?>
                </table><br/>
        </div>
        <?php include "footer.php";?>
    </body>
</html>
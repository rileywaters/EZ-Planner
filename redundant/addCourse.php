<!DOCTYPE html>
<?php 
session_start();
require "connection.php";

$uid = $_SESSION["uid"];
//connection
$conn = getConnection();
$add = $_GET['add'];
if ($conn == NULL) {
    header("Location: no_connection.php");
    exit;
}

    $search_sql= "SELECT cname FROM UserCourse WHERE cname LIKE'".$add."' AND uid = '".$uid."';"; 
    mysql_select_db('db_ioyedele');
    $search_query = mysql_query ($search_sql,$conn);
    $row = mysql_fetch_array($search_query, MYSQL_ASSOC);
  
    
    
?>
<html>
    <head>
         <title>Courses</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="ezplan_css.css">
        <style>
            html, body, #wrapper{ 
                height: 100%;
            }
            body
            {
                font-size: 14pt;
            }
			
            
           .homebtn {
                background-color: black;
                color: white;
                padding: 16px;
                font-size: 16px;
                border: none;
                cursor: pointer;
                margin-left: 20px;
                display: inline-block;
                border-radius: 50%;
                
  
            }
           
            .myinfobtn {
                background-color: black;
                color: white;
                padding: 16px;
                font-size: 16px;
                border: none;
                cursor: pointer;
                margin-left: 15px;
                display: inline-block;
                
                
            }
            
            .myschedbtn {
                background-color: black;
                color: white;
                padding: 16px;
                font-size: 16px;
                border: none;
                cursor: pointer;
                display: inline-block;
                
                
            }
            .coursebtn {
                background-color: black;
                color: white;
                padding: 16px;
                font-size: 16px;
                border: none;
                cursor: pointer;
                display: inline-block;
                
                
            }
            .sort{
                position: relative;
                display: inline-block;
                border: solid;
                cursor: pointer;
                display: inline-block;
                
            }
            .sortdropcont{
                display: none;
                position: absolute;
                background-color: #f9f9f9;
                min-width: 100px;
                box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                padding-left:6px;
            }
            .sort:hover .sortdropcont{
                display:block;
            }
            table{
                 border-style: ridge;
                border-color: powderblue;
                border-width: 5px;
                background-color: lightcyan;
                width: 600px;
                height: 440px;
                right: 100px;
                top: 100px;
                overflow-x: auto;
                overflow-y: auto;
                margin: 0px auto;
            }
            .hide { 
                list-style-type: none;
                list-style-position:inside;
                margin:0px;
                padding:0px;
            }
            
            
            input[type=submit]
            {
                margin-top: 10px;
                padding: 10px 20px;
                color: white;
                background-color: cadetblue;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 13pt;
            }
           
        </style>
    </head>
    <body>
        <?php include "header.php";?>
        <div>
            <?php
            if(!$row){
        $addcourse_sql= "INSERT INTO UserCourse (grade, uid, cname ) VALUES (null,'".$uid."','".$add."');";
        $add_query = mysql_query ($addcourse_sql,$conn);
        echo '<h1 style="text-align:center">Course was successfully added.</h1>';
        
        }else{
            echo'<h1 style="text-align:center">You already have this course on your list.</h1>';
    
            
              }
        ?>
        </div>
        <div id = "wrapper" align="center">
            <form name = "form1" method="get" action="course_browser.php">
                <input type="submit" name="Back" value="Back to Browser" />
               
            </form>
        </div>
        <?php include "footer.php";?>
    </body>


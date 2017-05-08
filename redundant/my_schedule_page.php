<?php
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <style>
            html, body, #wrapper{ 
                height: 100%;
            }
            #header{ 
                border-style: solid;
                width: 100%;
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
            .degdrop{
                position: relative;
                display: inline-block;
                border: solid;
                cursor: pointer;
                display: inline-block;
                
            }
            .degdropcont{
                display: none;
                position: absolute;
                background-color: #f9f9f9;
                min-width: 100px;
                box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                padding-left:6px;
            }
            .degdrop:hover .degdropcont{
                display:block;
            }
            #userinfo{
                background-color: cyan;
                width: 600px;
                height: 400px;
                padding-left: 25px;
                padding-right: 25px;
                float: left;
                overflow-x: auto;
                overflow-y: auto;
                
            }
            #degreeinfo{
                background-color: cyan;
                width: 600px;
                height: 400px;
                padding-left: 25px;
                padding-right: 25px;
                float: right;
                overflow-x: auto;
                overflow-y: auto;
            }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <?php include "header.php" ?>
                <form method="post" action="" style="width:30%">
                    <fieldset>
                        <legend>My Schedule</legend>
                        Year standing:<select name="Year Standing" onchange="if (this.selectedIndex) getCourseByYear();">
                            <option value="1st year">1</option>
                            <option value="2nd year">2</option>
                            <option value="3rd year">3</option>
                            <option value="4th year">4</option>
                            <option value="Greater than 4">>4</option>
                        </select><br/>
                        
                        
                        Current degree selected: <select name="Degree" onchange="if (this.selectedIndex) getDegreeSchedule();">
                            <option value="Computer Science Degree">Computer Science Degree</option>
                            <option value="Chemistry Degree">Chemistry Degree</option>
                        </select><br/>
                        
                        Required Courses:<textarea name="Courses" rows="8" cols="400">
                        Required courses shows up here
                        </textarea><br/>
                        
                        Electives Courses:<textarea name="Courses" rows="8" cols="400">
                        Possible elective courses shows up here
                        </textarea><br/>
                    </fieldset>
                </form>
            <?php include "footer.php" ?>
        </div>
    </body>
</html>
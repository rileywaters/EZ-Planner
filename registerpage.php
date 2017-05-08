<!DOCTYPE html>
<?php 
	// Set up session and database connection
	session_start();
	require "connection.php";
?>
<html>
    <head>
        <title>Register</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="ezplan_css.css">
        <style>
            body
            {
                font-size: 14pt;
            }
            
            #page
            {
                width: 100%;
                padding-top: 10%;
            }
            
            input[type=text], input[type=password], 
            input[type=email], input[type=number],
            select
            {
                width: 90%;
                padding: 5px;
                margin-bottom: 10px;
                display: inline-block;
                border: 2px solid #000;
                border-radius: 4px;
            }
            
            input[type=submit]
            {
		margin-top: 20px;
		width: 100%;
                padding: 10px 20px;
                background-color: cadetblue;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 13pt;
            }
            
             form
            {
                background-color: white;
                width: 20%;
                padding: 20px;
                border-style: solid;
                border-width: 1px;
                border-radius: 4px;
                border-color: grey;
            }
        </style>
    </head>
    <body>
        <div id="page" align="center">
            <img src="http://i.imgur.com/GR7Zect.png" style="margin-bottom: 10px;">
            <form method="post" action="registerpage2.php" style="width:30%">
                <input type="email" name="email" placeholder="Email" autofocus required/><br/>
                <!-- TODO PHP Validation of password -->
                <input type="password" name="password" placeholder="Password" required/><br/>
                <input type="password" name="cpassword" placeholder="Confirm Password" required/><br/><br/>
                <input type="text" name="fname" placeholder="First Name" required/><br/>
                <input type="text" name="lname" placeholder="Last Name" required/><br/>
                <!-- This select section realy should be gotten from the database (select degree(unique) from degreetype) -->
                <!-- however for time purposes this is hard coded in. -->
                Degree: <br/>
                <select name="major">
                    <option value="Bachelor of Science, Major in Computer Science" selected>Bachelor of Science, Major in Computer Science</option>
                </select><br/>
                Year: <br/>
                <input style="width: 100px;" type="number" min="1" max="5" name="year" value="1"/><br/>
                <input type="submit" value="submit" name="Submit" align="center"/>
            </form>
        </div>
    </body>
</html>
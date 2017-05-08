<!DOCTYPE html>
<html>
    <head>
         <title>Browser</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="ezplan_css.css">
        <style>
            body
            {
                font-size: 14pt;
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
            input[type=text]
            {
                width: 90%;
                padding: 5px;
                margin-bottom: 10px;
                display: inline-block;
                border: 2px solid #000;
                border-radius: 4px;
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
        <?php include "header.php";?>
        <h1 style="text-align:center">Course Browser</h1>
        <div id = "wrapper" align="center">
            <form name = "form1" method="get" action="searchresults.php">
                <input name ="course" type="text" size="40" maxlength="50" autofocus/>
                <input type="submit" name="Submit" value="Search" />
            </form>
        </div>
        <?php include "footer.php";?>
    </body>
</html>

<?php
    //returns connection to 
    function getConnection()
    {
        //Local variables to maintain encapsulation
        $servername = "cosc304.ok.ubc.ca";
        $username = "ioyedele";
        $password = "36547123";
        $dbname = "db_ioyedele";
		
        //Create connection
        $con = new mysqli($servername, $username, $password, $dbname);

        //Check connection
        if ($con -> connect_error)
        {
            die("Connection failed: ".$con -> connect_error);
        }
		
        return $con;
    }
?>
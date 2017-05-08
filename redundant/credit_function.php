<?php

$mysqli = mysqli_connect("localhost", "cs360user", "letmein", "testDB");
if ($mysqli->connect_error) {
    die("Connection failed: " .$mysqli->connect_error);
}
?>
<html>
<head>
</head>
<body>
    <div>
        <p>
<?php
    $sql = "SELECT * FROM credits WHERE Degree = 'Art'";
    $results = $mysqli->query($sql);
   
    if($results -> num_rows > 0){
        while($row = $results->fetch_assoc()){
            echo "<b>Degree:</b> " .$row["Degree"]. "<br><b>Year 1:</b> " .$row["Year1"]. "<br><b>Year 2:</b> " .$row["Year2"]. "<br><b>Year 3:</b> " .$row["Year3"]. "<br><b>Year 4:</b> " .$row["Year4"]."<br>"; 
        }   
    }
?>
        </p>
    </div>
</body>
</html>
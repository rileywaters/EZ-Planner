<?php
function listDeg($conn){
    /*$degrees = "";
    $conn = getConnection();
    $sql = "SELECT umajor FROM User where uid=$uid";
    $res = mysqli_query($conn, $sql);
    if($res != NULL){
        while ($row = mysqli_fetch_array($res)){
           $degrees = $row['umajor'];
       }
    }
    else{
        die("Query Error");
    }
    $display = "<form name = 'deglist'>".
            "<select>";
    $deg_array = explode(",",$degrees);
    foreach($deg_array as $deg){
        $display .= "<option value =\'".$deg."\'>".$deg."</option>";
    }
    $display .= "</select>".
            "</form>";
    //echo $display;*/
    echo "working";
}
?>

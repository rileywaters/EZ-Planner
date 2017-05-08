<!DOCTYPE html>
    
<?php
// Set up session and database connection
session_start();
include "connection.php";

// Set up connection; redirect to log in if cannot connect or not logged in
if (filter_input(INPUT_COOKIE, "auth") != 1) {
    header("Location: index.php");
}
$mysqli = getConnection();
if ($mysqli == NULL) {
    header("Location: no_connection.php");
    exit;
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Suggested Schedule</title>
        
        <link rel="stylesheet" type="text/css" href="ezplan_css.css">
        <style>
            div.tablesdiv {
                width:50%;
                margin:auto;
            }
            table.courselist {
                border:1px solid black;
                width:100%;
                background-color: #DDF5F8;
                border-radius: 4px;
            }
            table.courselist tr td {
                padding: 5px;
                margin: 5px;
                border-radius: 4px;
            }

            td.course {
                width:80%;
                border: 1px solid #80C1CA;
            }
            td.coursespace {
                width: 80%;
            }
            td.dviewbtn {
                width:10%;
            }
            td.dviewbtnwide {
                width:20%;
            }
            span.reqdesc, label.checkbox {
                font-style: italic;
                color: #999;
                font-size: small;
            }
            .message {
                padding: 20px;
                text-align: center;
                font-size: large;
                color: #FFF;
                background-color: #5199A3;
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
        </style>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    </head>
    
    <body>
        <?php include("header.php"); ?>
        <div class="tablesdiv">
<?php

// Get user ID associated with session email
// (better strategy long-term: store user id in session on login,
// since that is what other tables reference)
if (isset($_SESSION["uid"])) {
    $uid = $_SESSION["uid"];
}
else {
    $email = $_SESSION["email"];
    $sql = "SELECT uid FROM User WHERE email = '$email'";
    $result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
    if (mysqli_num_rows($result) >= 1) {
        $row = mysqli_fetch_assoc($result);
        $uid = $row["uid"];
        $_SESSION["uid"] = $uid;
    }
}

// If user has a valid degree type, use it as the major
// (Otherwise, just use the first valid one we find)
// (Assumes user can only select valid majors)
$sql_findmaj = "SELECT umajor FROM User, DegreeType "
        . "WHERE User.uid = '$uid' "
        . "AND degree = umajor";
$result_findmaj = mysqli_query($mysqli, $sql_findmaj) or die(mysqli_error($mysqli));
if (mysqli_num_rows($result_findmaj) != 0) {
    $row = mysqli_fetch_assoc($result_findmaj);
    $major = $row["umajor"];
}
else {
    $sql_makemaj = "SELECT degree FROM DegreeType";
    $result_makemaj = mysqli_query($mysqli, $sql_makemaj) or die(mysqli_error($mysqli));
    if (mysqli_num_rows($result_makemaj) != 0) {
        $row = mysqli_fetch_assoc($result_makemaj);
        $major = $row["degree"];
    }
}


// Apply changes from previous load if applicable
if (filter_input(INPUT_POST, "submitdels")
        OR filter_input(INPUT_POST, "submitsavereqs")
        OR filter_input(INPUT_POST, "submitelectives")) {
    
    $message = "Changes applied to current plan.<br><br>";
    if (isset($_POST["deleted"])) {
        $message .= "Removed the following courses:<br>";
        $sql_delete = "DELETE FROM DegreeView WHERE uid='$uid' AND ( ";
        foreach ($_POST["deleted"] as $cname) {
            $sql_delete .= " cname='$cname' OR ";
            $message .= "$cname; ";
        }
        $sql_delete = substr($sql_delete, 0, -3)." ) ";
        
        $mysqli->query($sql_delete);
        $message = substr($message, 0, -2)."<br>";
    }
    if (isset($_POST["saved"])) {
        $message .= "Saved the following courses:<br>";
        foreach ($_POST["saved"] as $cname) {
            $sql_insert = "INSERT INTO DegreeView ( dmajor, uid, cname ) "
                    . "VALUES ( '$major', $uid, '$cname' )";
            $message .= "$cname; ";
            $mysqli->query($sql_insert);
        }
        $message = substr($message, 0, -2)."<br>";
    }
}
// Put message at the top of the page if applicable
if (isset($message)) {
    echo "<p class='message'>$message</p>";
}

// Fetch fields from existing DegreeView fields linked to this user, if any
$sql_dview = "SELECT * FROM DegreeView WHERE uid = $uid";
$result_dview = mysqli_query($mysqli, $sql_dview) or die(mysqli_error($mysqli));
$rows_dview = mysqli_fetch_all($result_dview, MYSQLI_BOTH);
if (mysqli_num_rows($result_dview) != 0) {
    $row_dview = $rows_dview[0];
}
// Find out names of all taken courses (for later comparisons)
$saved_courses = array();
foreach ($rows_dview as $row_dview) {
    $saved_courses[] = $row_dview["cname"];
}

// Print what the major is
echo "<h1>Recommended Courses for:</h1>"
    . "<h2>$major</h2>"
    . "<hr>";

// Begin the page-long form
echo "<form action='' method='POST'>";

// ------------------------------

// First section: currently planned courses for user (delete option available)
echo "<h3>Current Plans:</h3>"
. "<table class='courselist'>";
// Results already available from earlier query
if (count($rows_dview) == 0) {
echo "<tr><td class='course'>"
    . "Specify courses below to be saved to your plan."
        . "</td></tr>";
}
foreach ($rows_dview as $row_dview) {
    $cname = $row_dview["cname"];
    ?>
    <tr>
        <td class="course"><?php courselink($cname); ?></td>
        <td class="dviewbtnwide"><?php
        dview_del_btn($uid, $major, $cname); 
        ?></td>
    </tr>
    <?php
} ?>
    <!-- Not currently working... (select all is harder than anticipated)
    <tr>
        <td class="coursespace"></td>
        <td class="dviewbtnwide">
            <label class="checkbox">
                <input type="checkbox" name="delete" class="checkAll">Select All
            </label>
        </td>
    </tr>
    -->
        </table>
        <input type="submit" name="submitdels" value="Apply Changes">

<?php

// ------------------------------


// Second section: all required courses for that degree
echo "<h3>Required Courses:</h3>"
. "<table class='courselist'>";
$sql_getreqs = "SELECT * FROM CourseRequirement "
        . "WHERE degree = '$major'";
$result_getreqs = mysqli_query($mysqli, $sql_getreqs);
$rows_reqs = mysqli_fetch_all($result_getreqs, MYSQLI_ASSOC);
$cnum = 0;
// Also set up list of specific requirements, for later
$req_courses = array();

foreach ($rows_reqs as $row_req) {
    $cnum++;
    $rdesc = $row_req["description"];
    // Only output a course in this section if not electives
    if (stristr($rdesc, 'elective') === false) {
        // Find courses that match this condition
        $req_condition = $row_req["cond"];
        $sql_getcourse = "SELECT * FROM Course WHERE $req_condition";
        $result_getcourse = mysqli_query($mysqli, $sql_getcourse);
        $rows_course = mysqli_fetch_all($result_getcourse, MYSQLI_ASSOC);
        // Fetch and display the first course in the list
        $index = 0;
        $row_course = $rows_course[$index];
        $cname = $row_course["cname"];
        $cspanid = "".$cnum."_".$index;
        $req_courses[] = $cname;
        ?>
    <tr>
        <td class="course">
            <?php echo "<span id='$cspanid'>"; courselink($cname); echo "</span><br>"
            . "<span class='reqdesc'>Requirement: '$rdesc'</span>"; ?>
        </td>
        <?php
        if (mysqli_num_rows($result_getcourse) > 1) { ?>
        <td class="dviewbtn">
            <?php 
            if (!in_array($cname, $saved_courses)) {
                dview_save_btn($uid, $major, $cname); 
            } else {
                echo "<span class='reqdesc'>Saved</span>";
            }
?>
        </td>
        <td class="dviewbtn">
            <?php dview_next_btn($cspanid, $rows_course, $index); ?>
        </td>
        <?php } else {?>
        <td class="dviewbtn">
            <?php 
            if (!in_array($cname, $saved_courses)) {
                dview_save_btn($uid, $major, $cname); 
            } else {
                echo "<span class='reqdesc'>Saved</span>";
            } 
            ?>
        </td>
        <?php } ?>
    </tr>
        <?php
    } // end of case if non-elective course
    else {
        $numcredits = $row_req["credits"];
        ?>
    <tr>
        <td>
            <br>
            <?php echo "$numcredits other credits in: <br>"
            . "<span class='reqdesc'>'$rdesc'</span>"; 
            // Options will be given in next section ?>
        </td>
    </tr>
        <?php
    } 
}
?>
    <!-- Not currently working... (select all is harder than anticipated)
    <tr>
        <td class="coursespace"></td>
        <td class="dviewbtnwide">
            <label class="checkbox">
                <input type="checkbox" name="saveall" class="checkAll">Select All
            </label>
        </td>
    </tr>
    -->
    
        </table>
        <input type="submit" name="submitsavereqs" value="Apply Changes">
<?php

// ------------------------------

// Third section: electives based off user's interests
echo "<h3>Possible Relevant Electives:</h3>"
. "<table class='courselist'>";

// Get user's interests as an array
$sql_getinterests = "SELECT interest FROM User "
        . "WHERE uid = '$uid'";
$result_getinterests = mysqli_query($mysqli, $sql_getinterests);
if (mysqli_num_rows($result_getinterests) > 0) {
    $val_interests = mysqli_fetch_assoc($result_getinterests)["interest"];
    $interests = array_unique(explode(",", $val_interests));
    foreach ($interests as $interest) {
        $iup = strtoupper(trim($interest));
       
        // Find all courses with any text fields matching this interest
        $sql_courseinterests = "SELECT * FROM Course "
                . "WHERE UPPER(cname) LIKE '%$iup%' "
                . "OR UPPER(title) LIKE '%$iup%' "
                . "OR UPPER(description) LIKE '%$iup%' "
                . "OR UPPER(prereq) LIKE '%$iup%' "
                . "OR UPPER(coreq) LIKE '%$iup%' ";
        $result_courseinterests = mysqli_query($mysqli, $sql_courseinterests);
        $rows_suggestions = mysqli_fetch_all($result_courseinterests, MYSQLI_ASSOC);
        $num_rows_suggestions = mysqli_num_rows($result_courseinterests);
        // Return up to 5 matches for each course
        if ($num_rows_suggestions > 0) {
            $index = 0; $count = 0;
            $max = min(array(5, $num_rows_suggestions));
            while ($count < $max AND $index < $num_rows_suggestions) {
                $cname = $rows_suggestions[$index++]["cname"];
                if (!in_array($cname, $saved_courses) AND !in_array($cname, $req_courses)) {
                    echo "<tr><td class='course'>";
                    courselink($cname);
                    echo "</td><td class='dviewbtnwide'>";
                    dview_save_indiv($uid, $major, $cname);
                    echo "</td></tr>";
                    $count++;
                }
            }
        } // TODO: add jQuery or dropdown options to see more
        else { // No suggestions available
            echo "<tr><td class='course'>No results<br>"
            . "<span class='reqdesc'>From interest \"$iup\"</span></td></tr>";
        }
                ?>
            </td>
        </tr>
        <tr><td><span class='reqdesc'>From interest '<?php echo $iup; ?>'</span><br><br></td></tr>
        <?php
    }
}
// If no interests specified
else {
echo "<tr><td class='course'>"
    . "Specify interests in <a href='my_info_edit.php'>your user profile</a> for elective suggestions."
        . "</td></tr>";
}
?>
        </table>
        <input type="submit" name="submitelectives" value="Apply Changes">
        <br>
        <p><a href="validate_degree.php">Check Degree Validity</a></p>
        <br>
        </form>
        
        <br></div>
        <?php include "footer.php" ?>
    </body>
</html>

<?php 

function courselink($cname) {
    // Just echo the variable, for now
    echo $cname;
}

function dview_save($uid, $dmajor, $cname) {
    // eventually, delete course from user's plan using AJAX if present
    echo "";
}
function dview_del($uid, $dmajor, $cname) {
    // eventually, add course to user's plan using AJAX if not present
    echo "";
}
function dview_next($spanid, $options, &$pos) {
    // Eventually, update div with next course in list (or loop to start) using AJAX
    if ($pos < count($options)) {
        $pos++;
    }
    else {
        $pos = 0;
    }
    // TODO: update contents of "spanid" to be courselink of the course named at the new index
}

function dview_save_btn($uid, $dmajor, $cname) {
    echo "<label class='checkbox'>"
    . "<input type='checkbox' class='save' name='saved[]' value='$cname' data-parent='saveall'>Save"
    . "</label>";
}
function dview_save_indiv($uid, $dmajor, $cname) {
    // For a save button not affected by the "save all" type
    echo "<label class='checkbox'>"
    . "<input type='checkbox' class='savei' name='saved[]' value='$cname'>Save"
    . "</label>";
}
function dview_del_btn($uid, $dmajor, $cname) {
    echo "<label class='checkbox'>"
    . "<input type='checkbox' class='delete' name='deleted[]' value='$cname' data-parent='deleteall'>Delete"
    . "</label>";
}
function dview_next_btn($spanid, $options, &$pos) {
    echo "<span class='reqdesc'>* Multiple options available</span>";
}

?>

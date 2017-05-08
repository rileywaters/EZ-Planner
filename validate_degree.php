<!DOCTYPE html>
    
<?php
// Set up session and database connection
session_start();
include "connection.php";

set_time_limit(180); // this script can take a while

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
        <title>Validate Schedule</title>
        
        <link rel="stylesheet" type="text/css" href="ezplan_css.css">
        <style>
            div.resultdiv {
                width:50%;
                margin:auto;
                background-color: #DDF5F8;
                padding: 25px;
                text-align: center;
            }
            img.confirmation {
                display: block;
                margin-left: auto;
                margin-right: auto;
                margin-top: 5px;
                margin-bottom: 5px;
            }
            span.reqdesc {
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
            
        </style>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    </head>
    
    <body>
        <?php include("header.php"); ?>
        <div class="resultdiv">
            
            
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
            
// Fetch fields from existing DegreeView fields linked to this user, if any
$sql_dview = "SELECT * FROM DegreeView, Course WHERE uid = $uid AND DegreeView.cname = Course.cname";
$result_dview = mysqli_query($mysqli, $sql_dview) or die(mysqli_error($mysqli));
$rows_dview = mysqli_fetch_all($result_dview, MYSQLI_BOTH);
if (mysqli_num_rows($result_dview) != 0) {
    $row_dview = $rows_dview[0];
}
// Keep track of full rows of all taken courses (for later comparisons)
$saved_courses = array();
foreach ($rows_dview as $row_dview) {
    $saved_courses[] = $row_dview;
}

// Keep track of any unmet requirements
$unmet = array();
$rem_deg_credits = 0.0 + mysqli_fetch_assoc(
        mysqli_query($mysqli, "SELECT mincredits FROM DegreeType WHERE degree = '$major'")
        )["mincredits"];
$elective_reqs = array();

// Find out conditions for all requirements
$sql_getreqs = "SELECT * FROM CourseRequirement "
        . "WHERE degree = '$major'";
$result_getreqs = mysqli_query($mysqli, $sql_getreqs);
$rows_reqs = mysqli_fetch_all($result_getreqs, MYSQLI_ASSOC);
foreach ($rows_reqs as $row_req) {
    // Find courses that match this condition
    $req_condition = $row_req["cond"];
    $sql_getcourse = "SELECT * FROM Course WHERE $req_condition";
    $result_getcourse = mysqli_query($mysqli, $sql_getcourse);
    $rows_course = mysqli_fetch_all($result_getcourse, MYSQLI_ASSOC);
    if (stristr($row_req["description"], 'elective') === false) {
        $credits_needed = 0.0 + $row_req["credits"];
        $fulfilled = FALSE;

        foreach ($rows_course as $req_course_opt) {
            // If standard courses, ensure the required number of options is taken
            $found = false;
            foreach ($saved_courses as $saved_course) {
                if ($saved_course["cname"] == $req_course_opt["cname"]) {
                    $found = $saved_course;
                }
            }
            $fulfilled = ($fulfilled OR !($found === FALSE)); // double negative because of way array_search returns
            if (!($found === FALSE)) { // if we found something
                $credits_needed -= $found["credits"];
                if ($credits_needed <= 0) {
                    break; // done confirming this req
                }
            }
        }
        // Once all possible courses have been checked,
        // if still not fulfilled, mark this requirement as unmet
        if ($fulfilled === FALSE) {
            $unmet[] = $row_req;
        }
    } else {
        // Add to list of electives (wait until after specific reqs are counted)
        $elective_reqs[] = $row_req;
    }

}
/* Electives seem to take too long currently

foreach ($elective_reqs as $elective_req) {
    // (If electives, just confirm the list has the appropriate number of credits)
    // Find courses that match this condition
    $req_condition = $row_req["cond"];
    $sql_getcourse = "SELECT * FROM Course WHERE $req_condition";
    $result_getcourse = mysqli_query($mysqli, $sql_getcourse);
    $rows_course = mysqli_fetch_all($result_getcourse, MYSQLI_ASSOC);
    $req_credits = $row_req["credits"];
    // This time, go through all user courses and see if it matches an elective
    // (may count multiple times)
    // (Essentially, whenever a course matches an elective option,
    // it subtracts the amount of credits it provides from that requirement)
    foreach ($saved_courses as $saved_course) {
        $found = FALSE;
        foreach ($rows_course as $row_course) {
            if ($saved_course["cname"] == $row_course["cname"]) {
                $found = $saved_course;
            }
        }
        if (!($found === FALSE)) { // if this course matched any options for this elective req
            $req_credits -= $found["credits"];
            if ($req_credits <= 0) {
                break;
            }
        }
        
    }
    // Elective is considered unmet if it has outstanding credits
    if ($req_credits > 0) {
        $unmet[] = $elective_req;
    }
}
 */
// Any courses remaining should be deducted from degree credits remaining
foreach ($saved_courses as $saved_course) {
    $rem_deg_credits -= $saved_course["credits"];
}

// Show results
if (count($unmet) > 0 OR $rem_deg_credits > 0) {
    // Fail
    echo "<img class='confirmation' src='validate_fail.png'><br>";
    echo "<p class='message'>You have unmet requirements:</p>";
    if (count ($unmet) > 0) {
        foreach ($unmet as $unmet_req) {
            echo $unmet_req['description']."<br>";
        }
    }
    if ($rem_deg_credits > 0) {
        echo "<p>".$rem_deg_credits." overall credits</p>";
    }
} else {
    // Success
    echo "<img class='confirmation' src='validate_succeed.png'><br>";
    echo "<p class='message'>All requirements seem to be met.</p>";
}
?>
            <p><span class="reqdesc">
                * Please note that this system cannot check pre-requisites or 
                co-requisites; consult with Academic Advising to confirm that 
                elective requirements are met, and that
                you are able to take all the specified courses.</span>
            </p>
            <p><a href="suggested_schedule.php">Return to Suggested Schedule</a></p>
        </div>
    </body>
</html>

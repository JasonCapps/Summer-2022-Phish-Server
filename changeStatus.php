<?php
/* The backend functionality for the changing the status/grade/comments on the details page provided to the professor. It just takes the submitted form data and sends it to the database, then loops back to the dashboard. */

// Continue the session
session_start();

// Verify that we're logged in as the professor
if($_SESSION['LoggedIn'] = FALSE OR $_SESSION['username'] != 'moleary'){
	include("logout.php");
	}

// Connect to the database
include("connection.php");

// Collect the values that were submitted by the form and set them to our own variables
$id = $_POST["id"];
$selected_status = $_POST["status"];
$grade = $_POST["grade"];
$comment = $_POST["comments"];

// Because an empty string doesn't equal null, we'll first check if any of the values for grade/comment/status are empty, then we can unset them before we make our update to the table. In a practical sense this just means that the professor can update the status or provide a comment before providing a grade or vice versa.
mysqli_query($conn, "use pond;");
if(strlen($comment) ===0){
        unset($comment);
}else{
        $sql1 = mysqli_prepare($conn, "UPDATE pond.grade SET comment =? WHERE id = '$id'");
	mysqli_stmt_bind_param($sql1,"s",$comment);
	if (mysqli_stmt_execute($sql1) === false){
                echo "failure" . mysqli_error($conn);
        }
}
if($selected_status == "--"){
        unset($selected_status);
}else{
	$sql2 = mysqli_prepare($conn, "UPDATE pond.grade SET approval =? WHERE id = '$id'");
	mysqli_stmt_bind_param($sql2,"s",$selected_status);
        if (mysqli_stmt_execute($sql2) === false){
                echo "failure" . mysqli_error($conn);
        }
}
if($grade == "--"){
        unset($grade);
}else{
        $sql3 = mysqli_prepare($conn, "UPDATE pond.grade SET grade =? WHERE id = '$id'");
        mysqli_stmt_bind_param($sql3,"s",$grade);
	if (mysqli_stmt_execute($sql3) === false){
                echo "failure" . mysqli_error($conn);
        }
}

// Back to the dashboard
include("profDashboard.php");

?>

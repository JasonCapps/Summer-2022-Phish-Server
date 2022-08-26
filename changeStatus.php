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
if(strlen($comment) ===0){
	unset($comment);
}else{
	if ((mysqli_query($conn, "UPDATE pond.grade SET comment = '$comment' WHERE id = '$id'")) === 'false');{
		echo mysqli_error($conn);
	}
}
if($selected_status == "--"){
	unset($selected_status);
}else{
	if ((mysqli_query($conn, "UPDATE pond.grade SET grade = '$grade' WHERE id = '$id'")) === 'false');{
		echo mysqli_error($conn);
	}
}
if($grade == "--"){
	unset($grade);
}else{
	if ((mysqli_query($conn, "UPDATE pond.grade SET approval = '$selected_status' WHERE id = '$id'")) === 'false');{
		echo mysqli_error($conn);
	}
}

// Back to the dashboard
include("profDashboard.php");
	
?>

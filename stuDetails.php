<?php
/* The student's version of the professor's details page. In summary it's an exact mirror of the details page albeit without the added functionality of being able to execute the phishing attack or change the grade/status/comments. Students will still see all of the details regarding the selected fishing attack as well as the anisble output/logfile once it's run by the professor. */

session_start();
// Verify login status
if($_SESSION['LoggedIn'] = FALSE){
	include("logout.php");
	}
// Connect to database
include("connection.php");

// Retrieve the id of the selected phishing attack from studentDashboard
$selectedID = $_POST['id'];

//Select database and make our query using the selected id. We'll use left join to bind the columns to a single table for formatting
$getDB = mysqli_query($conn,"USE pond");
$getTable = mysqli_query($conn,"SELECT phish.id, phish.name, phish.submit_date, victim.id,
victim.hostname, victim.username, victim.message, victim.os, payload.file, payload.command, grade.comment, grade.id, grade.approval, grade.grade, logs.id, logs,logs FROM phish LEFT JOIN victim on victim.id = phish.id
LEFT JOIN grade on grade.id = phish.id LEFT JOIN payload on payload.id = phish.id LEFT JOIN logs on logs.id = phish.id WHERE phish.id = '$selectedID'");

//Retrieve all of the values of our selected phish from the output of our query and set them to local variables
$table = mysqli_fetch_assoc($getTable);
$name = $table['name'];
$file = $table['file'];
$logs = $table['logs'];
$date = $table['submit_date'];
$hostname = $table['hostname'];
$username = $table['username'];
$command = $table['command'];
$approval = $table['approval'];
$grade = $table['grade'];
$comment = $table['comment'];
$message = $table['message'];
$os = $table['os'];

?>
<!DOCTYPE HTML>

<html>
  <head>
  <meta name = "viewport" content="width=device-width, initial-scale=1">	<!-- Provides formatting for certain div containers -->
  <style>

* {
	box-sizing: border-box;
}

input[type=text]{
	width: 150px;
}

.column-left{
	float: left;
	width: 33%;
	padding: 10px;
}

.column-right{
	float: right;
	width: 33%;
	padding: 10px;
}

.column-center{
	display: inline-block;
	width: 33%;
	padding: 10px;
}


.submit-button2 {
	background-color: #4CAF50;
	border: 1px solid black;
	font-size: 25px;
	height: 50px;
	width: 180px;
	margin: auto;
	text-align: center;
	display: inline-block;
}

.button-refresh {
	border: 1px solid black;
	font-size: 12px;
	height:15px;
	width25px;
}

div.first {
	display: inline-block;
	width: 180px;
	height: 50px;
}

div.second {
	color: black; 
	border: 2px solid black;
	margin: auto;
	font-size: 24px;
	text-align: center;
	position: relative;
	top: 5px;
}

div.third {
	text-align: center;
	display: block;
	position: absolute;
	overflow: scroll;
	height: 700px;
	width: 800px;
	top: 75%;
	left: 50%;
	transform: translate(-50%, -50%);
}

</style>
        <link rel="stylesheet" href="scss/bootstrap.min.css">
        <script src="dependencies/bootstrap.min.js"></script>
        <script src="jquery.js"></script>
</head>
  <!-- Create the 5 menu buttons to navigate each of the professor php files -->
  <body style = "background-color: #EAEAED";>
<nav class="navbar navbar-expand shadow" style="background-color: #e3f2fd;">
  <div class="container-fluid">
    <a class="navbar-brand">
      <img src="img/logo.jpg" alt="Logo" width="60" height="48" class="d-inline-block align-text-top">
    </a>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link" href="studentDashboard.php">Dashboard</a>
        <a class="nav-link" href="goPhish.php">Go-Phish</a>
        <a class="nav-link" href="logout.php">Logout</a>
      </div>
    </div>
  </div>
</nav>
    <!-- Use our declared variables to output all of the data retrieved from the query to the page -->

   <div class="container-fluid py-4">
     <div class="row gy-3">
       <div class="col-xxl">
         <label class="form-label">Phish Title</label>
         <input type="text" class="form-control w-100" name="title" value="<?php echo $name ?>"readonly>
         <label class="form-label">Date and Time Submitted</label>
         <input type="text" class="form-control w-100" name="date" value="<?php echo $date ?>"readonly>
         <label class="form-label">Message</label>
         <textarea class="form-control" rows="6" readonly><?php echo $message ?></textarea>
       </div>
       <div class="col-md">
                <label class="form-label">Victim Hostname </label>
                <input type="text" name="hostname" class="form-control w-100" value="<?php echo $hostname ?>"readonly>
                <label class="form-label">Target OS </label>
                <input type="text" name="hostname" class="form-control w-100" value="<?php echo $os ?>"readonly>
                <label class="form-label">Victim Username</label>
                <input type="text" name="username" class="form-control w-100" value="<?php echo $username ?>"readonly>
                <label class="form-label">File Name </label>
                <input type="text" name="fileName" class="form-control w-100" value="<?php echo $file ?>"readonly>
                <label class="form-label">Phishing Link </label>
                <input type="text" name="command" class="form-control w-100" value="<?php echo $command ?>"readonly>
       </div>
       <div class="col-xl">
        <form method = "POST" action="changeStatus.php">
        <input type="hidden" id="id" name="id" value="<?php echo $id ?>">
        <div class="row gy-3">
          <div class="col-sm-2">
            <label class="form-label">Current Status: </label>
          </div>
          <div class="col-sm-4">
            <input type="text" class="form-control" value="<?php echo $approval ?>"  style="max-width: 7.75rem" readonly></input>
          </div>
        </div>
        <div class="row gy-3">
          <div class="col-sm-2">
            <label class="form-label">Current Grade: </label>
          </div>
          <div class="col-sm-4">
            <input type="text" class="form-control"  style="max-width: 7.75rem" value="<?php echo $grade ?>" readonly></input>
          </div>
        </div>
        <label class="form-label">Comments </label><textarea name="comments" id ="comments" class="form-control" rows="6" readonly><?php echo $comment ?></textarea><br>
      </form>
     </div>
    </div>
   </div>

<div class="container-fluid">
  <div class="row d-flex justify-content-md-center justify-content-start py-5">
    <div class="card" style="min-width: 45rem; width: 60rem;">
      <div class="card-body">
          <?php readfile("/usr/local/phish/logs/" . "$logs"); ?>
      </div>
    </div>
  </div>
</div>

</body>
<html>


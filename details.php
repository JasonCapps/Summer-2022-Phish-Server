<?php
/* This page loads upon the 'details' buttons provided to the professor on either the profDashboard or profGrades page, and displays all of 
the information regarding the selected fishing attack from the database and displays it in a more digestible format. Furthermore, it
is on this page that the professor can change the grade, approval status, or add comments to the selected fishing attack, as well
as execute the attack which will run the ansible playbook and display the output of the phishing attack in a new text box. */

session_start();
// Check that the user is the professor and logged in
if($_SESSION['LoggedIn'] = FALSE OR $_SESSION['username'] != 'moleary'){
	include("logout.php");
	}
// Connect to the database
include("connection.php");

// Retrieve the id of the selected fishing attack from the button located in the table from either profGrades.php or profDashboard.php
$id = $_POST['id'];

/* Make our queries; in order to display the information in a single table with html/css, we need to select the columns we want and join
our tables together with left join on the condition that each of the id primary keys for each table match */
$getDB = mysqli_query($conn,"USE pond");
$getTable = mysqli_query($conn,"SELECT phish.id, phish.name, phish.submit_date, victim.id,
victim.hostname, victim.username, victim.message, victim.os, payload.id, payload.file, payload.command, grade.comment, grade.id, grade.approval, grade.grade, logs.id, logs.logs FROM phish LEFT JOIN victim on victim.id = phish.id LEFT JOIN payload on payload.id = phish.id
LEFT JOIN grade on grade.id = phish.id LEFT JOIN logs on logs.id = phish.id WHERE phish.id = '$id'");

// We'll fetch the result of our query and set it as our variable $table, then we snatch the value in each column from our selected phish
$table = mysqli_fetch_assoc($getTable);
$logs = $table['logs'];
$name = $table['name'];
$date = $table['submit_date'];
$hostname = $table['hostname'];
$username = $table['username'];
$approval = $table['approval'];
$grade = $table['grade'];
$comment = $table['comment'];
$message = $table['message'];
$os = $table['os'];
$file = $table['file'];
$command = $table['command'];


?>
<!DOCTYPE HTML>

<html>
  <head>
  <meta name = "viewport" content="width=device-width, initial-scale=1">	<!-- Helps with formatting certain div elements -->
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
</head>
  <!-- Create the 5 menu buttons to navigate each of the professor php files, each is linked to their respective php page -->
  <body style = "background-color: #EAEAED";>
    <div class="first">
      <form method = "POST" action="profDashboard.php">
        <input type="submit" class="submit-button2" value="Dashboard">
      </form>
    </div>
    <div class="first">
      <form method = "POST" action="profGrades.php">
        <input type="submit" class="submit-button2" value="Grades">
      </form>
    </div>
    <div class="first">
      <form method = "POST" action="logout.php">
        <input type="submit" class="submit-button2" value="Logout">
      </form>
    </div>
    <!-- HTML form for changing the status AND grade for a phishing attempt; cannot currently change them independently -->
    
    
<!-- execute button HTML form; like changing the status it operates based on id number selection -->
    <div style= "position:relative; top:10px; width:280px;">
      <form method = "POST" action="execute.php">
        <input type="hidden" id="id" name="id" value="<?php echo $id ?>">
        <input type="submit" value="!!!Execute!!!" style="color: red;">
      </form>
    </div>
      	<div class = 'column-left'>
      		Phish Title: <input type="text" name="title" value="<?php echo $name ?>"readonly><br>
      		Date and Time Submitted: <input type="text" name="date" value="<?php echo $date ?>"readonly><br>
      		Message: <textarea name="message" id ="message"cols="50" rows="5" readonly><?php echo $message ?></textarea><br>
      	</div><div class = 'column-center'>
      		Victim Hostname: <input type="text" name="hostname" value="<?php echo $hostname ?>"readonly><br>
      		Target OS: <input type="text" name="hostname" value="<?php echo $os ?>"readonly><br>
      		Victim Username: <input type="text" name="username" value="<?php echo $username ?>"readonly><br>
      		File Name: <input type="text" name="fileName" value="<?php echo $file ?>"readonly><br>
      		Command: <input type="text" name="command" value="<?php echo $command ?>"readonly>
      	</div><div class = 'column-right'>
      	<form method = "POST" action="changeStatus.php">	<!-- this handles the professor's ability to change grade/status/comments -->
        <input type="hidden" id="id" name="id" value="<?php echo $id ?>">
        <label for="status">Select Status:</label>
            <select id="status" name="status">
              <option value=""><?php echo $approval ?></option>
              <option value="Rejected">Rejected</option>
	      <option value="Approved">Approved</option>
            </select><br>
        <label for="grade">Select Grade:</label>
            <select id="grade" name="grade">
              <option value=""><?php echo $grade?></option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
              <option value="F">F</option>
            </select><br>
        Comments: <br><textarea name="comments" id ="comments"cols="50" rows="5"><?php echo $comment ?></textarea><br>
        <input type="submit" value="Change Status/Grade/Comment">
      </form>
        <!-- Retrieves the output of running the phishing attack and displays it; if there's no log there's no output -->
      	<div style="text-align: center;
	display: inline-block;
	position: absolute;
	width: 1000px;
	outline: solid black 1px;
	background-color: white;
	top: 70%;
	left: 50%;
	overflow: auto;
	transform: translate(-50%, -50%);">
      		<?php readfile("/usr/local/phish/logs/" . "$logs"); ?>      		
      	</div>  
</body>
<html>


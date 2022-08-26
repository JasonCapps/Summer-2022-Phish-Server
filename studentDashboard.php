<!DOCTYPE HTML>
<!-- As you might guess, this is the student homepage/dashboard file. It's the first thing they see when they log in, and will display each
of their submitted phishing attacks albeit with sparce detail. From this page students can go to goPhish.php or stuDetails.php -->

<html>
<style>

input[type=text]{
	width: 15px;
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
	display: inline-block;
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
}

	<!-- website navigation buttons -->
</style>
  <body style = "background-color: #EAEAED";>
    <div class="first">
<form method = "POST" action="studentDashboard.php">
  <input type="submit" class="submit-button2" value="Dashboard">
</form>
    </div>
    <div class="first">
<form method = "POST" action="goPhish.php">
  <input type="submit" class="submit-button2" value="Go-Phish">
</form>
    </div>
    <div class="first">
<form method = "POST" action="logout.php">
  <input type="submit" class="submit-button2" value="Logout">
</form>
    </div>
  </body>
<html>

<?php
// Continue the session so that we can keep our variables and login status
session_start();

// Check that we're still logged in, if not send them out of the website	
if($_SESSION['LoggedIn'] = FALSE){
	include("logout.php");
	}

// Connect to the database
include("connection.php");

// Welcome message using session username (team name)
echo "<div class='second'>" . "Welcome Team "
 . $_SESSION['username'] . "!" . "</div>";

// Create a local variable for our team name for ease of use
$username = $_SESSION['username'];
	
// Make our query, joining our tables and selecting only rows created by our selected team
$getDB = mysqli_query($conn,"USE pond;");													
$phishTable = mysqli_query($conn,"SELECT phish.id, phish.name, phish.submit_date, victim.id,
victim.hostname, victim.username, grade.id, grade.approval, grade.grade FROM phish LEFT JOIN victim on victim.id = phish.id LEFT JOIN grade on grade.id = phish.id WHERE phish.country = '$username' ORDER BY phish.submit_date DESC");

/* Create and populate a table for the data from our query. We'll first make our div container and specify some formatting details. Then we'll
create and name each of our columns. To populate our new columns, we'll use a while loop and fetch the output of our query. For each iteration, 
the loop will pull the value in each column for their corresponding $row or 'entry'. The values in each $row[''] variable are the actual
column names as they are stored in the database.

Below the grade entry, we have the creation of a button that will link each table entry to their respective details page. To do this, we 
create a form with a hidden value that contains that entries' id as it is stored in the database. We'll then pass this id value to the details.php
page upon pressing the submit button. */
echo "<div style='float: left; width: 35%; padding: 10px; text-align: center;'>";						
echo "<table border='1' style='background-color: white'>
<tr>
<th>Title</th>
<th>Timestamp</th>
<th>Hostname</th>
<th>Username</th>
<th>Approval</th>
<th>Grade</th>
<th>Details</th>
</tr>";
	
while($row= mysqli_fetch_array($phishTable))
{
echo "<tr>";
echo "<td>" . $row['name'] . "</td>";
echo "<td>" . $row['submit_date'] . "</td>";
echo "<td>" . $row['hostname'] . "</td>";
echo "<td>" . $row['username'] . "</td>";
echo "<td>" . $row['approval'] . "</td>";
echo "<td>" . $row['grade'] . "</td>";
echo "<td><form method = 'POST' action = 'stuDetails.php'>
	<input type='text' name='id' value = '$row[id]' hidden>
	<button type = 'submit' value='sendID'>Click</button>
	</form></td>";
echo "</tr>";
}
echo "</table>";
?>

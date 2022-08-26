<!DOCTYPE HTML>
<!-- This page provides a collection of all of the graded entries for each team with minimal detail, but retains the ability to display the logs associated with each executed phishing attack -->
<html>
  <head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script type="text/javascript" src="getGrades.js"></script>
  </head>
  <style>

input[type=text] {
	width: 20px;
	height: auto;
	text-align: center;
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
	background-color: red;
	border: 1px solid black;
	font-size: 12px;
	height: 30px;
	width: 50px;
	display: inline-block;
	text-align: center;
	maring: auto;
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
	font-size: 20px;
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

</style><!-- Website navigation buttons -->
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
    <!-- These buttons are linked to the javascript file that refreshes the table with whatever entries are associated with that team. For this file that'd be getGrades.js -->
    <div class="second">
      <button id="france">France</button>
      <button id="spain">Spain</button>
      <button id="england">England</button>
      <button id="germany">Germany</button>
      <button id="rome">Rome</button>
      <button id="russia">Russia</button>
      <button id="japan">Japan</button>
      <button id="china">China</button>
    </div>

<?php
// Continue our session to retain variables and login status
session_start();

// Verify that we are logged in as the professor
if($_SESSION['LoggedIn'] = FALSE OR $_SESSION['username'] != 'moleary'){
	include("logout.php");
	}
	
// Connect to the database
include("connection.php");

// Make our query and get the selected columns for output to the table, as well as the logs for each entry. Upon loading the page initially, the default team displayed is france. Entries are listed in order of most recent to least recent.
$getDB = mysqli_query($conn,"USE pond");
$getTable = mysqli_query($conn,"SELECT phish.id, phish.country, phish.name, phish.submit_date, victim.id,
victim.hostname, victim.username, grade.id, grade.approval, grade.grade, logs.logs, logs.id FROM phish LEFT JOIN victim on victim.id = phish.id
LEFT JOIN grade on grade.id = phish.id LEFT JOIN logs on logs.id = phish.id WHERE phish.country = 'france' AND grade.grade IS NOT NULL ORDER BY phish.submit_date DESC;");

/* Create a div container and give it a proper id. This id will be used by the javascript file to rewrite the contents inside that div element. Initially we'll declare our table's columns and their names, as well as a few formatting details. We'll then use a while function and retrieve the output of the query we made earlier while repeatedly populating the rows of the table with every entry in the database that matched our criteria. Every instance of $row[''] uses the column names as they are found in the database.

Below the grade entry, we have the creation of a button that will link each table entry to their respective details page. To do this, we 
create a form with a hidden value that contains that entries' id as it is stored in the database. We'll then pass this id value to the details.php
page upon pressing the submit button.

We'll do much the same in loading the log file for each entry. The form sends the loaded log filename to the reloaded page which then writes its contents as output visible to the user. */
echo "<div id='getGrades' style='float: left; width: 35%; padding: 10px; text-align: center;'>";
echo "<table border='1' style='background-color: white;'>
<tr>
<th>Title</th>
<th>Timestamp</th>
<th>Approval</th>
<th>Grade</th>
<th>Details</th>
<th>Logs</th>
</tr>";

while($row= mysqli_fetch_array($getTable))
{
echo "<tr>";
echo "<td>" . $row['name'] . "</td>";
echo "<td>" . $row['submit_date'] . "</td>";
echo "<td>" . $row['approval'] . "</td>";
echo "<td>" . $row['grade'] . "</td>";
echo "<td><form method = 'POST' action = 'details.php'>
	<input type='text' name='id' value = '$row[id]' hidden>
	<button type = 'submit' value='sendID'>Click</button>
	</form></td>";
echo "<td><form method='POST' action='profGrades.php'>
	<input type='text' name='filename' value = '$row[logs]' hidden>
	<button type = 'submit' value='sendLogs'>Click</button>
	</form></td>";
echo "</tr>";
}
echo "</table>";
echo "</div>";

//Get our selected log file and output it to the new page
$logFile = $_POST['filename'];

?>
<div style='float: left; width: 60%; padding: 10px; overflow: auto;'">
      		<?php readfile("/usr/local/phish/logs/" . "$logFile"); ?>      		
      	</div>
  </body>
<html>




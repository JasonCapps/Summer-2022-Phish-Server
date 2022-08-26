<!DOCTYPE HTML>
<!-- The first page the professor sees upon logging in. Upon doing so he'll see all of the entries from team france (by default) with some detail, along with buttons that lead to a details page for each entry and a grades page that shows every graded entry per team. This page uses ajax/jquery to allow the professor to retrieve database information and refresh the table without reloading the page. -->

<html>
  <head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script type="text/javascript" src="getTables.js"></script>
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

</style>
  <!-- Website navigation buttons -->
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
    <!-- These buttons are linked to the javascript file that refreshes the table with whatever entries are associated with that team. For this file that'd be getTables.js -->
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
// Continue our session to preserve variables and login status
session_start();

// Check that we're logged in as the professor
if($_SESSION['LoggedIn'] = FALSE OR $_SESSION['username'] != 'moleary'){
	include("logout.php");
	}

// Connect to the database
include("connection.php");

// We make our query and join each of our selected columns to a single table. The ouput displays the contents of team france by default
$getDB = mysqli_query($conn,"USE pond");
$getTable = mysqli_query($conn,"SELECT phish.id, phish.country, phish.name, phish.submit_date, victim.id,
victim.hostname, victim.username, grade.id, grade.approval, grade.grade FROM phish LEFT JOIN victim on victim.id = phish.id
LEFT JOIN grade on grade.id = phish.id WHERE phish.country = 'france' ORDER BY phish.submit_date DESC");

/* Create a div container and give it a proper id. This id will be used by the javascript file to rewrite the contents inside that div element. Initially we'll declare our table's columns and their names, as well as a few formatting details. We'll then use a while function and retrieve the output of the query we made earlier while repeatedly populating the rows of the table with every entry in the database that matched our criteria. Every instance of $row[''] uses the column names as they are found in the database.

Below the grade entry, we have the creation of a button that will link each table entry to their respective details page. To do this, we 
create a form with a hidden value that contains that entries' id as it is stored in the database. We'll then pass this id value to the details.php
page upon pressing the submit button. */
echo "<div id='getTables' style='float: left; width: 35%; padding: 10px; text-align: center;'>";
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

while($row= mysqli_fetch_array($getTable))
{
echo "<tr>";
echo "<td>" . $row['name'] . "</td>";
echo "<td>" . $row['submit_date'] . "</td>";
echo "<td>" . $row['hostname'] . "</td>";
echo "<td>" . $row['username'] . "</td>";
echo "<td>" . $row['approval'] . "</td>";
echo "<td>" . $row['grade'] . "</td>";
echo "<td><form method = 'POST' action = 'details.php'>
	<input type='text' name='id' value = '$row[id]' hidden>
	<button type = 'submit' value='sendID'>Click</button>
	</form></td>";

echo "</tr>";
}
echo "</table>";
echo "</div>";
?>
  </body>
<html>




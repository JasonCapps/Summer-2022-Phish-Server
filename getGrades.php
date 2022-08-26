<?php
/* Essentially a mirror of the same code used in profGrades.php used to display the tables after retrieving all of the database information for the selected team. The only difference is that this uses the value passed by the javascript file to set the team value instead of using a string like the original. */

// Continue our session
session_start();

// Check login status
if($_SESSION['LoggedIn'] = FALSE OR $_SESSION['username'] != 'moleary'){
	include("logout.php");
	}

// Connect to the database
include("connection.php");

// Get the string value of the country that was selected from the javascript file
$selected_country = $_POST["selected_country"];

// Make our query (the same as in profGrades.php) only now using our selected country string value
$getDB = mysqli_query($conn,"USE pond");
$getTable = mysqli_query($conn,"SELECT phish.id, phish.name, phish.submit_date, victim.id,
victim.hostname, victim.username, grade.id, grade.approval, grade.grade, logs.id, logs.logs FROM phish LEFT JOIN victim on victim.id = phish.id
LEFT JOIN grade on grade.id = phish.id LEFT JOIN logs on logs.id = phish.id WHERE phish.country = '$selected_country' AND grade.grade IS NOT NULL ORDER BY phish.submit_date DESC;");

/* Create a div container and give it a proper id. This id will be used by the javascript file to rewrite the contents inside that div element. Initially we'll declare our table's columns and their names, as well as a few formatting details. We'll then use a while function and retrieve the output of the query we made earlier while repeatedly populating the rows of the table with every entry in the database that matched our criteria. Every instance of $row[''] uses the column names as they are found in the database.

Below the grade entry, we have the creation of a button that will link each table entry to their respective details page. To do this, we 
create a form with a hidden value that contains that entries' id as it is stored in the database. We'll then pass this id value to the details.php
page upon pressing the submit button.

We'll do much the same in loading the log file for each entry. The form sends the loaded log filename to the reloaded page which then writes its contents as output visible to the user. */
echo "<table border='1' style='background-color: white'>
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
	<input type = 'text' name = 'selectID' value='".$row['id']."' hidden readonly>
	<button type = 'submit' value='sendID'>Click</button>
	</form></td>";
echo "<td><form method='POST' action='profGrades.php'>
	<input type='text' name='filename' value = '$row[logs]' hidden>
	<button type = 'submit' value='sendLogs'>Click</button>
	</form></td>";

echo "</tr>";
}
echo "</table>";

?>


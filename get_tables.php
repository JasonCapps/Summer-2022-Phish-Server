<?php
/* This file is essentially a mirror of the same code used in  profDashboard.php used to retrieve informatio from the database and output it as a formatted table to the webpage. This file is used in union with getTables.js to allow the professor to change the contents of the table to that of the selected country without refreshing the webpage. */

// Continue the session to preserve variables and login status
session_start();

// Verify that we're logged in as the professor
if($_SESSION['LoggedIn'] = FALSE OR $_SESSION['username'] != 'moleary'){
	include("logout.php");
	}
	
//Connect to the database
include("connection.php");
// Retrieve the selected country string value from the javascript file
$selected_country = $_POST["selected_country"];
// Use our selected team value to make another query to the database. The query mirrors that of the one in profDashboard but with a dynamic value for phish.country.

$_SESSION['country'] = $selected_country;
$getDB = mysqli_query($conn,"USE pond");
$getTable = mysqli_query($conn,"SELECT phish.id, phish.name, phish.submit_date, victim.id,
victim.hostname, victim.username, grade.id, grade.approval, grade.grade FROM phish LEFT JOIN victim on victim.id = phish.id
LEFT JOIN grade on grade.id = phish.id WHERE phish.country = '$selected_country' ORDER BY phish.submit_date DESC");

/* Create a div container and give it a proper id. This id will be used by the javascript file to rewrite the contents inside that div element. Initially we'll declare our table's columns and their names, as well as a few formatting details. We'll then use a while function and retrieve the output of the query we made earlier while repeatedly populating the rows of the table with every entry in the database that matched our criteria. Every instance of $row[''] uses the column names as they are found in the database.

Below the grade entry, we have the creation of a button that will link each table entry to their respective details page. To do this, we 
create a form with a hidden value that contains that entries' id as it is stored in the database. We'll then pass this id value to the details.php
page upon pressing the submit button. */
echo "<table class='table table-hover table-responsive-lg'>
<thead>
<tr>
<th scope='col'>Title</th>
<th scope='col'>Timestamp</th>
<th scope='col'>Hostname</th>
<th scope='col'>Username</th>
<th scope='col'>Approval</th>
<th scope='col'>Grade</th>
<th scope='col'>Details</th>
</tr>
</thead>
<tbody>";
while($row= mysqli_fetch_array($getTable))
{
echo "<tr>";
echo "<th scope='row'>" . $row['name'] . "</th>";
echo "<td>" . $row['submit_date'] . "</td>";
echo "<td>" . $row['hostname'] . "</td>";
echo "<td>" . $row['username'] . "</td>";
echo "<td>" . $row['approval'] . "</td>";
echo "<td>" . $row['grade'] . "</td>";
echo "<td><form method = 'POST' action = 'details.php'>
        <input type='text' name='id' value = '$row[id]' hidden>
        <button type = 'submit' class='btn btn-outline-primary' value='sendID'>Click</button>
        </form></td>";

echo "</tr>";
}
echo "</tbody>";
echo "</table>";
echo "</div>";
?>


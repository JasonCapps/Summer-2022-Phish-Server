<!DOCTYPE HTML>
<!-- As you might guess, this is the student homepage/dashboard file. It's the first thing they see when they log in, and will display each
of their submitted phishing attacks albeit with sparce detail. From this page students can go to goPhish.php or stuDetails.php -->

<html>
  <head>
    <script type="text/javascript" src="jquery.js"></script>
        <link rel="stylesheet" href="scss/bootstrap.min.css">
        <script src="dependencies/bootstrap.min.js"></script>
  </head>
	<!-- website navigation buttons -->

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
<div class="container-md py-5">
<div class="card shadow">

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
// Create a local variable for our team name for ease of use
$username = $_SESSION['username'];

echo "<h5 class='card-header'>Welcome Team $username!</h5>
<div class='card-body overflow-auto'>
<table class='table table-hover table-responsive-lg'>
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
// Make our query, joining our tables and selecting only rows created by our selected team
$getDB = mysqli_query($conn,"USE pond;");													
$phishTable = mysqli_query($conn,"SELECT phish.id, phish.name, phish.submit_date, victim.id,
victim.hostname, victim.username, grade.id, grade.approval, grade.grade FROM phish LEFT JOIN victim on victim.id = phish.id LEFT JOIN grade on grade.id = phish.id WHERE phish.country = '$username' ORDER BY phish.submit_date DESC");

while($row= mysqli_fetch_array($phishTable))
{
echo "<tr>";
echo "<th scope='row'>" . $row['name'] . "</th>";
echo "<td>" . $row['submit_date'] . "</td>";
echo "<td>" . $row['hostname'] . "</td>";
echo "<td>" . $row['username'] . "</td>";
echo "<td>" . $row['approval'] . "</td>";
echo "<td>" . $row['grade'] . "</td>";
echo "<td><form method = 'POST' action = 'stuDetails.php'>
        <input type='text' name='id' value = '$row[id]' hidden>
        <button type = 'submit' class='btn btn-outline-primary' value='sendID'>Click</button>
        </form></td>";

echo "</tr>";
}
echo "</tbody>";
echo "</table>";
echo "</div>";
/* Create and populate a table for the data from our query. We'll first make our div container and specify some formatting details. Then we'll
create and name each of our columns. To populate our new columns, we'll use a while loop and fetch the output of our query. For each iteration, 
the loop will pull the value in each column for their corresponding $row or 'entry'. The values in each $row[''] variable are the actual
column names as they are stored in the database.

Below the grade entry, we have the creation of a button that will link each table entry to their respective details page. To do this, we 
create a form with a hidden value that contains that entries' id as it is stored in the database. We'll then pass this id value to the details.php
page upon pressing the submit button. */
?>
      </div>
    </div>
  </body>
<html>

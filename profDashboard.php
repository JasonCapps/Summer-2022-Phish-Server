<!DOCTYPE HTML>
<!-- The first page the professor sees upon logging in. Upon doing so he'll see all of the entries from team france (by default) with some detail, along with buttons that lead to a details page for each entry and a grades page that shows every graded entry per team. This page uses ajax/jquery to allow the professor to retrieve database information and refresh the table without reloading the page. -->

<html>
  <head>
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="getTables.js"></script>
        <link rel="stylesheet" href="scss/bootstrap.min.css">
        <script src="dependencies/bootstrap.min.js"></script>
  </head>
  <body style = "background-color: #EAEAED";>
  <!-- Website navigation buttons -->
<nav class="navbar navbar-expand shadow" style="background-color: #e3f2fd;">
  <div class="container-fluid">
    <a class="navbar-brand">
      <img src="img/logo.jpg" alt="Logo" width="60" height="48" class="d-inline-block align-text-top">
    </a>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link" href="profDashboard.php">Dashboard</a>
        <a class="nav-link" href="logout.php">Logout</a>
      </div>
    </div>
  </div>
</nav>

<div class="container-md py-5">
<div class="card shadow">
  <div class="card-body overflow-auto">
    <ul class="nav nav-tabs justify-content-center">
      <li class="nav-item" type="button" data-bs-toggle="tab" data-bs-target="#france" role="tab">
        <a class="nav-link" id="france">France</a>
      </li>
      <li class="nav-item" type="button" data-bs-toggle="tab" data-bs-target="#england" role="tab">
        <a class="nav-link" id="england">England</a>
      </li>
      <li class="nav-item" type="button" data-bs-toggle="tab" data-bs-target="#spain" role="tab">
        <a class="nav-link" id="spain">Spain</a>
      </li>
      <li class="nav-item" type="button" data-bs-toggle="tab" data-bs-target="#germany" role="tab">
        <a class="nav-link" id="germany">Germany</a>
      </li>
      <li class="nav-item" type="button" data-bs-toggle="tab" data-bs-target="#rome" role="tab">
        <a class="nav-link" id="rome">Rome</a>
      </li>
      <li class="nav-item" type="button" data-bs-toggle="tab" data-bs-target="#russia" role="tab">
        <a class="nav-link" id="russia">Russia</a>
      </li>
      <li class="nav-item" type="button" data-bs-toggle="tab" data-bs-target="#japan" role="tab">
        <a class="nav-link" id="japan">Japan</a>
      </li>
      <li class="nav-item" type="button" data-bs-toggle="tab" data-bs-target="#china" role="tab">
        <a class="nav-link" id="china">China</a>
      </li>
    </ul>

    <!-- These buttons are linked to the javascript file that refreshes the table with whatever entries are associated with that team. For this file that'd be getTables.js -->

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

if(isset($_SESSION['country'])) {
  $getTable = mysqli_query($conn,"SELECT phish.id, phish.country, phish.name, phish.submit_date, victim.id,
  victim.hostname, victim.username, grade.id, grade.approval, grade.grade FROM phish LEFT JOIN victim on victim.id = phish.id
  LEFT JOIN grade on grade.id = phish.id WHERE phish.country = '" . $_SESSION['country'] . "' ORDER BY phish.submit_date DESC");
  $sessionVar = $_SESSION['country'];
  echo "<script type='text/Javascript'>
	  var sessionVar = '$sessionVar';
          var element = document.getElementById(sessionVar)
          element.classList.add('active');
  </script>";
} else {
  $_SESSION["country"] = 'france';
  $getTable = mysqli_query($conn,"SELECT phish.id, phish.country, phish.name, phish.submit_date, victim.id,
  victim.hostname, victim.username, grade.id, grade.approval, grade.grade FROM phish LEFT JOIN victim on victim.id = phish.id
  LEFT JOIN grade on grade.id = phish.id WHERE phish.country = 'france' ORDER BY phish.submit_date DESC");
  echo "<script type='text/Javascript'>
          var element = document.getElementById('france')
          element.classList.add('active');
  </script>";
}
/* Create a div container and give it a proper id. This id will be used by the javascript file to rewrite the contents inside that div element. Initially we'll declare our table's columns and their names, as well as a few formatting details. We'll then use a while function and retrieve the output of the query we made earlier while repeatedly populating the rows of the table with every entry in the database that matched our criteria. Every instance of $row[''] uses the column names as they are found in the database.

Below the grade entry, we have the creation of a button that will link each table entry to their respective details page. To do this, we 
create a form with a hidden value that contains that entries' id as it is stored in the database. We'll then pass this id value to the details.php
page upon pressing the submit button. */
echo "<div id='getTables'>";
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
      </div>
    </div>
    </div>
  </body>
<html>




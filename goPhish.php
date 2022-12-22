<?php
/* This page displays the form for students when attempting to submit a phishing attack and is paired with getPhish.php which then retreives and processes the data. getPhish.js only provides the functionality of changing the form per the selected radio buttons for either 'file' or 'command', and is not linked to getPhish.php in any way. */

// Continue the session to preserve login status and variables
session_start();

// Make sure we're still logged in so we have a team to attribute the phish to
if($_SESSION['LoggedIn'] = FALSE){
	include("logout.php");
	}	
?>

<!DOCTYPE HTML>

<html>
<head>
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="getPhish.js"></script>
        <link rel="stylesheet" href="scss/bootstrap.min.css">
        <script src="dependencies/bootstrap.min.js"></script>
</head>

<!-- Website navigation buttons -->
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

<div class="container-sm py-4">
  <div class="card shadow">
    <h5 class="card-header">Complete the following fields for your submission:</h5>
      <div class="card-body">
        <form id="form-phish" action="getPhish.php" method="POST" enctype="multipart/form-data">
          <label class="form-label">Title</label>
	  <input type="text" class="form-control" name="title" required placeholder="Name of your phishing email" title="Name of your phishing email"><br>
          <label class="form-label" for="country">Target Country:</label>
            <select id="country" class="form-select" name="country">
              <option value="france">France</option>
              <option value="spain">Spain</option>
              <option value="germany">Germany</option>
              <option value="england">England</option>
              <option value="rome">Rome</option>
              <option value="japan">Japan</option>
              <option value="china">China</option>
              <option value="russia">Russia</option>
            </select><br>
	  <div class="row g-4">
	    <div class="col-md-6">
	      <label class="form-label">Hostname</label>
	      <input type="text" name="hostname" class="form-control" placeholder="ex. athens.intel.greece" pattern="[a-z]{1,15}\.[a-z]{1,10}\.[a-z]{1,10}" required title="Lowercase FQDN ex. &quot;athens.intel.greece&quot;">
	    </div>
            <div class="col-md-6">
              <label class="form-label">User</label>
	      <input type="text" name="user" class="form-control" placeholder="ex. zeus" pattern="[a-z]{1,10}" required title="Lowercase username ex. zeus">
            </div>
	    <div class="col-md-2">
              <label class="form-label" for="os">Operating System:</label>
	    </div>
	    <div class="col-md-10">
                <select class="form-select" id="os" name="os">
                <option value="linux">Linux</option>
                <option value="Windows">Windows</option></select></br>
	    </div>
	    <div class="col-md-2">
	      <label class="form-label" for="os">File</label>
	    </div>
	    <div class="col-md-10">
	      <input class="form-control" name="fileUpload" type="file" id="fileUpload">
	    </div>
	  </div>
          <label class="form-label">Text/Message</label>
          <textarea class="form-control" name="text" rows="10" maxlength="5000"></textarea><br>
            <button type="submit" value="submit" class="btn btn-danger">Submit</button>
        </form>
  </div>
</div>
</div>

	</body>
<html>

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
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script type="text/javascript" src="getPhish.js"></script>
</head>
<style>
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
	top: 20%;
	left: 50%;
	transform: translate(-50%, -50%);
}

</style>
<!-- Website navigation buttons -->
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
    <div>
    	<!-- An unreasonably large form for collecting all of the details necessary for the phishing attempt. -->
        <form id="form-phish" action="getPhish.php" method="POST" enctype="multipart/form-data">
            <h4> Complete the following fields for your submission: </h4>
            Title: <input type="text" name="title"><br>
            <label for="country">Target Country:</label>
	    <select id="country" name="country">
	      <option value="france">France</option>
	      <option value="spain">Spain</option>
              <option value="germany">Germany</option>
              <option value="england">England</option>
	      <option value="rome">Rome</option>
	      <option value="japan">Japan</option>
	      <option value="china">China</option>
	      <option value="russia">Russia</option>
            </select><br>
            Hostname: <input type="text" name="hostname"><br>
	    User: <input type="text" name="user"><br>
	    <label for="os">Operating System:</label>
	    	<select id="os" name="os">
	    	<option value="linux">Linux</option>
	    	<option value="Windows">Windows</option></select></br>
	    <input type="radio" id="selCommand" name="method" value="Command">
	    <label for="selCommand">Command</label><br>
	    <input type="radio" id="selFile" name="method" value="File">
	    <label for="selFile">File</label><br>
	    <div id="inputField"></div>
            Text/Message: <br><textarea name="text" cols="50" rows="10"></textarea><br>
	    <input type="submit" value="submit">
	</form>
    </div>
</body>
<html>

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
	<link rel="stylesheet" href="scss/bootstrap.min.css">
	<script src="dependencies/bootstrap.min.js"></script>
	<script src="jquery.js"></script>
</head>
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
  <!-- Create the 5 menu buttons to navigate each of the professor php files, each is linked to their respective php page -->
  <body style = "background-color: #EAEAED";>
    <!-- HTML form for changing the status AND grade for a phishing attempt; cannot currently change them independently -->
    
    
<!-- execute button HTML form; like changing the status it operates based on id number selection -->
   <div class="container-fluid py-4">
       <form method = "POST" action="execute.php" id="executeform">
          <input type="hidden" id="id" name="id" value="<?php echo $id ?>">
       </form>
        <button type="submit" id="executeBtn" form="executeform" class="btn btn-danger">!!!EXECUTE!!!</button>
	<button type="button" data-bs-toggle="modal" data-bs-target="#executeModal" id="executeBtnRedo" class="btn btn-danger" hidden>!!!EXECUTE!!!</button>
    </div>
<?php
	$filename= "/usr/local/phish/logs/$logs";
	$contents= file_get_contents($filename);
	if (strlen($contents) > 0) {
  	echo "<script type='text/Javascript'>
          document.getElementById('executeBtn').hidden = true
          document.getElementById('executeBtnRedo').hidden = false
  	</script>";
	}
?>
   <script>
     const btn = document.getElementById('executeBtn');

     btn.addEventListener('click', function handleClick() {
	btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`;
     });
   </script>

   <div class="container-fluid">
     <div class="row gy-3">
       <div class="col-xxl">
         <label class="form-label">Phish Title</label>
         <input type="text" class="form-control w-100" name="title" value="<?php echo $name ?>"readonly>
         <label class="form-label">Date and Time Submitted</label>
         <input type="text" class="form-control w-100" name="date" value="<?php echo $date ?>"readonly>
         <label class="form-label">Message</label>
         <textarea class="form-control" rows="6" readonly><?php echo $message ?></textarea>
       </div>
       <div class="col-md">
		<label class="form-label">Victim Hostname </label>
		<input type="text" name="hostname" class="form-control w-100" value="<?php echo $hostname ?>"readonly>
		<label class="form-label">Target OS </label>
		<input type="text" name="hostname" class="form-control w-100" value="<?php echo $os ?>"readonly>
                <label class="form-label">Victim Username</label>
		<input type="text" name="username" class="form-control w-100" value="<?php echo $username ?>"readonly>
                <label class="form-label">File Name </label>
		<input type="text" name="fileName" class="form-control w-100" value="<?php echo $file ?>"readonly>
                <label class="form-label">Phishing Link </label>
		<input type="text" name="command" class="form-control w-100" value="<?php echo $command ?>"readonly>
       </div>
       <div class="col-xl">
        <form method = "POST" action="changeStatus.php">
        <input type="hidden" id="id" name="id" value="<?php echo $id ?>">
	<div class="row gy-3">
	  <div class="col-sm-2">
            <label for="status" class="form-label">Select Status:</label>
          </div>
          <div class="col-sm-4">
            <select id="status" name="status" class="form-select w-auto">
              <!-- <option value=""><?php echo $approval ?></option> -->
              <option value="Rejected">Rejected</option>
              <option value="Approved">Approved</option>
            </select>
          </div>
	  <div class="col-sm-2">
	    <label class="form-label">Current: </label>
	  </div>
	  <div class="col-sm-4">
	    <input type="text" class="form-control" value="<?php echo $approval ?>"  style="max-width: 7.75rem" readonly></input>
	  </div>
	</div>
	<div class="row gy-3">
	  <div class="col-sm-2">
            <label for="grade" class="form-label">Select Grade:</label>
	  </div>
	  <div class="col-sm-4">
            <select id="grade" name="grade" class="form-select w-auto">
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
              <option value="F">F</option>
            </select>
	  </div>
          <div class="col-sm-2">
            <label class="form-label">Current: </label>
          </div>
          <div class="col-sm-4">
            <input type="text" class="form-control"  style="max-width: 7.75rem" value="<?php echo $grade ?>" readonly></input>
          </div>
	</div>
        <label class="form-label">Comments </label><textarea name="comments" id ="comments" class="form-control" rows="6"><?php echo $comment ?></textarea><br>
        <input type="submit" class="btn btn-primary" value="Change Status/Grade/Comment">
      </form>
     </div>
    </div>
   </div>

<div class="container-fluid">
  <div class="row d-flex justify-content-md-center justify-content-start py-5">
    <div class="card" style="min-width: 45rem; width: 60rem;">
      <div class="card-body">
  	  <?php readfile("/usr/local/phish/logs/" . "$logs"); ?>
      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="executeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>This would be executing the phishing attack again, are you sure you want to do that?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    	<button type="submit" id="executeBtnConfirmation" form="executeform" class="btn btn-danger">Run It Back</button>
      </div>
    </div>
  </div>
</div>

<script>
     const btnTwo = document.getElementById("executeBtnConfirmation");
     btnTwo.addEventListener('click', function handleClick() {
        btnTwo.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Running It Back...`;
     });
</script>
</body>
<html>


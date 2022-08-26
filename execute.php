<?php
/* The magic of the whole operation, execute.php fires the selected phish from the details page and sends data straight to the ansible playbook. It first retrieves all the necessary data the playbook needs based on the id of the phish from details.php, fires the playbook, and is then followed by essentially a copy of details.php once the execution is finished, only now it should display the associated log file created after firing the phish. */

// Continue the session
session_start();

// Verify login status
if($_SESSION['LoggedIn'] = FALSE OR $_SESSION['username'] != 'moleary'){
	include("logout.php");
	}
include("connection.php");	//connect to db

	//get domain name
	//team name
	//retrieve id number from html form
$id = $_POST['id'];

// Make our query to the database and set all of the data we need to a variable we can feed to ansible
mysqli_query($conn, "use pond;");
$queryString = mysqli_query($conn, "SELECT * FROM phish LEFT JOIN payload on payload.id = phish.id LEFT JOIN victim on victim.id = phish.id WHERE phish.id = '$id';");
$getData = mysqli_fetch_assoc($queryString);
$directory = $getData['directory'];
$uuid = $getData['file'];
$directoryFile = $directory . $uuid;
$justUUID = trim(explode ("_", $uuid)[1]) . ".log";
$strHostname = $getData['hostname'];
$strUsername = $getData['username'];
$victimTeam = $getData['country'];
$os = $getData['os'];


$playbook = '';
	//get playbook type
if(strcmp($os, 'Windows') ==0){
	$playbook = 'win_runMalwareExe.yaml';
}
if(strcmp($os, 'linux')==0){
	$playbook = 'lin_runMalwareElf.yaml';
}
//echo "<p><br>$playbook</p>";

$nagios = '';
	//Get nagios connection for team
if(strcmp($victimTeam, 'japan') ==0 or strcmp($victimTeam, 'russia') ==0){
	$nagios = 'nagios3';
}elseif (strcmp($victimTeam, 'china')==0 or strcmp($victimTeam, 'rome')==0){
	$nagios = 'nagios4';
}

	//Use victim username and connect to nagios to retrieve their password	
$victimPassword = shell_exec("ssh moleary@'$nagios' cat /usr/local/nagios/etc/exercise/credentials/'$victimTeam' | grep '$strUsername',");

	//Trim result
$vPTempArray= explode (",", $victimPassword);
$strPassword = trim($vPTempArray[1]);
//echo "<p>Victim Username = $strUsername<br>Victim Password = $strPassword</p>";

	//get domain name
$expHostname= explode (".", $strHostname);
$domainName= trim($expHostname[1]);


$admUsername = shell_exec("ssh moleary@'$nagios' cat /usr/local/nagios/etc/exercise/'$victimTeam'.'$domainName'.* | grep -A 6 '$strHostname' | grep -A 5 'service_description  winrm\|service_description  SSH by password' | grep 'checked_user' | uniq");
//echo $admUsername;

$admUsername = trim(explode (" ", $admUsername)[9]);
//echo "<p>Admin Username = $admUsername</p>";

$admPassword = shell_exec("ssh moleary@'$nagios' cat /usr/local/nagios/etc/exercise/credentials/'$victimTeam' | grep '$admUsername'");
$admPassword = trim(explode (",", $admPassword)[1]);

//echo "<p>Admin Password = $admPassword</p>";

	// Create our ansible command with the variables we've cultivated so far
$command = "ansible-playbook /home/ceo/ansible/playbooks/'$playbook' -i /home/ceo/ansible/inventory.yaml --limit '$strHostname' --extra-vars \"usr_name='$strUsername' usr_passwd='$strPassword' directory='$directory' malware_name='$uuid' adm_name='$admUsername' adm_passwd='$admPassword'\"";

	// Execute our command
$ansibleRun = shell_exec($command);

	// Collect the output of the ansible command and store it as a variable 
$writeText = "<pre>$ansibleRun</pre>";

	// Create a log file using the contents of our recently created variable and update the database with the file name.
file_put_contents("/usr/local/phish/logs/" . $justUUID, $writeText);
mysqli_query($conn, "USE pond;");
mysqli_query($conn, "UPDATE logs SET logs.logs='$justUUID' WHERE id = '$id';");
	echo mysqli_error($conn);
	
//END OF ANSIBLE-LINKED CODE -----------------------------------------------------
?>



<?php
/* This code is an exact mirror of the details.php page. This was only done as a way to work around the issue of performing an 'include()' or 'header()' call to the details.php file while providing the id of the selected phish such that the file can collect all of the necessary data to display the info again. This solution most definitely isn't the cleanest, but it works. */

$id = $_POST['id'];

/* Make our queries; in order to display the information in a single table with html/css, we need to select the columns we want and join
our tables together with left join on the condition that each of the id primary keys for each table match */
$getDB = mysqli_query($conn,"USE pond");
$getTable = mysqli_query($conn,"SELECT phish.id, phish.name, phish.submit_date, victim.id,
victim.hostname, victim.username, victim.message, victim.os, payload.id, payload.file, payload.command, grade.comment, grade.id, grade.approval, grade.grade, logs.id, logs.logs FROM phish LEFT JOIN victim on victim.id = phish.id LEFT JOIN payload on payload.id=phish.id
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
  <meta name = "viewport" content="width=device-width, initial-scale=1">
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
</head>
  <!-- Create the 5 menu buttons to navigate each of the professor php files -->
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
<!-- execute button HTML form; like changing the status it operates based on id number selection -->
    <div style= "position:relative; top:10px; width:280px;">
      <form method = "POST" action="execute.php">
        <input type="hidden" id="id" name="id" value="<?php echo $id ?>">
        <input type="submit" value="!!!Execute!!!" style="color: red;">
      </form>
    </div>
      	<div class = 'column-left'>
      		Phish Title: <input type="text" name="title" value="<?php echo $name ?>"readonly><br>
      		Date and Time Submitted: <input type="text" name="date" value="<?php echo $date ?>"readonly><br>
      		Message: <textarea name="message" id ="message"cols="50" rows="5" readonly><?php echo $message ?></textarea><br>
      	</div><div class = 'column-center'>
      		Victim Hostname: <input type="text" name="hostname" value="<?php echo $hostname ?>"readonly><br>
      		Target OS: <input type="text" name="hostname" value="<?php echo $os ?>"readonly><br>
      		Victim Username: <input type="text" name="username" value="<?php echo $username ?>"readonly><br>
      		File Name: <input type="text" name="fileName" value="<?php echo $file ?>"readonly><br>
      		Command: <input type="text" name="command" value="<?php echo $command ?>"readonly>
      	</div><div class = 'column-right'>
      	<form method = "POST" action="changeStatus.php">
        <input type="hidden" id="selectID" name="selectID" value="<?php echo $id ?>">
        <label for="status">Select Status:</label>
            <select id="status" name="status">
              <option value=""><?php echo $approval ?></option>
              <option value="Rejected">Rejected</option>
	      <option value="Approved">Approved</option>
            </select><br>
        <label for="grade">Select Grade:</label>
            <select id="grade" name="grade">
              <option value=""><?php echo $grade?></option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
              <option value="F">F</option>
            </select><br>
        Comments: <br><textarea name="comments" id ="comments"cols="50" rows="5"><?php echo $comment ?></textarea><br>
        <input type="submit" value="Change Status/Grade/Comment">
      </form>
      	<div style="text-align: center;
	display: inline-block;
	position: absolute;
	outline: solid black 1px;
	background-color: white;
	overflow: auto;
	width: 1000px;
	top: 70%;
	left: 50%;
	transform: translate(-50%, -50%)">
      		<?php readfile("/usr/local/phish/logs/" . "$justUUID"); ?>      		
      	</div>  
</body>
<html>



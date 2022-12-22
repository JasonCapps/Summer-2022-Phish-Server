<?php
/* The magic of the whole operation, execute.php fires the selected phish from the details page and sends data straight to the ansible playbook. It first retrieves all the necessary data the playbook needs based on the id of the phish from details.php, fires the playbook, and is then followed by essentially a copy of details.php once the execution is finished, only now it should display the associated log file created after firing the phish. */

// Continue the session
session_start();
openlog("Phish_Execute", LOG_PID, LOG_LOCAL0);
// Verify login status
if($_SESSION['LoggedIn'] = FALSE OR $_SESSION['username'] != 'moleary'){
	include("logout.php");
	}
include("connection.php");	//connect to db
include('Net/SSH2.php');
include('Crypt/RSA.php');
	//get domain name
	//team name
	//retrieve id number from html form
$id = $_POST['id'];

// Make our query to the database and set all of the data we need to a variable we can feed to ansible
mysqli_query($conn, "use pond;");
$queryString = mysqli_query($conn, "SELECT * FROM phish LEFT JOIN payload on payload.id = phish.id LEFT JOIN victim on victim.id = phish.id WHERE phish.id = '$id';");

syslog(LOG_INFO,"SQL QUERY: SELECT * FROM phish LEFT JOIN payload on payload.id = phish.id LEFT JOIN victim on victim.id = phish.id WHERE phish.id = '$id';");
$getData = mysqli_fetch_assoc($queryString);
$directory = $getData['directory'];
$uuid = $getData['file'];
$directoryFile = $directory . $uuid;
$justUUID = trim(explode ("_", $uuid)[1]) . ".log";
$strHostname = $getData['hostname'];
$strUsername = $getData['username'];
$victimTeam = $getData['country'];
$os = $getData['os'];
$pwd = '/usr/local/phish/ansible';
$config = '/usr/local/phish/config/nagios_config.txt';

$attackTeam = mysqli_query($conn, "SELECT * FROM phish WHERE phish.id = '$id';");
$attackTeam = mysqli_fetch_assoc($attackTeam);
$attackTeam = $attackTeam['country'];
syslog(LOG_INFO,"Attacking Team: $attackTeam");

$nagios = shell_exec("cat $config | grep '$victimTeam'");
$nagios = preg_split('/\s+/',$nagios)[0];

syslog(LOG_INFO,"Nagios Host: Reading config file for '$victimTeam' to receive nagios host\nNagios Host: $nagios");

$nagiosUser = shell_exec("cat $config | grep 'username'");
$nagiosUser = preg_split('/\s+/',$nagiosUser)[1];

syslog(LOG_INFO,"Nagios User: Reading config file for Nagios User\nNagios User: $nagiosUser");


$playbook = '';
	//get playbook type
if(strcmp($os, 'Windows') ==0){
	$playbook = 'win_runMalwareExe.yaml';
}
if(strcmp($os, 'linux')==0){
	$playbook = 'lin_runMalwareElf.yaml';
}

syslog(LOG_INFO,"Playbook Type: $playbook");

//Connect via SSH
$ssh = new Net_SSH2($nagios);
$key = new Crypt_RSA();
$key->loadKey(file_get_contents('../.ssh/id_rsa'));
if (!$ssh->login($nagiosUser, $key)) {
	exit('Login Failed');
}

	//Use victim username and connect to nagios to retrieve their password	
$victimPassword = $ssh->exec("cat /usr/local/nagios/etc/exercise/credentials/$victimTeam | grep $strUsername,");

	//Trim result and store the password
$strPassword = trim((explode(",", $victimPassword))[1]);
syslog(LOG_INFO,"Victim Username: $strUsername\nVictim Password: $strPassword");

	//get domain name
	//domain name stored as example "valencia.intel.spain", we want the domain that they are a part of, in this case "intel"
$domainName= trim((explode(".", $strHostname))[1]);

syslog(LOG_INFO,"Victim Host: $strHostname\nVictim Domain: $domainName");

if(strcmp($os, 'Windows') ==0){
	$admUsername = $ssh->exec("cat /usr/local/nagios/etc/exercise/'$victimTeam'.'$domainName'.* | grep -A 6 '$strHostname' | grep -A 5 'service_description  winrm' | grep 'checked_user' | uniq");	
	$admUsername = trim(preg_split("/\s+/",$admUsername)[2]);

	$admPassword = $ssh->exec("cat /usr/local/nagios/etc/exercise/credentials/'$victimTeam' | grep '$admUsername'");
	$admPassword = trim(explode (",", $admPassword)[1]);

	syslog(LOG_INFO,"WINRM Username: $admUsername\nWINRM Password: $admPassword");
}

	// Create our ansible command with the variables we've cultivated so far
if(strcmp($os, 'Windows') ==0){
	$command = "ansible-playbook $pwd/playbooks/$playbook --limit '$strHostname' --extra-vars \"usr_name='$strUsername' usr_passwd='$strPassword' directory='$directory' malware_name='$uuid' adm_name='$admUsername' adm_passwd='$admPassword' attack_team='$attackTeam'\"";
}
if(strcmp($os, 'linux') ==0){
	$command = "ansible-playbook $pwd/playbooks/$playbook --limit '$strHostname' --extra-vars \"usr_name='$strUsername' usr_passwd='$strPassword' directory='$directory' malware_name='$uuid'\"";
}
    //$access = date("Y/m/d H:i:s");
syslog(LOG_INFO, "Ansible Command: $command");
	// Execute our command
$ansibleRun = shell_exec($command);

	// Collect the output of the ansible command and store it as a variable 
$dateAndTime = date("Y-m-d h:i:sa");
$dateAndTimeString = "Playbook Execution [$dateAndTime] *************************************";
$writeText = "<pre style=\"white-space: pre-wrap; text-align: left;\">$dateAndTimeString</br>$ansibleRun</pre>";

syslog(LOG_INFO, "Output stored in: /usr/local/phish/logs/$justUUID");
closelog();
	// Create a log file using the contents of our recently created variable and update the database with the file name.
file_put_contents("/usr/local/phish/logs/" . $justUUID, $writeText);
mysqli_query($conn, "USE pond;");
mysqli_query($conn, "UPDATE logs SET logs.logs='$justUUID' WHERE id = '$id';");
//	echo mysqli_error($conn);
	
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
  <meta name = "viewport" content="width=device-width, initial-scale=1">        <!-- Helps with formatting certain div elements -->
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
        <!--<link rel="stylesheet" href="dependencies/bootstrap.min.css">-->
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
         <textarea class="form-control" rows="6"><?php echo $message ?></textarea>
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
              <!-- <option value=""><?php echo $grade?></option> -->
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

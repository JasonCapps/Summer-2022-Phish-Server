<?php
/* The backend functionality for goPhish.php. Here all of the submitted values from that page's form are collected, verified, and submitted to the database. */

// Continue our session
session_start();

// Make sure we're still logged in
if($_SESSION['LoggedIn'] = FALSE){
	include("logout.php");
	}

// Make Database connection
include('connection.php');	

$fail = True; //Until we pass all of our checks and prove that ALL inputs are good, we assume they are bad. Only upon passing all of the checks
		//do we set this Boolean to false. Before runing the main functionality we check if this is True. If it is we stop, and return
		//the failMsg. If False then we know it didnt fail and we can continue.
$failMsg = 'no error'; //When setting to failure, make sure this variable gets set to whatever the message is. Can be snarky or helpful


// Generates UUID for file names
function guidv4($data = NULL){
	$data = $data ?? random_bytes(16);
	assert(strlen($data) == 16);
	$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
	$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}
// Get generated uuid and attach to variable
$myuuid = guidv4();


// Get all of our values to be inserted into the database
$user = $_POST['user']; //Getting the user from the Post request and then Stripping any tags off of it like <?php
$title = $_POST['title'];
$country = $_POST['country'];
$hostname = $_POST['hostname']; //Hostname and country are similar both strings
$command = $_POST['command'];
$os = $_POST['os'];
//echo $os . "	";
//$file = $_REQUEST['file'];
$text = $_POST['text'];
$attacker = $_SESSION['username'];

/*

	//This is where I'd put my input validation if I had any -- Dinkleburghhhh
	
//The input validation is basic. If we wanted to make it Hyper Secure and compare against the EC LDAP and such we could.
//These protections were designed to protect against 1. Layer 8 errors 2. Students who wanna try that cool thing they saw
//3. The ones who wanna try to bend the rules 4. Oopsie Daisys. Theoretically you could do some stuff with weird Char types,
//so hardcoding UTF8 as the valid Charset would help, and handling obscure characters. But for now this will prevent anything
//1. Intentional (but basic) 2. Unintentional. If you are deliberately trying to get up to funny business using this site,
//Then you are really cruising for a a bruisin from the Maximum Leader...."No soup for you"

	
//$user validation - The usernames in case studies are FAR less then 50 characters. That limit was built into the DB.
//Do validation to 1. limit chars to less than 50 and 2. usernames can only be lowercase letters, so we use the ctype_lower to ensure
//only characters that are lowercase letters this will prevent anyone using symbols in any form of injection (no <?php or true=true for you)
//This validation is assuming that the teams are attacking an Exercise/Simulated user and not a custom account that may have capital letters
//Which should be a fair assumption given what this Web App is for

//Check length
	if(strlen($user) <= 50){ 
		if(ctype_lower($user)){
			$fail = False; //success case
			//echo "usercheckYES";
			}
		else{
		$fail = True;
		$failMsg = "Bad Input: Username. Should be all Lower Case";
		}
	    }
	else{
		$fail = True;
		$failMsg = "Bad Input: Username. Max length is 50 Chars";
		}
//Checking The name of the phish. Locked in at 50 Chars max in the database. Will check for Length as well as any Alphabet chars. 	
	if(strlen($title) <= 50){
		if(ctype_alpha($title)){
			$fail = False; //success case
			//echo "titlecheckYES";
			}
		else{
		$fail = True;
		$failMsg = "Bad Input: Title. Not Alphabet A-Za-z characters.";
		}}
	else{
		$fail = True;
		$failMsg = "Bad Input: Username. Max length is 50 Chars";
		}

//Checking the target country is not neccessary as the Drop down list is hardcoded

//Checking the target hostname. The database has it limited to 14 chars, and hostnames will only be lowercase. Strip tags shouldnt remove periods.
//however each hostname if we require FQDN should have only two periods, and they should be the only symbols. So two options exist, 
//1. Make some sauce on the backend to not need FQDN or 2. Make the checks smart and ask the php to validate the name in dns. If no records
//return then we know its a bad hostname, if records do return then we know its good. We dont need to worry about the why, the students
//can proofread their own spelling, or contact the Prof. if the host is down or unroutable. Will look into this and ponder a bit before
//implementing 


if(strlen($user) <= 14){
		if(ctype_alpha($hostname)){
			$fail = False; //success case
			echo "hostcheckYES";
			}
			//FQDN check using potentially the checkdnsrr() or gethostbyname() functions
		else{
		$fail = True;
		$failMsg = "Bad Input: Hostname. Not a valid FQDN format.";
		}}
	else{
		$fail = True;
		$failMsg = "Bad Input: Hostname. Max length is 14 Chars";
		}
		
	
//Checking the block of text sent by the User. Stripped out any HTML tags already. Since this could theoretically contain numbers we will just
//check for the limit set in the DB and for weird characters that arent punctuation ( <, >,/ , \ , | , ^ , * , ` , ~). Also the text limit
//is 144. In the real world if its too long a user wont read it, leads to more flaws with catching errors and lies etc. Its hardcoded to 144
//in the DB

if(strlen($text) <= 144){
		if(preg_match("/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\-=\-_+\-¬\`\]]/", $text)){	//<------this thing
			$fail = True; //fail case
			//echo "textcheckYES";
			}
			
		else{
		$fail = False;
		$failMsg = "Bad Input: Message/Text. Invalid symbols ( <, >, /, \, | . ^, *, `, ~ ).";
		}}
	else{
		$fail = True;
		$failMsg = "Bad Input: Hostname. Max length is 144 Chars";
		}
			
//Main code aka the Juicy Bits			

if($fail == 'True'){
	echo $failMsg; //Print Fail Message because it failed. 

	
}else{ //run the main part of the program		
echo "into main";

*/

$target_dir = "/usr/local/phish/uploads/";		//where we want the file saved
$target_basename = basename($_FILES["fileUpload"]["name"]) . "_" . $myuuid;		//retrieve file name + extension (text.txt)
$target_file = $target_dir . $target_basename;	//concatenate our desired directory and filename
$File_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));		//just the file extension
$upload_OK = 1;				//file input validation value
			// where FILE_NAME is the name attribute of the file input in your form

if($upload_OK == 1){
    if(move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $target_file)){		//try saving the uploaded file to our directory
    echo "the file " . htmlspecialchars(basename($_FILES["fileUpload"]["name"])) . " was uploaded.";
    } else {
    echo "the file " . htmlspecialchars(basename($_FILES["fileUpload"]["name"])) . " was not uploaded.";
    }
}

	

	//Define our queries for inputting the values into the database
	
$sql1 = "INSERT INTO phish(country, name) VALUES('$attacker', '$title')";
$sql2 = "INSERT INTO grade(approval) VALUES('Pending')";
$sql3 = "INSERT INTO payload(type, directory, file, command) VALUES('a', '$target_dir', '$target_basename', '$command')";
$sql4 = "INSERT INTO victim(country, hostname, os, username, message) VALUES('$country', '$hostname', '$os', '$user', '$text');";
$sql5 = "INSERT INTO logs(logs) VALUES('');";

mysqli_query($conn, "use pond;");
if(mysqli_query($conn, $sql1) === false){
	echo "failure" . mysqli_error($conn);
	}
if(mysqli_query($conn, $sql2) === false){
	echo "failure" . mysqli_error($conn);
	}
if(mysqli_query($conn, $sql3) === false){
	echo "failure" . mysqli_error($conn);
	}
if(mysqli_query($conn, $sql4) === false){
	echo "failure" . mysqli_error($conn);
	}
if(mysqli_query($conn, $sql5) === false){
	echo mysqli_error($conn);}
else{
	header("location: studentDashboard.php");	//re-navigate to the studentDashboard when all is done
	}						//NOTE: this doesn't actually redirect you to that page, it just loads the page's data
	

?>

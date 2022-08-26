<?php
session_start();
include('connection.php');

//error_reporting(0);

//input from login.php - auth entries
$username = mysqli_real_escape_string($conn,htmlspecialchars($_POST['username']));
$password = mysqli_real_escape_string($conn,htmlspecialchars($_POST['password']));

$_SESSION["username"] = $username;
//exit function - destroy session
function sendExit(){
        header("Location: logout.php");
}

//  openlog("authentication PHP", LOG_PID | LOG_PERROR, LOG_LOCAL0);
//  syslog(LOG_WARNING, "WE ARE HERE #2");
//  closelog();

//passing function
//Not sure about this one yet
function toStudentDashboard(){
    session_start();
    $_SESSION["LoggedIn"] = TRUE;
    header("location: studentDashboard.php");
    exit();
}

function toProfDashboard(){
    session_start();
    $_SESSION["LoggedIn"] = TRUE;
    header("location: profDashboard.php");
    exit();
}

//this array is to match the team name to a team number, after being verfied it is in the array.
$user_array = array(2 => 'china', 3 => 'england', 4 => 'france', 5 => 'germany',
6 => 'japan', 7 => 'rome', 9 => 'russia', 9 => 'spain', 10 => 'redteam');
//This array is used to check if the entered user name is in the list
$team_array = array('china', 'england', 'france', 'germany', 'japan', 'rome'
, 'russia', 'spain', 'redteam');
//checking team is valid (not an anon user) and matching team name to team number
$team_number = 0;

if(in_array($username, $team_array)){//LDAP
    $team_number = array_search($username, $user_array);
    //LDAP connection to Ex Cont
    $user = "uid=" . $username . ",ou=People,dc=classex,dc=tu";
    $ldaphost = "ldap://172.21.252.200/";
    $ds = ldap_connect($ldaphost);
    ldap_start_tls($ds);
    /*
    try{
            $bind = ldap_bind($ds,$user,$password);
    }catch (Exception $e) {
        sendExit();
    }
    */
    //end ldap config
    if(ldap_bind($ds,$user,$password)){//LDAP successfull = team exists
            $_SESSION["account_number"] = $team_number;
            $_SESSION["user_id"] = $team_number;
            toStudentDashboard();
        }else{
                sendExit();
        }
    }
else if($username == 'moleary'){//CENTRAL_BANKER local DB auth ONLY
    $hash = hash('sha256', "$password");
    $sql = "SELECT * FROM user WHERE username = 'moleary' AND password_hash = '$hash';";
    $result = mysqli_query($conn, $sql);
    $retnum = mysqli_num_rows($result);
    $row = mysqli_fetch_assoc($result);
    if($retnum ==1){//is banker
        $_SESSION["account_number"] = $row["account_id"];
        $_SESSION["user_id"] = $row["account_id"];
        toProfDashboard();
    }else{
            sendExit();
    }
}else{// neither authentication worked
sendExit();
}
?>

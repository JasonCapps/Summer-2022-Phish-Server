<?php
session_start();
?>

<!DOCTYPE HTML>

<html>
<style>
    .error {color: #FF000;}
        body{
                background-color:MistyRose;
                text-align:center;
                margin-left:15%;
                margin-right:15%;
        }
        form{
                background-color:LightGrey;
                border-style:solid;
        }

</style>
  <body>
		<div>
        <form action= "authentication.php" method="post">
            <h2> Welcome to O'Leary's Phishing Hole</h2>
            <h4> "I've gotta big'un on the line!"</h4>
            Username: <input type="text" name="username"><br>
            Password: <input type="password" name="password"><br>
            <input type="submit" value="Login">
		</div>
	</form>
    </body>
</html>

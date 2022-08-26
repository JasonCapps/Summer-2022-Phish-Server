<?php
    session_start();
    include('connection.php');
    session_unset();
    session_destroy();
    mysqli_close($conn);
    header("location: login.php");
    exit();
?>
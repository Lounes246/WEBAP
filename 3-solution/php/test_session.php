<?php
    session_start();
    if(isset($_SESSION["user"])) {
        echo "Welcome back, " . $_SESSION["user"];
    } else {
        echo "You are not yet logged in.";
    }
?>
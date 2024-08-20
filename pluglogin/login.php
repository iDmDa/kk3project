<?php

if($_SESSION['login'] != "login") {
    require("winlogin.html");
    exit;
    $_SESSION['login'] = "login";
}

//echo session_id();
?>
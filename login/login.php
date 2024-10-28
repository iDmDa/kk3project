<?php

$password = "vhod";

if($_SESSION['login'] != "login") {
    require("index.html");
    if($_POST['pass'] != md5($password)) exit;
    $_SESSION['login'] = "login";
    header("Refresh:0");
    exit;
}

?>
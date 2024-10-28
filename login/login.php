<?php

$logResult = login($_POST['log'], $_POST['pass']);

if($_SESSION['login'] != "login" and $logResult == 1) {
    $_SESSION['login'] = "login";
}

if($_SESSION['login'] != "login") {
    require("winlogin.html");
    exit;
}

function login($login, $pass) {
    include("dbdata.php");

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $sql = "SELECT * FROM users WHERE login ='{$login}' and pass = '{$pass}'";
    
        $result = $pdo->query($sql);
        $rowCount = $result->rowCount();
        //$rows = $result->fetchAll(PDO::FETCH_ASSOC);
        //$rows = $result->fetchAll();
    
        if($rowCount) return 1;
        return 0;
        
    } catch (PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
    }
}

?>
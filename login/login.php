<?php

if ($_SERVER["CONTENT_TYPE"] == "application/json" and $_SESSION['login'] != "login") {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if ($data['reg'] == "newReg") {
        $result = (createNewUser($data)) ? "ok" : "-1";
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($data['reg'] == "login") {
        $result = login($data['login'], $data['pass']) ? "login" : "-2";;
        if ($result == "login") $_SESSION['login'] = "login";
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function createNewUser($data)
{
    include("dbdata.php");
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM users WHERE login = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['login' => $data['login']]);
        $rowCount = $stmt->rowCount();

        if (!$rowCount) {
            $login = $data['login'];
            $pass = $data['pass'];
            unset($data['login'], $data['pass'], $data['reg']);

            $sql = "INSERT INTO users (login, pass, other) VALUES (:login, :pass, :other)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'login' => $login,
                'pass' => $pass,
                'other' => json_encode($data, JSON_UNESCAPED_UNICODE)
            ]);
            return 1;
        }
        return 0;
    } catch (PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
    }
}

function login($login, $pass)
{
    include("dbdata.php");

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM users WHERE login = :login AND pass = :pass";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':login' => $login,
            ':pass' => $pass
        ]);

        $rowCount = $stmt->rowCount();

        if ($rowCount) return 1;
        return 0;
    } catch (PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
    }
}

/*
if ($_SESSION['login'] != "login") {
    require("index.html");
    exit;
}

if ($_SESSION['login'] == "login") {
    require("index.html");
}
*/

require("index.html");

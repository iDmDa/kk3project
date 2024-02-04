<?
include("dbdata.php");
$mysqli = new mysqli($db_host, $db_login, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    echo "Нет связи с базой данных!";
    exit;
}

//echo $_POST['create_new_cat'];
if(isset($_POST['create_new_cat'])) {

    $sql = "insert into catalog (" .$_POST['name'] .") VALUES ('" .$_POST['value'] ."')";
    $mysqli->query($sql);
    echo $mysqli->insert_id;
    $mysqli->close();
};

if(isset($_POST['move_item'])) {

    $sql = "update " .$_POST['table'] ." set catalog = '" .$_POST['catalog'] ."' where id = " .$_POST['item'] ." ";
    $mysqli->query($sql);
    $mysqli->close();
};


if(isset($_POST['delete_item'])) {

    $sql = "delete from catalog where id = " .$_POST['id'] ." ";
    $mysqli->query($sql);
    $mysqli->close();
};

if(isset($_POST['rename'])) {

    $sql = "update catalog set name = '" .$_POST['name'] ."' where id = " .$_POST['id'] ." ";
    $mysqli->query($sql);
    $mysqli->close();
};

if(isset($_POST['change_doctype'])) {
    $sql = "update docwork set doctype = '" .$_POST['change_doctype'] ."' where id = " .$_POST['id'] ." ";
    $mysqli->query($sql);
    $mysqli->close();
};

?>
<?
session_start();

include ("dbconnect.php");

switch ($_POST["relocate"])
{ 
case "move":

$r = mysql_query("SELECT * FROM " .$_POST['table'] ." where id = '" .$_POST['id'] ."' ");
$f = mysql_fetch_array($r);
$before = $f['detid'];

mysql_query("UPDATE " .$_POST['table'] ." SET detid = '" .$_POST['newdetid'] ."' WHERE id = '" .$_POST['id'] ."' ");

mysql_query ("INSERT INTO kk3project.log (date, time, tab, field_id, deistvie, content, ip) VALUES (CURDATE(), CURTIME(), '" .$_POST['table'] ."', '" .$_POST['id'] ."', 'move','detid: " .$before ." -> " .$_POST['newdetid'] ."', '" .$_SERVER['REMOTE_ADDR'] ."')");

break;

case "calendar":

$r = mysql_query("SELECT * FROM kk3project.Calendar where date = '" .$_POST['day'] ."' ");
$f = mysql_fetch_array($r);

if($f['date']) mysql_query("UPDATE kk3project.Calendar SET holydays = '" .$_POST['holydays'] ."', upd = 1 WHERE date = '" .$_POST['day'] ."' ");
else mysql_query ("INSERT INTO kk3project.Calendar (date, holydays, upd) VALUES ('" .$_POST['day'] ."', '" .$_POST['holydays'] ."', 1)");

break;

case "session":

$_SESSION[$_POST['data1']] = $_POST['data2'];
/*
$fp = fopen("file.txt", "w");
fwrite($fp, $_POST['data1']);
fclose($fp);
*/
break;

default:

break;

}
?>
<?
include ("dbconnect.php");

switch ($_GET["dbdata"])
{ 
case "2":
	mysql_query ("INSERT INTO " .$db_name ."." .$_GET['table'] ." (" .$_GET['field1'] ."," .$_GET['field2'] ."," .$_GET['field3'] ."," .$_GET['field4'] .") VALUES ('" .$_GET['value1'] ."','" .$_GET['value2'] ."','" .$_GET['value3'] ."','" .$_GET['value4'] ."')");
break;

default:
if($_GET['field'] == "") mysql_query ("INSERT INTO " .$db_name ."." .$_GET['table'] ." (hide) VALUES ('0')");
else mysql_query ("INSERT INTO " .$db_name ."." .$_GET['table'] ." (" .$_GET['field'] .") VALUES ('" .$_GET['value'] ."')");
break;

}
?>
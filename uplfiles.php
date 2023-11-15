<?
require ("dbconnect.php");

require ("megatable.php");



$dtable = new megatable;

$dtable->htag = "dialog";
$dtable->dbtable = "uplfiles";
$dtable->show_big_header = 0;
$dtable->pos_nomer_col = 0;
//$dtable->getdata = "&id=" .$_GET['id'] ."&tabname=" .$_GET['tabname'] ."&type=" .$_GET['type'];
$dtable->getdata = $_SERVER['QUERY_STRING'];
//$dtable->getdata = "&id=" .$_GET['id'] ."&tabname=" .$dtable->dbtable ."&type=" .$_GET['type'];
$dtable->filter = " and tabname = '" .$_GET['tabname'] ."' and type = '" .$_GET['type'] ."' and detid = " .$_GET['id'];

$dtable->datatable();
//echo $_SERVER['QUERY_STRING'];
echo "<script>upload('uplnm','&tabname=" .$_GET['tabname'] ."&type=" .$_GET['type'] ."&detid=" .$_GET['id'] ."&id=" .$_GET['id'] ."');</script>";
echo "<a id = 'uplnm'><img src = 'include/file_add.png'></a><br>";

?>
<script>
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}
</script>

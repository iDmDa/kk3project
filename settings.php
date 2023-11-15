<?
require ("dbconnect.php");

require ("megatable.php");



$dtable = new megatable;

$dtable->htag = $_GET['htag']; //"dialog";
$dtable->dbtable = $_GET['dbtable']; //"izdelie";
$dtable->pos_nomer_col = 0;
$dtable->show_big_header = 0;
$dtable->show_add_db_button = 1;
$dtable->getdata = $_SERVER['QUERY_STRING'];

//$dtable->filter = " and tabname = '" .$_GET['tabname'] ."' and type = '" .$_GET['type'] ."' and detid = " .$_GET['id'];

$dtable->datatable();


?>
<script>
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}
</script>

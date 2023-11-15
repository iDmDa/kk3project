
<?
require ("dbconnect.php");

require ("megatable.php");

if($_GET['print'] == 1) echo "<link href='css/litemgtable.css' rel='stylesheet'>";

class mgtable extends megatable
{

public function table_big_header() //заголовок таблицы
{
	echo "<tr>";
	echo "<td class = 'table_big_header' colspan = 999 align = center><div style='float:left;width:100%;text-align:center;'>" .$this->header_big_name ."</div><div class = 'mg_table_button'><div class = 'printer_layer'><a onclick = window.open('" .basename($_SERVER['PHP_SELF']) ."?print=1" .$_SERVER['QUERY_STRING'] ."','Печать','widht=600,height=800,location=no')><img src='include/Printer.png' alt='закрыто'></a></div></div></td>"; //малый заголовок
	echo "</tr>";
}

}

$dtable = new mgtable;

$dtable->htag = "varframe";
$dtable->addfield = "detid";
$dtable->addvalue = $_GET['id'];
$dtable->getdata = "&id=" .$_GET['id'];
//$dtable->getdata = $_SERVER['QUERY_STRING'];
$dtable->filter = " and detid = " .$_GET['id'];

$dtable->dbtable = "history";
$dtable->header_big_name = "Развитие проекта";
$dtable->bd_query_text();
$dtable->tabposcount >= 1 ? $dtable->show_add_db_button = 0 : $dtable->show_add_db_button = 1;
//$dtable->pos_nomer_col = 0;
//$dtable->nomer_col_style = "hide_field";
$dtable->datatable();
echo "<br>";
echo "<br>";
//***************************************

?>
<script>

function chkzamok() {
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}
}

$(document).ready(function() {
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}


});

</script>
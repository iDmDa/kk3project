
<?
require ("dbconnect.php");

require ("megatable.php");

class mgtable extends megatable
{


	public function add_col($r,$f,$i,$j)
{
	require ("global_path.php");

	
	//Доп колонка Отчетные документы
	if(!isset($j) and $i == 10) print "<td class = 'table_header' style = 'width:130px;'>Отчётные документы по этапам</td>";  //заголовок
	if(isset($j) and $i == 10) 
	{

		$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'contragent' and detid = '" .$f['id'] ."' and type = 1 ORDER BY " .$this->sortirovka);
	
		echo "<td class = 'table_field'>";

		for ($ii=0; $ii<mysql_num_rows($rr); $ii++) //чтение строк
		{
			$ff=mysql_fetch_array($rr);
			$full_link_path = "http://" .$_SERVER['SERVER_NAME'] .$save_dir .$ff['local_path'] ."/" .$ff['prefix'] ."_";
			
			$file_icon = $this->icon_file($ff['filename']);
		
			echo "<a href = '".$full_link_path .$ff['filename'] ."' target = '_blank'><img src= 'include/" .$file_icon ."' title = '" .$ff['maskname'] ."'></a>";
			echo " ";
		}
		echo "<div class = 'button_field button_layer' style = 'display:none;'>";
			echo "<a onClick = okno_show('dialog','&tabname=" .$this->dbtable ."&type=1&id=" .$f['id'] ."');><img src = 'include/new window.png' title = ''></a>";
		echo "</div>";

		echo "</td>";  //колонка
		
	}

	
}
}


$dtable = new mgtable;


$dtable->htag = "varframe";
$dtable->addfield = "detid";
$dtable->addvalue = $_GET['id'];
//$dtable->getdata = "&id=" .$_GET['id'];
$dtable->getdata = $_SERVER['QUERY_STRING'];
$dtable->filter = " and detid = " .$_GET['id'];

$dtable->dbtable = "contragent";
$dtable->header_big_name = "Контрагенты";
$dtable->bd_query_text();
$dtable->show_add_db_button = 1;
$dtable->pos_nomer_col = 0;
$dtable->date_field_list = "datestart,datestop";
$dtable->datatable();
echo "<br>";

?>
<script>
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}


</script>
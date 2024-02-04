
<?
require ("megatable.php");

if($_GET['print'] == 1) echo "<link href='css/litemgtable.css' rel='stylesheet'>";

class mgtable extends megatable
{

public function table_big_header() //заголовок таблицы
{

$sql_date_field = " CONCAT(SUBSTRING_INDEX(date,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(date,'.',2),'.',-1), SUBSTRING_INDEX(date,'.',1)) ";
$sql_date_begin = " CONCAT(SUBSTRING_INDEX('01.01." .date('Y') ."','.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX('01.01." .date('Y') ."','.',2),'.',-1), SUBSTRING_INDEX('01.01." .date('Y') ."','.',1)) ";
//$sql_date_end = " CONCAT(SUBSTRING_INDEX('" .date('d.m.Y') ."','.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX('" .date('d.m.Y') ."','.',2),'.',-1), SUBSTRING_INDEX('" .date('d.m.Y') ."','.',1)) ";
$sql_date_end = " CONCAT(SUBSTRING_INDEX('31.12." .date('Y') ."','.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX('31.12." .date('Y') ."','.',2),'.',-1), SUBSTRING_INDEX('31.12." .date('Y') ."','.',1)) ";
$find_date1 = " and " .$sql_date_field ." >= " .$sql_date_begin;
$find_date2 = " and " .$sql_date_field ." <= " .$sql_date_end;

$r = mysql_query("SELECT * FROM docwork where hide <> 1 and detid = " .$_GET['id'] .$find_date1 .$find_date2);
for ($i=0; $i<mysql_num_rows($r); $i++) //чтение строк
{
	$f = mysql_fetch_array($r);
	$result_truda += (float)$f['sumnormachas'];
}//****


	echo "<tr>";
	echo "<td class = 'table_big_header' colspan = 999 align = center><div style='float:left;width:100%;text-align:center;'>" .$this->header_big_name ."</div><div class = 'mg_table_button'><div class = 'printer_layer'><a onclick = window.open('" .basename($_SERVER['PHP_SELF']) ."?print=1" .$_SERVER['QUERY_STRING'] ."','Печать','widht=600,height=800,location=no')><img src='include/Printer.png' alt='закрыто'></a></div></div></td>"; //малый заголовок
	echo "</tr>";
	echo "<tr>";
	echo "<td class = 'table_colspan_header' colspan = 4 align = center>Документы и работы</td>"; //малый заголовок
	echo "<td class = 'table_colspan_header' colspan = 2 align = center>Исполнители</td>";
	echo "<td class = 'table_colspan_header' colspan = 6 align = center title = 'Суммарная трудоемкость за " .date('Y') ." год: " .$result_truda ." н/ч'>Трудоемкость</td>"; //title = 'stroka 1 &#13;stroka 2'
	echo "<td class = 'table_colspan_header' colspan = 2 align = center>Ссылки на документ или результат работы</td>";
	echo "<td class = 'table_colspan_header' colspan = 2 align = center>Готовность</td>";
	echo "</tr>";
}

public function filter_vvoda_listov($str_data1, $str_data2)
{
	$rule = array("A5", "А5", "A4", "А4", "A3", "А3",  "A2", "А2", "A1", "А1", "A0", "А0", "ф.", "н/ч", ",");
	$newchar = array("*0.5", "*0.5", "*1", "*1", "*2", "*2", "*4", "*4", "*8", "*8", "*16", "*16", "", "", ".");

	if ($str_data1 == "") $str_data1 = "0";
	if ($str_data2 == "") $str_data2 = "0";

	$str_data1 = str_replace("+", "<br>", $str_data1);
	$str_data2 = str_replace("+", "<br>", $str_data2);
	
	$st1 = explode("<br>", $str_data1);
	$st2 = explode("<br>", $str_data2);

	for($i=0;$i<count($st1);$i++)
	{
		$s1 = (float)str_replace($rule, $newchar, $st1[$i]);
		count($st1) != count($st2) ? $s2 = "0" : $s2 = (float)str_replace($rule, $newchar, $st2[$i]);
		is_numeric($s2) ? $s2 : $s2 = 0;
		is_numeric($s1) ? $s1 : $s1 = 0;
		$str_s .= $s2 ."*" .$s1;
		$i < (count($st1) - 1) ? $str_s .= "+" : "";
	}

	return $str_s;
}

	public function add_col($r,$f,$i,$j)
{
	require ("global_path.php");
	


	//Доп колонка расчетная
	if(!isset($j) and $i == 14) print "<td class = 'table_header' style = 'width:50px;'>фактич. отчётная, час</td>";  //заголовок
	if(isset($j) and $i == 14) 
	{

		echo "<td class = 'table_field' title = '" ."(" .$this->filter_vvoda_listov($f['kolvoformatov'], $f['normativtruda']) .")*1.2" ."' date-id = " .$f['id'] .">";

		//@eval("\$summa_normachasov = (" .$this->filter_vvoda_listov($f['kolvoformatov'], $f['normativtruda']) .")*1.2;");
		//echo $summa_normachasov;
		//mysql_query("UPDATE kk3project.docwork SET sumnormachas = '" .$summa_normachasov ."' WHERE id = '" .$f['id'] ."' ");

		echo $f['sumnormachas'];

		echo "</td>";  //колонка
		
	}	



	//Доп колонка Черновик
	if(!isset($j) and $i == 14) print "<td class = 'table_header' style = 'width:70px;'>Черно-<br>вик</td>";  //заголовок
	if(isset($j) and $i == 14) 
	{

		$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'docwork' and detid = '" .$f['id'] ."' and type = 1 ORDER BY id");
	
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

	//Доп колонка Сканированный
	if(!isset($j) and $i == 14) print "<td class = 'table_header' style = 'width:70px;'>Скани-<br>рован-<br>ный</td>";  //заголовок
	if(isset($j) and $i == 14) 
	{

		$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'docwork' and detid = '" .$f['id'] ."' and type = 2 ORDER BY id");
	
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
			echo "<a onClick = okno_show('dialog','&tabname=" .$this->dbtable ."&type=2&id=" .$f['id'] ."');><img src = 'include/new window.png' title = ''></a>";
		echo "</div>";

		echo "</td>";  //колонка
		
	}
	
}

public function table_content($r,$f,$j)
{
	for ($i=0; $i<mysql_num_fields($r); $i++) // чтение значений в строке
	{
		if ($i==0) 
		{
			if($f['gotovnost'] == "100") echo "<tr class = 'tab_line_ready'>";
			else {
					if(strtotime(date("d.m.Y")) > strtotime($f['date']) and $f['date'] != "") echo "<tr class = 'tab_line_notready'>";
					else echo "<tr>";
				}
		}
		$this->all_col($r,$f,$i,$j);
		if($this->hide_field($r,$i)) continue;
		$this->date_field_check($r,$f,$j,$i) ? $this->date_field($r,$f,$j,$i) : $this->normal_field($r,$f,$j,$i);
		//$this->date_field($r,$f,$j,$i);
	}
}

public function add_db_button($dbtable, $field, $fieldvalue, $htag, $getdata) //добавить кнопку добавления поля
{
	if($this->filename == "") $this->filename = basename($_SERVER['PHP_SELF']); // обновить файл из которого вызван
	echo "<div class = 'button_field' style = 'display:none;'>";
	echo "<a onclick=add_2refresh();><img src = 'include/addline.png'></a>"; 
	echo "</div>";
}

}

?>
<script>

function chkzamok() {
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}
}

var findfio_txtfield="";
var findotd_txtfield="";
var chk_hidecheckbox = "";
var findwname_txtfield = "";
function startfind() {
	//$( "#find" ).empty();
	x = "<? echo $_SERVER['QUERY_STRING'] ?>&fio=" + $(".findfio").val()+ "&workname=" + $(".wname").val() + "&otd=" + $(".findotd").val() + "&chekhidegotov=" + chk_hidecheckbox;
	findfio_txtfield = $(".findfio").val();
	findotd_txtfield = $(".findotd").val();
	findwname_txtfield = $(".wname").val();
	//alert(findfio_txtfield);
	$("#find").load("docwork.php #find", x,function(){
			chkzamok();
			$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
	});
}

function add_2refresh() 
{
	$.ajax({
	url: 'add.php', 
	data: {
	dbdata:'2',
	table:'docwork',
	field1:'detid',
	field2:'fio',
	field3:'otd',
	field4:'naimenovenie',
	value1:<?echo $_GET['id'];?>,
	value2:findfio_txtfield,
	value3:findotd_txtfield,
	value4:findwname_txtfield
	},
	success: function(data)
		{
			startfind();
		}
		
	});
	
}; 
</script>
<?
echo $_GET['print'] == 1 ? "<div style = 'display:none'>" : "<div>";
echo "Наименование: <input type='text' class='wname' > Номер отдела: <input type='text' style='width:25px;' class='findotd'> Фамилия исполнителя: <input type='text' class='findfio' > <input id = 'hidework' type='checkbox'> Скрыть выполненные работы<br><br>";
//$hide_ready_work = " and gotovnost < 100 ";
echo "</div>";

echo "<div id='find'>";
require ("dbconnect.php");
$dtable = new mgtable;

//Перерасчет измененных полей
$r = mysql_query("SELECT * FROM docwork where hide <> 1 and upd = '1' ");
for ($i=0; $i<mysql_num_rows($r); $i++) //чтение строк
{
	$f = mysql_fetch_array($r);
	@eval("\$summa_normachasov = (" .$dtable->filter_vvoda_listov($f['kolvoformatov'], $f['normativtruda']) .")*1.2;");
	mysql_query("UPDATE kk3project.docwork SET sumnormachas = '" .$summa_normachasov ."', upd = '0' WHERE id = '" .$f['id'] ."' ");
}//****

$_GET['chekhidegotov'] == 1 ? $hide_ready_work = " and gotovnost < 100 " : $hide_ready_work = "";

$dtable->htag = "varframe";
$dtable->addfield = "detid";
$dtable->addvalue = $_GET['id'];
//$dtable->getdata = "&id=" .$_GET['id'];
$dtable->getdata = $_SERVER['QUERY_STRING'];
$dtable->filter = " and doctype = 0 and detid = " .$_GET['id'] ." and (naimenovenie LIKE '%" .$_GET['workname'] ."%' and fio LIKE '%" .$_GET['fio'] ."%' and otd LIKE '%" .$_GET['otd'] ."%') " .$hide_ready_work ." ";

$dtable->dbtable = "docwork";
$dtable->header_big_name = "Документы и работы";
//$dtable->bd_query_text();
//$dtable->show_add_db_button = 1;
$dtable->show_add_db_button = 1;
$dtable->pos_nomer_col = 0;
$dtable->nomer_col_menu_style = " relocnomer ";
$dtable->date_field_list = "date";
$dtable->sortirovka = "if(date = '' or date is null, 1, 0), SUBSTRING_INDEX(docwork.date,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(docwork.date,'.',2),'.',-1), SUBSTRING_INDEX(docwork.date,'.',1), id";
$dtable->max_line = 100;
if(isset($_GET['page'])) $dtable->show_page = $_GET['page'];
$dtable->datatable();

echo "<br>";
echo "</div>";
?>
<script>



$(document).ready(function() {
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}

$(".findfio, .wname").keyup(function(e) {
startfind();
});

$(".findotd").keyup(function(e) {
startfind();
});

$("#hidework").change(function() {
if(this.checked) {chk_hidecheckbox = 1;}
else {chk_hidecheckbox = 0;}
startfind();
});

});


</script>
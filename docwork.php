<?
function db_connect() {
	try {
		include("dbdata.php");
		$db = new PDO("mysql:host={$db_host};port=3306;dbname={$db_name}", "{$db_login}", "{$db_pass}");
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage();
		die();
	}
	return $db;
}

if(isset($_POST['docwork'])) {

	function columnNameList() {
		$name["naimenovenie"] = "Наименование";
		$name["date"] = "Дата выпуска";
		$name["numstage"] = "Номер этапа";
		$name["otd"] = "Отделы";
		$name["fio"] = "ФИО";
		$name["codnorm"] = "Код норма-<br>тива";
		$name["normativtruda"] = "Норматив трудо-<br>емкости";
		$name["kolvoformatov"] = "Кол-во форма-<br>тов";
		$name["planchas"] = "плано-<br>вая, <rb>час";
		$name["ispolnchas"] = "испол-<br>нителя, час";
		$name["scan"] = "Скан";
		$name["chernovik"] = "Черно-<br>вик";
		$name["gotovnost"] = "%<br>готов-<br>ности";
		$name["prim"] = "Примечание";
		return $name;
	}

    function getDatatable($tableName, $table_id, $fieldList, $page, $limitLine, $find = "", $fieldForSearch = ""){
		$db = db_connect();
		if($_POST['sort_col'] != "1") {		
			$fieldCreate = ", CONCAT(SUBSTRING_INDEX(date, '.', -1), 
						SUBSTRING_INDEX(SUBSTRING_INDEX(date, '.', 2), '.', -1), 
						SUBSTRING_INDEX(date, '.', 1)) as txtToDate";
			$sortirovka = "CASE WHEN txtToDate = '' THEN 1 ELSE 0 END, txtToDate ASC, CASE WHEN txtToDate = '' THEN docwork.id END";
		}
		if($_POST['sort_col'] == "1") {
			$fieldCreate = ", SUBSTRING_INDEX(numii, '.', -1) AS onlynum";
			$sortirovka = "CASE WHEN onlynum = '' then 2 ELSE 1 END, onlynum +0 ASC, id ASC";
		}
		$findlist = findlist($find, $fieldForSearch);
		if($table_id != "0") $r = $db->prepare("SELECT {$fieldList}{$fieldCreate} FROM {$tableName} WHERE hide = 0 and doctype = 0 and detid = '{$table_id}' {$findlist} ORDER BY {$sortirovka} LIMIT {$page}, {$limitLine}");
		if($table_id == "-1") $r = $db->prepare("SELECT name as izdname, docwork.{$fieldList}{$fieldCreate} FROM {$tableName} LEFT JOIN izdelie ON izdelie.id = docwork.detid WHERE docwork.hide = 0 and doctype = 0 {$findlist} ORDER BY {$sortirovka} LIMIT {$page}, {$limitLine}");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC);
	}

	function maxResult($tableName, $table_id, $find = "", $fieldForSearch) {
		$db = db_connect();
		$findlist = findlist($find, $fieldForSearch);
		if($table_id != "0") $r = $db->prepare("SELECT COUNT(id) as maxResult FROM {$tableName} WHERE hide = 0 and doctype = 0 and detid = '{$table_id}' {$findlist}");
		if($table_id == "-1") $r = $db->prepare("SELECT COUNT(id) as maxResult FROM {$tableName} WHERE hide = 0 and doctype = 0 {$findlist}");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC)[0]['maxResult'];
	}

	function findlist($find, $fieldForSearch = "date") {
		$findlist = "";
		if($find != null and $find != "" and $find != "undefined") {
			$arrSearchList = explode(",", trim($fieldForSearch));
			$findlist = "and (";
			foreach ($arrSearchList as $value) {
				$findlist .= "{$value} LIKE '%{$find}%' or ";
			}
			$findlist = substr($findlist, 0, -4);
			$findlist .= ")";
		}
		return $findlist;
	}

	$showLine = $_POST['showLine'];
	$fieldForSearch = "naimenovenie, date, numstage, otd, fio, codnorm, normativtruda, kolvoformatov, planchas, ispolnchas, gotovnost, prim";
	$showField = "naimenovenie, date, numstage, otd, fio, codnorm, normativtruda, kolvoformatov, planchas, ispolnchas, chernovik, scan, gotovnost, prim";
    $fieldList = "id, " .$showField;
	
	if($_POST['tab_id'] == "-1") $showField = "izdname, " .$showField;
	$page = $_POST['page'] == 0 ? (maxResult("docwork", $_POST['tab_id'], $_POST['find'], $fieldForSearch)/$showLine|0)*$showLine : ($_POST['page']-1)*$showLine;
    $jsonArray = getDatatable("docwork", $_POST['tab_id'], $fieldList, $page, $showLine, $_POST['find'], $fieldForSearch);

	$headerNameList = columnNameList();
	$headerNameList['fieldList'] = $fieldList;
	$headerNameList['showField'] = $showField;
	$headerNameList['db'] = "docwork";
	$headerNameList['maxResult'] = maxResult("docwork", $_POST['tab_id'], $_POST['find'], $fieldForSearch);
	$headerNameList['maxPage'] = ($headerNameList['maxResult']/$showLine|0) + 1;
	$headerNameList['page'] = ($page / $showLine) + 1;
	array_unshift($jsonArray, $headerNameList); // Добавляет один или несколько элементов в начало массива
	$jsonArray = array_values($jsonArray);// Переиндексируем массив, чтобы ключи начинались с 0
	$json = json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
    echo $json;

    unset($_POST['docwork']);
	exit;
}

if(isset($_POST['finditems'])) {

	function createList($items) {
		$items = explode(",", $items);
		$result = "";
		foreach ($items as $item) {
			$result .= "detid = '{$item}' or ";
		}
		$result = substr($result, 0, -4);
		return $result;
	}

	function finditems($items) {
		$db = db_connect();
		$r = $db->prepare("SELECT * FROM uplfiles WHERE hide = '0' and tabname = 'docwork' and ({$items})");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC);
	}

	$array = finditems(createList($_POST['finditems']));

	$json = json_encode($array, JSON_UNESCAPED_UNICODE);

	echo $json;

	unset($_POST['finditems']);
	exit;
}

if(isset($_POST['add'])) {

	function add($id) {
		$db = db_connect();
		$r = $db->prepare("INSERT INTO docwork(hide, detid, doctype) VALUES ('0', '{$id}','0')");
		$r->execute();
		return $db->lastInsertId();
	}

	$lastId = add($_POST['add']);

	echo $lastId;

	unset($_POST['add']);
	exit;
}

//echo "izv";
?>
<div id="test_version">
	<div id = "test_ver_div1"></div>
	<div id = "test_ver_div2">✖</div>
</div>
<script>
	try {
		
		let msg1 = document.getElementById("test_version");
		if(sessionStorage.getItem("msg1_hide") == "1") msg1.style.display = "none";
		function msg_view(msg1) {
			if(localStorage.getItem("docwork_test_table") == null){
				msg1.childNodes[1].innerHTML = "Тестовая версия (<span>переключиться в старую версию</span>)";
				document.cookie = "onlyNew=0";
			}
			else {
				msg1.childNodes[1].innerHTML = "Старая версия (<span>переключиться в новую версию</span>)";
				document.cookie = "onlyNew=1";
			}
		}

		msg_view(msg1)

		msg1.addEventListener("click", function(event){
			if(event.target.nodeName == "SPAN" ) {
				if(localStorage.getItem("docwork_test_table") == null) localStorage.setItem("docwork_test_table", "old");
				else localStorage.removeItem("docwork_test_table");
				msg_view(msg1);
				refresh('docwork.php', 'varframe', "&id=" + izdelieid);
			}

			if(event.target.id == "test_ver_div2") {
				msg1.style.display = "none";
				sessionStorage.setItem("msg1_hide", "1");
			}
		})

		if(localStorage.getItem("docwork_test_table") == null) docworkfindbox(<?=$_GET['id']?>);
	}
	catch {
		console.log("errrrrrr");
		let varframe = document.getElementById("varframe");
		varframe.innerHTML = "<p style='font-size: 25px;'>Версия вашего браузера устарела и не может отобразить эту страницу! Скачайте новую версию браузера по <a href = 'http://server-kk3/projectdata/docwork/1706508740_FirefoxPortable112.zip'>ссылке</a></p>";
	}

	let sendObject = {
		"tab_id": <?=$_GET['id']?>, 
		"page": 0, 
		"find": ""
	}
    if(localStorage.getItem("docwork_test_table") == null) docworkLoad(sendObject);
</script>

















<?
if($_COOKIE["onlyNew"] != "1") exit;
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
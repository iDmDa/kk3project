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

if(isset($_POST['xhrload'])) {

	function columnNameList() {
		$name["datevh"] = "Дата";
		$name["nomervh"] = "Номер";
		$name["adresvh"] = "Адресат";
		$name["contentvh"] = "Краткое содержание";
		$name["scanvh"] = "Скан";
		$name["countlistvh"] = "Кол. листов";
		$name["sumnormchasvh"] = "Трудо-<br>емкость";
		$name["dateish"] = "Дата";
		$name["nomerish"] = "Номер";
		$name["adresish"] = "Адресат";
		$name["contentish"] = "Краткое содержание";
		$name["scanish"] = "Скан";
		$name["countlistish"] = "Кол. листов";
		$name["sumnormchasish"] = "Трудо-<br>емкость";
		$name["fioispish"] = "ФИО исполнителя";
		$name["datereg"] = "Рег. дата";
		$name["nomerreg"] = "Рег. номер";
		$name["datecontrol"] = "На контроле";
		$name["izdname"] = "Изделие";
		$name["prim"] = "Примечание";
		return $name;
	}

	function findlist($find) {
		$findlist = "";
		if($find != null and $find != "" and $find != "undefined") {
			$fieldForSearch = "contentvh, datevh, nomervh, adresvh, contentvh, countlistvh, dateish, nomerish, adresish, contentish, countlistish, fioispish";
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
	
	function getDatatable($tableName, $table_id, $fieldList, $page, $limitLine, $find){
		$db = db_connect();
		$sortfield = "if(datevh != '', datevh, dateish) as summ ";
		$sortirovka = "if(summ = '' or summ is null, 1, 0), SUBSTRING_INDEX(summ,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(summ,'.',2),'.',-1), SUBSTRING_INDEX(summ,'.',1), id";
		$findlist = findlist($find);
		if($table_id != "0") $r = $db->prepare("SELECT {$fieldList}, {$sortfield} FROM {$tableName} WHERE hide = 0 and detid = '{$table_id}' {$findlist} ORDER BY {$sortirovka} LIMIT {$page}, {$limitLine}");
		if($table_id == "0") $r = $db->prepare("SELECT name as izdname, {$fieldList}, {$sortfield} FROM {$tableName} LEFT JOIN izdelie on izdelie.id = mailbox.detid WHERE mailbox.hide = 0 {$findlist} and datecontrol != '' ORDER BY {$sortirovka} LIMIT {$page}, {$limitLine}");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC);
	}

	function maxResult($tableName, $table_id, $find) {
		$db = db_connect();
		$findlist = findlist($find);
		if($table_id != "0") $r = $db->prepare("SELECT COUNT(id) as maxResult FROM {$tableName} WHERE hide = 0 and detid = '{$table_id}' {$findlist}");
		if($table_id == "0") $r = $db->prepare("SELECT COUNT(id) as maxResult FROM {$tableName} WHERE hide = 0 {$findlist} and datecontrol != ''");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC)[0]['maxResult'];
	}

	$fieldList = "mailbox.id, datevh, nomervh, adresvh, contentvh, scanvh, countlistvh, sumnormchasvh, datereg, nomerreg, datecontrol,
	dateish, nomerish, adresish, contentish, scanish, countlistish, sumnormchasish, fioispish, prim";
	$page = $_POST['page'] == 0 ? (maxResult("mailbox", $_POST['tabNumber'], $_POST['find'])/100|0)*100 : ($_POST['page']-1)*100;
	$jsonArray = getDatatable("mailbox", $_POST['tabNumber'], $fieldList, $page, 100, $_POST['find']);

	$headerNameList = columnNameList();
	$headerNameList['db'] = "mailbox";
	$headerNameList['maxResult'] = maxResult("mailbox", $_POST['tabNumber'], $_POST['find']);
	$headerNameList['maxPage'] = (maxResult("mailbox", $_POST['tabNumber'], $_POST['find'])/100|0) + 1;
	$headerNameList['page'] = ($page / 100) + 1;
	array_unshift($jsonArray, $headerNameList); // Добавляет один или несколько элементов в начало массива
	$jsonArray = array_values($jsonArray);// Переиндексируем массив, чтобы ключи начинались с 0
	$json = json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
    echo $json;
	
	unset($_POST['xhrload']);
	unset($_POST['page']);
	exit;
}

if(isset($_POST['scanload'])) {

	function getLinkList($id, $type) {
		$db = db_connect();
		$r = $db->prepare("SELECT prefix, filename, maskname FROM uplfiles WHERE hide = 0 and detid = '{$id}' and tabname = 'mailbox' and type = '{$type}'");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC);
	}

	$jsonArray = getLinkList($_POST['scan_id'], $_POST['scan_type']);

	$json = json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
    echo $json;
	unset($_POST['scanload']);
	unset($_POST['scan_id']);
	unset($_POST['scan_type']);
	exit;
}

if(isset($_POST['add'])) {

	function add($id) {
		$db = db_connect();
		$r = $db->prepare("INSERT INTO mailbox(hide, detid) VALUES ('0', '{$id}')");
		$r->execute();
		return $db->lastInsertId();
	}

	$lastId = add($_POST['add']);

	echo $lastId;

	unset($_POST['add']);
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
		$r = $db->prepare("SELECT prefix, filename, maskname, type, detid FROM uplfiles WHERE hide = '0' and tabname = 'mailbox' and ({$items})");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC);
	}

	$array = finditems(createList($_POST['finditems']));

	$json = json_encode($array, JSON_UNESCAPED_UNICODE);

	echo $json;

	unset($_POST['finditems']);
	exit;
}

?>

<script>
	mailfindbox(<?=$_GET['id']?>);
	if(!document.getElementById("tablediv")) {
		let tablayer = document.createElement("div");
		tablayer.id = "tablediv";
		varframe = document.getElementById("varframe");
		varframe.appendChild(tablayer);
	}

	xhrLoad("xhrload", <?=$_GET['id']?>, 0);
</script>

<?

require ("megatable.php");

if($_GET['print'] == 1) echo "<link href='css/litemgtable.css' rel='stylesheet'>";

class mgtable extends megatable
{

public function table_big_header() //заголовок таблицы
{
	echo "<tr>";
	echo "<td class = 'table_big_header' colspan = 999 align = center><div style='float:left;width:100%;text-align:center;'>" .$this->header_big_name ."</div><div class = 'mg_table_button'><div class = 'printer_layer'><a onclick = window.open('" .basename($_SERVER['PHP_SELF']) ."?print=1" .$_SERVER['QUERY_STRING'] ."','Печать','widht=600,height=800,location=no')><img src='include/Printer.png' alt='закрыто'></a></div></div></td>"; //малый заголовок
	echo "</tr>";
	echo "<tr>";
	echo "<td class = 'table_colspan_header' colspan = 8 align = center>Входящие</td>"; //малый заголовок
	echo "<td class = 'table_colspan_header' colspan = 8 align = center>Исходящие</td>";
	echo "</tr>";
}

public function table_content($r,$f,$j)
{
	for ($i=0; $i<mysql_num_fields($r); $i++) // чтение значений в строке
	{
		if ($i==0) echo "<tr>";
		$this->all_col($r,$f,$i,$j);
		if($this->hide_field($r,$i)) continue;
		if (mysql_field_name($r, $i) == "datevh" or mysql_field_name($r, $i) == "dateish") print "<td><input type='text' id = '" .$f['id'] ."_" .mysql_field_name($r, $i) ."_" .$this->dbtable ."' class='dateinput' onchange='update_db(this.id,this.value);' value='" .$f[$i] ."'></td>"; //заполнение полей (ячейки в строке)
		else print "<td id = '" .$f['id'] ."_" .mysql_field_name($r, $i) ."_" .$this->dbtable ."' class = 'simplefield'>" .$f[$i] ."</td>"; //заполнение полей (ячейки в строке)
	}
}

public function bd_query_text1()
{
	if($this->show_hide == 0) $this->hfilter = " and hide <> 1 ";
//$local_sortirovka = "if(datevh = '' or datevh is null, 1, 0), SUBSTRING_INDEX(mailbox.datevh,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(mailbox.datevh,'.',2),'.',-1), SUBSTRING_INDEX(mailbox.datevh,'.',1), id";

	$dbquery = "SELECT " .$this->field ." FROM " .$this->dbtable ." where id >= 0 " .$this->hfilter .$this->filter ." ORDER BY " .$local_sortirovka ." LIMIT " .$this->show_line .", " .$this->max_line;
	$this->tabposcount = mysql_num_rows(mysql_query($dbquery));
	return $dbquery;
}

public function date_type_field($field)
{
	
}

public function filter_vvoda_listov($str_data1)
{
	$rule = array("A5", "А5", "A4", "А4", "A3", "А3",  "A2", "А2", "A1", "А1", "A0", "А0", "ф.", "н/ч", ",");
	$newchar = array("*0.5", "*0.5", "*1", "*1", "*2", "*2", "*4", "*4", "*8", "*8", "*16", "*16", "", "", ".");

	if ($str_data1 == "") $str_data1 = "0";

	$str_data1 = str_replace("+", "<br>", $str_data1);
	
	$st1 = explode("<br>", $str_data1);

	for($i=0;$i<count($st1);$i++)
	{
		$s1 = (float)str_replace($rule, $newchar, $st1[$i]);
		is_numeric($s1) ? $s1 : $s1 = 0;
		$str_s .= $s1;
		$i < (count($st1) - 1) ? $str_s .= "+" : "";
	}

	return $str_s;
}

	public function add_col($r,$f,$i,$j)
{
	require ("global_path.php");

	
	//Доп колонка Скан входящие
	if(!isset($j) and $i == 8) print "<td class = 'table_header' style = 'width:60px;'>Скан</td>";  //заголовок
	if(isset($j) and $i == 8) 
	{

		$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'mailbox' and detid = '" .$f['id'] ."' and type = 1 ");
	
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

	//Доп колонка Скан исходящие
	if(!isset($j) and $i == 14) print "<td class = 'table_header' style = 'width:60px;'>Скан</td>";  //заголовок
	if(isset($j) and $i == 14) 
	{

		$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'mailbox' and detid = '" .$f['id'] ."' and type = 2 ");
	
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


	//Доп колонка расчетная
	if(!isset($j) and $i == 10) print "<td class = 'table_header' style = 'width:50px;'>Трудо-<br>емкость</td>";  //заголовок
	if(isset($j) and $i == 10) 
	{

		echo "<td class = 'table_field' title = '" ."(" .$this->filter_vvoda_listov($f['countlistvh']) .")*0.5" ."' date-id = " .$f['id'] .">";

		echo $f['sumnormchasvh'];

		echo "</td>";  //колонка
		
	}	

	//Доп колонка расчетная
	if(!isset($j) and $i == 16) print "<td class = 'table_header' style = 'width:50px;'>Трудо-<br>емкость</td>";  //заголовок
	if(isset($j) and $i == 16) 
	{

		echo "<td class = 'table_field' title = '" ."(" .$this->filter_vvoda_listov($f['countlistish']) .")*5" ."' date-id = " .$f['id'] .">";

		echo $f['sumnormchasish'];

		echo "</td>";  //колонка
		
	}	
	
}
}

/*
echo $_GET['print'] == 1 ? "<div style = 'display:none;'>" : "<div style = 'position:sticky;top:0px;'>";
echo "Найти: <input type='text' style='width:600px;' class='findall' >";
echo " Дата от <input id = 'finddatebegin' type='text'  style='width:70px;' class=''> до <input id = 'finddateend' type='text' style='width:70px;' class=''> <button onclick='startfind();'>Найти</button><br><br>";
//$hide_ready_work = " and gotovnost < 100 ";
echo "</div>";

echo "<div id='filter'>"; //filter
*/


require ("dbconnect.php");
$dtable = new mgtable;

//Перерасчет измененных полей
//$r = mysql_query("SELECT * FROM mailbox where hide <> 1 "); // and upd = '1' ");
$r = mysql_query("SELECT * FROM mailbox where hide <> 1 and upd = '1' ");
for ($i=0; $i<mysql_num_rows($r); $i++) //чтение строк
{
	$f = mysql_fetch_array($r);
	@eval("\$summa_normachasovvh = (" .$dtable->filter_vvoda_listov($f['countlistvh']) .")*0.5;");
	@eval("\$summa_normachasovish = (" .$dtable->filter_vvoda_listov($f['countlistish']) .")*5;");
	mysql_query("UPDATE kk3project.mailbox SET sumnormchasvh = '" .$summa_normachasovvh ."', upd = '0' WHERE id = '" .$f['id'] ."' ");
	mysql_query("UPDATE kk3project.mailbox SET sumnormchasish = '" .$summa_normachasovish ."', upd = '0' WHERE id = '" .$f['id'] ."' ");
}//****

$dtable->htag = "varframe";
$dtable->addfield = "detid";
$dtable->field = " *, if(datevh != '', datevh, dateish) as summ ";
$dtable->sortirovka = " if(summ = '' or summ is null, 1, 0), SUBSTRING_INDEX(summ,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(summ,'.',2),'.',-1), SUBSTRING_INDEX(summ,'.',1), id ";

if(isset( $_GET['id'])) {
$dtable->getdata = "&id=" .$_GET['id'];
$dtable->addvalue = $_GET['id'];
}
else {
$dtable->getdata = "&id=" .$_POST['id'];
$dtable->addvalue = $_POST['id'];
}
//$dtable->getdata = $_SERVER['QUERY_STRING'];
//$dtable->filter = " and detid = " .$_GET['id'];

$sql_date_field = " CONCAT(SUBSTRING_INDEX(datevh,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(datevh,'.',2),'.',-1), SUBSTRING_INDEX(datevh,'.',1)) ";
$sql_date_begin = " CONCAT(SUBSTRING_INDEX('" .$_POST['datebegin'] ."','.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX('" .$_POST['datebegin'] ."','.',2),'.',-1), SUBSTRING_INDEX('" .$_POST['datebegin'] ."','.',1)) ";
$sql_date_end = " CONCAT(SUBSTRING_INDEX('" .$_POST['dateend'] ."','.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX('" .$_POST['dateend'] ."','.',2),'.',-1), SUBSTRING_INDEX('" .$_POST['dateend'] ."','.',1)) ";

$_POST['datebegin'] == "" ? $find_date1 = "" : $find_date1 = "and " .$sql_date_field ." >= " .$sql_date_begin;
$_POST['dateend'] == "" ? $find_date2 = "" : $find_date2 = "and " .$sql_date_field ." <= " .$sql_date_end;

if(isset($_GET['id'])) $dtable->filter = " and detid = " .$_GET['id'];
else $dtable->filter = " and detid = " .$_POST['id'] ." and (datevh LIKE '%" .$_POST['findany'] ."%' or nomervh LIKE '%" .$_POST['findany'] ."%' or adresvh LIKE '%" .$_POST['findany'] ."%' or contentvh LIKE '%" .$_POST['findany'] ."%' or dateish LIKE '%" .$_POST['findany'] ."%' or nomerish LIKE '%" .$_POST['findany'] ."%' or adresish LIKE '%" .$_POST['findany'] ."%' or contentish LIKE '%" .$_POST['findany'] ."%' or fioispish LIKE '%" .$_POST['findany'] ."%') " .$find_date1 .$find_date2;

//else $dtable->filter = " and detid = " .$_POST['id'] ." and (datevh LIKE '%" .$_GET['datevh'] ."%' and nomervh LIKE '%" .$_GET['nomervh'] ."%' and adresvh LIKE '%" .$_POST['adresvh'] ."%' and contentvh LIKE '%" .$_GET['contentvh'] ."%' and dateish LIKE '%" .$_GET['dateish'] ."%' and nomerish LIKE '%" .$_GET['nomerish'] ."%' and adresish LIKE '%" .$_GET['adresish'] ."%' and contentish LIKE '%" .$_GET['contentish'] ."%' and fioispish LIKE '%" .$_GET['fioispish'] ."%') ";

//$dtable->sortirovka = "";  if(datevh = '' or datevh is null, 1, 0), SUBSTRING_INDEX(mailbox.datevh,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(mailbox.datevh,'.',2),'.',-1), SUBSTRING_INDEX(mailbox.datevh,'.',1), id



$dtable->dbtable = "mailbox";
$dtable->header_big_name = "Переписка";
//$dtable->bd_query_text();
$dtable->show_add_db_button = 1;
$dtable->pos_nomer_col = 0;
$dtable->nomer_col_menu_style = " relocnomer ";
$dtable->max_line = 100;
if(isset($_GET['page'])) $dtable->show_page = $_GET['page'];
//$dtable->datatable();
//echo $dtable->max_pages ." page: " .$dtable->show_page;
echo "<br>";
echo "</div>"; //filter


?>
<script>/*

var list_nomer_id = <?echo $_GET['id'];?>; //Переменная для сохранения id страницы

function startfind() {

$("#filter").load('mailbox.php #filter',{
		'id':list_nomer_id,
		'findany':$(".findall").val(),
		'datebegin':$("#finddatebegin").val(),
		'dateend':$("#finddateend").val()
	},
	function(){
		$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
		chkzamok();
	}
);

}

function chkzamok() {
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}
}


$(document).ready(function() {
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}
$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
$("#finddatebegin, #finddateend").mask("99.99.9999", {placeholder: "дд.мм.гггг" });

$(".findall").keyup(function(e) {
startfind();
});

});*/
</script>
</body>
</html>
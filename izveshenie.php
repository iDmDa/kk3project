<?php
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

if(isset($_POST['izv'])) {

	function columnNameList() {
		$name["numii"] = "№ ИИ";
		$name["editdoc"] = "Корректируемые документы";
		$name["naimenovenie"] = "Краткое содержание корректировки";
		$name["reason"] = "Основание для корректировки";
		$name["fio"] = "Исполнитель";
		$name["otd"] = "Подразделение";
		$name["codii"] = "Код";
		$name["zadel"] = "Указание о заделе";
		$name["vnedrenie"] = "Указание о внедрении";
		$name["date"] = "Дата выпуска";
		$name["numish"] = "№ исх. на отправку дубликатов";
		$name["scan"] = "Скан";
		$name["trudoemc"] = "Трудо-<br>емкость";
		$name["prim"] = "Примечание";
		$name["izdname"] = "Изделие";
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
		if($table_id != "0") $r = $db->prepare("SELECT {$fieldList}{$fieldCreate} FROM {$tableName} WHERE hide = 0 and doctype = 1 and detid = '{$table_id}' {$findlist} ORDER BY {$sortirovka} LIMIT {$page}, {$limitLine}");
		if($table_id == "-1") $r = $db->prepare("SELECT name as izdname, docwork.{$fieldList}{$fieldCreate} FROM {$tableName} LEFT JOIN izdelie ON izdelie.id = docwork.detid WHERE docwork.hide = 0 and doctype = 1 {$findlist} ORDER BY {$sortirovka} LIMIT {$page}, {$limitLine}");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC);
	}

	function maxResult($tableName, $table_id, $find = "", $fieldForSearch) {
		$db = db_connect();
		$findlist = findlist($find, $fieldForSearch);
		if($table_id != "0") $r = $db->prepare("SELECT COUNT(id) as maxResult FROM {$tableName} WHERE hide = 0 and doctype = 1 and detid = '{$table_id}' {$findlist}");
		if($table_id == "-1") $r = $db->prepare("SELECT COUNT(id) as maxResult FROM {$tableName} WHERE hide = 0 and doctype = 1 {$findlist}");
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
	$fieldForSearch = "numii, editdoc, naimenovenie, reason, fio, otd, codii, zadel, vnedrenie, date, numish, prim";
    $fieldList = "id, numii, editdoc, naimenovenie, reason, fio, otd, codii, zadel, vnedrenie, date, numish, scan, trudoemc, prim";
	$showField = "numii, editdoc, naimenovenie, reason, fio, otd, codii, zadel, vnedrenie, date, numish, scan, trudoemc, prim";
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

    unset($_POST['izv']);
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
		$r = $db->prepare("SELECT tabname, prefix, filename, maskname, type, detid FROM uplfiles WHERE hide = '0' and tabname = 'docwork' and ({$items})");
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
		$r = $db->prepare("INSERT INTO docwork(hide, detid, doctype) VALUES ('0', '{$id}','1')");
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
<script>
	try {
		izvfindbox(<?=$_GET['id']?>);
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
    izvLoad(sendObject);
</script>
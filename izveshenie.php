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
		$name["trudoemc"] = "Трудоемкость";
		$name["prim"] = "Примечание";
		return $name;
	}

    function getDatatable($tableName, $table_id, $fieldList, $page, $limitLine, $find = "", $fieldForSearch = ""){
		$db = db_connect();
		$fieldCreate = ", CONCAT(SUBSTRING_INDEX(date, '.', -1), 
						SUBSTRING_INDEX(SUBSTRING_INDEX(date, '.', 2), '.', -1), 
						SUBSTRING_INDEX(date, '.', 1)) as txtToDate";
		$sortirovka = "CASE WHEN txtToDate = '' THEN 1 ELSE 0 END, txtToDate ASC, CASE WHEN txtToDate = '' THEN id END";
		$findlist = ""; //findlist($find);
		$r = $db->prepare("SELECT {$fieldList}{$fieldCreate} FROM {$tableName} WHERE hide = 0 and doctype = 1 and detid = '{$table_id}' {$findlist} ORDER BY {$sortirovka} LIMIT {$page}, {$limitLine}");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC);
	}

	function maxResult($tableName, $table_id, $find = "", $fieldForSearch) {
		$db = db_connect();
		$findlist = findlist($find, $fieldForSearch);
		$r = $db->prepare("SELECT COUNT(id) as maxResult FROM {$tableName} WHERE hide = 0 and doctype = 1 and detid = '{$table_id}' {$findlist}");
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
	$fieldForSearch = "numii, editdoc, naimenovenie, reason, fio, otd, codii, zadel, vnedrenie, date, numish, prim";
    $fieldList = "id, numii, editdoc, naimenovenie, reason, fio, otd, codii, zadel, vnedrenie, date, numish, scan, trudoemc, prim";
	$showField = "numii, editdoc, naimenovenie, reason, fio, otd, codii, zadel, vnedrenie, date, numish, scan, trudoemc, prim";
    $jsonArray = getDatatable("docwork", $_POST['tab_id'], $fieldList, 0, 100, $_POST['find'], $fieldForSearch);

	$headerNameList = columnNameList();
	$headerNameList['fieldList'] = $fieldList;
	$headerNameList['showField'] = $showField;
	$headerNameList['db'] = "docwork";
	$headerNameList['maxResult'] = maxResult("docwork", $_POST['tab_id'], $_POST['find'], $fieldForSearch);
	$headerNameList['maxPage'] = ($headerNameList['maxResult']/100|0) + 1;
	$headerNameList['page'] = ($page / 100) + 1;
	array_unshift($jsonArray, $headerNameList); // Добавляет один или несколько элементов в начало массива
	$jsonArray = array_values($jsonArray);// Переиндексируем массив, чтобы ключи начинались с 0
	$json = json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
    echo $json;

    unset($_POST['izv']);
	exit;
}

//echo "izv";
?>
<script>
    let windata = window.location.search;
    let urlParams = new URLSearchParams(windata);
    let get_id = urlParams.get('id');
    console.log(window.location);
    izvLoad("izv", <?=$_GET['id']?>);
</script>
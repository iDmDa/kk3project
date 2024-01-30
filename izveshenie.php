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

    function getDatatable($tableName, $table_id, $fieldList, $page, $limitLine, $find = ""){
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
    $fieldList = "numii, editdoc, naimenovenie, reason, fio, otd, codii, zadel, vnedrenie, date, numish, scan, trudoemc, prim";
    $jsonArray = getDatatable("docwork", $_POST['tab_id'], $fieldList, 0, 100, $_POST['find']);
	$json = json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
    echo $json;

    unset($_POST['izv']);
	exit;
}

echo "izv";
?>
<script>
    let windata = window.location.search;
    let urlParams = new URLSearchParams(windata);
    let get_id = urlParams.get('id');
    console.log(window.location);
    izvLoad("izv", <?=$_GET['id']?>);
</script>
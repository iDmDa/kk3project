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
    function getDatatable($tableName, $table_id, $fieldList, $page = 0, $limitLine = 0, $find = ""){
		$db = db_connect();
		$sortfield = "if(datevh != '', datevh, dateish) as summ ";
		$sortirovka = "if(summ = '' or summ is null, 1, 0), SUBSTRING_INDEX(summ,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(summ,'.',2),'.',-1), SUBSTRING_INDEX(summ,'.',1), id";
		$findlist = ""; //findlist($find);
		//$r = $db->prepare("SELECT {$fieldList}, {$sortfield} FROM {$tableName} WHERE hide = 0 and detid = '{$table_id}' {$findlist} ORDER BY {$sortirovka} LIMIT {$page}, {$limitLine}");
        $r = $db->prepare("SELECT {$fieldList} FROM {$tableName} WHERE hide = 0 and detid = '{$table_id}' and doctype = 1");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC);
	}

    $fieldList = "numii, editdoc, naimenovenie, reason, fio, otd, codii, zadel, vnedrenie, date, numish, scan, trudoemc, prim";
    $jsonArray = getDatatable("docwork", $_POST['tab_id'], $fieldList, $page, 100, $_POST['find']);
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
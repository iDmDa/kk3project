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
		if($table_id == "-1") $r = $db->prepare("SELECT name as izdname, {$fieldList}, {$sortfield} FROM {$tableName} LEFT JOIN izdelie on izdelie.id = mailbox.detid WHERE mailbox.hide = 0 {$findlist} ORDER BY {$sortirovka} LIMIT {$page}, {$limitLine}");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC);
	}

	function maxResult($tableName, $table_id, $find) {
		$db = db_connect();
		$findlist = findlist($find);
		if($table_id != "0") $r = $db->prepare("SELECT COUNT(id) as maxResult FROM {$tableName} WHERE hide = 0 and detid = '{$table_id}' {$findlist}");
		if($table_id == "0") $r = $db->prepare("SELECT COUNT(id) as maxResult FROM {$tableName} WHERE hide = 0 {$findlist} and datecontrol != ''");
		if($table_id == "-1") $r = $db->prepare("SELECT COUNT(id) as maxResult FROM {$tableName} WHERE hide = 0 {$findlist}");
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC)[0]['maxResult'];
	}

	$fieldList = "mailbox.id, datevh, nomervh, adresvh, contentvh, scanvh, countlistvh, sumnormchasvh, datereg, nomerreg, datecontrol,
	dateish, nomerish, adresish, contentish, scanish, countlistish, sumnormchasish, fioispish, prim";
	$page = $_POST['page'] == 0 ? (maxResult("mailbox", $_POST['tabNumber'], $_POST['find'])/100|0)*100 : ($_POST['page']-1)*100;
	$jsonArray = getDatatable("mailbox", $_POST['tabNumber'], $fieldList, $page, 100, $_POST['find']);

	$headerNameList = columnNameList();
	$headerNameList['tabNumber'] = $_POST['tabNumber'];
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
		$r = $db->prepare("SELECT tabname, prefix, filename, maskname, type, detid FROM uplfiles WHERE hide = '0' and tabname = 'mailbox' and ({$items})");
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
	try {
		mailfindbox(<?=$_GET['id']?>);
	}
	catch {
		console.log("errrrrrr");
		console.log("errrrrrr");
		let varframe = document.getElementById("varframe");
		varframe.innerHTML = "<p style='font-size: 25px;'>Версия вашего браузера устарела и не может отобразить эту страницу! Скачайте новую версию браузера по <a href = 'http://server-kk3/projectdata/docwork/1706508740_FirefoxPortable112.zip'>ссылке</a></p>";
		console.log("errrrrrr");
	}
	
	if(!document.getElementById("tablediv")) {
		let tablayer = document.createElement("div");
		tablayer.id = "tablediv";
		varframe = document.getElementById("varframe");
		varframe.appendChild(tablayer);
	}

	let sendObject = {
		"tabNumber": <?=$_GET['id']?>,
		"page": 0,
		"find": findline.value
	};
	xhrLoad(sendObject);
</script>
</body>
</html>
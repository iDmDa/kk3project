<?
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Если используете CORS и preflight
    http_response_code(200);
    exit;
}

function findlist($find) {
    $findlist = "";
    if(substr_count($find, '#') == 2) {
        $diapazon = trim(explode("#", $find)[1]);
        $data1 = trim(explode("-", $diapazon)[0]);
        $data2 = trim(explode("-", $diapazon)[1]);

        $data1 = trim(explode(".", $data1)[2]) ."-" .trim(explode(".", $data1)[1])."-" .trim(explode(".", $data1)[0]);
        $data2 = trim(explode(".", $data2)[2]) ."-" .trim(explode(".", $data2)[1])."-" .trim(explode(".", $data2)[0]);

        $findlist = "and ((STR_TO_DATE(date, '%d.%m.%Y') BETWEEN '{$data1}' AND '$data2')) ";
        $find = trim(explode("#", $find)[2]);
    }

    if($find != null and $find != "" and $find != "undefined") {
        $fieldForSearch = "numii, editdoc, naimenovenie, reason, fio, otd, codii, zadel, vnedrenie, date, numish, prim";
        $arrSearchList = explode(",", trim($fieldForSearch));
        $findlist .= "and (";
        foreach ($arrSearchList as $value) {
            $findlist .= "{$value} LIKE '%{$find}%' or ";
        }
        $findlist = substr($findlist, 0, -4);
        $findlist .= ")";
    }
    return $findlist;
}

// Читаем "сырое" тело запроса (JSON)
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

header('Content-Type: application/json; charset=utf-8');

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
    exit;
}

$detid = $data['izdelieid'];
$page = (int) $data['page'];
$filter = findlist($data['filter']);
$sortrule = $data['sortRule'] ?? "byNumber";

include("../dbdata.php");

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_pass);
    // Устанавливаем атрибуты для обработки ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo json_encode(['Ok' => 'Соединение установлено!'], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка подключения:'. $e->getMessage()]);
}

// Подсчитаем общее количество результатов
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM docwork WHERE doctype = 1 and hide = 0 {$filter} AND detid = :detid");
$stmt->execute(['detid' => $detid]);
$totalResults = $stmt->fetchColumn();

// Вычислим количество страниц (100 записей на страницу)
$pageSize = 100;
$totalPages = ceil($totalResults / $pageSize);

// Получим данные для последней страницы
$startLine = $page >= 0 ? $page * $pageSize : ($totalPages - 1) * $pageSize;

//$sortfield = "date as summ ";
//$sortirovka = "if(summ = '' or summ is null, 1, 0), SUBSTRING_INDEX(summ,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(summ,'.',2),'.',-1), SUBSTRING_INDEX(summ,'.',1), id";

if($sortrule = "byNumber") $sortirovka = "numii IS NULL OR numii = '' OR numii NOT LIKE '%.%', CAST(SUBSTRING_INDEX(numii, '.', -1) AS UNSIGNED), (date IS NULL OR date = ''), STR_TO_DATE(date, '%d.%m.%Y')";
if($sortrule = "byDate") $sortirovka = "(date IS NULL OR date = ''), STR_TO_DATE(date, '%d.%m.%Y'), numii IS NULL OR numii = '' OR numii NOT LIKE '%.%', CAST(SUBSTRING_INDEX(numii, '.', -1) AS UNSIGNED)";

//$query = "SELECT *, {$sortfield} FROM docwork where doctype = 1 and hide = 0 {$filter} and detid = :detid ORDER BY {$sortirovka} LIMIT :pageSize OFFSET :startLine";
$query = "SELECT * FROM docwork where doctype = 1 and hide = 0 {$filter} and detid = :detid ORDER BY {$sortirovka} LIMIT :pageSize OFFSET :startLine";

try {
    // Подготовка запроса
    $stmt = $pdo->prepare($query);

    // Привязка параметра
    $stmt->bindParam(':detid', $detid, PDO::PARAM_INT);
    $stmt->bindValue(':pageSize', $pageSize, PDO::PARAM_INT);  // Всегда 100
    $stmt->bindValue(':startLine', $startLine, PDO::PARAM_INT);  // Индекс первой записи на последней странице

    // Выполнение запроса
    $stmt->execute();
    
    // Получение данных
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = findLinks($result, $pdo);
    
    if ($result) {
        echo json_encode([$result, "pages" => $totalPages]);
    } else {
        //echo "Пользователь не найден.";
    }
} catch (PDOException $e) {
    echo json_encode([null, null]);
    //echo "Ошибка выполнения запроса: " . $e->getMessage();
}

function findLinks($data, $pdo) {
    //Поиск файлов, загруженных в запись
    $detidArr = [];
    foreach ($data as $value) {
        $detidArr[] = $value["id"];
    }

    $detidList = implode(',', $detidArr);
    $query = "SELECT * FROM uplfiles where hide = 0 and tabname = 'docwork' and detid IN ($detidList)";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$value) { //&$value - необходим для изменения исходного массива, иначе меняет копию
        $scanvh = [];
        $scanish = [];
        foreach ($result as $link) {
            if($value["id"] == $link["detid"] and $link["type"] == 2) $scanvh[] = $link;
            if($value["id"] == $link["detid"] and $link["type"] == 1) $scanish[] = $link;
        }
        $value['scan'] = $scanvh;
        $value['trudoemc'] = $scanish;
        unset($scanvh);
        unset($scanish);
    }

    unset($value); // важно

    return $data;
}
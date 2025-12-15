<?
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Если используете CORS и preflight
    http_response_code(200);
    exit;
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
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM mailbox WHERE hide = 0 AND detid = :detid");
$stmt->execute(['detid' => $detid]);
$totalResults = $stmt->fetchColumn();

// Вычислим количество страниц (100 записей на страницу)
$pageSize = 100;
$totalPages = ceil($totalResults / $pageSize);

// Получим данные для последней страницы
$startLine = $page > 0 ? $page * $pageSize : ($totalPages - 1) * $pageSize;

$sortfield = "if(datevh != '', datevh, dateish) as summ ";
$sortirovka = "if(summ = '' or summ is null, 1, 0), SUBSTRING_INDEX(summ,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(summ,'.',2),'.',-1), SUBSTRING_INDEX(summ,'.',1), id";
$query = "SELECT *, {$sortfield} FROM mailbox where hide = 0 and detid = :detid ORDER BY {$sortirovka} LIMIT :pageSize OFFSET :startLine";

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
        echo json_encode([$result, $totalPages]);
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
    $query = "SELECT * FROM uplfiles where hide = 0 and tabname = 'mailbox' and detid IN ($detidList)";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$value) { //&$value - необходим для изменения исходного массива, иначе меняет копию
        $scanvh = [];
        $scanish = [];
        foreach ($result as $link) {
            if($value["id"] == $link["detid"] and $link["type"] == 1) $scanvh[] = $link;
            if($value["id"] == $link["detid"] and $link["type"] == 2) $scanish[] = $link;
        }
        $value['scanvh'] = $scanvh;
        $value['scanish'] = $scanish;
        unset($scanvh);
        unset($scanish);
    }

    unset($value); // важно

    return $data;
}
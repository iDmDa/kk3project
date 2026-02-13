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

$table = $data['table'];
$detid = $data['id'];
$hide = $data['hide'];
$doctype = $data['doctype'];
//echo "doctype - ".$doctype;
include("../dbdata.php");

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_pass);
    // Устанавливаем атрибуты для обработки ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo json_encode(['Ok' => 'Соединение установлено!'], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Ошибка подключения'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Список разрешенных таблиц
$allowed_tables = ['mailbox', 'docwork', 'contragent']; // Список таблиц, с которыми можно работать

// Проверяем, что таблица из POST входит в список разрешенных
if (in_array($table, $allowed_tables)) {
    // Создаем SQL-запрос для вставки данных
    $query = "INSERT INTO $table (detid, hide) VALUES (:detid, :hide)";
    if(isset($doctype)) $query = "INSERT INTO $table (detid, hide, doctype) VALUES (:detid, :hide, :doctype)";

    // Подготовка запроса
    $stmt = $pdo->prepare($query);

    // Привязываем параметры
    $stmt->bindParam(':detid', $detid, PDO::PARAM_INT);
    $stmt->bindParam(':hide', $hide, PDO::PARAM_INT);
    if(isset($doctype)) $stmt->bindParam(':doctype', $doctype, PDO::PARAM_INT);

    // Выполнение запроса
    if ($stmt->execute()) {
        echo json_encode([
            'ok' => true,
            'message' => 'Данные успешно добавлены'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode([
            'ok' => false,
            'error' => 'Ошибка при добавлении данных'
        ], JSON_UNESCAPED_UNICODE);
    }

} else {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => 'Таблица не разрешена'
    ], JSON_UNESCAPED_UNICODE);
}
exit;
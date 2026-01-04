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
$column = $data['column'];
$id = $data['id'];
$content = $data['content'];

// Удаляем все теги, кроме <br>
$content = preg_replace('/<(?!br\s*\/?)[^>]+>/i', '', $content);

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

// Подготовка SQL-запроса для обновления данных
$query = "UPDATE $table SET $column = :content WHERE id = :id";

// Подготовка запроса
$stmt = $pdo->prepare($query);

// Привязка параметров
$stmt->bindParam(':content', $content, PDO::PARAM_STR);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

// Выполнение запроса
if ($stmt->execute()) {
    echo json_encode([
        'ok' => true,
        'message' => 'Данные успешно обновлены'
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'ok' => false,
        'error' => 'Ошибка при добавлении данных'
    ], JSON_UNESCAPED_UNICODE);
}
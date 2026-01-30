<?php
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

$id = $data['id'];
$detid = $data['column'];
$table = $data['table'];
$content = $data['content'];

include("../dbdata.php");

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_pass);
    // Устанавливаем атрибуты для обработки ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo json_encode(['Ok' => 'Соединение установлено!'], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка подключения:'. $e->getMessage()]);
}

// Получаем оригинальную запись
$query = "SELECT * FROM $table WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$original_record = $stmt->fetch(PDO::FETCH_ASSOC);

// Если запись найдена
if ($original_record) {
    // Убираем из массива значение столбца, которое будет заменено
    unset($original_record['id']); // Убираем поле id, если оно автоинкрементное (оно будет создано заново)

    // Меняем нужное поле на новое значение
    $original_record['detid'] = $content;

    // Формируем запрос для вставки копии записи
    $columns = implode(", ", array_keys($original_record));
    $placeholders = ":" . implode(", :", array_keys($original_record));
    
    $insertQuery = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $insertStmt = $pdo->prepare($insertQuery);
    
    // Привязываем значения
    foreach ($original_record as $key => $value) {
        $insertStmt->bindValue(":$key", $value);
    }

    // Выполнение запроса
    if ($insertStmt->execute()) {
        echo json_encode([
            'ok' => true,
            'message' => 'Строка скопирована'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'ok' => false,
            'error' => 'Ошибка при копировании данных'
        ], JSON_UNESCAPED_UNICODE);
    }
}
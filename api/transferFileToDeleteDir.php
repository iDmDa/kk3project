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

function transferFileToDeleteDir(string $from, string $to): bool
{
    // Проверяем, существует ли исходный файл
    if (!file_exists($from)) {
        return false;
    }

    // Создаём директорию назначения, если её нет
    $dir = dirname($to);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    // Перенос файла
    return rename($from, $to);
}

$id = $data['id'];


include("../dbdata.php");

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_pass);
    // Устанавливаем атрибуты для обработки ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo json_encode(['Ok' => 'Соединение установлено!'], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка подключения:'. $e->getMessage()]);
}

$query = "SELECT * FROM uplfiles where id = :id";

try {
    // Подготовка запроса
    $stmt = $pdo->prepare($query);

    // Привязка параметра
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Выполнение запроса
    $stmt->execute();
    
    // Получение данных
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $local_path = $result['local_path'];
    $fullFilename = $result['prefix'] ."_" .$result['filename'];

    transferFileToDeleteDir("../../projectdata/$local_path/$fullFilename", "../../projectdata/deleted/$fullFilename");
    
    if ($result) {
        echo json_encode([$result]);
    } else {
        //echo "Пользователь не найден.";
    }
} catch (PDOException $e) {
    echo json_encode([null, null]);
    //echo "Ошибка выполнения запроса: " . $e->getMessage();
}

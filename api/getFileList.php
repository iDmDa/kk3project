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

$detid = (int) $data['detid'];
$type = (int) $data['type'];
$tabname = $data['tableName'];

include("../dbdata.php");

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка подключения:'. $e->getMessage()]);
}

$query = "SELECT * FROM uplfiles where hide = 0 and detid = :detid and type = :type and tabname = :tabname";

try {
    // Подготовка запроса
    $stmt = $pdo->prepare($query);

    // Привязка параметра
    $stmt->bindParam(':detid', $detid, PDO::PARAM_INT);
    $stmt->bindValue(':type', $type, PDO::PARAM_INT);
    $stmt->bindParam(':tabname', $tabname, PDO::PARAM_STR);


    // Выполнение запроса
    $stmt->execute();
    
    // Получение данных
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(null);
    }
} catch (PDOException $e) {
    echo json_encode(null);
    //echo "Ошибка выполнения запроса: " . $e->getMessage();
}

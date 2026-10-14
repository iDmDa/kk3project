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

include("../dbdata.php");

function getData($query1, $query2) {
    global $pdo;

    try {
        $stmt1 = $pdo->query($query1);
        $stmt2 = $pdo->query($query2);

        $r1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $r2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        if ($r1 and $r2) {
            echo json_encode([$r1, $r2], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([]);
        }

    } catch (PDOException $e) {
        echo json_encode([]);
    }
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка подключения:'. $e->getMessage()]);
}

$sql1 = <<<SQL
    SELECT izdelie.id, 
    izdelie.name, 
    izdelie.catalog, 
    dogovor.nomer 
    FROM izdelie 
    LEFT JOIN dogovor ON izdelie.id=dogovor.detid 
    WHERE izdelie.hide = 0
SQL;

$sql2 = <<<SQL
    SELECT * FROM catalog
SQL;

getData($sql1, $sql2);

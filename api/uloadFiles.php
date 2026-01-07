<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tableName = $_POST['tableName'] ?? 'other';
    $type = $_POST['type'] ?? '1';
    $detid = $_POST['detid'] ?? '0';
    $prim = $_POST['prim'] ?? '';

    $fname = $_FILES['files']['name'][0];

    $year = date("Y");
    $local_path = "$tableName/$year";

    // Проверка наличия файлов
    if (isset($_FILES['files']) && count($_FILES['files']['name']) > 0) {
        // Папка для загрузки файлов

        $uploadDirectory = "../../projectdata/$local_path/";
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        // Обработка каждого файла
        $files = $_FILES['files'];
        $uploadedFiles = [];

        $fileprefix = $_SERVER['REQUEST_TIME'];

        foreach ($files['name'] as $index => $fileName) {
            $fileTmpName = $files['tmp_name'][$index];
            $fileError = $files['error'][$index];

            if ($fileError === UPLOAD_ERR_OK) {

                // новое имя: дата_оригинальноеИмя.ext
                $newFileName = $fileprefix . '_' . $fileName;

                $filePath = $uploadDirectory . $newFileName;

                if (move_uploaded_file($fileTmpName, $filePath)) {
                    $uploadedFiles[] = $filePath;
                    addInfo($tableName, $type, $detid, $fileprefix, $fname, $fname, $prim, $local_path);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Ошибка перемещения файла.'
                    ]);
                    exit;
                }
            }
        }

        // Возвращаем успешный ответ
        echo json_encode(['success' => true, 'uploadedFiles' => $uploadedFiles]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Файлы не были загружены.']);
    }
}

function addInfo($tabname, $type, $detid, $prefix, $filename, $maskname, $prim, $local_path) {
    include("../dbdata.php");

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Ошибка подключения'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $query = "INSERT INTO uplfiles (tabname, type, detid, prefix, filename, maskname, prim, local_path) VALUES (:tabname, :type, :detid, :prefix, :filename, :maskname, :prim, :local_path)";
    $stmt = $pdo->prepare($query);

    $stmt->bindValue(':tabname', $tabname, PDO::PARAM_STR);
    $stmt->bindValue(':type', $type, PDO::PARAM_STR);
    $stmt->bindValue(':detid', $detid, PDO::PARAM_STR);
    $stmt->bindValue(':prefix', $prefix, PDO::PARAM_STR);
    $stmt->bindValue(':filename', $filename, PDO::PARAM_STR);
    $stmt->bindValue(':maskname', $maskname, PDO::PARAM_STR);
    $stmt->bindValue(':prim', $prim, PDO::PARAM_STR);
    $stmt->bindValue(':local_path', $local_path, PDO::PARAM_STR);

    // Выполнение запроса
    if ($stmt->execute()) {
        //echo json_encode(['ok' => true, 'message' => 'Данные успешно добавлены'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        json_encode(['ok' => false, 'error' => 'Ошибка при добавлении данных'], JSON_UNESCAPED_UNICODE);
    }
}

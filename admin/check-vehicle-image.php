<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['alogin'])) {
    http_response_code(403);
    echo json_encode(array('used' => false));
    exit;
}

$hash = strtolower(trim($_POST['hash'] ?? ''));
if (!preg_match('/^[a-f0-9]{64}$/', $hash)) {
    http_response_code(400);
    echo json_encode(array('used' => false));
    exit;
}

require_once __DIR__ . '/includes/config.php';
$rows = $dbh->query('SELECT Vimage1, Vimage2, Vimage3, Vimage4, Vimage5 FROM tblvehicles')->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $vehicleImages) {
    foreach ($vehicleImages as $filename) {
        $path = __DIR__ . '/img/vehicleimages/' . basename((string) $filename);
        if ($filename && is_file($path) && hash_equals($hash, hash_file('sha256', $path))) {
            echo json_encode(array('used' => true));
            exit;
        }
    }
}

echo json_encode(array('used' => false));

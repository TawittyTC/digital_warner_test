<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

$valid_user = getenv('API_USER');
$valid_pass = getenv('API_PASS');

// ถ้าไม่ส่ง Authorization header มา
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Restricted API"');
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(["error" => "Authentication required"]);
    exit;
}

// ตรวจสอบ username/password
if ($_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_pass) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(["error" => "Invalid credentials"]);
    exit;
}
?>

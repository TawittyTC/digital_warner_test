<?php
date_default_timezone_set("Asia/Bangkok");
error_reporting(E_ALL);
ini_set("display_errors", 1);
header("Content-Type: application/json; charset=utf-8");

require_once(dirname(__FILE__) . "/../common/auth.php");
require_once(dirname(__FILE__) . "/../class/tbl_product.php");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $obj = new tbl_product();
            $obj->id = $_GET['id'];
            if ($obj->getById()) {
                echo json_encode(get_object_vars($obj), JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Product not found"]);
            }
        } else {
            $obj = new tbl_product();
            $result = $obj->getActivate();
            $rows = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            echo json_encode($rows, JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid JSON"]);
            exit;
        }

        $obj = new tbl_product();
        foreach ($data as $k => $v) {
            if (property_exists($obj, $k)) {
                $obj->$k = is_string($v) ? trim($v) : $v;
            }
        }
        $obj->create_date = date("Y-m-d H:i:s");

        if ($obj->insertDB()) {
            echo json_encode(["success" => true, "id" => $obj->id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Insert failed"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id"]);
            exit;
        }

        $obj = new tbl_product();
        $obj->id = $data['id'];
        if (!$obj->getById()) {
            http_response_code(404);
            echo json_encode(["error" => "Product not found"]);
            exit;
        }

        foreach ($data as $k => $v) {
            if (property_exists($obj, $k)) {
                $obj->$k = is_string($v) ? trim($v) : $v;
            }
        }
        $obj->update_date = date("Y-m-d H:i:s");

        if ($obj->updateDB()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Update failed"]);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $_DELETE);
        if (!isset($_DELETE['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id"]);
            exit;
        }

        $obj = new tbl_product();
        $obj->id = $_DELETE['id'];
        if ($obj->deleteDB()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Delete failed"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
?>

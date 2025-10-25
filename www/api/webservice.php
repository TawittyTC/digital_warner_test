<?php
date_default_timezone_set("Asia/Bangkok");
error_reporting(E_ALL);
ini_set("display_errors", 1);
header("Content-Type: application/json; charset=utf-8");

require_once(dirname(__FILE__) . "/../commond/auth.php"); 
require_once(dirname(__FILE__) . "/../class/tbl_product.php");

function get_product() {
        if (empty(trim($_GET['id']))) {
            http_response_code(400);
            echo json_encode(["success" => false, "msg" => "Product ID cannot be empty"]);
            return;
        }

        $obj = new tbl_product();
        $obj->id = $_GET['id'];
        if ($obj->getById()) {
            echo json_encode(get_object_vars($obj), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "msg" => "Product not found"]);
        }
        return;
}

function create_product() {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Invalid JSON payload"]);
        return;
    }
    if(empty($data['product_name'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Product name is required"]);
        return;
    }
    if(empty($data['product_type'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Product type is required"]);
        return;
    }
    if(empty($data['product_detail'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Product detail is required"]);
        return;
    }
    if(!isset($data['price_per_unit']) || !is_numeric($data['price_per_unit'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Valid price per unit is required"]);
        return;
    }
    if(empty($data['unit_name'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Unit name is required"]);
        return;
    }
    if(!empty($data['is_stock']) && !in_array($data['is_stock'], ['T', 'F'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "is_stock must be 'T' or 'F'"]);
        return;
    }
    

    $obj_product = new tbl_product();
    $obj_product->product_name = trim($data['product_name']);
    $obj_product->product_type = trim($data['product_type']);
    $obj_product->product_detail = trim($data['product_detail']);
    $obj_product->price_per_unit = floatval($data['price_per_unit']);
    $obj_product->unit_name = trim($data['unit_name']);
    $obj_product->is_stock = isset($data['is_stock']) ? trim($data['is_stock']) : 'F';
    $obj_product->create_by  = $_SERVER['PHP_AUTH_USER'];
    $obj_product->create_date = date("Y-m-d H:i:s");
    $obj_product->update_by = $_SERVER['PHP_AUTH_USER'];
    $obj_product->update_date = date("Y-m-d H:i:s");
    $obj_product->is_enable = 'T';
    $obj_product->is_active = 'T';
    if ($obj_product->insertDB()) {
        echo json_encode(["success" => true, "msg" => "Product created successfully", "Product Name" => $obj_product->product_name]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "msg" => "Product creation failed"]);
    }
}

function update_product() {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Invalid JSON payload"]);
        return;
    }

    // ตรวจสอบ: ต้องมี id ใน Body payload และต้องไม่ว่างเปล่า
    if (!isset($data['id']) || empty(trim($data['id']))) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Missing or empty product id in payload"]);
        return;
    }
    if(empty($data['product_name'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Product name is required"]);
        return;
    }
    if(empty($data['product_type'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Product type is required"]);
        return;
    }
    if(empty($data['product_detail'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Product detail is required"]);
        return;
    }
    if(!isset($data['price_per_unit']) || !is_numeric($data['price_per_unit'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Valid price per unit is required"]);
        return;
    }
    if(empty($data['unit_name'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Unit name is required"]);
        return;
    }
    if(!empty($data['is_stock']) && !in_array($data['is_stock'], ['T', 'F'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "is_stock must be 'T' or 'F'"]);
        return;
    }

    $obj_product = new tbl_product();
    $obj_product->id = $data['id'];
    if (!$obj_product->getById()) {
        http_response_code(404);
        echo json_encode(["success" => false, "msg" => "Product not found for update"]);
        return;
    }
    $obj_product->product_name = trim($data['product_name']);
    $obj_product->product_type = trim($data['product_type']);
    $obj_product->product_detail = trim($data['product_detail']);
    $obj_product->price_per_unit = floatval($data['price_per_unit']);
    $obj_product->unit_name = trim($data['unit_name']);
    $obj_product->is_stock = isset($data['is_stock']) ? trim($data['is_stock']) : 'F';
    $obj_product->update_by = $_SERVER['PHP_AUTH_USER'];
    $obj_product->update_date = date("Y-m-d H:i:s");
    $obj_product->is_enable = 'T';
    $obj_product->is_active = 'T';

    if ($obj_product->updateDB()) {
        echo json_encode(["success" => true, "msg" => "Product updated successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "msg" => "Product update failed"]);
    }
}

function delete_product() {
    parse_str(file_get_contents("php://input"), $delete_data);
    // ตรวจสอบ: ต้องมี id และต้องไม่ว่างเปล่า
    if (!isset($delete_data['id']) || empty(trim($delete_data['id']))) {
        http_response_code(400);
        echo json_encode(["success" => false, "msg" => "Missing or empty product id for deletion"]);
        return;
    }

    $obj = new tbl_product();
    $obj->id = $delete_data['id'];
    if ($obj->disableDB()) { 
        echo json_encode(["success" => true, "msg" => "Product soft deleted successfully (is_enable='F', is_active='F')"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "msg" => "Product deletion failed"]);
    }
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        get_product();
        break;

    case 'POST':
        create_product();
        break;

    case 'PUT':
        update_product();
        break;

    case 'DELETE':
        delete_product();
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "msg" => "Method not allowed"]);
}
?>
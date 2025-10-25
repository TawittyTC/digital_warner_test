# 📦 RESTful API: E-commerce Product Management (PHP/MySQL)

โปรเจกต์นี้คือ **RESTful Web Service API** ที่พัฒนาด้วย PHP สำหรับจัดการข้อมูลสินค้า (Product CRUD) ในระบบ E-commerce โดยเน้นที่ความถูกต้องของข้อมูลผ่านการตรวจสอบ Input (Validation) ที่เข้มงวด และใช้ HTTP Method อย่างถูกต้องตามหลักการ REST

---

## 🎯 Endpoint และการทำงาน (RESTful Methods)

Endpoint หลักสำหรับทุกการดำเนินการคือ **`/api/webservice.php`**

| Method | การดำเนินการ | ตำแหน่งการรับ ID | คำอธิบาย Logic |
| :--- | :--- | :--- | :--- |
| **GET** | Read By ID | **Query Parameter** (`?id=...`) | ดึงข้อมูลสินค้า **ตาม ID เท่านั้น** |
| **POST** | Create | JSON Body | สร้างสินค้าใหม่ (Create) |
| **PUT** | Update | JSON Body | แก้ไขข้อมูลสินค้าทั้งหมด (Update) |
| **DELETE** | Soft Delete | JSON Body | ลบสินค้าแบบ Soft Delete (`is_enable='F'`) |

---

## 🔒 กฎการตรวจสอบข้อมูล (Validation Rules)

API จะส่ง **`400 Bad Request`** หากข้อมูลที่ส่งมาไม่เป็นไปตามกฎเหล่านี้ เพื่อรับประกันความสมบูรณ์ของข้อมูลในฐานข้อมูล:

### 1. **GET (Read By ID)**

* **Required:** ต้องระบุ **`id`** ใน Query String และต้องไม่ว่างเปล่า
* **Response:** หากไม่พบสินค้าตาม ID จะคืนค่า **`404 Not Found`**

### 2. **POST (Create) และ PUT (Update)**

เมธอดเหล่านี้ **บังคับ** ให้ต้องมี Fields ต่อไปนี้ใน **JSON Body**:

| Field | Requirement | เงื่อนไขเพิ่มเติม |
| :--- | :--- | :--- |
| **`id`** | **Required สำหรับ PUT** | ต้องมีและไม่ว่างเปล่า (ใช้เพื่อค้นหาสินค้าที่ต้องการอัปเดต) |
| **`product_name`** | Required | ต้องไม่ว่างเปล่า |
| **`product_type`** | Required | ต้องไม่ว่างเปล่า |
| **`product_detail`** | Required | ต้องไม่ว่างเปล่า |
| **`price_per_unit`** | Required | ต้องมีและเป็น **ตัวเลข** (`is_numeric`) |
| **`unit_name`** | Required | ต้องไม่ว่างเปล่า |
| `is_stock` | Optional | ถ้ามี ต้องเป็นค่า **`'T'`** หรือ **`'F'`** เท่านั้น |

### 3. **DELETE (Soft Delete)**

* **Required:** ต้องส่ง **JSON Body** ที่มี Field **`id`** และค่าต้องไม่ว่างเปล่า

---

## 💾 Database Schema (ตาราง tbl_product)

การออกแบบฐานข้อมูลสนับสนุนการตรวจสอบข้อมูลและคุณสมบัติ Audit:

| Field Name | Data Type | Null | Key | คำอธิบาย |
| :--- | :--- | :--- | :--- | :--- |
| **`id`** | INT | NO | PRI | Primary Key (Auto Increment) |
| `product_name` | VARCHAR(255) | NO | | ชื่อสินค้า |
| `product_type` | VARCHAR(100) | NO | | ประเภทสินค้า |
| `product_detail` | TEXT | NO | | รายละเอียดสินค้า |
| `price_per_unit` | DECIMAL(10,2) | NO | | ราคาต่อหน่วย |
| `unit_name` | VARCHAR(50) | NO | | หน่วยนับ (เช่น ถุง, ชิ้น) |
| `is_stock` | CHAR(1) | NO | | สถานะสต็อก ('T'/'F') |
| **`is_enable`** | CHAR(1) | NO | | **Soft Delete Flag**: ('F' คือถูกลบ) |
| **`is_active`** | CHAR(1) | NO | | สถานะการขาย ('T'/'F') |
| `create_by` | VARCHAR(100) | NO | | ผู้สร้างรายการ (ดึงจาก Basic Auth) |
| `create_date` | DATETIME | NO | | วันที่สร้างรายการ |
| `update_by` | VARCHAR(100) | NO | | ผู้อัปเดตล่าสุด (ดึงจาก Basic Auth) |
| `update_date` | DATETIME | NO | | วันที่อัปเดตล่าสุด |

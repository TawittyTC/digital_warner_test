# 📦 Web Service API (Product CRUD)

โปรเจกต์นี้คือ Web Service API ที่พัฒนาด้วย PHP สำหรับจัดการข้อมูลสินค้า (Product) โดยเน้นที่ความถูกต้องของข้อมูลผ่านการตรวจสอบ Input (Validation) ที่เข้มงวด

---

## 🎯 Endpoint และการทำงาน

Endpoint หลักสำหรับทุกการดำเนินการคือ **`/api/webservice.php`**

| Method | การดำเนินการ | คำอธิบาย |
| :--- | :--- | :--- |
| **GET** | Read By ID | ดึงข้อมูลสินค้า **ตาม ID ที่ระบุใน Query Parameter (`?id=`)** เท่านั้น |
| **POST** | Create | สร้างสินค้าใหม่ โดยรับข้อมูลผ่าน **JSON Body** |
| **PUT** | Update | แก้ไขข้อมูลสินค้า โดยรับข้อมูล **JSON Body** ที่มี `id` |
| **DELETE** | Soft Delete | ลบสินค้า (เปลี่ยนสถานะ `is_enable` / `is_active` เป็น `'F'`) โดยรับ `id` ผ่าน **JSON Body** |

---

## 🔒 กฎการตรวจสอบข้อมูล (Validation Rules)

API จะส่ง `400 Bad Request` หากข้อมูลที่ส่งมาไม่เป็นไปตามกฎเหล่านี้:

### 1. **GET (Read By ID)**

* ต้องระบุ **`id`** ใน Query String (`?id=...`) และต้องไม่ว่างเปล่า
* หากไม่พบสินค้าตาม ID จะคืนค่า `404 Not Found`

### 2. **POST (Create) และ PUT (Update)**

เมธอดเหล่านี้ **บังคับ** ให้ต้องมี Fields ต่อไปนี้ใน **JSON Body**:

| Field | Requirement | เงื่อนไขเพิ่มเติม |
| :--- | :--- | :--- |
| **`id`** | **Required สำหรับ PUT** | ต้องมีและไม่ว่างเปล่า (สำหรับ PUT) |
| **`product_name`** | Required | ต้องไม่ว่างเปล่า |
| **`product_type`** | Required | ต้องไม่ว่างเปล่า |
| **`product_detail`** | Required | ต้องไม่ว่างเปล่า |
| **`price_per_unit`** | Required | ต้องมีและเป็น **ตัวเลข** (`is_numeric`) |
| **`unit_name`** | Required | ต้องไม่ว่างเปล่า |
| `is_stock` | Optional | ถ้ามี ต้องเป็นค่า **`'T'`** หรือ **`'F'`** เท่านั้น |

### 3. **DELETE (Soft Delete)**

* ต้องส่ง **JSON Body** ที่มี Field **`id`** และค่าต้องไม่ว่างเปล่า

---

## ⚙️ Audit Fields (จัดการโดย API)

Fields เหล่านี้จะถูกจัดการโดย PHP Script อัตโนมัติ:

| Field | คำอธิบาย |
| :--- | :--- |
| `create_by` / `update_by` | ดึงค่าจาก `$_SERVER['PHP_AUTH_USER']` (Basic Auth) |
| `create_date` / `update_date` | ประทับเวลาปัจจุบัน (`Y-m-d H:i:s`) |
| `is_enable` / `is_active` | ถูกตั้งเป็น `'T'` เมื่อสร้างและอัปเดต และเป็น `'F'` เมื่อลบ |

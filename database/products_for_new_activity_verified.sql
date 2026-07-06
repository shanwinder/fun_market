-- Verified product catalog SQL for a newly created activity.
-- ใช้กับเว็บจริงหลังจากสร้างกิจกรรมใหม่แล้ว
-- วิธีใช้:
-- 1) เปิด phpMyAdmin แล้วเลือก database ของเว็บจริง
-- 2) ถ้ากิจกรรมใหม่คือ activity ล่าสุด ให้รันไฟล์นี้ได้เลย
-- 3) ถ้าต้องการระบุ activity เอง ให้แก้บรรทัด SET @activity_id := ... เป็น SET @activity_id := 2;

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @expected_count := 19;

SET @activity_id := (
    SELECT id
    FROM activities
    ORDER BY id DESC
    LIMIT 1
);

-- หยุดทันทีถ้าไม่พบกิจกรรมเป้าหมาย
SET @missing_activity := IF(@activity_id IS NULL, 1, 0);
SET @message := 'ไม่พบกิจกรรม กรุณาสร้างกิจกรรมใหม่ก่อนรัน SQL นี้';
SET @sql := IF(
    @missing_activity = 1,
    CONCAT('SIGNAL SQLSTATE ''45000'' SET MESSAGE_TEXT = ''', @message, ''''),
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ตรวจสอบก่อนเพิ่มสินค้า: ต้องเป็นกิจกรรมใหม่ที่ต้องการ
SELECT
    id AS target_activity_id,
    title,
    status,
    created_at
FROM activities
WHERE id = @activity_id;

START TRANSACTION;

-- เพิ่ม/อัปเดตรายการสินค้า 19 รายการสำหรับกิจกรรมใหม่
-- ไม่ลบสินค้าเดิม เพื่อไม่กระทบกิจกรรมหรือประวัติอื่นโดยไม่ตั้งใจ
INSERT INTO products (
    activity_id,
    product_name,
    description,
    price,
    image_path,
    qr_token,
    stock_qty,
    is_active,
    sort_order,
    teacher_note
)
SELECT
    @activity_id,
    seed.product_name,
    NULL,
    seed.price,
    NULL,
    CONCAT('product-a', @activity_id, '-', seed.token_suffix),
    NULL,
    1,
    seed.sort_order,
    NULL
FROM (
    SELECT 'เนื้อปลา' AS product_name, 50.00 AS price, '01' AS token_suffix, 10 AS sort_order
    UNION ALL SELECT 'เนื้อไก่', 50.00, '02', 20
    UNION ALL SELECT 'เนื้อหมู', 50.00, '03', 30
    UNION ALL SELECT 'ปลาทู', 50.00, '04', 40
    UNION ALL SELECT 'กุ้ง', 50.00, '05', 50
    UNION ALL SELECT 'หมึก', 50.00, '06', 60
    UNION ALL SELECT 'มะละกอ', 20.00, '07', 70
    UNION ALL SELECT 'ข้าว', 20.00, '08', 80
    UNION ALL SELECT 'แครอท', 20.00, '09', 90
    UNION ALL SELECT 'น้ำมัน', 20.00, '10', 100
    UNION ALL SELECT 'คะน้า', 20.00, '11', 110
    UNION ALL SELECT 'ตำลึง', 20.00, '12', 120
    UNION ALL SELECT 'เนย', 20.00, '13', 130
    UNION ALL SELECT 'นม', 20.00, '14', 140
    UNION ALL SELECT 'น้ำตาล', 20.00, '15', 150
    UNION ALL SELECT 'ไข่', 20.00, '16', 160
    UNION ALL SELECT 'กระเพรา', 20.00, '17', 170
    UNION ALL SELECT 'กระเทียม', 20.00, '18', 180
    UNION ALL SELECT 'มะนาว', 20.00, '19', 190
) AS seed
ON DUPLICATE KEY UPDATE
    product_name = VALUES(product_name),
    description = VALUES(description),
    price = VALUES(price),
    image_path = VALUES(image_path),
    stock_qty = VALUES(stock_qty),
    is_active = VALUES(is_active),
    sort_order = VALUES(sort_order),
    teacher_note = VALUES(teacher_note);

COMMIT;

-- ตรวจสอบหลังรัน: product_count ต้องเป็น 19
SELECT
    COUNT(*) AS product_count,
    @expected_count AS expected_count
FROM products
WHERE activity_id = @activity_id
  AND qr_token LIKE CONCAT('product-a', @activity_id, '-%');

-- รายการสินค้าที่เพิ่มเข้าไป
SELECT
    sort_order AS ลำดับ,
    product_name AS รายการ,
    price AS ราคา,
    qr_token,
    is_active
FROM products
WHERE activity_id = @activity_id
  AND qr_token LIKE CONCAT('product-a', @activity_id, '-%')
ORDER BY sort_order ASC, id ASC;

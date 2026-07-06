-- Replace product catalog for the current active activity.
-- ใช้ใน phpMyAdmin/SQL ของเว็บจริงหลังเลือก database ของเว็บแล้ว
-- ถ้าต้องการระบุ activity เอง ให้แก้บรรทัด SET @activity_id := ... เป็น SET @activity_id := 2;

SET NAMES utf8mb4;

SET @activity_id := (
    SELECT id
    FROM activities
    WHERE status = 'active'
    ORDER BY id DESC
    LIMIT 1
);

-- ตรวจสอบก่อนรัน: ต้องได้ activity_id ของกิจกรรมที่ต้องการ
SELECT @activity_id AS target_activity_id;

START TRANSACTION;

-- ลบสินค้าเดิมของกิจกรรมนี้ก่อน
-- หมายเหตุ: ถ้าสินค้าเดิมถูกอ้างอิงใน cart_items/order_items แล้ว MySQL จะไม่ให้ลบ เพื่อป้องกันประวัติข้อมูลเสีย
DELETE FROM products
WHERE activity_id = @activity_id;

-- เพิ่มรายการสินค้าใหม่ 19 รายการ
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
    product_name,
    NULL,
    price,
    NULL,
    CONCAT('product-a', @activity_id, '-', token_suffix),
    NULL,
    1,
    sort_order,
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
WHERE @activity_id IS NOT NULL;

COMMIT;

-- ตรวจสอบหลังรัน: ต้องได้ 19 รายการ
SELECT
    id,
    product_name,
    price,
    qr_token,
    is_active,
    sort_order
FROM products
WHERE activity_id = @activity_id
ORDER BY sort_order ASC, id ASC;

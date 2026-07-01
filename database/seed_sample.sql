USE fun_market;

SET @activity_id := (SELECT id FROM activities WHERE status = 'active' ORDER BY id DESC LIMIT 1);

INSERT INTO student_groups (activity_id, group_name, group_pin, initial_budget, current_balance, public_token, is_active)
SELECT @activity_id, group_name, group_pin, 100.00, 100.00, public_token, 1
FROM (
    SELECT 'กลุ่ม 1' AS group_name, '1234' AS group_pin, 'group-token-1' AS public_token
    UNION ALL SELECT 'กลุ่ม 2', '2468', 'group-token-2'
    UNION ALL SELECT 'กลุ่ม 3', '1357', 'group-token-3'
    UNION ALL SELECT 'กลุ่ม 4', '9876', 'group-token-4'
) seed
WHERE @activity_id IS NOT NULL
ON DUPLICATE KEY UPDATE public_token = student_groups.public_token;

INSERT INTO products (activity_id, product_name, description, price, qr_token, is_active, sort_order)
SELECT @activity_id, product_name, description, price, qr_token, 1, sort_order
FROM (
    SELECT 'ข้าวสวย' AS product_name, 'ข้าวสวยร้อน ๆ' AS description, 10.00 AS price, 'product-rice' AS qr_token, 10 AS sort_order
    UNION ALL SELECT 'ไข่ต้ม', 'ไข่ต้มพร้อมรับประทาน', 8.00, 'product-egg', 20
    UNION ALL SELECT 'นมจืด', 'นมกล่องรสจืด', 12.00, 'product-milk', 30
    UNION ALL SELECT 'กล้วย', 'กล้วยหอม 1 ผล', 6.00, 'product-banana', 40
    UNION ALL SELECT 'หมูทอด', 'หมูทอดชิ้นพอดีคำ', 15.00, 'product-pork', 50
    UNION ALL SELECT 'ปลาย่าง', 'ปลาย่างชิ้นเล็ก', 18.00, 'product-fish', 60
    UNION ALL SELECT 'ผักต้ม', 'ผักต้ม 1 จาน', 7.00, 'product-vegetable', 70
    UNION ALL SELECT 'น้ำเปล่า', 'น้ำดื่ม 1 ขวด', 5.00, 'product-water', 80
    UNION ALL SELECT 'แอปเปิล', 'แอปเปิล 1 ผล', 10.00, 'product-apple', 90
    UNION ALL SELECT 'ขนมปัง', 'ขนมปัง 1 แผ่น', 9.00, 'product-bread', 100
) seed
WHERE @activity_id IS NOT NULL
ON DUPLICATE KEY UPDATE qr_token = products.qr_token;

INSERT INTO wallet_transactions (group_id, transaction_type, amount_change, balance_before, balance_after, note)
SELECT sg.id, 'initial', sg.initial_budget, 0, sg.initial_budget, 'เงินตั้งต้นจาก seed'
FROM student_groups sg
WHERE sg.activity_id = @activity_id
  AND NOT EXISTS (SELECT 1 FROM wallet_transactions wt WHERE wt.group_id = sg.id);

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

DELETE FROM products WHERE activity_id = @activity_id;

INSERT INTO products (activity_id, product_name, description, price, qr_token, is_active, sort_order)
SELECT @activity_id, product_name, description, price, CONCAT('product-a', @activity_id, '-', token_suffix), 1, sort_order
FROM (
    SELECT 'เนื้อปลา' AS product_name, NULL AS description, 50.00 AS price, '01' AS token_suffix, 10 AS sort_order
    UNION ALL SELECT 'เนื้อไก่', NULL, 50.00, '02', 20
    UNION ALL SELECT 'เนื้อหมู', NULL, 50.00, '03', 30
    UNION ALL SELECT 'ปลาทู', NULL, 50.00, '04', 40
    UNION ALL SELECT 'กุ้ง', NULL, 50.00, '05', 50
    UNION ALL SELECT 'หมึก', NULL, 50.00, '06', 60
    UNION ALL SELECT 'มะละกอ', NULL, 20.00, '07', 70
    UNION ALL SELECT 'ข้าว', NULL, 20.00, '08', 80
    UNION ALL SELECT 'แครอท', NULL, 20.00, '09', 90
    UNION ALL SELECT 'น้ำมัน', NULL, 20.00, '10', 100
    UNION ALL SELECT 'คะน้า', NULL, 20.00, '11', 110
    UNION ALL SELECT 'ตำลึง', NULL, 20.00, '12', 120
    UNION ALL SELECT 'เนย', NULL, 20.00, '13', 130
    UNION ALL SELECT 'นม', NULL, 20.00, '14', 140
    UNION ALL SELECT 'น้ำตาล', NULL, 20.00, '15', 150
    UNION ALL SELECT 'ไข่', NULL, 20.00, '16', 160
    UNION ALL SELECT 'กระเพรา', NULL, 20.00, '17', 170
    UNION ALL SELECT 'กระเทียม', NULL, 20.00, '18', 180
    UNION ALL SELECT 'มะนาว', NULL, 20.00, '19', 190
) seed
WHERE @activity_id IS NOT NULL
ON DUPLICATE KEY UPDATE qr_token = products.qr_token;

INSERT INTO wallet_transactions (group_id, transaction_type, amount_change, balance_before, balance_after, note)
SELECT sg.id, 'initial', sg.initial_budget, 0, sg.initial_budget, 'เงินตั้งต้นจาก seed'
FROM student_groups sg
WHERE sg.activity_id = @activity_id
  AND NOT EXISTS (SELECT 1 FROM wallet_transactions wt WHERE wt.group_id = sg.id);

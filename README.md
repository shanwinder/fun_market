# ตลาดอาหาร 5 หมู่ ป.3

Web App ร้านค้าจำลองสำหรับกิจกรรมการเรียนรู้ ใช้ PHP, MySQL, Bootstrap และ JavaScript ตามแผนใน `docs/แผนการพัฒนา_web_app_ตลาดอาหาร_5_หมู่_ป3.md`

## ติดตั้งบน MAMP

1. สร้างฐานข้อมูลและตารางด้วยไฟล์ `database/schema.sql`
2. ถ้าต้องการข้อมูลทดลอง ให้รัน `database/seed_sample.sql`
3. ปรับค่าฐานข้อมูลได้ด้วย environment variables: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
4. ค่าเริ่มต้นเหมาะกับ MAMP: host `127.0.0.1`, port `8889`, database `fun_market`, user `root`, password `root`
5. เปิด `http://localhost:8888/fun_market/public/index.php`

บัญชีครูเริ่มต้น:

- Username: `teacher`
- Password: `teacher123`

## หน้าหลัก

- ครู: `teacher/login.php`
- นักเรียน: `student/join.php`
- จอรวม: `display/summary.php`
- QR สินค้า: `teacher/qrcodes.php`

## หมายเหตุ

ฝั่งนักเรียนไม่แสดงหมู่อาหาร คุณค่าทางอาหาร หรือคำแนะนำที่เฉลยคำตอบระหว่างกิจกรรม ข้อมูลบันทึกสำหรับครูในสินค้าแสดงเฉพาะหลังบ้านเท่านั้น


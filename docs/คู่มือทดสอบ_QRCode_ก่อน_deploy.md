# คู่มือทดสอบ QR Code ก่อน deploy

ใช้ checklist นี้ก่อนนำระบบ Fun Market ไปใช้งานจริงบน hosting เช่น InfinityFree

## ฝั่งครู

- [ ] เปิดเว็บผ่าน HTTPS ได้
- [ ] login ครูได้
- [ ] เปิดหน้า `teacher/qrcodes.php` ได้
- [ ] QR Code สินค้าแสดงครบทุกสินค้า
- [ ] QR Code เข้ากลุ่มแสดงครบทุกกลุ่ม
- [ ] กดพิมพ์ QR Code ได้
- [ ] QR Code ที่พิมพ์ออกมา scan ได้จริง

## ฝั่งนักเรียน

- [ ] เข้ากลุ่มด้วย PIN ได้
- [ ] เปิดหน้า `student/scan.php` ได้
- [ ] browser ขออนุญาตใช้กล้อง
- [ ] สแกน QR Code สินค้าได้
- [ ] เปิดหน้าสินค้าถูกต้อง
- [ ] เพิ่มสินค้าลงตะกร้าได้
- [ ] ยืนยันซื้อได้เมื่อเงินพอ
- [ ] ระบบแจ้งเมื่อเงินไม่พอ
- [ ] เงินคงเหลือของกลุ่มลดถูกต้อง
- [ ] ประวัติการซื้อแสดงถูกต้อง

## Dependency

- [ ] ไม่มีการโหลด QR Code จาก `api.qrserver.com`
- [ ] ไม่มีการโหลดตัวสแกนจาก `unpkg.com`
- [ ] `public/qrcode.php?data=test` แสดงรูป QR ได้
- [ ] `public/assets/vendor/html5-qrcode/html5-qrcode.min.js` เปิดได้จาก browser
- [ ] `vendor/autoload.php` อยู่บน server จริง

## ข้อควรจำตอน deploy

- ต้อง upload `composer.json`, `composer.lock` และ `vendor/` ไปด้วย หาก server ไม่มี Composer
- ต้องเปิดหน้า scan ผ่าน HTTPS เพื่อให้มือถืออนุญาตใช้กล้อง
- ถ้าเปิดผ่าน LINE/Facebook แล้วกล้องไม่ขึ้น ให้เปิดด้วย Chrome หรือ Safari แทน

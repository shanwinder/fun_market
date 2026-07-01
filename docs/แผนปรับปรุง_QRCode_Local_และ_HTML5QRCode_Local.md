# แผนปรับปรุงระบบ QR Code และตัวสแกน QR ให้ใช้งานได้มั่นคงบนเว็บออนไลน์

> โปรเจกต์: **Fun Market / ตลาดอาหาร 5 หมู่ ป.3**  
> Repository: `shanwinder/fun_market`  
> เป้าหมายหลัก: ลดการพึ่งพาบริการภายนอก เพื่อให้ระบบพร้อมใช้งานจริงเมื่อ deploy ฟรี เช่น InfinityFree และใช้งานในห้องเรียนได้มั่นคงขึ้น

---

## 1. เหตุผลที่ต้องปรับปรุง

ระบบปัจจุบันสามารถใช้งาน QR Code ได้ แต่ยังมี dependency ภายนอก 2 จุดสำคัญ

1. รูป QR Code ถูกสร้างจากบริการภายนอก `api.qrserver.com`
2. หน้าแสกน QR Code โหลด `html5-qrcode` จาก CDN ของ `unpkg.com`

ถ้านำเว็บไปใช้จริงในห้องเรียน ปัญหาที่อาจเกิดขึ้นคือ

- อินเทอร์เน็ตโรงเรียนไม่เสถียร
- CDN ถูกบล็อกหรือโหลดช้า
- บริการสร้าง QR ภายนอกล่ม
- หน้า QR Code แสดงรูปไม่ครบ
- มือถือนักเรียนเปิดกล้องสแกนไม่ได้เพราะไฟล์ JavaScript โหลดไม่สำเร็จ

ดังนั้นควรปรับระบบให้ไฟล์สำคัญอยู่ในเว็บของเราเองมากที่สุด

---

## 2. สถานะปัจจุบันจากโค้ดจริง

### 2.1 จุดที่ 1: QR Code ยังสร้างผ่านเว็บภายนอก

ไฟล์ปัจจุบัน:

```text
includes/helpers.php
```

ฟังก์ชันปัจจุบัน:

```php
function qr_image_url(string $targetUrl, int $size = 240): string
{
    return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . rawurlencode($targetUrl);
}
```

ผลคือหน้า `teacher/qrcodes.php` จะโหลดรูป QR จากเว็บภายนอกทุกครั้ง

### 2.2 จุดที่ 2: ตัวสแกน QR ยังโหลดจาก CDN

ไฟล์ปัจจุบัน:

```text
student/scan.php
```

โค้ดปัจจุบัน:

```html
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
```

ผลคือถ้า CDN โหลดไม่ได้ หน้าแสกน QR Code จะไม่ทำงาน

---

## 3. เป้าหมายหลังปรับปรุง

หลังปรับปรุงแล้ว ระบบควรมีลักษณะดังนี้

- QR Code ถูกสร้างโดย PHP ภายในเว็บของเราเอง
- ไม่ต้องเรียก `api.qrserver.com`
- ไฟล์ `html5-qrcode.min.js` ถูกเก็บไว้ในโปรเจกต์
- หน้า `student/scan.php` โหลดตัวสแกนจากไฟล์ local
- ใช้งานได้ทั้งบน MAMP และหลัง deploy ขึ้น InfinityFree
- ถ้าเว็บเปิดผ่าน HTTPS มือถือควรเปิดกล้องสแกนได้
- ครูสามารถพิมพ์ QR Code ได้โดยไม่ต้องพึ่งเว็บสร้าง QR ภายนอก

---

## 4. ขอบเขตงาน

แผนนี้โฟกัสเฉพาะ 2 งานหลัก

| งาน | สถานะที่ต้องการ |
|---|---|
| สร้าง QR Code เองในเว็บ | ใช้ PHP library หรือไฟล์ generator ภายในระบบ |
| โหลด `html5-qrcode` จาก local | เก็บไฟล์ JS ไว้ใน `public/assets/vendor/` |

ยังไม่รวมงานอื่น เช่น

- ระบบ stock
- ระบบ timer
- ระบบ reflection หลังจบกิจกรรม
- ระบบรอบกิจกรรม
- ปรับ QR เข้ากลุ่มให้ต้องกรอก PIN อีกครั้ง

---

# ส่วนที่ 1: แผนเปลี่ยน QR Code ให้สร้างเองในเว็บ

## 5. แนวทางที่แนะนำ

แนะนำให้ใช้ PHP library สร้าง QR Code เอง เช่น

```text
chillerlan/php-qrcode
```

เหตุผลที่เหมาะกับโปรเจกต์นี้

- ใช้กับ PHP ได้โดยตรง
- สร้าง QR เป็น PNG หรือ Data URI ได้
- ไม่ต้องพึ่งเว็บภายนอก
- เหมาะกับหน้า QR Code ที่ครูใช้พิมพ์
- สามารถ commit โค้ดและ vendor ขึ้น repo เพื่อ deploy บน hosting ฟรีได้

---

## 6. โครงสร้างไฟล์ที่ควรเพิ่ม

หลังปรับปรุง ควรมีโครงสร้างประมาณนี้

```text
fun_market/
├── composer.json
├── composer.lock
├── vendor/
│   └── ...
├── public/
│   └── qrcode.php
├── storage/
│   └── qrcodes/
└── includes/
    └── helpers.php
```

คำอธิบาย

| ไฟล์/โฟลเดอร์ | หน้าที่ |
|---|---|
| `composer.json` | ระบุ PHP package ที่ใช้ |
| `composer.lock` | ล็อก version package ให้เหมือนกันทุกเครื่อง |
| `vendor/` | ไฟล์ library จาก Composer |
| `public/qrcode.php` | endpoint สำหรับสร้างรูป QR Code |
| `storage/qrcodes/` | ที่เก็บ cache รูป QR ถ้าต้องการ |
| `includes/helpers.php` | แก้ฟังก์ชัน `qr_image_url()` ให้ชี้มาที่ระบบเราเอง |

> หมายเหตุ: ถ้า deploy บน InfinityFree แล้วไม่มี command line ให้รัน Composer บน server ให้รัน Composer ในเครื่องก่อน แล้วอัปโหลดโฟลเดอร์ `vendor/` ขึ้นไปพร้อมโปรเจกต์

---

## 7. ขั้นตอนพัฒนา QR Code แบบ local

### Phase QR-1: เพิ่ม Composer package

บนเครื่อง local ในโฟลเดอร์โปรเจกต์ ให้รัน

```bash
composer require chillerlan/php-qrcode
```

ผลที่ควรได้

```text
composer.json
composer.lock
vendor/
```

ถ้าเครื่องยังไม่มี Composer ให้ติดตั้ง Composer ก่อน หรือใช้แนวทางสำรองคือดาวน์โหลด library มาใส่เอง แต่แนวทาง Composer จะจัดการง่ายกว่า

---

### Phase QR-2: เพิ่ม endpoint สำหรับสร้าง QR

สร้างไฟล์ใหม่

```text
public/qrcode.php
```

หน้าที่ของไฟล์นี้คือรับข้อมูล `data` และ `size` แล้วสร้างรูป QR Code กลับไปเป็น PNG

ตัวอย่างแนวทางโค้ด

```php
<?php

// เปิดโหมด strict เพื่อช่วยลดข้อผิดพลาดของชนิดข้อมูล
declare(strict_types=1);

// โหลด helper และ autoload ของ Composer
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// รับข้อความที่จะนำไปสร้าง QR Code จาก query string
$data = trim($_GET['data'] ?? '');

// รับขนาด QR Code โดยจำกัดช่วงเพื่อป้องกันการเรียกขนาดใหญ่เกินไป
$size = (int) ($_GET['size'] ?? 240);
$size = max(120, min(600, $size));

// ถ้าไม่มีข้อมูล ให้ตอบกลับเป็น error
if ($data === '') {
    http_response_code(400);
    exit('Missing QR data');
}

// จำกัดความยาวข้อมูลเพื่อป้องกันการ abuse
if (mb_strlen($data) > 1000) {
    http_response_code(400);
    exit('QR data too long');
}

// ตั้งค่า QR Code
$options = new QROptions([
    // สร้างเป็น PNG
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,

    // ขนาด scale ของ QR Code
    'scale' => max(4, (int) round($size / 60)),

    // เว้นขอบ QR Code เพื่อให้สแกนง่าย
    'quietzoneSize' => 2,
]);

// ส่ง header เป็นรูป PNG
header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');

// สร้างและแสดงรูป QR Code
// หมายเหตุ: ข้อมูล QR อาจเป็น URL สินค้าหรือ token ก็ได้
echo (new QRCode($options))->render($data);
```

> หมายเหตุ: syntax จริงของ library อาจต้องตรวจจาก package version ที่ติดตั้ง หาก class constant หรือ options เปลี่ยน ให้ยึดเอกสารของ version ที่ติดตั้งใน `composer.lock`

---

### Phase QR-3: แก้ฟังก์ชัน `qr_image_url()`

ไฟล์ที่ต้องแก้

```text
includes/helpers.php
```

ของเดิม

```php
function qr_image_url(string $targetUrl, int $size = 240): string
{
    return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . rawurlencode($targetUrl);
}
```

เปลี่ยนเป็น

```php
function qr_image_url(string $targetUrl, int $size = 240): string
{
    // สร้าง URL ไปยัง endpoint QR Code ของระบบเราเอง
    // ไม่เรียกใช้บริการภายนอก เพื่อให้ใช้งานมั่นคงขึ้นตอน deploy จริง
    return url('public/qrcode.php?size=' . (int) $size . '&data=' . rawurlencode($targetUrl));
}
```

ผลที่ต้องการ

หน้า `teacher/qrcodes.php` ยังเรียก `qr_image_url()` เหมือนเดิม แต่เบื้องหลังจะเปลี่ยนจากเว็บภายนอกมาเป็นไฟล์ในระบบเราเอง

---

### Phase QR-4: ทดสอบ QR Code บน MAMP

หลังแก้แล้วให้ทดสอบตามนี้

1. เปิด MAMP
2. เข้าเว็บ

```text
http://localhost:8888/fun_market/public/index.php
```

3. login ครู
4. เปิดหน้า

```text
teacher/qrcodes.php
```

5. ตรวจว่า QR Code ของสินค้าแสดงครบ
6. คลิกขวาที่รูป QR แล้วดู URL ต้องเป็นประมาณนี้

```text
/fun_market/public/qrcode.php?size=260&data=...
```

ไม่ควรเป็น

```text
https://api.qrserver.com/...
```

7. ใช้มือถือสแกน QR Code สินค้า
8. ต้องเปิดไปที่หน้า `student/product.php?token=...` ได้ถูกต้อง

---

### Phase QR-5: ทดสอบบน InfinityFree

หลัง upload ขึ้น InfinityFree ให้ทดสอบ

1. เปิดเว็บผ่าน HTTPS
2. login ครู
3. เข้า `teacher/qrcodes.php`
4. ตรวจว่า QR Code แสดงครบ
5. ใช้มือถือสแกน QR Code
6. ต้องเปิดหน้าสินค้าได้จริง

ข้อควรระวัง

- ต้อง upload `vendor/` ขึ้น server ด้วย ถ้า server ไม่มี Composer
- ต้องตรวจ path ว่า `vendor/autoload.php` อยู่จริง
- ถ้ารูป QR ไม่ขึ้น ให้เปิด `public/qrcode.php?data=test` ตรง ๆ เพื่อดู error

---

# ส่วนที่ 2: แผนโหลด html5-qrcode จากไฟล์ local

## 8. แนวทางที่แนะนำ

ปัจจุบันหน้า scan ใช้ CDN

```html
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
```

ให้เปลี่ยนเป็นไฟล์ local ในโปรเจกต์

```text
public/assets/vendor/html5-qrcode/html5-qrcode.min.js
```

แล้วเรียกใช้แบบนี้

```php
<script src="<?= h(url('public/assets/vendor/html5-qrcode/html5-qrcode.min.js')) ?>"></script>
```

---

## 9. โครงสร้างไฟล์ที่ควรเพิ่ม

```text
public/
└── assets/
    └── vendor/
        └── html5-qrcode/
            ├── html5-qrcode.min.js
            └── README.md
```

ไฟล์ `README.md` ควรบันทึกว่า

- ไฟล์นี้นำมาจาก package/version ใด
- วันที่นำเข้า
- ใช้กับหน้าใด
- ห้ามแก้ไขไฟล์ minified โดยตรง

---

## 10. ขั้นตอนพัฒนา html5-qrcode แบบ local

### Phase SCAN-1: ดาวน์โหลดไฟล์ library

ให้ดาวน์โหลดไฟล์ `html5-qrcode.min.js` version เดียวกับที่ใช้อยู่ปัจจุบัน คือ `2.3.8`

ตำแหน่งปลายทาง

```text
public/assets/vendor/html5-qrcode/html5-qrcode.min.js
```

> หมายเหตุ: ถ้าต้องการอัปเดต version ในอนาคต ให้ทดสอบกับมือถือจริงก่อนทุกครั้ง เพราะกล้องมือถือแต่ละ browser อาจมีพฤติกรรมต่างกัน

---

### Phase SCAN-2: เพิ่ม README ของ vendor

สร้างไฟล์

```text
public/assets/vendor/html5-qrcode/README.md
```

เนื้อหาแนะนำ

```md
# html5-qrcode

ใช้สำหรับสแกน QR Code ผ่านกล้องมือถือในหน้า `student/scan.php`

## Version

- html5-qrcode: 2.3.8

## เหตุผลที่เก็บแบบ local

เพื่อให้ระบบ Fun Market ใช้งานได้มั่นคงขึ้น ไม่ต้องพึ่ง CDN ภายนอกขณะใช้งานในห้องเรียน

## หมายเหตุ

ห้ามแก้ไขไฟล์ `html5-qrcode.min.js` โดยตรง หากต้องเปลี่ยน version ให้ดาวน์โหลดไฟล์ใหม่และทดสอบบนมือถือจริงก่อน deploy
```

---

### Phase SCAN-3: แก้ `student/scan.php`

ไฟล์ที่ต้องแก้

```text
student/scan.php
```

ของเดิม

```html
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
```

เปลี่ยนเป็น

```php
<script src="<?= h(url('public/assets/vendor/html5-qrcode/html5-qrcode.min.js')) ?>"></script>
```

---

### Phase SCAN-4: เพิ่ม fallback กรณี library โหลดไม่ได้

ควรปรับ JavaScript ให้แจ้งนักเรียน/ครูชัดเจน หากตัวสแกนไม่พร้อมใช้งาน

ตัวอย่างแนวทาง

```html
<script>
    window.addEventListener('load', () => {
        // ตรวจว่ามี library สแกน QR หรือไม่
        if (!window.Html5QrcodeScanner) {
            const reader = document.getElementById('reader');
            if (reader) {
                reader.innerHTML = '<div class="alert alert-warning">ไม่สามารถโหลดระบบสแกน QR ได้ กรุณาแจ้งครู หรือใช้แอปกล้องของเครื่องสแกน QR Code แทน</div>';
            }
            return;
        }

        // เริ่มต้นตัวสแกน QR Code
        const scanner = new Html5QrcodeScanner('reader', {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        }, false);

        scanner.render((decodedText) => {
            // ถ้า QR เป็น URL ให้เปิด URL นั้นโดยตรง
            if (decodedText.startsWith('http')) {
                window.location.href = decodedText;
                return;
            }

            // ถ้า QR เป็น token ให้ส่งไปหน้า product ของระบบ
            window.location.href = <?= json_encode(BASE_URL . '/student/product.php?token=') ?> + encodeURIComponent(decodedText);
        });
    });
</script>
```

---

### Phase SCAN-5: ทดสอบบน MAMP

ทดสอบตามลำดับ

1. เปิดหน้า

```text
student/scan.php
```

2. เปิด Developer Tools
3. ไปที่ Network
4. reload หน้า
5. ตรวจว่าไฟล์ที่โหลดคือ

```text
public/assets/vendor/html5-qrcode/html5-qrcode.min.js
```

ไม่ควรมีการโหลดจาก

```text
unpkg.com
```

6. ทดสอบบนมือถือที่อยู่ Wi-Fi เดียวกันกับเครื่อง MAMP ถ้าต้องการทดสอบกล้องจริง

> หมายเหตุ: กล้องมือถือมักต้องใช้ HTTPS หรือ localhost ถ้าทดสอบผ่าน IP local แบบ HTTP บาง browser อาจไม่อนุญาตให้เปิดกล้อง

---

### Phase SCAN-6: ทดสอบบน InfinityFree

หลัง deploy ให้ทดสอบ

1. เปิดเว็บด้วย HTTPS เท่านั้น
2. เข้ากลุ่มนักเรียน
3. เปิดหน้าแสกน QR
4. browser ต้องถาม permission กล้อง
5. กดอนุญาต
6. สแกน QR สินค้า
7. ต้องเปิดหน้าสินค้าถูกต้อง
8. เพิ่มสินค้าลงตะกร้า
9. checkout แล้วเงินลดถูกต้อง

ถ้ากล้องไม่ขึ้น ให้ตรวจ

- เว็บเปิดด้วย `https://` หรือไม่
- ไฟล์ `html5-qrcode.min.js` อัปโหลดครบหรือไม่
- browser บล็อก permission กล้องหรือไม่
- เปิดผ่าน in-app browser เช่น LINE/Facebook หรือไม่ ถ้าใช่ ให้เปิดด้วย Chrome/Safari แทน

---

# ส่วนที่ 3: ลำดับ Commit ที่แนะนำ

เพื่อให้ตรวจสอบง่าย ควรแยกเป็น commit ย่อย ๆ

## Commit 1: เพิ่ม QR library

```text
chore: add php qr code dependency
```

ไฟล์ที่เกี่ยวข้อง

```text
composer.json
composer.lock
vendor/
```

ถ้าไม่ต้องการ commit `vendor/` ในระยะยาว ให้เพิ่ม workflow deploy ที่ติดตั้ง Composer ได้ แต่สำหรับ InfinityFree ฟรี การ commit หรือ upload `vendor/` ไปด้วยจะง่ายกว่าสำหรับมือใหม่

---

## Commit 2: เพิ่ม endpoint สร้าง QR Code

```text
feat: add local qr code generator endpoint
```

ไฟล์ที่เกี่ยวข้อง

```text
public/qrcode.php
includes/helpers.php
```

---

## Commit 3: เพิ่ม html5-qrcode แบบ local

```text
chore: vendor html5-qrcode locally
```

ไฟล์ที่เกี่ยวข้อง

```text
public/assets/vendor/html5-qrcode/html5-qrcode.min.js
public/assets/vendor/html5-qrcode/README.md
```

---

## Commit 4: แก้หน้า scan ให้ใช้ local scanner

```text
feat: load qr scanner from local asset
```

ไฟล์ที่เกี่ยวข้อง

```text
student/scan.php
```

---

## Commit 5: เพิ่มเอกสารทดสอบก่อน deploy

```text
docs: add qr deployment checklist
```

ไฟล์ที่เกี่ยวข้อง

```text
docs/คู่มือทดสอบ_QRCode_ก่อน_deploy.md
```

---

# ส่วนที่ 4: Checklist ทดสอบก่อนใช้จริง

## 11. Checklist ฝั่งครู

- [ ] เปิดเว็บผ่าน HTTPS ได้
- [ ] login ครูได้
- [ ] เปิดหน้า `teacher/qrcodes.php` ได้
- [ ] QR Code สินค้าแสดงครบทุกสินค้า
- [ ] QR Code เข้ากลุ่มแสดงครบทุกกลุ่ม
- [ ] กดพิมพ์ QR Code ได้
- [ ] QR Code ที่พิมพ์ออกมา scan ได้จริง

---

## 12. Checklist ฝั่งนักเรียน

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

---

## 13. Checklist ด้าน dependency

- [ ] ไม่มีการโหลด QR Code จาก `api.qrserver.com`
- [ ] ไม่มีการโหลดตัวสแกนจาก `unpkg.com`
- [ ] `public/qrcode.php?data=test` แสดงรูป QR ได้
- [ ] `public/assets/vendor/html5-qrcode/html5-qrcode.min.js` เปิดได้จาก browser
- [ ] `vendor/autoload.php` อยู่บน server จริง

---

# ส่วนที่ 5: ความเสี่ยงและแนวทางรับมือ

## 14. ความเสี่ยง: InfinityFree ไม่มี Composer command

แนวทางรับมือ

- รัน Composer บนเครื่อง local
- upload `vendor/`, `composer.json`, `composer.lock` ขึ้น hosting พร้อมกัน
- ทดสอบว่า `vendor/autoload.php` อยู่จริง

---

## 15. ความเสี่ยง: กล้องมือถือไม่เปิด

สาเหตุที่เป็นไปได้

- เว็บไม่ได้เปิดผ่าน HTTPS
- browser ไม่อนุญาต permission กล้อง
- เปิดผ่าน in-app browser
- ไฟล์ `html5-qrcode.min.js` ไม่โหลด

แนวทางรับมือ

- ใช้ HTTPS เท่านั้น
- ให้เด็กเปิดผ่าน Chrome หรือ Safari
- เพิ่มข้อความ fallback ในหน้า scan
- เตรียมทางเลือกให้ใช้แอปกล้องของเครื่องสแกน QR แทน

---

## 16. ความเสี่ยง: QR Code ไม่ขึ้น

สาเหตุที่เป็นไปได้

- ไม่ได้ upload `vendor/`
- `public/qrcode.php` error
- package QR Code ไม่ตรง version
- hosting ปิด error display ทำให้เห็นเป็นรูปเสีย

แนวทางรับมือ

- เปิด `public/qrcode.php?data=test` โดยตรง
- ตรวจ PHP error log ถ้ามี
- ทดสอบบน MAMP ให้ผ่านก่อน upload
- ถ้ายังมีปัญหา ให้เปลี่ยนมาใช้วิธี cache QR เป็นไฟล์ PNG ล่วงหน้า

---

# ส่วนที่ 6: แนวทางสำรองถ้า QR library ใช้งานยากบน InfinityFree

ถ้าใช้ `chillerlan/php-qrcode` แล้วติดปัญหาบน hosting ฟรี ให้ใช้แนวทางสำรองดังนี้

## 17. แนวทางสำรอง A: สร้าง QR ล่วงหน้าแล้วเก็บเป็นไฟล์ PNG

แนวคิด

- สร้าง QR Code บนเครื่อง local
- บันทึกเป็นไฟล์ PNG เช่น `public/uploads/qrcodes/product-rice.png`
- upload รูป QR Code ขึ้น hosting
- หน้า `teacher/qrcodes.php` ใช้รูปที่สร้างไว้แล้ว

ข้อดี

- ไม่ต้อง generate QR แบบ runtime
- ลดภาระ server ฟรี
- ใช้งานเสถียรมาก

ข้อเสีย

- ถ้า URL เปลี่ยน ต้อง generate QR ใหม่
- ถ้าเปลี่ยน domain จาก MAMP ไป InfinityFree ต้องสร้าง QR ใหม่

---

## 18. แนวทางสำรอง B: ให้ QR เก็บเฉพาะ token ไม่ใช่ URL เต็ม

แนวคิด

แทนที่จะให้ QR Code เก็บ URL เต็ม เช่น

```text
https://example.com/student/product.php?token=product-rice
```

ให้เก็บเฉพาะ token เช่น

```text
product-rice
```

จากนั้นหน้า scan จะนำ token ไปต่อ URL เอง

ข้อดี

- QR Code ใช้ซ้ำได้แม้เปลี่ยน domain
- ข้อมูลใน QR สั้นลง สแกนง่ายขึ้น
- เหมาะกับกิจกรรมที่ย้ายจาก MAMP ไป hosting จริง

ข้อเสีย

- ถ้าใช้แอปกล้องปกติสแกน token จะไม่เปิดเว็บโดยตรง
- ต้องใช้หน้า `student/scan.php` ของระบบในการสแกน

แนวทางที่เหมาะสำหรับโปรเจกต์นี้

- QR สำหรับใช้กับหน้า scanner ของระบบ: ใช้ token
- QR สำหรับพิมพ์ให้เปิดด้วยแอปกล้องทั่วไป: ใช้ URL เต็ม

---

# ส่วนที่ 7: ผลลัพธ์ที่คาดหวัง

หลังทำแผนนี้เสร็จ ระบบจะดีขึ้นดังนี้

- QR Code แสดงได้จากระบบของเราเอง
- ลดการพึ่งพา external QR service
- ตัวสแกน QR โหลดจากไฟล์ในโปรเจกต์
- ลดความเสี่ยงจาก CDN ล่มหรือถูกบล็อก
- เหมาะกับการ deploy บน InfinityFree มากขึ้น
- เหมาะกับการใช้งานจริงในห้องเรียนมากขึ้น
- ครูสามารถพิมพ์ QR Code ก่อนสอนได้อย่างมั่นใจมากขึ้น

---

# 8. สรุปลำดับทำงานแบบสั้น

1. เพิ่ม Composer และ QR library
2. สร้าง `public/qrcode.php`
3. แก้ `qr_image_url()` ใน `includes/helpers.php`
4. ทดสอบ QR บน MAMP
5. ดาวน์โหลด `html5-qrcode.min.js` มาไว้ใน `public/assets/vendor/html5-qrcode/`
6. แก้ `student/scan.php` ให้โหลดไฟล์ local
7. เพิ่ม fallback กรณี scanner โหลดไม่ได้
8. ทดสอบบนมือถือจริง
9. deploy ขึ้น InfinityFree พร้อม `vendor/`
10. ทดสอบผ่าน HTTPS ก่อนใช้จริงในห้องเรียน

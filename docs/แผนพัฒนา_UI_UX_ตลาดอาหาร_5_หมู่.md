# 🎨 แผนพัฒนา UI/UX — ตลาดอาหาร 5 หมู่ ป.3

> **วันที่จัดทำ:** 1 กรกฎาคม 2569  
> **ระบบ:** Fun Market — Web App ร้านค้าจำลองสำหรับกิจกรรมการเรียนรู้  
> **เทคโนโลยีปัจจุบัน:** PHP 8+, MySQL, Bootstrap 5.3, jQuery, DataTables, Chart.js  
> **เป้าหมาย:** ปรับปรุง UI/UX ให้สวยงาม อลังการ ทันสมัย เหมาะกับนักเรียน ป.3 และใช้งานง่ายสำหรับครู

---

## 📋 สารบัญ

1. [สถาปัตยกรรมปัจจุบัน & การวิเคราะห์](#1-สถาปัตยกรรมปัจจุบัน--การวิเคราะห์)
2. [Design System ใหม่](#2-design-system-ใหม่)
3. [การปรับปรุง Global Layout](#3-การปรับปรุง-global-layout)
4. [ฝั่งนักเรียน (Student)](#4-ฝั่งนักเรียน-student)
5. [ฝั่งครู (Teacher)](#5-ฝั่งครู-teacher)
6. [จอแสดงผลรวม (Display Board)](#6-จอแสดงผลรวม-display-board)
7. [หน้า Public Landing](#7-หน้า-public-landing)
8. [Micro-Animations & Transitions](#8-micro-animations--transitions)
9. [Responsive & Mobile-First](#9-responsive--mobile-first)
10. [Accessibility (A11y)](#10-accessibility-a11y)
11. [Performance Optimization](#11-performance-optimization)
12. [ลำดับการพัฒนา (Roadmap)](#12-ลำดับการพัฒนา-roadmap)
13. [รายละเอียดไฟล์ที่ต้องแก้ไข](#13-รายละเอียดไฟล์ที่ต้องแก้ไข)

---

## 1. สถาปัตยกรรมปัจจุบัน & การวิเคราะห์

### 1.1 โครงสร้างไฟล์

```
fun_market/
├── public/
│   ├── index.php                  ← Landing page
│   └── assets/
│       ├── css/app.css            ← Stylesheet หลัก (112 บรรทัด)
│       ├── js/app.js              ← JavaScript หลัก (19 บรรทัด)
│       └── images/
│           └── placeholder-food.svg
├── includes/
│   ├── header.php                 ← Template header + navbar
│   ├── footer.php                 ← Template footer + scripts
│   ├── helpers.php                ← PHP helper functions
│   ├── auth.php                   ← Middleware ตรวจสอบครู
│   ├── csrf.php                   ← CSRF token
│   └── flash.php                  ← Flash messages
├── student/                       ← 7 หน้าฝั่งนักเรียน
│   ├── join.php                   ← เลือกกลุ่ม + ใส่ PIN
│   ├── home.php                   ← หน้ากลุ่ม
│   ├── scan.php                   ← สแกน QR Code
│   ├── product.php                ← รายละเอียดสินค้า
│   ├── cart.php                   ← ตะกร้า
│   ├── checkout.php               ← ซื้อสำเร็จ
│   └── history.php                ← ประวัติการซื้อ
├── teacher/                       ← 14 หน้าฝั่งครู
│   ├── login.php                  ← เข้าสู่ระบบ
│   ├── dashboard.php              ← Dashboard หลัก
│   ├── activities.php             ← จัดการกิจกรรม
│   ├── activity_form.php          ← ฟอร์มกิจกรรม
│   ├── groups.php                 ← จัดการกลุ่ม
│   ├── group_form.php             ← ฟอร์มกลุ่ม
│   ├── group_detail.php           ← รายละเอียดกลุ่ม
│   ├── products.php               ← จัดการสินค้า
│   ├── product_form.php           ← ฟอร์มสินค้า
│   ├── qrcodes.php                ← พิมพ์ QR Code
│   ├── reports.php                ← รายงาน + กราฟ
│   ├── live_orders.php            ← รายการซื้อแบบสด
│   ├── reset_activity.php         ← รีเซ็ตกิจกรรม
│   └── logout.php                 ← ออกจากระบบ
├── display/                       ← 3 หน้าจอแสดงผล
│   ├── summary.php                ← จอรวมภาพรวมกลุ่ม
│   ├── live.php                   ← ความเคลื่อนไหวล่าสุด
│   └── timer.php                  ← จับเวลา
├── actions/                       ← 7 ไฟล์ backend actions
├── config/                        ← app.php, database.php
├── database/                      ← schema.sql, seed_sample.sql
└── qrcode/                        ← group.php, product.php
```

### 1.2 จุดอ่อน UI/UX ปัจจุบัน

| หมวด | ปัญหา | ระดับ |
|------|-------|-------|
| **Visual Design** | ใช้ Bootstrap default ล้วน ไม่มี custom theme | 🔴 สำคัญ |
| **Color Palette** | มี CSS Variables แต่ใช้น้อยมาก ไม่สอดคล้องกัน | 🔴 สำคัญ |
| **Typography** | ใช้ system-ui ไม่มี Google Fonts ที่เหมาะกับเด็ก | 🟡 ปานกลาง |
| **Icons** | ไม่มี icon เลย ใช้แต่ข้อความล้วน | 🔴 สำคัญ |
| **Animations** | ไม่มี animation/transition ใดๆ | 🔴 สำคัญ |
| **Loading States** | ไม่มี loading indicator | 🟡 ปานกลาง |
| **Empty States** | ใช้ข้อความเปล่า ไม่มี illustration | 🟡 ปานกลาง |
| **Student UX** | ปุ่มเล็กเกินไปสำหรับเด็ก ป.3 | 🔴 สำคัญ |
| **Product Cards** | ไม่มี card layout สำหรับสินค้า | 🟡 ปานกลาง |
| **Dark Mode (Display)** | มีแต่เบื้องต้นมาก ไม่มี gradient | 🟡 ปานกลาง |
| **Success/Error States** | ใช้ alert แบบ default ไม่มี celebration | 🟡 ปานกลาง |
| **Navigation** | Navbar ธรรมดา ไม่ highlight หน้าปัจจุบัน | 🟡 ปานกลาง |
| **Forms** | ฟอร์มเรียบเกินไป ไม่มี visual feedback | 🟡 ปานกลาง |
| **Charts** | กราฟเรียบ ไม่มีสี/animation | 🟡 ปานกลาง |

### 1.3 จุดแข็ง (สิ่งที่ต้องรักษาไว้)

- ✅ โครงสร้างไฟล์ชัดเจน แยก concern ดี
- ✅ มี CSS Variables อยู่แล้ว (ต่อยอดได้ง่าย)
- ✅ ใช้ Bootstrap 5.3 (มี utility classes ครบ)
- ✅ มี DataTables, Chart.js พร้อมใช้
- ✅ ระบบ header/footer template ที่ดี
- ✅ มี `.student-shell`, `.display-board` class แยก theme

---

## 2. Design System ใหม่

### 2.1 Color Palette

```css
:root {
    /* ── Primary Colors ── */
    --fm-primary:       #6366f1;    /* Indigo สดใส */
    --fm-primary-light: #818cf8;
    --fm-primary-dark:  #4f46e5;
    --fm-primary-50:    #eef2ff;
    
    /* ── Secondary / Accent ── */
    --fm-accent:        #f59e0b;    /* Amber อบอุ่น */
    --fm-accent-light:  #fbbf24;
    --fm-accent-dark:   #d97706;
    
    /* ── Semantic Colors ── */
    --fm-success:       #10b981;    /* Emerald */
    --fm-success-light: #d1fae5;
    --fm-warning:       #f59e0b;
    --fm-warning-light: #fef3c7;
    --fm-danger:        #ef4444;
    --fm-danger-light:  #fee2e2;
    --fm-info:          #06b6d4;
    --fm-info-light:    #cffafe;
    
    /* ── Food Group Colors (อาหาร 5 หมู่) ── */
    --fm-food-1:        #ef4444;    /* แดง — เนื้อสัตว์ */
    --fm-food-2:        #22c55e;    /* เขียว — ผัก */
    --fm-food-3:        #f97316;    /* ส้ม — ผลไม้ */
    --fm-food-4:        #eab308;    /* เหลือง — ข้าว/แป้ง */
    --fm-food-5:        #3b82f6;    /* ฟ้า — นม/ไขมัน */
    
    /* ── Neutral ── */
    --fm-ink:           #1e293b;
    --fm-ink-light:     #64748b;
    --fm-ink-muted:     #94a3b8;
    --fm-border:        #e2e8f0;
    --fm-border-light:  #f1f5f9;
    --fm-bg:            #f8fafc;
    --fm-bg-warm:       #fffbf0;
    --fm-surface:       #ffffff;
    
    /* ── Shadows ── */
    --fm-shadow-sm:     0 1px 2px rgba(0,0,0,0.05);
    --fm-shadow:        0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
    --fm-shadow-md:     0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.05);
    --fm-shadow-lg:     0 20px 25px -5px rgba(0,0,0,0.10), 0 8px 10px -6px rgba(0,0,0,0.05);
    --fm-shadow-glow:   0 0 20px rgba(99, 102, 241, 0.15);
    
    /* ── Border Radius ── */
    --fm-radius-sm:     6px;
    --fm-radius:        12px;
    --fm-radius-lg:     16px;
    --fm-radius-xl:     24px;
    --fm-radius-full:   9999px;
    
    /* ── Spacing ── */
    --fm-space-xs:      0.25rem;
    --fm-space-sm:      0.5rem;
    --fm-space-md:      1rem;
    --fm-space-lg:      1.5rem;
    --fm-space-xl:      2rem;
    --fm-space-2xl:     3rem;
    
    /* ── Transitions ── */
    --fm-ease:          cubic-bezier(0.4, 0, 0.2, 1);
    --fm-duration:      200ms;
    --fm-duration-slow: 400ms;
    --fm-spring:        cubic-bezier(0.34, 1.56, 0.64, 1);
}
```

### 2.2 Typography

```css
/* Google Fonts: Noto Sans Thai + Inter */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+Thai:wght@400;500;600;700;800;900&display=swap');

body {
    font-family: 'Noto Sans Thai', 'Inter', system-ui, -apple-system, sans-serif;
    font-optical-sizing: auto;
}

/* Heading Scale */
.fm-display   { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 900; line-height: 1.1; }
.fm-h1        { font-size: clamp(1.8rem, 3.5vw, 2.5rem); font-weight: 800; line-height: 1.2; }
.fm-h2        { font-size: clamp(1.4rem, 2.5vw, 1.8rem); font-weight: 700; line-height: 1.3; }
.fm-h3        { font-size: 1.25rem; font-weight: 700; }
.fm-body      { font-size: 1rem; font-weight: 400; line-height: 1.6; }
.fm-body-lg   { font-size: 1.125rem; font-weight: 500; }
.fm-caption   { font-size: 0.875rem; font-weight: 500; color: var(--fm-ink-light); }
.fm-overline  { font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; }
```

### 2.3 Icon System

ใช้ **Lucide Icons** (lightweight, MIT license, ~24KB gzipped) ผ่าน CDN:

```html
<script src="https://unpkg.com/lucide@latest"></script>
```

ตัวอย่าง icon ที่จะใช้:
| Context | Icon | Usage |
|---------|------|-------|
| Dashboard | `layout-dashboard` | เมนู Dashboard |
| กิจกรรม | `calendar-days` | เมนูกิจกรรม |
| กลุ่ม | `users` | เมนูกลุ่ม |
| สินค้า | `shopping-bag` | เมนูสินค้า |
| QR Code | `qr-code` | เมนู QR |
| รายงาน | `bar-chart-3` | เมนูรายงาน |
| ตะกร้า | `shopping-cart` | ปุ่มตะกร้า |
| สแกน | `scan-line` | ปุ่มสแกน |
| เงิน | `wallet` | แสดงยอดเงิน |
| สำเร็จ | `check-circle-2` | checkout สำเร็จ |
| ประวัติ | `clock` | ประวัติการซื้อ |
| เพิ่ม | `plus-circle` | ปุ่มเพิ่ม |
| แก้ไข | `pencil` | ปุ่มแก้ไข |
| ลบ | `trash-2` | ปุ่มลบ |
| เข้าสู่ระบบ | `log-in` | ปุ่ม login |
| ออกจากระบบ | `log-out` | ปุ่ม logout |

---

## 3. การปรับปรุง Global Layout

### 3.1 Navbar ใหม่ (`includes/header.php`)

**ก่อน (ปัจจุบัน):**
- Navbar สีขาว border-bottom เรียบๆ
- ไม่มี icon
- ไม่ highlight active page

**หลัง (เป้าหมาย):**
```
┌─────────────────────────────────────────────────────────────┐
│  🛒 ตลาดอาหาร 5 หมู่     [📊 Dashboard] [📅 กิจกรรม]      │
│                           [👥 กลุ่ม] [🛍 สินค้า]            │
│                           [📱 QR Code] [📈 รายงาน]          │
│                                          ── ครูผู้สอน [ออก] │
└─────────────────────────────────────────────────────────────┘
```

**รายละเอียดการปรับปรุง:**

```css
/* Navbar Glass Effect */
.fm-navbar {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(226, 232, 240, 0.5);
    box-shadow: var(--fm-shadow-sm);
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: background var(--fm-duration) var(--fm-ease),
                box-shadow var(--fm-duration) var(--fm-ease);
}

/* Navbar Brand — Logo + ชื่อ */
.fm-navbar-brand {
    font-size: 1.2rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--fm-primary), var(--fm-accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.fm-navbar-brand::before {
    content: '🛒';
    font-size: 1.5rem;
    -webkit-text-fill-color: initial;
}

/* Active Nav Link */
.fm-nav-link {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 0.75rem;
    border-radius: var(--fm-radius-sm);
    color: var(--fm-ink-light);
    font-weight: 500;
    transition: all var(--fm-duration) var(--fm-ease);
    position: relative;
}

.fm-nav-link:hover {
    color: var(--fm-primary);
    background: var(--fm-primary-50);
}

.fm-nav-link.active {
    color: var(--fm-primary);
    background: var(--fm-primary-50);
    font-weight: 600;
}

.fm-nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 3px;
    border-radius: 2px;
    background: var(--fm-primary);
}
```

### 3.2 Footer & Flash Messages

**Flash Messages ใหม่:**
```css
/* Toast-style flash messages */
.fm-toast {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    min-width: 300px;
    padding: 1rem 1.25rem;
    border-radius: var(--fm-radius);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: var(--fm-shadow-lg);
    animation: fm-slideIn 0.4s var(--fm-spring);
    backdrop-filter: blur(10px);
}

.fm-toast-success {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    border-left: 4px solid var(--fm-success);
    color: #065f46;
}

.fm-toast-danger {
    background: linear-gradient(135deg, #fef2f2, #fee2e2);
    border-left: 4px solid var(--fm-danger);
    color: #991b1b;
}

.fm-toast-warning {
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border-left: 4px solid var(--fm-warning);
    color: #92400e;
}

@keyframes fm-slideIn {
    from { transform: translateX(100px); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}
```

### 3.3 Main Content Container

```css
.fm-main {
    padding: 2rem 0;
    min-height: calc(100vh - 72px);
    animation: fm-fadeUp 0.4s var(--fm-ease);
}

@keyframes fm-fadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
```

---

## 4. ฝั่งนักเรียน (Student)

> **หลักการ:** UI สำหรับเด็ก ป.3 ต้อง **ปุ่มใหญ่ สีสดใส มี emoji/icon ช่วย เข้าใจง่าย สนุก**

### 4.1 Student Shell (Background & Theme)

```css
.student-shell {
    /* Gradient background สีนุ่มๆ */
    background: linear-gradient(
        180deg,
        #eef2ff 0%,
        #fdf4ff 30%,
        #fff7ed 60%,
        #f0fdf4 100%
    );
    min-height: 100vh;
}

/* Floating decorative elements */
.student-shell::before {
    content: '';
    position: fixed;
    top: -150px;
    right: -150px;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(99,102,241,0.08), transparent 70%);
    z-index: 0;
    pointer-events: none;
}

.student-shell::after {
    content: '';
    position: fixed;
    bottom: -100px;
    left: -100px;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(245,158,11,0.08), transparent 70%);
    z-index: 0;
    pointer-events: none;
}
```

### 4.2 หน้าเลือกกลุ่ม (`student/join.php`)

**Design Concept:** การ์ดกลุ่มแบบ 3D floating พร้อม emoji ตัวแทนกลุ่ม

```
 ┌─────────────────────────────────────┐
 │           🎉 เลือกกลุ่มของฉัน       │
 │          ── ตลาดอาหาร 5 หมู่ ──     │
 │                                     │
 │  ┌──────────┐  ┌──────────┐        │
 │  │  🦁      │  │  🐯      │        │
 │  │ กลุ่ม 1  │  │ กลุ่ม 2  │        │
 │  │          │  │          │        │
 │  │ [PIN__] │  │ [PIN__] │        │
 │  │ [เข้ากลุ่ม]│  │ [เข้ากลุ่ม]│        │
 │  └──────────┘  └──────────┘        │
 │  ┌──────────┐  ┌──────────┐        │
 │  │  🐻      │  │  🐼      │        │
 │  │ กลุ่ม 3  │  │ กลุ่ม 4  │        │
 │  │ [PIN__] │  │ [PIN__] │        │
 │  │ [เข้ากลุ่ม]│  │ [เข้ากลุ่ม]│        │
 │  └──────────┘  └──────────┘        │
 └─────────────────────────────────────┘
```

**CSS ใหม่:**
```css
.fm-group-card {
    background: var(--fm-surface);
    border-radius: var(--fm-radius-lg);
    padding: 1.5rem;
    border: 2px solid var(--fm-border);
    box-shadow: var(--fm-shadow);
    transition: all 0.3s var(--fm-spring);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.fm-group-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: var(--fm-shadow-lg);
    border-color: var(--fm-primary-light);
}

.fm-group-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--fm-primary), var(--fm-accent));
    border-radius: var(--fm-radius-lg) var(--fm-radius-lg) 0 0;
}

.fm-group-emoji {
    font-size: 3rem;
    display: block;
    text-align: center;
    margin-bottom: 0.5rem;
    animation: fm-bounce 2s infinite;
}

@keyframes fm-bounce {
    0%, 100% { transform: translateY(0); }
    50%      { transform: translateY(-6px); }
}

.fm-group-name {
    font-size: 1.4rem;
    font-weight: 800;
    text-align: center;
    color: var(--fm-ink);
}

/* PIN Input — ขนาดใหญ่ เหมาะกับเด็ก */
.fm-pin-input {
    font-size: 1.8rem;
    font-weight: 800;
    text-align: center;
    letter-spacing: 0.3em;
    padding: 0.75rem;
    border: 2px solid var(--fm-border);
    border-radius: var(--fm-radius);
    transition: all var(--fm-duration) var(--fm-ease);
}

.fm-pin-input:focus {
    border-color: var(--fm-primary);
    box-shadow: 0 0 0 4px var(--fm-primary-50);
    outline: none;
}

/* ปุ่ม "เข้ากลุ่ม" — ขนาดใหญ่มาก */
.fm-btn-join {
    width: 100%;
    padding: 1rem;
    font-size: 1.2rem;
    font-weight: 800;
    border: none;
    border-radius: var(--fm-radius);
    background: linear-gradient(135deg, var(--fm-primary), var(--fm-primary-dark));
    color: white;
    cursor: pointer;
    transition: all 0.3s var(--fm-spring);
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
}

.fm-btn-join:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
}

.fm-btn-join:active {
    transform: translateY(0) scale(0.98);
}
```

### 4.3 หน้ากลุ่ม (`student/home.php`)

**Design Concept:** Hub หลักที่เห็นยอดเงินชัดเจน พร้อมปุ่มใหญ่สำหรับ actions

```
 ┌───────────────────────────────────┐
 │           🦁 กลุ่ม 1              │
 │                                   │
 │     ┌─────────────────────┐       │
 │     │  💰 เงินคงเหลือ     │       │
 │     │    ████ 85.50 บาท  │       │
 │     │  ████████████░░░░░ │       │
 │     │  ใช้ไป 14.50 / 100  │       │
 │     └─────────────────────┘       │
 │                                   │
 │  ┌──────────────────────────┐     │
 │  │  📷  สแกน QR Code       │     │
 │  └──────────────────────────┘     │
 │  ┌──────────────────────────┐     │
 │  │  🛒  ดูตะกร้า (3)        │     │
 │  └──────────────────────────┘     │
 │  ┌──────────────────────────┐     │
 │  │  📝  ประวัติการซื้อ       │     │
 │  └──────────────────────────┘     │
 │        ↻ เปลี่ยนกลุ่ม            │
 └───────────────────────────────────┘
```

**องค์ประกอบใหม่:**

```css
/* Wallet Card — การ์ดแสดงเงิน */
.fm-wallet-card {
    background: linear-gradient(135deg, var(--fm-primary), #7c3aed);
    border-radius: var(--fm-radius-xl);
    padding: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
}

.fm-wallet-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -30%;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
}

.fm-wallet-card::after {
    content: '';
    position: absolute;
    bottom: -40%;
    left: -20%;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
}

.fm-wallet-label {
    font-size: 0.875rem;
    font-weight: 600;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.fm-wallet-amount {
    font-size: clamp(2rem, 5vw, 3rem);
    font-weight: 900;
    line-height: 1.1;
    margin: 0.5rem 0;
}

/* Progress Bar แสดงเงินที่ใช้ไป */
.fm-wallet-progress {
    height: 8px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 1rem;
}

.fm-wallet-progress-bar {
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 4px;
    transition: width 1s var(--fm-ease);
}

/* Student Action Buttons — ปุ่มขนาดใหญ่สำหรับเด็ก */
.fm-student-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    width: 100%;
    padding: 1.25rem 1.5rem;
    font-size: 1.2rem;
    font-weight: 700;
    border: none;
    border-radius: var(--fm-radius-lg);
    text-decoration: none;
    transition: all 0.3s var(--fm-spring);
    position: relative;
    overflow: hidden;
}

.fm-student-btn:hover {
    transform: translateY(-3px);
}

.fm-student-btn:active {
    transform: scale(0.97);
}

.fm-student-btn-scan {
    background: linear-gradient(135deg, var(--fm-primary), var(--fm-primary-dark));
    color: white;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
}

.fm-student-btn-cart {
    background: linear-gradient(135deg, var(--fm-success), #059669);
    color: white;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
}

.fm-student-btn-history {
    background: var(--fm-surface);
    color: var(--fm-ink);
    border: 2px solid var(--fm-border);
    box-shadow: var(--fm-shadow);
}

/* Cart Badge */
.fm-cart-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    border-radius: var(--fm-radius-full);
    background: rgba(255, 255, 255, 0.25);
    font-size: 0.875rem;
    font-weight: 800;
    padding: 0 0.5rem;
}
```

### 4.4 หน้าสินค้า (`student/product.php`)

**Design Concept:** Product card สวยงาม พร้อม quantity selector แบบ haptic

```
 ┌───────────────────────────────────┐
 │  ┌───────────────────────────┐   │
 │  │                           │   │
 │  │     [Product Image]       │   │
 │  │                           │   │
 │  └───────────────────────────┘   │
 │                                   │
 │  🍎 ส้มเขียวหวาน                  │
 │  รายละเอียดสินค้า...               │
 │                                   │
 │  ┌──────────────────────────┐     │
 │  │     💰 5.00 บาท         │     │
 │  └──────────────────────────┘     │
 │                                   │
 │  จำนวน:                           │
 │  ┌────┐ ┌──────┐ ┌────┐          │
 │  │ ➖ │ │  1   │ │ ➕ │          │
 │  └────┘ └──────┘ └────┘          │
 │                                   │
 │  ┌──────────────────────────┐     │
 │  │  🛒 เพิ่มลงตะกร้า        │     │
 │  └──────────────────────────┘     │
 │                                   │
 │  [ดูตะกร้า]  [สแกนต่อ]            │
 └───────────────────────────────────┘
```

**CSS:**
```css
/* Product Detail Card */
.fm-product-detail {
    background: var(--fm-surface);
    border-radius: var(--fm-radius-xl);
    overflow: hidden;
    box-shadow: var(--fm-shadow-md);
}

.fm-product-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: var(--fm-radius-xl) var(--fm-radius-xl) 0 0;
}

.fm-product-image-lg {
    width: 100%;
    aspect-ratio: 4 / 3;
    object-fit: cover;
    transition: transform 0.6s var(--fm-ease);
}

.fm-product-detail:hover .fm-product-image-lg {
    transform: scale(1.03);
}

/* Price Badge */
.fm-price-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.6rem;
    font-weight: 900;
    color: var(--fm-primary);
    background: var(--fm-primary-50);
    padding: 0.5rem 1.25rem;
    border-radius: var(--fm-radius-full);
}

/* Quantity Selector */
.fm-qty-selector {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    background: var(--fm-bg);
    border-radius: var(--fm-radius);
    overflow: hidden;
    border: 2px solid var(--fm-border);
}

.fm-qty-btn {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--fm-primary);
    cursor: pointer;
    transition: background var(--fm-duration) var(--fm-ease);
}

.fm-qty-btn:hover {
    background: var(--fm-primary-50);
}

.fm-qty-btn:active {
    background: var(--fm-primary);
    color: white;
}

.fm-qty-input {
    width: 80px;
    height: 56px;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 800;
    border: none;
    background: var(--fm-surface);
    border-left: 2px solid var(--fm-border);
    border-right: 2px solid var(--fm-border);
}

/* Add to Cart Button */
.fm-btn-add-cart {
    width: 100%;
    padding: 1.25rem;
    font-size: 1.2rem;
    font-weight: 800;
    border: none;
    border-radius: var(--fm-radius-lg);
    background: linear-gradient(135deg, var(--fm-success), #059669);
    color: white;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
    transition: all 0.3s var(--fm-spring);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.fm-btn-add-cart:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45);
}
```

### 4.5 ตะกร้า (`student/cart.php`)

**Design Concept:** รายการชัดเจน, สรุปยอดเงินแบบ visual

```css
/* Cart Item */
.fm-cart-item {
    display: grid;
    grid-template-columns: 72px 1fr auto;
    gap: 1rem;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--fm-border-light);
    transition: background var(--fm-duration) var(--fm-ease);
}

.fm-cart-item:hover {
    background: var(--fm-bg);
}

.fm-cart-item-img {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: var(--fm-radius);
}

/* Cart Summary */
.fm-cart-summary {
    background: linear-gradient(135deg, var(--fm-bg), #eef2ff);
    border-radius: var(--fm-radius-lg);
    padding: 1.5rem;
    border: 2px solid var(--fm-primary-50);
}

.fm-cart-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
}

.fm-cart-total-row.highlight {
    font-size: 1.25rem;
    font-weight: 800;
    border-top: 2px dashed var(--fm-border);
    margin-top: 0.5rem;
    padding-top: 1rem;
}
```

### 4.6 หน้าซื้อสำเร็จ (`student/checkout.php`)

**Design Concept:** Celebration moment! confetti + animation

```css
/* Success Screen */
.fm-success-screen {
    text-align: center;
    padding: 3rem 2rem;
}

.fm-success-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--fm-success), #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    animation: fm-scaleIn 0.5s var(--fm-spring);
    box-shadow: 0 8px 30px rgba(16, 185, 129, 0.3);
}

.fm-success-icon svg {
    width: 48px;
    height: 48px;
    color: white;
    stroke-width: 3;
}

@keyframes fm-scaleIn {
    from { transform: scale(0); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}

/* Confetti particles */
.fm-confetti {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 9999;
    overflow: hidden;
}

.fm-confetti-piece {
    position: absolute;
    width: 10px;
    height: 10px;
    border-radius: 2px;
    animation: fm-confettiFall 3s ease-out forwards;
}

@keyframes fm-confettiFall {
    0%   { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
    100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
}
```

**JavaScript สำหรับ Confetti:**
```javascript
function createConfetti() {
    const container = document.createElement('div');
    container.className = 'fm-confetti';
    document.body.appendChild(container);
    
    const colors = ['#6366f1', '#f59e0b', '#10b981', '#ef4444', '#06b6d4', '#8b5cf6'];
    
    for (let i = 0; i < 50; i++) {
        const piece = document.createElement('div');
        piece.className = 'fm-confetti-piece';
        piece.style.left = Math.random() * 100 + '%';
        piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        piece.style.animationDelay = Math.random() * 2 + 's';
        piece.style.animationDuration = (2 + Math.random() * 2) + 's';
        container.appendChild(piece);
    }
    
    setTimeout(() => container.remove(), 5000);
}
```

### 4.7 หน้าสแกน (`student/scan.php`)

```css
/* Scanner Container */
.fm-scanner-wrapper {
    background: var(--fm-surface);
    border-radius: var(--fm-radius-xl);
    overflow: hidden;
    box-shadow: var(--fm-shadow-md);
    position: relative;
}

.fm-scanner-frame {
    position: relative;
}

.fm-scanner-frame::after {
    content: '';
    position: absolute;
    inset: 15%;
    border: 3px solid var(--fm-primary);
    border-radius: var(--fm-radius);
    animation: fm-scanPulse 2s infinite;
    pointer-events: none;
}

@keyframes fm-scanPulse {
    0%, 100% { opacity: 0.5; }
    50%      { opacity: 1; box-shadow: 0 0 20px rgba(99, 102, 241, 0.3); }
}
```

### 4.8 ประวัติการซื้อ (`student/history.php`)

```css
/* Timeline History */
.fm-history-item {
    position: relative;
    padding-left: 2rem;
    padding-bottom: 1.5rem;
    border-left: 2px solid var(--fm-border);
    margin-left: 0.75rem;
}

.fm-history-item::before {
    content: '';
    position: absolute;
    left: -7px;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--fm-primary);
    border: 2px solid var(--fm-surface);
    box-shadow: 0 0 0 3px var(--fm-primary-50);
}

.fm-history-item:last-child {
    border-left-color: transparent;
}

.fm-history-card {
    background: var(--fm-surface);
    border-radius: var(--fm-radius);
    padding: 1rem 1.25rem;
    box-shadow: var(--fm-shadow-sm);
    border: 1px solid var(--fm-border-light);
    transition: all var(--fm-duration) var(--fm-ease);
}

.fm-history-card:hover {
    box-shadow: var(--fm-shadow);
    border-color: var(--fm-primary-50);
}
```

---

## 5. ฝั่งครู (Teacher)

> **หลักการ:** Dashboard สวยงามระดับ premium, ใช้งานง่าย, ข้อมูลครบ

### 5.1 Sidebar Navigation (Optional Enhancement)

สำหรับหน้าจอกว้าง ≥992px สามารถเปลี่ยนจาก top navbar เป็น sidebar ได้:

```css
@media (min-width: 992px) {
    .fm-teacher-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        min-height: 100vh;
    }
    
    .fm-sidebar {
        background: var(--fm-surface);
        border-right: 1px solid var(--fm-border);
        padding: 1.5rem;
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
    }
    
    .fm-sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: var(--fm-radius);
        color: var(--fm-ink-light);
        font-weight: 500;
        text-decoration: none;
        transition: all var(--fm-duration) var(--fm-ease);
        margin-bottom: 2px;
    }
    
    .fm-sidebar-link:hover {
        background: var(--fm-primary-50);
        color: var(--fm-primary);
    }
    
    .fm-sidebar-link.active {
        background: linear-gradient(135deg, var(--fm-primary-50), #ede9fe);
        color: var(--fm-primary);
        font-weight: 600;
    }
}
```

### 5.2 Dashboard (`teacher/dashboard.php`)

**Design Concept:** Stat cards สวยงาม + กราฟ interactive + ข้อมูล real-time

**Stat Cards ใหม่:**
```css
.fm-stat-card {
    background: var(--fm-surface);
    border-radius: var(--fm-radius-lg);
    padding: 1.5rem;
    border: 1px solid var(--fm-border-light);
    box-shadow: var(--fm-shadow-sm);
    transition: all 0.3s var(--fm-ease);
    position: relative;
    overflow: hidden;
}

.fm-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--fm-shadow-md);
}

.fm-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: var(--fm-radius-lg) var(--fm-radius-lg) 0 0;
}

.fm-stat-card-groups::before   { background: linear-gradient(90deg, #6366f1, #818cf8); }
.fm-stat-card-products::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.fm-stat-card-orders::before   { background: linear-gradient(90deg, #10b981, #34d399); }
.fm-stat-card-items::before    { background: linear-gradient(90deg, #06b6d4, #22d3ee); }

.fm-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--fm-radius);
    display: flex;
    align-items: center;
    justify-content: center;
}

.fm-stat-icon-groups   { background: #eef2ff; color: #6366f1; }
.fm-stat-icon-products { background: #fef3c7; color: #d97706; }
.fm-stat-icon-orders   { background: #d1fae5; color: #059669; }
.fm-stat-icon-items    { background: #cffafe; color: #0891b2; }

.fm-stat-value {
    font-size: 2rem;
    font-weight: 900;
    line-height: 1.1;
    color: var(--fm-ink);
    margin-top: 0.75rem;
}

.fm-stat-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--fm-ink-light);
    margin-top: 0.25rem;
}

/* Counter animation via JS */
.fm-stat-value[data-count] {
    transition: opacity 0.3s;
}
```

**Counter Animation (JavaScript):**
```javascript
function animateCounters() {
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = parseFloat(el.dataset.count);
        const isMoney = el.dataset.money === 'true';
        const duration = 1200;
        const start = performance.now();
        
        function update(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 4); // ease-out quart
            const current = target * eased;
            
            if (isMoney) {
                el.textContent = current.toFixed(2) + ' บาท';
            } else {
                el.textContent = Math.round(current);
            }
            
            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    });
}
```

### 5.3 Panel & Table ใหม่

```css
.fm-panel {
    background: var(--fm-surface);
    border-radius: var(--fm-radius-lg);
    border: 1px solid var(--fm-border-light);
    box-shadow: var(--fm-shadow-sm);
    overflow: hidden;
}

.fm-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--fm-border-light);
    background: linear-gradient(180deg, var(--fm-bg), var(--fm-surface));
}

.fm-panel-body {
    padding: 1.5rem;
}

/* Enhanced Table */
.fm-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.fm-table thead th {
    background: var(--fm-bg);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--fm-ink-light);
    padding: 0.75rem 1rem;
    border-bottom: 2px solid var(--fm-border);
    white-space: nowrap;
}

.fm-table tbody td {
    padding: 0.875rem 1rem;
    border-bottom: 1px solid var(--fm-border-light);
    vertical-align: middle;
    transition: background var(--fm-duration) var(--fm-ease);
}

.fm-table tbody tr:hover td {
    background: var(--fm-primary-50);
}

.fm-table tbody tr:last-child td {
    border-bottom: none;
}
```

### 5.4 Login (`teacher/login.php`)

**Design Concept:** Split screen — ซ้ายเป็น gradient graphic, ขวาเป็นฟอร์ม

```css
.fm-login-page {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr;
}

@media (min-width: 768px) {
    .fm-login-page {
        grid-template-columns: 1fr 1fr;
    }
}

.fm-login-visual {
    background: linear-gradient(135deg, #6366f1, #7c3aed, #a855f7);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.fm-login-visual::before {
    content: '';
    position: absolute;
    width: 200%;
    height: 200%;
    background: repeating-conic-gradient(
        rgba(255,255,255,0.03) 0% 25%,
        transparent 25% 50%
    );
    animation: fm-patternRotate 30s linear infinite;
}

@keyframes fm-patternRotate {
    to { transform: rotate(360deg); }
}

.fm-login-form-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: var(--fm-bg);
}

.fm-login-form {
    width: 100%;
    max-width: 400px;
}

/* Form Input Enhanced */
.fm-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--fm-border);
    border-radius: var(--fm-radius);
    font-size: 1rem;
    font-weight: 500;
    background: var(--fm-surface);
    transition: all var(--fm-duration) var(--fm-ease);
}

.fm-input:focus {
    border-color: var(--fm-primary);
    box-shadow: 0 0 0 4px var(--fm-primary-50);
    outline: none;
}

.fm-input-group {
    position: relative;
}

.fm-input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--fm-ink-muted);
    pointer-events: none;
}

.fm-input-group .fm-input {
    padding-left: 2.75rem;
}
```

### 5.5 ฟอร์มต่างๆ (Products, Groups, Activities)

```css
/* Form Layout */
.fm-form-section {
    background: var(--fm-surface);
    border-radius: var(--fm-radius-lg);
    padding: 2rem;
    box-shadow: var(--fm-shadow-sm);
    border: 1px solid var(--fm-border-light);
}

.fm-form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--fm-ink);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.fm-form-label-required::after {
    content: '*';
    color: var(--fm-danger);
    font-weight: 700;
}

.fm-form-helper {
    font-size: 0.8125rem;
    color: var(--fm-ink-muted);
    margin-top: 0.375rem;
}

/* Image Upload Preview */
.fm-image-upload {
    border: 2px dashed var(--fm-border);
    border-radius: var(--fm-radius);
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all var(--fm-duration) var(--fm-ease);
    background: var(--fm-bg);
}

.fm-image-upload:hover {
    border-color: var(--fm-primary);
    background: var(--fm-primary-50);
}

.fm-image-upload.has-image {
    border-style: solid;
    padding: 0.5rem;
}

.fm-image-preview {
    max-height: 200px;
    border-radius: var(--fm-radius);
    object-fit: cover;
}

/* Toggle Switch Enhanced */
.fm-switch {
    position: relative;
    width: 52px;
    height: 28px;
    background: var(--fm-border);
    border-radius: 14px;
    transition: background var(--fm-duration) var(--fm-ease);
    cursor: pointer;
}

.fm-switch.active {
    background: var(--fm-primary);
}

.fm-switch::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: white;
    box-shadow: var(--fm-shadow-sm);
    transition: transform 0.3s var(--fm-spring);
}

.fm-switch.active::after {
    transform: translateX(24px);
}
```

### 5.6 Reports (`teacher/reports.php`)

```css
/* Enhanced Chart Container */
.fm-chart-container {
    background: var(--fm-surface);
    border-radius: var(--fm-radius-lg);
    padding: 1.5rem;
    border: 1px solid var(--fm-border-light);
    box-shadow: var(--fm-shadow-sm);
}

/* Chart color improvements in JS */
/* Chart.js config — gradient bars */
```

**Chart.js Enhanced Config:**
```javascript
const ctx = document.getElementById('spentChart').getContext('2d');
const gradient = ctx.createLinearGradient(0, 0, 0, 300);
gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)');
gradient.addColorStop(1, 'rgba(99, 102, 241, 0.2)');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: groupNames,
        datasets: [{
            label: 'ใช้ไป (บาท)',
            data: spentData,
            backgroundColor: gradient,
            borderColor: '#6366f1',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
            hoverBackgroundColor: '#818cf8',
        }]
    },
    options: {
        responsive: true,
        animation: {
            duration: 1200,
            easing: 'easeOutQuart'
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f1f5f9' },
                ticks: { font: { family: 'Noto Sans Thai', weight: 500 } }
            },
            x: {
                grid: { display: false },
                ticks: { font: { family: 'Noto Sans Thai', weight: 600 } }
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                cornerRadius: 8,
                titleFont: { family: 'Noto Sans Thai', weight: 700 },
                bodyFont: { family: 'Noto Sans Thai' },
                padding: 12,
            }
        }
    }
});
```

### 5.7 QR Codes (`teacher/qrcodes.php`)

```css
/* QR Card Enhanced */
.fm-qr-card {
    background: var(--fm-surface);
    border-radius: var(--fm-radius-lg);
    padding: 1.5rem;
    border: 1px solid var(--fm-border-light);
    box-shadow: var(--fm-shadow-sm);
    text-align: center;
    break-inside: avoid;
    page-break-inside: avoid;
    transition: all 0.3s var(--fm-ease);
}

.fm-qr-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--fm-shadow-md);
}

.fm-qr-image {
    border-radius: var(--fm-radius);
    border: 3px solid var(--fm-border-light);
    padding: 0.5rem;
    background: white;
}

.fm-qr-product-image {
    width: 100%;
    aspect-ratio: 4 / 3;
    object-fit: cover;
    border-radius: var(--fm-radius);
    margin-bottom: 1rem;
}
```

---

## 6. จอแสดงผลรวม (Display Board)

> **หลักการ:** จอ fullscreen สำหรับโปรเจกเตอร์ สวยงาม อ่านง่ายจากระยะไกล

### 6.1 Display Theme

```css
/* Display Board — Dark Glassmorphism */
.display-board {
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0f23, #1a1a3e, #0d1117);
    color: #f0f6fc;
    position: relative;
    overflow: hidden;
}

/* Animated background particles */
.display-board::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background:
        radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(245, 158, 11, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.08) 0%, transparent 60%);
    animation: fm-auroraShift 8s ease-in-out infinite alternate;
    pointer-events: none;
}

@keyframes fm-auroraShift {
    0%   { opacity: 0.6; }
    100% { opacity: 1; }
}

/* Display Card — Glass Effect */
.fm-display-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--fm-radius-lg);
    padding: 1.5rem;
    transition: all 0.3s var(--fm-ease);
}

.fm-display-card:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

/* Group Name — Display Board */
.fm-display-group-name {
    font-size: clamp(1.5rem, 2.5vw, 2rem);
    font-weight: 800;
    background: linear-gradient(135deg, #fff, rgba(255,255,255,0.7));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Balance — Large Display */
.fm-display-balance {
    font-size: clamp(2rem, 4vw, 3.5rem);
    font-weight: 900;
    line-height: 1;
    color: #10b981;
    text-shadow: 0 0 30px rgba(16, 185, 129, 0.3);
}

/* Animated Progress Bar */
.fm-display-progress {
    height: 10px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
    overflow: hidden;
    margin-top: 1rem;
}

.fm-display-progress-bar {
    height: 100%;
    border-radius: 5px;
    background: linear-gradient(90deg, #6366f1, #8b5cf6, #a855f7);
    transition: width 1s var(--fm-ease);
    box-shadow: 0 0 12px rgba(99, 102, 241, 0.5);
    position: relative;
}

.fm-display-progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
    );
    animation: fm-shimmer 2s infinite;
}

@keyframes fm-shimmer {
    0%   { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Total Orders Counter */
.fm-display-counter {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
    border: 1px solid rgba(99, 102, 241, 0.3);
    border-radius: var(--fm-radius-lg);
    padding: 1.5rem 2rem;
    text-align: center;
}

.fm-display-counter-value {
    font-size: 3rem;
    font-weight: 900;
    color: #818cf8;
    text-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
}
```

### 6.2 Timer (`display/timer.php`)

```css
/* Timer — Fullscreen Center */
.fm-timer-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 80px);
}

.fm-timer-value {
    font-size: clamp(5rem, 15vw, 12rem);
    font-weight: 900;
    font-variant-numeric: tabular-nums;
    background: linear-gradient(135deg, #fff, rgba(255,255,255,0.8));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: none;
    filter: drop-shadow(0 0 30px rgba(255,255,255,0.1));
    letter-spacing: 0.05em;
    line-height: 1;
}

.fm-timer-value.warning {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    -webkit-background-clip: text;
    animation: fm-pulse 1s infinite;
}

.fm-timer-value.danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    -webkit-background-clip: text;
    animation: fm-pulse 0.5s infinite;
}

@keyframes fm-pulse {
    0%, 100% { opacity: 1; }
    50%      { opacity: 0.6; }
}

/* Circular Progress Ring */
.fm-timer-ring {
    position: relative;
    width: 350px;
    height: 350px;
}

.fm-timer-ring svg circle {
    fill: none;
    stroke-width: 8;
    stroke-linecap: round;
}

.fm-timer-ring-bg { stroke: rgba(255, 255, 255, 0.1); }

.fm-timer-ring-progress {
    stroke: url(#timerGradient);
    transition: stroke-dashoffset 1s linear;
    filter: drop-shadow(0 0 6px rgba(99, 102, 241, 0.5));
}
```

---

## 7. หน้า Public Landing

### 7.1 Design Concept

```
┌─────────────────────────────────────────────────────────────┐
│  ┌─────────────────────────┬────────────────────────────┐  │
│  │                         │  ╭──────────────────────╮  │  │
│  │  🛒 ตลาดอาหาร 5 หมู่    │  │   สถานะกิจกรรม      │  │  │
│  │  ──────────────────     │  │                      │  │  │
│  │  ร้านค้าจำลองสำหรับ      │  │  🟢 กำลังใช้งาน      │  │  │
│  │  กิจกรรมการเรียนรู้       │  │  "ตลาดอาหาร 5 หมู่"  │  │  │
│  │                         │  │                      │  │  │
│  │  [🎒 เริ่มใช้งานนักเรียน] │  ╰──────────────────────╯  │  │
│  │  [👩‍🏫 ครูเข้าสู่ระบบ]    │                            │  │
│  │  [📺 เปิดจอรวม]          │                            │  │
│  │                         │                            │  │
│  └─────────────────────────┴────────────────────────────┘  │
│                                                             │
│  ┌───────┐ ┌───────┐ ┌───────┐ ┌───────┐ ┌───────┐       │
│  │ 🥩    │ │ 🥬    │ │ 🍊    │ │ 🍚    │ │ 🥛    │       │
│  │ เนื้อ  │ │ ผัก   │ │ ผลไม้ │ │ ข้าว  │ │ นม    │       │
│  └───────┘ └───────┘ └───────┘ └───────┘ └───────┘       │
└─────────────────────────────────────────────────────────────┘
```

```css
.fm-hero {
    padding: 4rem 0 3rem;
    position: relative;
}

.fm-hero-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 900;
    line-height: 1.15;
    background: linear-gradient(135deg, var(--fm-ink), var(--fm-primary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Food Group Icons Row */
.fm-food-icons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 3rem;
}

.fm-food-icon {
    width: 80px;
    height: 80px;
    border-radius: var(--fm-radius-lg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    font-size: 2rem;
    transition: all 0.3s var(--fm-spring);
    cursor: default;
}

.fm-food-icon:hover {
    transform: translateY(-4px) scale(1.05);
}

.fm-food-icon:nth-child(1) { background: #fee2e2; }
.fm-food-icon:nth-child(2) { background: #dcfce7; }
.fm-food-icon:nth-child(3) { background: #ffedd5; }
.fm-food-icon:nth-child(4) { background: #fef9c3; }
.fm-food-icon:nth-child(5) { background: #dbeafe; }

.fm-food-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--fm-ink-light);
}
```

---

## 8. Micro-Animations & Transitions

### 8.1 Global Animations

```css
/* ── Page Transitions ── */
@keyframes fm-fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

@keyframes fm-fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

@keyframes fm-fadeDown {
    from { opacity: 0; transform: translateY(-16px); }
    to   { opacity: 1; transform: translateY(0); }
}

@keyframes fm-scaleIn {
    from { opacity: 0; transform: scale(0.95); }
    to   { opacity: 1; transform: scale(1); }
}

@keyframes fm-slideInLeft {
    from { opacity: 0; transform: translateX(-20px); }
    to   { opacity: 1; transform: translateX(0); }
}

@keyframes fm-slideInRight {
    from { opacity: 0; transform: translateX(20px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* ── Stagger Children ── */
.fm-stagger > * {
    animation: fm-fadeUp 0.5s var(--fm-ease) both;
}

.fm-stagger > *:nth-child(1)  { animation-delay: 0.05s; }
.fm-stagger > *:nth-child(2)  { animation-delay: 0.10s; }
.fm-stagger > *:nth-child(3)  { animation-delay: 0.15s; }
.fm-stagger > *:nth-child(4)  { animation-delay: 0.20s; }
.fm-stagger > *:nth-child(5)  { animation-delay: 0.25s; }
.fm-stagger > *:nth-child(6)  { animation-delay: 0.30s; }
.fm-stagger > *:nth-child(7)  { animation-delay: 0.35s; }
.fm-stagger > *:nth-child(8)  { animation-delay: 0.40s; }

/* ── Button Press Effect ── */
.fm-btn {
    transition: all 0.2s var(--fm-spring);
}

.fm-btn:active {
    transform: scale(0.96);
}

/* ── Hover Lift ── */
.fm-hover-lift {
    transition: transform 0.3s var(--fm-ease), box-shadow 0.3s var(--fm-ease);
}

.fm-hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: var(--fm-shadow-md);
}

/* ── Skeleton Loading ── */
.fm-skeleton {
    background: linear-gradient(90deg, #f1f5f9, #e2e8f0, #f1f5f9);
    background-size: 200% 100%;
    animation: fm-skeleton 1.5s infinite;
    border-radius: var(--fm-radius-sm);
}

@keyframes fm-skeleton {
    0%   { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* ── Ripple Effect for Buttons ── */
.fm-ripple {
    position: relative;
    overflow: hidden;
}

.fm-ripple::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s, opacity 0.6s;
    opacity: 0;
}

.fm-ripple:active::after {
    width: 300px;
    height: 300px;
    opacity: 0;
}

/* ── Number Counting ── */
.fm-count-up {
    display: inline-block;
}

/* ── Tooltip ── */
.fm-tooltip {
    position: relative;
}

.fm-tooltip::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%) translateY(4px);
    padding: 0.5rem 0.75rem;
    border-radius: var(--fm-radius-sm);
    background: var(--fm-ink);
    color: white;
    font-size: 0.8rem;
    font-weight: 500;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s var(--fm-ease);
}

.fm-tooltip:hover::after {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}
```

### 8.2 JavaScript Animations

```javascript
/* ── Intersection Observer for scroll animations ── */
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fm-visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

document.querySelectorAll('.fm-animate-on-scroll').forEach(el => {
    observer.observe(el);
});

/* ── Smooth number counting ── */
function countUp(element, target, duration = 1000) {
    let start = 0;
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        
        element.textContent = Math.round(target * eased);
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}
```

---

## 9. Responsive & Mobile-First

### 9.1 Breakpoints

```css
/* Mobile First Breakpoints */
/* xs: 0      — Default (phones) */
/* sm: 576px  — Large phones */
/* md: 768px  — Tablets */
/* lg: 992px  — Desktops */
/* xl: 1200px — Large desktops */

/* ── Student Pages — Optimized for Tablets ── */
@media (max-width: 767.98px) {
    .fm-student-btn {
        padding: 1rem;
        font-size: 1.1rem;
    }
    
    .fm-wallet-amount {
        font-size: 2rem;
    }
    
    .fm-group-card {
        padding: 1rem;
    }
    
    .fm-pin-input {
        font-size: 1.4rem;
    }
    
    .fm-qty-btn,
    .fm-qty-input {
        height: 48px;
    }
}

/* ── Teacher Pages ── */
@media (max-width: 991.98px) {
    .fm-sidebar {
        display: none;
    }
    
    .fm-teacher-layout {
        grid-template-columns: 1fr;
    }
}

/* ── Display Board — Projector Optimization ── */
@media (min-width: 1200px) {
    .fm-display-balance {
        font-size: 4rem;
    }
    
    .fm-display-group-name {
        font-size: 2.5rem;
    }
}

/* ── Print Styles ── */
@media print {
    .fm-navbar,
    .fm-sidebar,
    .no-print,
    .btn,
    .fm-toast {
        display: none !important;
    }
    
    body {
        background: white !important;
        color: black !important;
    }
    
    .fm-panel,
    .fm-stat-card,
    .fm-qr-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }
}
```

### 9.2 Touch Optimization

```css
/* Touch Target Sizes (WCAG 2.2 — ≥ 44px) */
@media (pointer: coarse) {
    .fm-btn,
    .fm-nav-link,
    .fm-student-btn,
    button,
    a.btn {
        min-height: 48px;
        min-width: 48px;
    }
    
    .fm-table tbody td {
        padding: 1rem;
    }
    
    .fm-input {
        padding: 1rem;
        font-size: 1rem;
    }
}
```

---

## 10. Accessibility (A11y)

### 10.1 Focus States

```css
/* Custom Focus Ring */
:focus-visible {
    outline: 3px solid var(--fm-primary);
    outline-offset: 2px;
    border-radius: 4px;
}

/* Skip to content */
.fm-skip-link {
    position: absolute;
    top: -40px;
    left: 0;
    padding: 0.75rem 1rem;
    background: var(--fm-primary);
    color: white;
    font-weight: 600;
    z-index: 10000;
    transition: top 0.2s;
}

.fm-skip-link:focus {
    top: 0;
}
```

### 10.2 ARIA & Semantic

```html
<!-- ตัวอย่าง Stat Card ที่เพิ่ม ARIA -->
<div class="fm-stat-card" role="status" aria-label="จำนวนกลุ่ม">
    <div class="fm-stat-label" id="stat-groups-label">จำนวนกลุ่ม</div>
    <div class="fm-stat-value" aria-labelledby="stat-groups-label">5</div>
</div>

<!-- ตัวอย่าง Progress Bar ที่เพิ่ม ARIA -->
<div class="fm-wallet-progress" role="progressbar" 
     aria-valuenow="85.50" aria-valuemin="0" aria-valuemax="100"
     aria-label="ใช้เงินไปแล้ว 14.50 จาก 100 บาท">
    <div class="fm-wallet-progress-bar" style="width: 14.5%"></div>
</div>

<!-- Flash Message ที่ screen reader อ่านได้ -->
<div class="fm-toast" role="alert" aria-live="polite">
    <i data-lucide="check-circle-2"></i>
    <span>ซื้อสำเร็จ!</span>
</div>
```

### 10.3 Color Contrast

ทุกคู่สี text/background ต้องผ่าน **WCAG AA** (contrast ratio ≥ 4.5:1 สำหรับ normal text, ≥ 3:1 สำหรับ large text)

| Text Color | Background | Ratio | Pass? |
|-----------|-----------|-------|-------|
| `#1e293b` (ink) | `#f8fafc` (bg) | 14.8:1 | ✅ AAA |
| `#ffffff` (white) | `#6366f1` (primary) | 4.6:1 | ✅ AA |
| `#ffffff` (white) | `#10b981` (success) | 3.6:1 | ✅ AA (large) |
| `#065f46` (dark green) | `#d1fae5` (light green) | 7.1:1 | ✅ AAA |
| `#f0f6fc` (light) | `#0f0f23` (display bg) | 16.2:1 | ✅ AAA |

---

## 11. Performance Optimization

### 11.1 CSS Optimization

```css
/* ── Reduce Motion for users who prefer it ── */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* ── Content Visibility for off-screen elements ── */
.fm-panel,
.fm-stat-card {
    content-visibility: auto;
    contain-intrinsic-size: 0 200px;
}
```

### 11.2 Resource Loading

```html
<!-- Preload critical fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- Font loading strategy -->
<link rel="preload" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;600;800&display=swap" as="style">

<!-- Defer non-critical CSS -->
<link rel="stylesheet" href="dataTables.bootstrap5.css" media="print" onload="this.media='all'">

<!-- Lazy load images -->
<img loading="lazy" decoding="async" src="..." alt="...">
```

### 11.3 JavaScript Optimization

```javascript
// Debounced resize handler
function debounce(fn, delay = 150) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

// Lazy initialize DataTables only when table is visible
const tableObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            new DataTable(entry.target, { /* options */ });
            tableObserver.unobserve(entry.target);
        }
    });
});

document.querySelectorAll('[data-table]').forEach(t => tableObserver.observe(t));
```

---

## 12. ลำดับการพัฒนา (Roadmap)

### Phase 1: Foundation (1–2 วัน)
| # | งาน | ไฟล์ | ความสำคัญ |
|---|------|------|-----------|
| 1.1 | สร้าง Design System CSS ใหม่ (variables, typography, colors) | `public/assets/css/app.css` | 🔴 |
| 1.2 | เพิ่ม Google Fonts (Noto Sans Thai + Inter) | `includes/header.php` | 🔴 |
| 1.3 | เพิ่ม Lucide Icons CDN | `includes/header.php` | 🔴 |
| 1.4 | ปรับปรุง Navbar ใหม่ (glass effect, icons, active state) | `includes/header.php` | 🔴 |
| 1.5 | ปรับ Flash Messages → Toast style | `includes/header.php` | 🟡 |
| 1.6 | ปรับ Footer | `includes/footer.php` | 🟢 |

### Phase 2: Student Pages (2–3 วัน)
| # | งาน | ไฟล์ | ความสำคัญ |
|---|------|------|-----------|
| 2.1 | Student Shell background + decorations | `app.css` | 🔴 |
| 2.2 | หน้าเลือกกลุ่ม — Group cards + PIN input | `student/join.php` | 🔴 |
| 2.3 | หน้ากลุ่ม — Wallet card + action buttons | `student/home.php` | 🔴 |
| 2.4 | หน้าสินค้า — Product detail + qty selector | `student/product.php` | 🔴 |
| 2.5 | ตะกร้า — Cart items + summary | `student/cart.php` | 🔴 |
| 2.6 | ซื้อสำเร็จ — Celebration + confetti | `student/checkout.php` | 🟡 |
| 2.7 | สแกน QR — Scanner frame | `student/scan.php` | 🟡 |
| 2.8 | ประวัติ — Timeline style | `student/history.php` | 🟡 |

### Phase 3: Teacher Pages (2–3 วัน)
| # | งาน | ไฟล์ | ความสำคัญ |
|---|------|------|-----------|
| 3.1 | Login page — Split screen design | `teacher/login.php` | 🔴 |
| 3.2 | Dashboard — Stat cards + counter animation | `teacher/dashboard.php` | 🔴 |
| 3.3 | Enhanced tables & panels | ทุกหน้า teacher | 🔴 |
| 3.4 | ฟอร์มสินค้า — Image upload preview | `teacher/product_form.php` | 🟡 |
| 3.5 | ฟอร์มกลุ่ม — Enhanced toggle | `teacher/group_form.php` | 🟡 |
| 3.6 | รายงาน — Enhanced charts | `teacher/reports.php` | 🟡 |
| 3.7 | QR Codes — Enhanced cards | `teacher/qrcodes.php` | 🟡 |
| 3.8 | Live Orders — Real-time feel | `teacher/live_orders.php` | 🟡 |
| 3.9 | Group Detail — Enhanced stats | `teacher/group_detail.php` | 🟢 |
| 3.10 | Activities — Enhanced list | `teacher/activities.php` | 🟢 |
| 3.11 | Activity Form — Enhanced | `teacher/activity_form.php` | 🟢 |
| 3.12 | Reset Activity — Warning design | `teacher/reset_activity.php` | 🟢 |

### Phase 4: Display Board (1–2 วัน)
| # | งาน | ไฟล์ | ความสำคัญ |
|---|------|------|-----------|
| 4.1 | Display Board — Dark glass theme | `display/summary.php` | 🔴 |
| 4.2 | Live Feed — Enhanced cards | `display/live.php` | 🟡 |
| 4.3 | Timer — Circular progress ring | `display/timer.php` | 🟡 |

### Phase 5: Landing + Polish (1 วัน)
| # | งาน | ไฟล์ | ความสำคัญ |
|---|------|------|-----------|
| 5.1 | Landing page — Hero section + food icons | `public/index.php` | 🔴 |
| 5.2 | ปรับ placeholder-food.svg ให้สวยขึ้น | `public/assets/images/` | 🟡 |
| 5.3 | Global animations (scroll, stagger, hover) | `app.css` + `app.js` | 🟡 |
| 5.4 | Performance tuning (lazy load, preload) | `includes/header.php` | 🟢 |
| 5.5 | Accessibility audit | ทุกไฟล์ | 🟢 |
| 5.6 | Cross-browser testing | - | 🟢 |

---

## 13. รายละเอียดไฟล์ที่ต้องแก้ไข

### 13.1 สรุปรวม

| ไฟล์ | ประเภทการแก้ | ปริมาณงาน |
|------|-------------|-----------|
| `public/assets/css/app.css` | **เขียนใหม่ทั้งหมด** — จาก 112 → ประมาณ 800+ บรรทัด | 🔴 มาก |
| `public/assets/js/app.js` | **เขียนใหม่ทั้งหมด** — จาก 19 → ประมาณ 200+ บรรทัด | 🔴 มาก |
| `includes/header.php` | แก้ไข Navbar, เพิ่ม fonts/icons CDN, Toast | 🔴 มาก |
| `includes/footer.php` | เพิ่ม Lucide init script | 🟢 เล็กน้อย |
| `public/index.php` | เปลี่ยน layout → Hero section + food icons | 🟡 ปานกลาง |
| `student/join.php` | เปลี่ยน group cards, PIN input | 🟡 ปานกลาง |
| `student/home.php` | เพิ่ม Wallet card, action buttons | 🟡 ปานกลาง |
| `student/product.php` | เปลี่ยน product card, qty selector | 🟡 ปานกลาง |
| `student/cart.php` | เปลี่ยน cart layout, summary | 🟡 ปานกลาง |
| `student/checkout.php` | เพิ่ม celebration, confetti | 🟡 ปานกลาง |
| `student/scan.php` | เพิ่ม scanner frame styling | 🟢 เล็กน้อย |
| `student/history.php` | เปลี่ยนเป็น timeline | 🟢 เล็กน้อย |
| `teacher/login.php` | เปลี่ยน layout → Split screen | 🟡 ปานกลาง |
| `teacher/dashboard.php` | เปลี่ยน stat cards, tables | 🟡 ปานกลาง |
| `teacher/products.php` | เปลี่ยน table, panel | 🟢 เล็กน้อย |
| `teacher/product_form.php` | เปลี่ยน form layout, image upload | 🟡 ปานกลาง |
| `teacher/groups.php` | เปลี่ยน table, panel | 🟢 เล็กน้อย |
| `teacher/group_form.php` | เปลี่ยน form layout | 🟢 เล็กน้อย |
| `teacher/group_detail.php` | เปลี่ยน stat cards, tables | 🟢 เล็กน้อย |
| `teacher/activities.php` | เปลี่ยน table, panel | 🟢 เล็กน้อย |
| `teacher/activity_form.php` | เปลี่ยน form layout | 🟢 เล็กน้อย |
| `teacher/qrcodes.php` | เปลี่ยน QR cards | 🟢 เล็กน้อย |
| `teacher/reports.php` | เปลี่ยน charts, tables | 🟡 ปานกลาง |
| `teacher/live_orders.php` | เปลี่ยน table, auto-refresh | 🟢 เล็กน้อย |
| `teacher/reset_activity.php` | เปลี่ยน warning UI | 🟢 เล็กน้อย |
| `display/summary.php` | เปลี่ยน dark theme, cards | 🟡 ปานกลาง |
| `display/live.php` | เปลี่ยน feed cards | 🟢 เล็กน้อย |
| `display/timer.php` | เพิ่ม circular progress, pulse | 🟡 ปานกลาง |

### 13.2 ไฟล์ใหม่ที่ต้องสร้าง

| ไฟล์ | วัตถุประสงค์ |
|------|-------------|
| `public/assets/css/student.css` | (optional) แยก CSS เฉพาะฝั่งนักเรียน |
| `public/assets/css/display.css` | (optional) แยก CSS เฉพาะจอแสดงผล |
| `public/assets/js/confetti.js` | (optional) แยก confetti animation |
| `public/assets/js/counter.js` | (optional) แยก counter animation |
| `public/assets/images/` | อาจเพิ่ม SVG icons/illustrations |

### 13.3 Dependencies ใหม่ (CDN)

| Library | URL | Size | ใช้ทำ |
|---------|-----|------|-------|
| Google Fonts | `fonts.googleapis.com` | ~50KB | Noto Sans Thai + Inter |
| Lucide Icons | `unpkg.com/lucide@latest` | ~24KB gzip | Icon system |
| (มีอยู่แล้ว) Bootstrap 5.3 | `cdn.jsdelivr.net` | ~60KB | Base framework |
| (มีอยู่แล้ว) Chart.js 4.4 | `cdn.jsdelivr.net` | ~70KB | Charts |
| (มีอยู่แล้ว) DataTables 2.0 | `cdn.datatables.net` | ~30KB | Tables |
| (มีอยู่แล้ว) html5-qrcode | `unpkg.com` | ~200KB | QR Scanner |

> **หมายเหตุ:** ไม่ต้องเพิ่ม library หลักใหม่ ใช้เฉพาะ font และ icon เท่านั้น

---

## 📎 Appendix: ตัวอย่าง HTML ที่ปรับปรุง

### A1. Navbar ใหม่ (header.php)

```html
<nav class="fm-navbar navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="fm-navbar-brand" href="<?= h(url('public/index.php')) ?>">
            <?= h(APP_NAME) ?>
        </a>
        <button class="navbar-toggler border-0" type="button" 
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="เมนู">
            <i data-lucide="menu" style="width:24px;height:24px"></i>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                <?php if ($teacher): ?>
                    <li class="nav-item">
                        <a class="fm-nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>" 
                           href="<?= h(url('teacher/dashboard.php')) ?>">
                            <i data-lucide="layout-dashboard" style="width:18px;height:18px"></i>
                            Dashboard
                        </a>
                    </li>
                    <!-- ... more items ... -->
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
```

### A2. Stat Card (dashboard.php)

```html
<div class="fm-stat-card fm-stat-card-groups fm-hover-lift">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div class="fm-stat-label">จำนวนกลุ่ม</div>
            <div class="fm-stat-value" data-count="<?= $stats['groups'] ?>">0</div>
        </div>
        <div class="fm-stat-icon fm-stat-icon-groups">
            <i data-lucide="users" style="width:24px;height:24px"></i>
        </div>
    </div>
</div>
```

### A3. Wallet Card (student/home.php)

```html
<div class="fm-wallet-card">
    <div class="fm-wallet-label">
        <i data-lucide="wallet" style="width:16px;height:16px"></i>
        เงินคงเหลือ
    </div>
    <div class="fm-wallet-amount"><?= money($group['current_balance']) ?></div>
    <div class="fm-wallet-progress">
        <?php $spent = (float)$group['initial_budget'] - (float)$group['current_balance']; ?>
        <?php $pct = (float)$group['initial_budget'] > 0 ? ($spent / (float)$group['initial_budget']) * 100 : 0; ?>
        <div class="fm-wallet-progress-bar" style="width: <?= h((string)$pct) ?>%"></div>
    </div>
    <div class="d-flex justify-content-between mt-2" style="font-size:0.875rem;opacity:0.9">
        <span>ใช้ไป <?= money($spent) ?></span>
        <span>จาก <?= money($group['initial_budget']) ?></span>
    </div>
</div>
```

---

## 🎯 สรุป

| หมวด | ก่อนปรับปรุง | หลังปรับปรุง |
|------|-------------|-------------|
| **Color System** | 6 สี CSS variables | 30+ สี design tokens ครบชุด |
| **Typography** | system-ui | Noto Sans Thai + Inter (Google Fonts) |
| **Icons** | ไม่มี | Lucide Icons ครบทุกหน้า |
| **Animations** | 0 animations | 15+ micro-animations + transitions |
| **Components** | Bootstrap default | Custom premium components |
| **Student UX** | ปุ่มเล็ก เรียบ | ปุ่มใหญ่ สีสดใส มี emoji/icon |
| **Teacher UX** | ตารางเรียบ | Dashboard premium + animated charts |
| **Display Board** | Dark basic | Glassmorphism + aurora background |
| **Loading States** | ไม่มี | Skeleton loading + counter animation |
| **Empty States** | ข้อความเปล่า | Illustration + call-to-action |
| **Success States** | Alert ธรรมดา | Confetti + celebration animation |
| **Responsive** | Bootstrap grid เท่านั้น | Touch-optimized + print styles |
| **Accessibility** | ไม่มี | ARIA labels + focus states + skip link |
| **CSS Lines** | ~112 | ~800+ (ขยายขนาด ~7 เท่า) |
| **JS Lines** | ~19 | ~200+ (ขยายขนาด ~10 เท่า) |

> **ประมาณระยะเวลาทั้งหมด:** 7–11 วันทำงาน (ขึ้นอยู่กับรายละเอียดที่ต้องการ)

---

*แผนฉบับนี้ออกแบบให้ปรับปรุง UI/UX อย่างครอบคลุมทั้งระบบ โดยคงโครงสร้าง PHP เดิมไว้ เปลี่ยนเฉพาะ HTML classes, CSS, และ JavaScript เพื่อลดความเสี่ยงต่อ logic ที่ทำงานอยู่*

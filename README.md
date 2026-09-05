# chookiat_system

## Tech Stack ที่ใช้
| งานด้าน | เทคโนโลยี / เครื่องมือ | |
| :--- | :--- | :--- |
| **Backend** | Laravel |
| **Frontend** | Blade Template, Bootstrap , jQuery |
| **Database** |  mariaDB |
| **Tools** |  Git |

---
## บัญชีสำหรับเข้าสู่ระบบ
| username | Password | สิทธิ์ |
| :--- | :--- | :--- |
| admin | password123 | admin |
| user1 | password123 | user |
| test | 123456 | admin |

---
## 🚀 ขั้นตอนการติดตั้งและเปิดใช้งาน (Installation)
```bash
# 1. Clone repository
git clone <URL_REPOSITORY>
# 2. ติดตั้ง Dependencies
composer install
npm install
# 3. คัดลอกไฟล์ Environment
cp .env.example .env
# 4. Generate Key & Run Migration พร้อม Seed ข้อมูล
php artisan key:generate
php artisan migrate --seed
# 5. รันเซิร์ฟเวอร์
php artisan serve
```

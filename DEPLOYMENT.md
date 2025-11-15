# 🚀 دليل النشر - نظام الأباسي المحاسبي

هذا الدليل يشرح كيفية نشر النظام على منصات الاستضافة المختلفة.

## 📋 جدول المحتويات

1. [النشر على InfinityFree](#infinityfree)
2. [النشر على Render.com](#render)
3. [النشر على Railway.app](#railway)
4. [النشر على استضافة مشتركة](#shared-hosting)
5. [النشر على VPS](#vps)

---

## 🆓 النشر على InfinityFree {#infinityfree}

### الخطوة 1: التسجيل
1. اذهب إلى [InfinityFree](https://infinityfree.net)
2. سجل حساب جديد (مجاني)
3. قم بتأكيد البريد الإلكتروني

### الخطوة 2: إنشاء موقع
1. من لوحة التحكم، اضغط على "Create Account"
2. اختر دومين فرعي مجاني أو استخدم دومينك
3. انتظر تفعيل الحساب (5-10 دقائق)

### الخطوة 3: رفع الملفات
1. افتح لوحة التحكم (Control Panel)
2. اذهب إلى "File Manager"
3. ارفع جميع ملفات النظام إلى مجلد `htdocs`

أو استخدم FTP:
```
Host: ftpupload.net
Username: if0_XXXXXXX
Password: [كلمة المرور من لوحة التحكم]
Port: 21
```

### الخطوة 4: إنشاء قاعدة البيانات
1. من لوحة التحكم، اذهب إلى "MySQL Databases"
2. أنشئ قاعدة بيانات جديدة
3. احفظ بيانات الاتصال:
   - Database Name: `if0_XXXXXXX_alabasi`
   - Username: `if0_XXXXXXX`
   - Password: [كلمة المرور]
   - Host: `sql###.infinityfreeapp.com`

### الخطوة 5: استيراد البيانات
1. اذهب إلى "phpMyAdmin"
2. اختر قاعدة البيانات
3. اضغط على "Import"
4. ارفع ملف `database.sql`
5. اضغط "Go"

### الخطوة 6: تحديث الإعدادات
عدّل ملف `includes/db.php`:
```php
define('DB_HOST', 'sql###.infinityfreeapp.com');
define('DB_NAME', 'if0_XXXXXXX_alabasi');
define('DB_USER', 'if0_XXXXXXX');
define('DB_PASS', 'your_password');
```

### الخطوة 7: الوصول للنظام
افتح: `http://your-domain.infinityfreeapp.com`

---

## ☁️ النشر على Render.com {#render}

### الخطوة 1: التحضير
تأكد من وجود ملف `render.yaml` في المشروع (موجود مسبقاً)

### الخطوة 2: التسجيل
1. اذهب إلى [Render.com](https://render.com)
2. سجل دخول بحساب GitHub
3. اربط حساب GitHub

### الخطوة 3: إنشاء Web Service
1. اضغط "New +" → "Web Service"
2. اختر المستودع: `alabasi2025/alabasi-accounting-system`
3. املأ البيانات:
   - **Name:** alabasi-accounting
   - **Environment:** Docker
   - **Plan:** Free

### الخطوة 4: إنشاء قاعدة البيانات
1. اضغط "New +" → "PostgreSQL"
2. املأ البيانات:
   - **Name:** alabasi-db
   - **Plan:** Free
3. احفظ بيانات الاتصال

### الخطوة 5: ربط المتغيرات
في إعدادات Web Service، أضف:
```
DB_HOST=<من بيانات PostgreSQL>
DB_NAME=<من بيانات PostgreSQL>
DB_USER=<من بيانات PostgreSQL>
DB_PASS=<من بيانات PostgreSQL>
```

### الخطوة 6: النشر
- Render سينشر تلقائياً
- انتظر 5-10 دقائق
- افتح الرابط المعطى

---

## 🚂 النشر على Railway.app {#railway}

### الخطوة 1: التسجيل
1. اذهب إلى [Railway.app](https://railway.app)
2. سجل دخول بحساب GitHub

### الخطوة 2: إنشاء مشروع جديد
1. اضغط "New Project"
2. اختر "Deploy from GitHub repo"
3. اختر `alabasi2025/alabasi-accounting-system`

### الخطوة 3: إضافة MySQL
1. اضغط "+ New"
2. اختر "Database" → "MySQL"
3. انتظر حتى يتم الإنشاء

### الخطوة 4: ربط المتغيرات
Railway سيربط تلقائياً، أو أضف يدوياً:
```
MYSQL_URL=<من بيانات MySQL>
```

### الخطوة 5: النشر
- Railway سينشر تلقائياً
- احصل على الرابط من "Settings" → "Domains"

---

## 🌐 النشر على استضافة مشتركة {#shared-hosting}

### المتطلبات
- PHP 7.4+
- MySQL 5.7+
- وصول FTP أو File Manager

### الخطوات

#### 1. رفع الملفات
عبر FTP أو File Manager، ارفع جميع الملفات إلى:
```
public_html/
```

#### 2. إنشاء قاعدة البيانات
من cPanel:
1. اذهب إلى "MySQL Databases"
2. أنشئ قاعدة بيانات
3. أنشئ مستخدم
4. امنح الصلاحيات

#### 3. استيراد البيانات
من phpMyAdmin:
1. اختر القاعدة
2. Import → اختر `database.sql`
3. Go

#### 4. تحديث الإعدادات
عدّل `includes/db.php` ببيانات الاتصال

#### 5. ضبط الصلاحيات
```bash
chmod 755 uploads/
chmod 755 assets/
```

---

## 🖥️ النشر على VPS {#vps}

### المتطلبات
- Ubuntu 20.04+ أو CentOS 7+
- وصول SSH

### الخطوة 1: تثبيت المتطلبات

#### على Ubuntu:
```bash
sudo apt update
sudo apt install -y apache2 php php-mysql mysql-server
sudo apt install -y php-mbstring php-xml php-curl php-zip
```

#### على CentOS:
```bash
sudo yum install -y httpd php php-mysqlnd mariadb-server
sudo yum install -y php-mbstring php-xml php-curl php-zip
```

### الخطوة 2: تكوين MySQL
```bash
sudo mysql_secure_installation
sudo mysql -u root -p
```

في MySQL:
```sql
CREATE DATABASE alabasi_unified CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'alabasi'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON alabasi_unified.* TO 'alabasi'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### الخطوة 3: استنساخ المشروع
```bash
cd /var/www/html
sudo git clone https://github.com/alabasi2025/alabasi-accounting-system.git
sudo chown -R www-data:www-data alabasi-accounting-system
sudo chmod -R 755 alabasi-accounting-system
```

### الخطوة 4: استيراد قاعدة البيانات
```bash
mysql -u alabasi -p alabasi_unified < /var/www/html/alabasi-accounting-system/database.sql
```

### الخطوة 5: تكوين Apache
```bash
sudo nano /etc/apache2/sites-available/alabasi.conf
```

أضف:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/alabasi-accounting-system
    
    <Directory /var/www/html/alabasi-accounting-system>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/alabasi_error.log
    CustomLog ${APACHE_LOG_DIR}/alabasi_access.log combined
</VirtualHost>
```

تفعيل:
```bash
sudo a2ensite alabasi.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### الخطوة 6: تكوين SSL (اختياري)
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d your-domain.com
```

---

## 🔧 نصائح عامة

### الأمان
- ✅ غيّر كلمة المرور الافتراضية
- ✅ استخدم HTTPS
- ✅ عطّل عرض الأخطاء في الإنتاج
- ✅ احذف ملفات التثبيت بعد الانتهاء

### الأداء
- ✅ فعّل التخزين المؤقت (Caching)
- ✅ استخدم CDN للملفات الثابتة
- ✅ ضغط الملفات (Gzip)
- ✅ تحسين الصور

### النسخ الاحتياطي
- ✅ نسخ احتياطي يومي لقاعدة البيانات
- ✅ نسخ احتياطي أسبوعي للملفات
- ✅ اختبر الاستعادة دورياً

---

## 🆘 حل المشاكل

### خطأ في الاتصال بقاعدة البيانات
- تحقق من بيانات الاتصال في `includes/db.php`
- تأكد من تشغيل MySQL
- تحقق من الصلاحيات

### صفحة بيضاء
- فعّل عرض الأخطاء مؤقتاً
- تحقق من سجل الأخطاء
- تأكد من صلاحيات الملفات

### مشاكل الترميز (العربية)
- تأكد من UTF-8 في قاعدة البيانات
- تحقق من `charset` في PHP
- راجع إعدادات Apache/Nginx

---

## 📞 الدعم

للحصول على المساعدة:
- افتح Issue على GitHub
- راجع التوثيق الكامل
- تواصل مع المطور

---

**آخر تحديث:** نوفمبر 2025

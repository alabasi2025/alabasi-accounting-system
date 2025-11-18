# 🌟 نظام الأباسي المحاسبي

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-Proprietary-red?style=for-the-badge)

**نظام محاسبي متكامل مبني على Laravel مع جميع الميزات الرسمية الـ21**

[التوثيق](COMPLETE_DOCUMENTATION.md) • [التقدم](FEATURES_PROGRESS.md) • [الموقع](https://alabasi.es)

</div>

---

## 📋 نظرة عامة

نظام **الأباسي المحاسبي** هو نظام محاسبي ذكي ومتكامل ومستدام مبني على إطار عمل **Laravel**، يتضمن جميع الميزات الرسمية الـ21 عبر كل الإصدارات، مع **الوحدة المركزية** الذكية لإدارة النظام بالكامل.

### ✨ المميزات الرئيسية

- ✅ **21 ميزة Laravel** مكتملة ومفعّلة بالكامل
- ✅ **الوحدة المركزية** لإدارة شاملة للنظام
- ✅ **واجهة عربية** كاملة مع دعم RTL
- ✅ **API متكامل** مع Sanctum Authentication
- ✅ **نشر تلقائي** بدون توقف الخدمة (Zero Downtime)
- ✅ **اختبارات شاملة** مع PHPUnit
- ✅ **توثيق كامل** باللغة العربية

---

## 🎯 الميزات الـ21 المكتملة

<table>
<tr>
<td width="50%">

### 🔧 الأساسيات
- ✅ **Laravel Telescope** - مراقبة وتحليل
- ✅ **Laravel Pint** - جودة الكود
- ✅ **Laravel Sanctum** - مصادقة API
- ✅ **Eloquent ORM** - قواعد البيانات
- ✅ **Blade Templates** - القوالب
- ✅ **Middleware System** - التحكم في الطلبات

### 📊 البيانات والتخزين
- ✅ **Database Management** - إدارة قواعد البيانات
- ✅ **Migrations** - هجرات قواعد البيانات
- ✅ **File Storage** - التخزين المحلي والسحابي
- ✅ **Caching & Session** - الذاكرة المؤقتة

### ⚡ الأداء والمهام
- ✅ **Queues & Jobs** - قوائم الانتظار
- ✅ **Task Scheduling** - جدولة المهام
- ✅ **Events & Listeners** - الأحداث

</td>
<td width="50%">

### 🌐 API والواجهات
- ✅ **API Resources** - واجهات برمجية منظمة
- ✅ **Laravel Livewire** - مكونات تفاعلية
- ✅ **UI Components** - مكونات الواجهة

### 🔔 الإشعارات والاتصال
- ✅ **Notifications System** - نظام إشعارات شامل
- ✅ **Localization** - دعم اللغات المتعددة

### 🚀 النشر والاختبار
- ✅ **Zero Downtime Deployment** - نشر بدون توقف
- ✅ **Testing Environment** - بيئة اختبار كاملة
- ✅ **Laravel Pennant** - Feature Flags

</td>
</tr>
</table>

---

## 🚀 التثبيت السريع

### المتطلبات

- PHP 8.1 أو أحدث
- Composer
- MySQL 5.7 أو أحدث
- Node.js 16 أو أحدث

### خطوات التثبيت

```bash
# 1. استنساخ المستودع
git clone https://github.com/alabasi2025/alabasi-php.git
cd alabasi-php

# 2. تثبيت الحزم
composer install
npm install && npm run build

# 3. إعداد البيئة
cp .env.example .env
php artisan key:generate

# 4. إعداد قاعدة البيانات
php artisan migrate --seed

# 5. إنشاء Symbolic Link
php artisan storage:link

# 6. تشغيل الخادم
php artisan serve
```

الآن يمكنك زيارة: `http://localhost:8000`

---

## 🎨 الوحدة المركزية

الوحدة المركزية هي لوحة تحكم شاملة لإدارة جميع ميزات النظام.

### الوصول

```
URL: /admin/dashboard
```

### التبويبات المتاحة (21 تبويب)

| التبويب | المسار | الوصف |
|---------|--------|-------|
| Dashboard | `/admin/dashboard` | لوحة التحكم الرئيسية |
| Telescope | `/telescope` | مراقبة النظام |
| Pint | `/admin/pint` | جودة الكود |
| Pennant | `/admin/pennant` | Feature Flags |
| API | `/admin/api` | إدارة API |
| Cache | `/admin/cache` | الذاكرة المؤقتة |
| Queues | `/admin/queues` | قوائم الانتظار |
| Scheduler | `/admin/scheduler` | جدولة المهام |
| Notifications | `/admin/notifications` | الإشعارات |
| Storage | `/admin/storage` | التخزين |
| Database | `/admin/database` | قواعد البيانات |
| Events | `/admin/events` | الأحداث |
| Middleware | `/admin/middleware` | Middleware |
| Testing | `/admin/testing` | الاختبارات |
| Localization | `/admin/localization` | اللغات |
| Components | `/admin/components` | Livewire |
| Deployment | `/admin/deployment` | النشر |
| Migrations | `/admin/migrations` | الهجرات |
| UI | `/admin/ui` | مكونات الواجهة |
| Templates | `/admin/templates` | القوالب |
| Auth | `/admin/auth` | المصادقة |

---

## 🌟 الميزات المحاسبية

- 📊 **هيكلية هرمية** (الوحدة ← المؤسسة ← الفرع)
- 💼 **دليل حسابات شجري** متعدد المستويات
- 💰 **الصناديق والحسابات البنكية**
- 👥 **إدارة العملاء والموردين والموظفين**
- 📄 **نظام سندات القبض والصرف**
- 📊 **حسابات تحليلية مرنة**

---

## 📚 التوثيق

- [📖 التوثيق الكامل](COMPLETE_DOCUMENTATION.md) - دليل شامل لجميع الميزات
- [📊 تقرير التقدم](FEATURES_PROGRESS.md) - حالة الميزات الـ21
- [📝 قائمة الميزات](FEATURES_CHECKLIST.md) - قائمة مرجعية
- [📘 دليل النظام](SYSTEM_GUIDE.md) - دليل النظام المحاسبي
- [📋 سجل التحديثات](CHANGELOG.md) - سجل التغييرات

---

## 🔧 الأوامر المفيدة

### التطوير

```bash
# تشغيل الخادم
php artisan serve

# تشغيل Queue Worker
php artisan queue:work

# تشغيل Scheduler
php artisan schedule:work

# تشغيل Pint
./vendor/bin/pint
```

### الاختبار

```bash
# تشغيل جميع الاختبارات
php artisan test

# مع Code Coverage
php artisan test --coverage
```

### الصيانة

```bash
# مسح جميع الذاكرة المؤقتة
php artisan optimize:clear

# تحسين التطبيق
php artisan optimize

# تنظيف Telescope
php artisan telescope:prune
```

---

## 🚀 النشر

### النشر التلقائي (GitHub Actions)

يتم النشر تلقائياً عند Push إلى `master`:

```bash
git add .
git commit -m "feat: إضافة ميزة جديدة"
git push origin master
```

### النشر اليدوي

```bash
ssh user@server
cd domains/alabasi.es/public_html
git pull origin master
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```

---

## 📊 الإحصائيات

| المؤشر | القيمة |
|--------|--------|
| **الميزات المكتملة** | 21/21 (100%) |
| **الملفات المنشأة** | 60+ |
| **الأسطر البرمجية** | 10,000+ |
| **صفحات الإدارة** | 21 |
| **Controllers** | 5+ |
| **API Endpoints** | 15+ |

---

## 🤝 المساهمة

هذا المشروع ملك لـ **الأباسي** ومحمي بحقوق الملكية الفكرية.

---

## 📞 الدعم

- **الموقع:** [https://alabasi.es](https://alabasi.es)
- **GitHub:** [https://github.com/alabasi2025/alabasi-php](https://github.com/alabasi2025/alabasi-php)
- **البريد:** info@alabasi.es

---

## 📝 الترخيص

جميع الحقوق محفوظة © 2025 الأباسي

---

<div align="center">

**صُنع بـ ❤️ في السعودية**

**Laravel 11.x • PHP 8.1+ • MySQL 5.7+**

</div>

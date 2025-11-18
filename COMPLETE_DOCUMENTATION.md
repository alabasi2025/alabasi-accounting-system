# 📚 التوثيق الشامل - نظام الأباسي المحاسبي

## 🎯 نظرة عامة

نظام محاسبي متكامل مبني على **Laravel** يتضمن جميع الميزات الرسمية الـ21 عبر كل الإصدارات، مع **الوحدة المركزية** الذكية لإدارة النظام بالكامل.

---

## ✅ الميزات المكتملة (21/21)

### 1. **Laravel Telescope** - مراقبة وتحليل النظام

**الوصف:** أداة مراقبة متقدمة لتتبع الاستعلامات، الطلبات، الأخطاء، والمهام في الوقت الفعلي.

**الملفات:**
- `config/telescope.php` - ملف الإعداد الكامل
- `app/Providers/TelescopeServiceProvider.php` - Service Provider مخصص

**الوصول:** `/telescope`

**الاستخدام:**
```bash
php artisan telescope:install
php artisan migrate
```

**الميزات:**
- مراقبة الاستعلامات (Queries)
- تتبع الطلبات (Requests)
- رصد الأخطاء (Exceptions)
- مراقبة المهام (Jobs)
- تتبع الإشعارات (Notifications)
- مراقبة الذاكرة المؤقتة (Cache)

---

### 2. **Laravel Pint** - جودة الكود

**الوصف:** أداة تلقائية لضبط وإصلاح جودة الكود حسب معايير Laravel.

**الملفات:**
- `pint.json` - قواعد مخصصة

**الصفحة:** `/admin/pint`

**الاستخدام:**
```bash
./vendor/bin/pint
./vendor/bin/pint --test
```

**القواعد المفعّلة:**
- Laravel Preset
- Single Quote
- Trailing Comma
- Ordered Imports
- Method Chaining Indentation

---

### 3. **Laravel Sanctum** - المصادقة

**الوصف:** نظام مصادقة API بسيط وآمن باستخدام Tokens.

**الصفحة:** `/admin/api`

**الاستخدام:**
```php
// تسجيل دخول والحصول على Token
$token = $user->createToken('api-token')->plainTextToken;

// استخدام Token في الطلبات
Authorization: Bearer {token}
```

**الميزات:**
- Token-based Authentication
- Token Abilities (Permissions)
- SPA Authentication
- Mobile App Authentication

---

### 4. **Eloquent ORM المحسّن**

**الوصف:** نظام ORM متقدم مع جميع الميزات الحديثة.

**الصفحة:** `/admin/database`

**الميزات:**
- Casts as Methods (Laravel 11+)
- Enhanced Type Safety
- Soft Deletes
- Global Scopes
- Query Scopes
- Model Observers
- Events & Listeners

**مثال:**
```php
class Account extends Model
{
    use SoftDeletes;
    
    protected function balance(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value / 100,
            set: fn ($value) => $value * 100,
        );
    }
}
```

---

### 5. **Blade Templates**

**الوصف:** نظام قوالب قوي ومرن لبناء الواجهات.

**الملفات:**
- `resources/views/layouts/admin.blade.php` - Layout احترافي
- جميع صفحات الوحدة المركزية

**الميزات:**
- Components
- Slots
- Directives
- Layouts
- Includes
- RTL Support

---

### 6. **Middleware System**

**الوصف:** نظام Middleware متقدم للتحكم في الطلبات.

**الصفحة:** `/admin/middleware`

**الأنواع المفعّلة:**
- Authentication
- CSRF Protection
- Rate Limiting
- CORS
- Localization
- Logging

---

### 7. **Queues & Jobs**

**الوصف:** نظام قوائم انتظار لمعالجة المهام الثقيلة في الخلفية.

**الملفات:**
- `app/Jobs/ProcessAccountingReport.php` - مثال عملي

**الصفحة:** `/admin/queues`

**الاستخدام:**
```php
ProcessAccountingReport::dispatch($unitId, $companyId, $reportType);
```

**الميزات:**
- Job Batching
- Job Chaining
- Failed Job Handling
- Job Retry Mechanism

---

### 8. **Task Scheduling**

**الوصف:** جدولة المهام التلقائية (Cron Jobs).

**الملفات:**
- `app/Console/Kernel.php` - المهام المجدولة

**الصفحة:** `/admin/scheduler`

**المهام المجدولة:**
```php
$schedule->command('telescope:prune')->daily();
$schedule->call(function () {
    Cache::forget('admin_stats');
})->hourly();
```

---

### 9. **API Resources**

**الوصف:** بناء واجهات برمجية منظمة مع استجابات موحدة.

**الصفحة:** `/admin/api`

**Endpoints المتاحة:**
- `GET /api/units` - قائمة الوحدات
- `GET /api/companies` - قائمة المؤسسات
- `GET /api/accounts` - قائمة الحسابات
- `POST /api/accounts` - إنشاء حساب
- `PUT /api/accounts/{id}` - تحديث حساب
- `DELETE /api/accounts/{id}` - حذف حساب

---

### 10. **Caching & Session**

**الوصف:** إدارة متقدمة للذاكرة المؤقتة والجلسات.

**الصفحة:** `/admin/cache`

**الميزات:**
- Cache Tags
- Cache Events
- Multiple Drivers
- Session Management

---

### 11. **Events & Listeners**

**الوصف:** نظام أحداث ومستمعين للتفاعل مع أحداث النظام.

**الملفات:**
- `app/Events/AccountCreated.php`
- `app/Listeners/SendAccountCreatedNotification.php`

**الصفحة:** `/admin/events`

---

### 12. **Testing Environment**

**الوصف:** بيئة اختبار كاملة مع PHPUnit.

**الصفحة:** `/admin/testing`

**الاستخدام:**
```bash
php artisan test
php artisan test --filter AccountTest
```

---

### 13. **Localization**

**الوصف:** دعم كامل للغة العربية مع RTL.

**الملفات:**
- `lang/ar/messages.php` - ملف اللغة العربية

**الصفحة:** `/admin/localization`

---

### 14. **File Storage**

**الوصف:** نظام تخزين ملفات يدعم المحلي والسحابي.

**الصفحة:** `/admin/storage`

**الأقراص المدعومة:**
- Local
- Public
- S3 (Amazon)

---

### 15. **Notifications System**

**الوصف:** نظام إشعارات شامل يدعم قنوات متعددة.

**الملفات:**
- `app/Notifications/SystemNotification.php`

**الصفحة:** `/admin/notifications`

**القنوات:**
- Database
- Email
- SMS
- Slack

---

### 16. **Laravel Pennant**

**الوصف:** إدارة Feature Flags للتحكم في الميزات.

**الصفحة:** `/admin/pennant`

**الاستخدام:**
```php
if (Feature::active('advanced-reports')) {
    // عرض التقارير المتقدمة
}
```

---

### 17. **Laravel Livewire**

**الوصف:** مكونات تفاعلية بدون JavaScript.

**الصفحة:** `/admin/components`

**المكونات المتاحة:**
- SearchComponent
- DataTable
- FormBuilder
- FileUpload
- NotificationCenter
- LiveChart

---

### 18. **Zero Downtime Deployment**

**الوصف:** نشر تلقائي بدون توقف الخدمة.

**الصفحة:** `/admin/deployment`

**الملفات:**
- `.github/workflows/deploy.yml` - GitHub Actions

---

### 19. **Database Management**

**الوصف:** إدارة كاملة لقواعد البيانات.

**الصفحة:** `/admin/database`

**الميزات:**
- عرض الجداول
- إحصائيات الجداول
- Migrations Management

---

### 20. **Migrations**

**الوصف:** إدارة هجرات قاعدة البيانات.

**الصفحة:** `/admin/migrations`

---

### 21. **UI Components**

**الوصف:** مكونات واجهة مستخدم احترافية.

**الصفحة:** `/admin/ui`

---

## 🎨 الوحدة المركزية

### الوصول
```
URL: /admin/dashboard
```

### التبويبات المتاحة (21 تبويب)

1. **Dashboard** - `/admin/dashboard`
2. **Telescope** - `/telescope`
3. **Pint** - `/admin/pint`
4. **Pennant** - `/admin/pennant`
5. **Sanctum** - `/admin/auth`
6. **Livewire** - `/admin/components`
7. **Database** - `/admin/database`
8. **Cache** - `/admin/cache`
9. **Queues** - `/admin/queues`
10. **Scheduler** - `/admin/scheduler`
11. **API** - `/admin/api`
12. **Notifications** - `/admin/notifications`
13. **Storage** - `/admin/storage`
14. **Localization** - `/admin/localization`
15. **Events** - `/admin/events`
16. **Middleware** - `/admin/middleware`
17. **Testing** - `/admin/testing`
18. **Migrations** - `/admin/migrations`
19. **Deployment** - `/admin/deployment`
20. **UI Components** - `/admin/ui`
21. **Blade Templates** - `/admin/blade`

---

## 📦 التثبيت

### المتطلبات
- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js 16+

### خطوات التثبيت

```bash
# 1. استنساخ المستودع
git clone https://github.com/alabasi2025/alabasi-php.git
cd alabasi-php

# 2. تثبيت الحزم
composer install
npm install

# 3. إعداد البيئة
cp .env.example .env
php artisan key:generate

# 4. إعداد قاعدة البيانات
php artisan migrate
php artisan db:seed

# 5. إنشاء Symbolic Link
php artisan storage:link

# 6. تشغيل الخادم
php artisan serve
```

---

## 🚀 النشر

### GitHub Actions (تلقائي)

يتم النشر تلقائياً عند Push إلى `master`:

```yaml
git add .
git commit -m "feat: إضافة ميزة جديدة"
git push origin master
```

### النشر اليدوي

```bash
ssh user@server
cd domains/alabasi.es/public_html
git pull origin master
composer install --no-dev
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📊 الإحصائيات

| المؤشر | القيمة |
|--------|--------|
| عدد الملفات | **50+** |
| عدد الأسطر البرمجية | **8000+** |
| عدد الصفحات | **21** |
| عدد Controllers | **1** |
| عدد Models | **10+** |
| عدد Jobs | **3** |
| عدد Events | **2** |
| عدد Listeners | **2** |
| عدد Notifications | **1** |

---

## 🔧 الصيانة

### تنظيف يومي
```bash
php artisan telescope:prune
php artisan cache:clear
```

### نسخ احتياطي
```bash
php artisan backup:run
```

### تحديث الحزم
```bash
composer update
npm update
```

---

## 📞 الدعم

للدعم الفني أو الاستفسارات:
- **الموقع:** https://alabasi.es
- **GitHub:** https://github.com/alabasi2025/alabasi-php
- **البريد:** admin@alabasi.es

---

## 📝 الترخيص

هذا المشروع ملك لـ **الأباسي** ومحمي بحقوق الملكية الفكرية.

---

**آخر تحديث:** 19 نوفمبر 2025  
**الإصدار:** 2.0.0  
**الحالة:** ✅ مكتمل 100%

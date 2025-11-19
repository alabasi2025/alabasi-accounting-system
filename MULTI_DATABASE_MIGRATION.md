# تحويل النظام إلى Multi-Database نقي

## الهدف
إزالة `company_id` من جميع الجداول والـ Models والـ Controllers لأن كل وحدة لها قاعدة بيانات منفصلة.

## الملفات المتأثرة

### Models (7 ملفات)
1. app/Models/Account.php
2. app/Models/AccountImproved.php
3. app/Models/AccountType.php
4. app/Models/AnalyticalAccount.php
5. app/Models/AnalyticalAccountType.php
6. app/Models/Branch.php
7. app/Models/Voucher.php

### Controllers (9 ملفات)
1. app/Http/Controllers/AccountController.php
2. app/Http/Controllers/AccountTypeController.php
3. app/Http/Controllers/AnalyticalAccountController.php
4. app/Http/Controllers/AnalyticalAccountTypeController.php
5. app/Http/Controllers/BranchController.php
6. app/Http/Controllers/ClearingTransactionController.php
7. app/Http/Controllers/ContextSelectorController.php
8. app/Http/Controllers/DashboardController.php
9. app/Http/Controllers/SetupController.php

## الخطوات

### 1. إزالة company_id من Models
- حذف `company_id` من `$fillable`
- حذف علاقة `company()` إذا وجدت
- حذف scope `forCompany()` إذا وجد

### 2. تعديل Controllers
- إزالة `where('company_id', ...)` من جميع الاستعلامات
- إزالة `$companyId` من المتغيرات
- الاعتماد على قاعدة البيانات النشطة فقط (من Session)

### 3. إنشاء Migration
- إنشاء migration لحذف عمود `company_id` من الجداول:
  - accounts
  - account_types
  - analytical_accounts
  - analytical_account_types
  - branches
  - vouchers

### 4. تحديث Views
- إزالة dropdown اختيار المؤسسة
- إزالة company_id من الفورمات

## ملاحظات
- الإبقاء على `unit_id` في الجداول للربط مع الوحدة المركزية
- الإبقاء على جدول `companies` في القاعدة المركزية فقط
- كل وحدة عمل لها قاعدة بيانات منفصلة بدون company_id

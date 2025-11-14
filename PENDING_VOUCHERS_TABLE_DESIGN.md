# 📋 تصميم جدول السندات المعلقة (Pending Vouchers)

## 🎯 الهدف
تخزين جميع السندات (صرف/قبض) والقيود التي تحتوي على حسابات وسيطة والتي تحتاج إلى ترحيل بين الوحدات والمؤسسات.

---

## 📊 بنية الجدول: `pending_vouchers`

### الحقول الأساسية

| الحقل | النوع | الوصف | القيود |
|-------|------|-------|--------|
| `id` | INT | المعرف الفريد | PRIMARY KEY, AUTO_INCREMENT |
| `voucherType` | ENUM | نوع السند | 'payment', 'receipt', 'journal_entry' |
| `voucherNumber` | VARCHAR(50) | رقم السند الأصلي | NOT NULL |
| `voucherDate` | DATE | تاريخ السند | NOT NULL |
| `fiscalYearId` | INT | السنة المالية | NOT NULL, FK |
| `amount` | DECIMAL(15,2) | المبلغ الإجمالي | NOT NULL |
| `description` | TEXT | البيان/الوصف | NOT NULL |

### معلومات الكيان المصدر

| الحقل | النوع | الوصف | القيود |
|-------|------|-------|--------|
| `sourceType` | ENUM | نوع الكيان المصدر | 'unit', 'company' |
| `sourceUnitId` | INT | معرف الوحدة المصدر | NULL, FK |
| `sourceCompanyId` | INT | معرف المؤسسة المصدر | NULL, FK |
| `sourceBranchId` | INT | معرف الفرع المصدر | NULL, FK |
| `sourceVoucherId` | INT | معرف السند الأصلي | NOT NULL |
| `sourceTableName` | VARCHAR(50) | اسم الجدول المصدر | NOT NULL |

### معلومات الكيان الهدف

| الحقل | النوع | الوصف | القيود |
|-------|------|-------|--------|
| `targetType` | ENUM | نوع الكيان الهدف | 'unit', 'company' |
| `targetUnitId` | INT | معرف الوحدة الهدف | NULL, FK |
| `targetCompanyId` | INT | معرف المؤسسة الهدف | NULL, FK |
| `targetBranchId` | INT | معرف الفرع الهدف | NULL, FK |

### معلومات الحساب الوسيط

| الحقل | النوع | الوصف | القيود |
|-------|------|-------|--------|
| `intermediateAccountId` | INT | معرف الحساب الوسيط | NOT NULL, FK |
| `intermediateAccountCode` | VARCHAR(20) | كود الحساب الوسيط | NOT NULL |
| `intermediateAccountName` | VARCHAR(255) | اسم الحساب الوسيط | NOT NULL |
| `mappingId` | INT | معرف الربط من جدول intermediate_accounts_mapping | NOT NULL, FK |

### معلومات الحساب النهائي في الهدف

| الحقل | النوع | الوصف | القيود |
|-------|------|-------|--------|
| `targetAccountId` | INT | معرف الحساب النهائي في الهدف | NULL, FK |
| `targetAccountCode` | VARCHAR(20) | كود الحساب النهائي | NULL |
| `targetAccountName` | VARCHAR(255) | اسم الحساب النهائي | NULL |
| `targetAccountType` | ENUM | نوع الحساب النهائي | 'debit', 'credit' |

### معلومات الترحيل

| الحقل | النوع | الوصف | القيود |
|-------|------|-------|--------|
| `status` | ENUM | حالة السند | 'pending', 'transferred', 'rejected', 'cancelled' |
| `isTransferred` | BOOLEAN | هل تم الترحيل؟ | DEFAULT FALSE |
| `transferredAt` | DATETIME | تاريخ ووقت الترحيل | NULL |
| `transferredBy` | INT | المستخدم الذي قام بالترحيل | NULL, FK |
| `linkedVoucherId` | INT | معرف السند المُنشأ في الهدف | NULL |
| `linkedVoucherNumber` | VARCHAR(50) | رقم السند المُنشأ في الهدف | NULL |
| `linkedTableName` | VARCHAR(50) | اسم جدول السند المُنشأ | NULL |

### معلومات القيد المحاسبي

| الحقل | النوع | الوصف | القيود |
|-------|------|-------|--------|
| `debitAccountId` | INT | الحساب المدين | NOT NULL, FK |
| `debitAccountCode` | VARCHAR(20) | كود الحساب المدين | NOT NULL |
| `debitAccountName` | VARCHAR(255) | اسم الحساب المدين | NOT NULL |
| `creditAccountId` | INT | الحساب الدائن | NOT NULL, FK |
| `creditAccountCode` | VARCHAR(20) | كود الحساب الدائن | NOT NULL |
| `creditAccountName` | VARCHAR(255) | اسم الحساب الدائن | NOT NULL |

### معلومات إضافية

| الحقل | النوع | الوصف | القيود |
|-------|------|-------|--------|
| `notes` | TEXT | ملاحظات إضافية | NULL |
| `attachments` | TEXT | مرفقات (JSON) | NULL |
| `priority` | ENUM | الأولوية | 'low', 'medium', 'high', 'urgent' |
| `dueDate` | DATE | تاريخ الاستحقاق | NULL |
| `rejectionReason` | TEXT | سبب الرفض (إن وجد) | NULL |
| `rejectedAt` | DATETIME | تاريخ الرفض | NULL |
| `rejectedBy` | INT | المستخدم الذي رفض | NULL, FK |

### معلومات التدقيق (Audit Trail)

| الحقل | النوع | الوصف | القيود |
|-------|------|-------|--------|
| `createdBy` | INT | المستخدم الذي أنشأ السند | NOT NULL, FK |
| `createdAt` | DATETIME | تاريخ ووقت الإنشاء | DEFAULT CURRENT_TIMESTAMP |
| `updatedBy` | INT | آخر مستخدم قام بالتعديل | NULL, FK |
| `updatedAt` | DATETIME | تاريخ آخر تعديل | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP |

---

## 🔗 العلاقات (Foreign Keys)

| الحقل | يشير إلى | الجدول المرتبط |
|-------|----------|----------------|
| `fiscalYearId` | `id` | `fiscal_years` |
| `sourceUnitId` | `id` | `units` |
| `sourceCompanyId` | `id` | `companies` |
| `sourceBranchId` | `id` | `branches` |
| `targetUnitId` | `id` | `units` |
| `targetCompanyId` | `id` | `companies` |
| `targetBranchId` | `id` | `branches` |
| `intermediateAccountId` | `id` | `accounts` |
| `mappingId` | `id` | `intermediate_accounts_mapping` |
| `targetAccountId` | `id` | `accounts` |
| `debitAccountId` | `id` | `accounts` |
| `creditAccountId` | `id` | `accounts` |
| `transferredBy` | `id` | `users` |
| `createdBy` | `id` | `users` |
| `updatedBy` | `id` | `users` |
| `rejectedBy` | `id` | `users` |

---

## 📑 الفهارس (Indexes)

### فهارس أساسية
- `idx_voucher_number` على `voucherNumber`
- `idx_voucher_date` على `voucherDate`
- `idx_status` على `status`
- `idx_source_type` على `sourceType`
- `idx_target_type` على `targetType`

### فهارس مركبة
- `idx_source_entity` على `(sourceType, sourceUnitId, sourceCompanyId)`
- `idx_target_entity` على `(targetType, targetUnitId, targetCompanyId)`
- `idx_transfer_status` على `(status, isTransferred, transferredAt)`
- `idx_created_date` على `(createdAt, status)`
- `idx_priority_due` على `(priority, dueDate, status)`

---

## 🎯 الحالات المختلفة (Status Flow)

```
pending → transferred ✅
pending → rejected ❌
pending → cancelled 🚫
rejected → pending (إعادة المحاولة)
```

### شرح الحالات:

| الحالة | الوصف | الإجراء التالي |
|--------|-------|----------------|
| `pending` | ⏳ معلق - في انتظار الترحيل | يمكن الترحيل |
| `transferred` | ✅ تم الترحيل بنجاح | لا إجراء |
| `rejected` | ❌ مرفوض | يمكن إعادة المحاولة |
| `cancelled` | 🚫 ملغي | لا إجراء |

---

## 🔄 دورة حياة السند المعلق

```
1. إنشاء السند الأصلي (payment/receipt voucher)
   ↓
2. اكتشاف حساب وسيط
   ↓
3. إنشاء سجل في pending_vouchers (status = pending)
   ↓
4. عرض في شاشة "السندات المعلقة"
   ↓
5. المستخدم يضغط "ترحيل"
   ↓
6. اختيار الحساب النهائي في الهدف
   ↓
7. إنشاء القيد في الوحدة/المؤسسة الهدف
   ↓
8. تحديث السجل (status = transferred, linkedVoucherId, transferredAt, transferredBy)
   ↓
9. ربط السندين معاً
```

---

**تاريخ الإنشاء:** 2025-01-14  
**الإصدار:** 1.0  
**الحالة:** ✅ جاهز للتنفيذ

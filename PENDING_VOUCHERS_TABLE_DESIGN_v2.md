# 📋 تصميم جدول السندات المعلقة (Pending Vouchers) - الإصدار 2.0

## 🎯 الهدف
تخزين جميع السندات (صرف/قبض) والقيود التي تحتوي على حسابات وسيطة والتي تحتاج إلى ترحيل بين الوحدات والمؤسسات.

## ✨ الجديد في الإصدار 2.0
- إضافة حقل `transferType` لتحديد نوع الترحيل بوضوح
- 4 أنواع ترحيل: بين الوحدات، بين المؤسسات، من وحدة لمؤسسة، من مؤسسة لوحدة
- تسهيل العمل مع الذكاء الاصطناعي عبر أوامر بسيطة

---

## 📊 بنية الجدول: `pending_vouchers`

### الحقول الأساسية

| الحقل | النوع | الوصف | القيود |
|-------|------|-------|--------|
| `id` | INT | المعرف الفريد | PRIMARY KEY, AUTO_INCREMENT |
| `voucherType` | ENUM | نوع السند | 'payment', 'receipt', 'journal_entry' |
| `transferType` | ENUM | **نوع الترحيل** | 'unit_to_unit', 'company_to_company', 'unit_to_company', 'company_to_unit' |
| `voucherNumber` | VARCHAR(50) | رقم السند الأصلي | NOT NULL |
| `voucherDate` | DATE | تاريخ السند | NOT NULL |
| `fiscalYearId` | INT | السنة المالية | NOT NULL, FK |
| `amount` | DECIMAL(15,2) | المبلغ الإجمالي | NOT NULL |
| `description` | TEXT | البيان/الوصف | NOT NULL |

---

## 🔄 أنواع الترحيل (Transfer Types)

### 1. `unit_to_unit` - بين الوحدات
**الاستخدام:** ترحيل من وحدة إلى وحدة أخرى (أنظمة منفصلة تماماً)

**مثال:**
```
الوحدة 1 → الوحدة 2
"رحّل السند من الوحدة 1 إلى الوحدة 2"
```

### 2. `company_to_company` - بين المؤسسات
**الاستخدام:** ترحيل بين مؤسستين في نفس الوحدة

**مثال:**
```
المؤسسة 1 → المؤسسة 2 (نفس الوحدة)
"رحّل السند من المؤسسة 1 إلى المؤسسة 2"
```

### 3. `unit_to_company` - من وحدة إلى مؤسسة
**الاستخدام:** ترحيل من وحدة إلى مؤسسة في وحدة أخرى

**مثال:**
```
الوحدة 1 → المؤسسة 3 (في الوحدة 2)
"رحّل السند من الوحدة 1 إلى المؤسسة 3"
```

### 4. `company_to_unit` - من مؤسسة إلى وحدة
**الاستخدام:** ترحيل من مؤسسة إلى وحدة

**مثال:**
```
المؤسسة 2 → الوحدة 1
"رحّل السند من المؤسسة 2 إلى الوحدة 1"
```

---

## 📑 الفهارس (Indexes)

### فهارس أساسية
- `idx_voucher_number` على `voucherNumber`
- `idx_voucher_date` على `voucherDate`
- `idx_status` على `status`
- `idx_transfer_type` على `transferType` ⭐ **جديد**
- `idx_source_type` على `sourceType`
- `idx_target_type` على `targetType`

### فهارس مركبة
- `idx_source_entity` على `(sourceType, sourceUnitId, sourceCompanyId)`
- `idx_target_entity` على `(targetType, targetUnitId, targetCompanyId)`
- `idx_transfer_status` على `(status, isTransferred, transferredAt)`
- `idx_created_date` على `(createdAt, status)`
- `idx_priority_due` على `(priority, dueDate, status)`

---

## 📊 أمثلة على الاستخدام

### مثال 1: سند صرف بين الوحدات

```sql
INSERT INTO pending_vouchers (
    voucherType, transferType, voucherNumber, voucherDate, 
    fiscalYearId, amount, description,
    sourceType, sourceUnitId, sourceVoucherId, sourceTableName,
    targetType, targetUnitId,
    intermediateAccountId, intermediateAccountCode, intermediateAccountName, mappingId,
    debitAccountId, debitAccountCode, debitAccountName,
    creditAccountId, creditAccountCode, creditAccountName,
    status, priority, createdBy
) VALUES (
    'payment', 'unit_to_unit', 'SV-2025-001', '2025-01-15', 
    1, 50000.00, 'دفع للمورد أحمد - الوحدة 2',
    'unit', 1, 123, 'payment_vouchers',
    'unit', 2,
    1050, '1050', 'حساب وسيط - الوحدة 2', 5,
    1050, '1050', 'حساب وسيط - الوحدة 2',
    1010, '1010', 'الصندوق',
    'pending', 'high', 1
);
```

### مثال 2: سند قبض بين المؤسسات

```sql
INSERT INTO pending_vouchers (
    voucherType, transferType, voucherNumber, voucherDate, 
    fiscalYearId, amount, description,
    sourceType, sourceCompanyId, sourceBranchId, sourceVoucherId, sourceTableName,
    targetType, targetCompanyId,
    intermediateAccountId, intermediateAccountCode, intermediateAccountName, mappingId,
    debitAccountId, debitAccountCode, debitAccountName,
    creditAccountId, creditAccountCode, creditAccountName,
    status, priority, createdBy
) VALUES (
    'receipt', 'company_to_company', 'RV-2025-010', '2025-01-16', 
    1, 75000.00, 'تحصيل من العميل محمد - المؤسسة 2',
    'company', 1, 1, 456, 'receipt_vouchers',
    'company', 2,
    2050, '2050', 'حساب وسيط - المؤسسة 2', 8,
    1010, '1010', 'الصندوق',
    2050, '2050', 'حساب وسيط - المؤسسة 1',
    'pending', 'medium', 2
);
```

---

## 🔍 استعلامات مفيدة

### 1. عرض السندات المعلقة حسب نوع الترحيل

```sql
SELECT 
    transferType,
    CASE transferType
        WHEN 'unit_to_unit' THEN 'بين الوحدات'
        WHEN 'company_to_company' THEN 'بين المؤسسات'
        WHEN 'unit_to_company' THEN 'من وحدة لمؤسسة'
        WHEN 'company_to_unit' THEN 'من مؤسسة لوحدة'
    END AS transferTypeName,
    COUNT(*) AS count,
    SUM(amount) AS totalAmount
FROM pending_vouchers
WHERE status = 'pending'
GROUP BY transferType;
```

### 2. السندات المعلقة بين الوحدات فقط

```sql
SELECT * 
FROM pending_vouchers
WHERE transferType = 'unit_to_unit' 
  AND status = 'pending'
ORDER BY priority DESC, voucherDate ASC;
```

### 3. السندات المعلقة بين المؤسسات فقط

```sql
SELECT * 
FROM pending_vouchers
WHERE transferType = 'company_to_company' 
  AND status = 'pending'
ORDER BY priority DESC, voucherDate ASC;
```

---

## 🤖 أوامر الذكاء الاصطناعي

### أمثلة على الأوامر البسيطة:

```
✅ "رحّل السند SV-2025-001 بين الوحدات"
✅ "أنشئ قيد مقابل للسند بين المؤسسات رقم RV-2025-010"
✅ "رحّل جميع السندات المعلقة بين الوحدات"
✅ "عرض السندات المعلقة من وحدة إلى مؤسسة"
```

---

## ✅ الفوائد

| الميزة | الوصف |
|--------|-------|
| **وضوح** | معرفة نوع الترحيل فوراً |
| **سهولة** | أوامر بسيطة للذكاء الاصطناعي |
| **تقارير** | إحصائيات حسب نوع الترحيل |
| **صلاحيات** | تحديد صلاحيات حسب النوع |

---

**تاريخ الإنشاء:** 2025-01-14  
**الإصدار:** 2.0  
**الحالة:** ✅ جاهز للتنفيذ

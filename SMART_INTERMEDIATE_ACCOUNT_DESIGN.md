# 🧠 تصميم الحساب الوسيط الذكي (Smart Intermediate Account)

## 🎯 المفهوم الجديد

بدلاً من إنشاء حساب وسيط لكل علاقة، سيكون لكل **وحدة** و **مؤسسة** حساب وسيط واحد فقط، مع نظام فلترة متقدم لتتبع جميع التفاصيل.

---

## 📊 الهيكل الجديد

### 1️⃣ **حساب وسيط لكل وحدة**
```
الوحدة 1 → حساب وسيط واحد (كود: 1900)
الوحدة 2 → حساب وسيط واحد (كود: 1900)
الوحدة 3 → حساب وسيط واحد (كود: 1900)
```

### 2️⃣ **حساب وسيط لكل مؤسسة**
```
المؤسسة 1 → حساب وسيط واحد (كود: 1950)
المؤسسة 2 → حساب وسيط واحد (كود: 1950)
المؤسسة 3 → حساب وسيط واحد (كود: 1950)
```

---

## 🔍 نظام الفلترة المتقدم

### الفلاتر المتاحة:

| الفلتر | الوصف | مثال |
|--------|-------|------|
| **نوع السند** | صرف/قبض/قيد يومية | `voucherType = 'payment'` |
| **نوع الترحيل** | بين وحدات/مؤسسات | `transferType = 'unit_to_unit'` |
| **الكيان المصدر** | من أي وحدة/مؤسسة | `sourceUnitId = 1` |
| **الكيان الهدف** | إلى أي وحدة/مؤسسة | `targetUnitId = 2` |
| **الصندوق** | أي صندوق استُخدم | `cashAccountId = 1010` |
| **الحساب النهائي** | الحساب في الطرف الآخر | `targetAccountId = 3010` |
| **الفترة الزمنية** | من تاريخ إلى تاريخ | `voucherDate BETWEEN '2025-01-01' AND '2025-01-31'` |
| **الحالة** | معلق/مُرحّل/مرفوض | `status = 'transferred'` |
| **المبلغ** | نطاق المبلغ | `amount BETWEEN 10000 AND 50000` |
| **الأولوية** | منخفض/متوسط/عالي/عاجل | `priority = 'high'` |
| **المستخدم** | من أنشأ/رحّل السند | `createdBy = 5` |

---

## 📋 جدول تفاصيل الحساب الوسيط

### الجدول الجديد: `intermediate_account_transactions`

هذا الجدول يخزن **جميع التفاصيل** لكل عملية تمر عبر الحساب الوسيط:

```sql
CREATE TABLE intermediate_account_transactions (
    -- المعرف الفريد
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- ربط بالحساب الوسيط
    intermediateAccountId INT NOT NULL,
    
    -- ربط بالسند المعلق
    pendingVoucherId INT NOT NULL,
    
    -- معلومات السند
    voucherType ENUM('payment', 'receipt', 'journal_entry') NOT NULL,
    transferType ENUM('unit_to_unit', 'company_to_company', 'unit_to_company', 'company_to_unit') NOT NULL,
    voucherNumber VARCHAR(50) NOT NULL,
    voucherDate DATE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    description TEXT NOT NULL,
    
    -- معلومات الكيان المصدر
    sourceType ENUM('unit', 'company') NOT NULL,
    sourceUnitId INT NULL,
    sourceCompanyId INT NULL,
    sourceBranchId INT NULL,
    
    -- معلومات الكيان الهدف
    targetType ENUM('unit', 'company') NOT NULL,
    targetUnitId INT NULL,
    targetCompanyId INT NULL,
    targetBranchId INT NULL,
    
    -- معلومات الحسابات المستخدمة
    cashAccountId INT NULL COMMENT 'الصندوق أو البنك المستخدم',
    cashAccountCode VARCHAR(20) NULL,
    cashAccountName VARCHAR(255) NULL,
    
    targetAccountId INT NULL COMMENT 'الحساب النهائي في الطرف الآخر',
    targetAccountCode VARCHAR(20) NULL,
    targetAccountName VARCHAR(255) NULL,
    
    -- معلومات القيد
    debitAccountId INT NOT NULL,
    debitAccountCode VARCHAR(20) NOT NULL,
    debitAccountName VARCHAR(255) NOT NULL,
    
    creditAccountId INT NOT NULL,
    creditAccountCode VARCHAR(20) NOT NULL,
    creditAccountName VARCHAR(255) NOT NULL,
    
    -- معلومات الترحيل
    status ENUM('pending', 'transferred', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    isTransferred BOOLEAN NOT NULL DEFAULT FALSE,
    transferredAt DATETIME NULL,
    transferredBy INT NULL,
    
    -- معلومات إضافية
    priority ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    notes TEXT NULL,
    
    -- Audit Trail
    createdBy INT NOT NULL,
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    CONSTRAINT fk_iat_intermediate_account FOREIGN KEY (intermediateAccountId) REFERENCES accounts(id),
    CONSTRAINT fk_iat_pending_voucher FOREIGN KEY (pendingVoucherId) REFERENCES pending_vouchers(id),
    CONSTRAINT fk_iat_source_unit FOREIGN KEY (sourceUnitId) REFERENCES units(id),
    CONSTRAINT fk_iat_source_company FOREIGN KEY (sourceCompanyId) REFERENCES companies(id),
    CONSTRAINT fk_iat_target_unit FOREIGN KEY (targetUnitId) REFERENCES units(id),
    CONSTRAINT fk_iat_target_company FOREIGN KEY (targetCompanyId) REFERENCES companies(id),
    CONSTRAINT fk_iat_cash_account FOREIGN KEY (cashAccountId) REFERENCES accounts(id),
    CONSTRAINT fk_iat_target_account FOREIGN KEY (targetAccountId) REFERENCES accounts(id),
    CONSTRAINT fk_iat_debit_account FOREIGN KEY (debitAccountId) REFERENCES accounts(id),
    CONSTRAINT fk_iat_credit_account FOREIGN KEY (creditAccountId) REFERENCES accounts(id),
    CONSTRAINT fk_iat_transferred_by FOREIGN KEY (transferredBy) REFERENCES users(id),
    CONSTRAINT fk_iat_created_by FOREIGN KEY (createdBy) REFERENCES users(id)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- الفهارس
CREATE INDEX idx_iat_intermediate_account ON intermediate_account_transactions(intermediateAccountId);
CREATE INDEX idx_iat_voucher_type ON intermediate_account_transactions(voucherType);
CREATE INDEX idx_iat_transfer_type ON intermediate_account_transactions(transferType);
CREATE INDEX idx_iat_voucher_date ON intermediate_account_transactions(voucherDate);
CREATE INDEX idx_iat_status ON intermediate_account_transactions(status);
CREATE INDEX idx_iat_source_entity ON intermediate_account_transactions(sourceType, sourceUnitId, sourceCompanyId);
CREATE INDEX idx_iat_target_entity ON intermediate_account_transactions(targetType, targetUnitId, targetCompanyId);
CREATE INDEX idx_iat_cash_account ON intermediate_account_transactions(cashAccountId);
CREATE INDEX idx_iat_target_account ON intermediate_account_transactions(targetAccountId);
CREATE INDEX idx_iat_amount ON intermediate_account_transactions(amount);
CREATE INDEX idx_iat_priority ON intermediate_account_transactions(priority);
```

---

## 🎨 واجهة الفلترة

### صفحة "تفاصيل الحساب الوسيط"

```
╔══════════════════════════════════════════════════════════════╗
║  🧠 الحساب الوسيط الذكي - الوحدة 1                         ║
╠══════════════════════════════════════════════════════════════╣
║                                                              ║
║  📊 الإحصائيات السريعة:                                     ║
║  ┌──────────────┬──────────────┬──────────────┬────────────┐║
║  │ معلق: 15     │ مُرحّل: 45   │ مرفوض: 2     │ ملغي: 1    │║
║  │ 750,000 ر.س  │ 2,250,000 ر.س│ 100,000 ر.س  │ 50,000 ر.س │║
║  └──────────────┴──────────────┴──────────────┴────────────┘║
║                                                              ║
║  🔍 الفلاتر المتقدمة:                                        ║
║  ┌────────────────────────────────────────────────────────┐ ║
║  │ نوع السند:      [▼ الكل ▼]                            │ ║
║  │ نوع الترحيل:    [▼ الكل ▼]                            │ ║
║  │ من وحدة/مؤسسة:  [▼ الكل ▼]                            │ ║
║  │ إلى وحدة/مؤسسة: [▼ الكل ▼]                            │ ║
║  │ الصندوق:        [▼ الكل ▼]                            │ ║
║  │ الحساب النهائي: [▼ الكل ▼]                            │ ║
║  │ من تاريخ:       [📅 2025-01-01]                        │ ║
║  │ إلى تاريخ:      [📅 2025-01-31]                        │ ║
║  │ الحالة:         [▼ الكل ▼]                            │ ║
║  │ المبلغ من:      [_________] إلى: [_________]          │ ║
║  │ الأولوية:       [▼ الكل ▼]                            │ ║
║  │                                                        │ ║
║  │ [🔍 بحث] [🔄 إعادة تعيين]                             │ ║
║  └────────────────────────────────────────────────────────┘ ║
║                                                              ║
║  📋 النتائج (45 سند):                                       ║
║  ┌──────┬────────┬──────────┬──────────┬──────────┬────────┐║
║  │ رقم  │ التاريخ│ النوع    │ من       │ إلى      │ المبلغ │║
║  ├──────┼────────┼──────────┼──────────┼──────────┼────────┤║
║  │ SV-1 │ 01/15  │ صرف      │ وحدة 1   │ وحدة 2   │ 50,000 │║
║  │ RV-2 │ 01/16  │ قبض      │ مؤسسة 1  │ مؤسسة 2  │ 75,000 │║
║  │ ...  │ ...    │ ...      │ ...      │ ...      │ ...    │║
║  └──────┴────────┴──────────┴──────────┴──────────┴────────┘║
║                                                              ║
║  [📊 تصدير Excel] [📄 طباعة] [📈 تقرير مفصل]               ║
╚══════════════════════════════════════════════════════════════╝
```

---

## 📊 أمثلة على الاستعلامات

### 1. عرض جميع العمليات عبر حساب وسيط معين

```sql
SELECT 
    iat.*,
    CASE iat.sourceType
        WHEN 'unit' THEN CONCAT('وحدة: ', u1.unitName)
        WHEN 'company' THEN CONCAT('مؤسسة: ', c1.companyName)
    END AS source,
    CASE iat.targetType
        WHEN 'unit' THEN CONCAT('وحدة: ', u2.unitName)
        WHEN 'company' THEN CONCAT('مؤسسة: ', c2.companyName)
    END AS target
FROM intermediate_account_transactions iat
LEFT JOIN units u1 ON iat.sourceUnitId = u1.id
LEFT JOIN companies c1 ON iat.sourceCompanyId = c1.id
LEFT JOIN units u2 ON iat.targetUnitId = u2.id
LEFT JOIN companies c2 ON iat.targetCompanyId = c2.id
WHERE iat.intermediateAccountId = 1900
ORDER BY iat.voucherDate DESC;
```

### 2. فلترة حسب نوع السند والفترة

```sql
SELECT * 
FROM intermediate_account_transactions
WHERE intermediateAccountId = 1900
  AND voucherType = 'payment'
  AND voucherDate BETWEEN '2025-01-01' AND '2025-01-31'
  AND status = 'transferred'
ORDER BY voucherDate DESC;
```

### 3. فلترة حسب الكيان المصدر والهدف

```sql
SELECT * 
FROM intermediate_account_transactions
WHERE intermediateAccountId = 1900
  AND sourceUnitId = 1
  AND targetUnitId = 2
  AND transferType = 'unit_to_unit'
ORDER BY voucherDate DESC;
```

### 4. فلترة حسب الصندوق المستخدم

```sql
SELECT * 
FROM intermediate_account_transactions
WHERE intermediateAccountId = 1900
  AND cashAccountId = 1010
  AND status = 'transferred'
ORDER BY voucherDate DESC;
```

### 5. فلترة حسب نطاق المبلغ

```sql
SELECT * 
FROM intermediate_account_transactions
WHERE intermediateAccountId = 1900
  AND amount BETWEEN 10000 AND 50000
  AND status = 'pending'
ORDER BY amount DESC;
```

### 6. إحصائيات حسب نوع الترحيل

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
    SUM(amount) AS totalAmount,
    AVG(amount) AS avgAmount
FROM intermediate_account_transactions
WHERE intermediateAccountId = 1900
GROUP BY transferType;
```

### 7. العمليات المعلقة حسب الأولوية

```sql
SELECT 
    priority,
    COUNT(*) AS count,
    SUM(amount) AS totalAmount
FROM intermediate_account_transactions
WHERE intermediateAccountId = 1900
  AND status = 'pending'
GROUP BY priority
ORDER BY FIELD(priority, 'urgent', 'high', 'medium', 'low');
```

---

## 🤖 أوامر الذكاء الاصطناعي

### أمثلة على الأوامر البسيطة:

```
✅ "أعرض جميع السندات المعلقة في الحساب الوسيط للوحدة 1"
✅ "أعرض السندات المُرحّلة بين الوحدة 1 والوحدة 2 في يناير 2025"
✅ "أعرض جميع عمليات الصرف من الصندوق الرئيسي عبر الحساب الوسيط"
✅ "أعرض السندات المعلقة ذات الأولوية العالية"
✅ "أعرض السندات بمبلغ أكثر من 50,000 ريال"
✅ "أعرض إحصائيات الحساب الوسيط للمؤسسة 1"
```

---

## 📈 التقارير المتاحة

### 1. تقرير الحركة الشهرية
```sql
SELECT 
    DATE_FORMAT(voucherDate, '%Y-%m') AS month,
    transferType,
    COUNT(*) AS count,
    SUM(amount) AS totalAmount
FROM intermediate_account_transactions
WHERE intermediateAccountId = 1900
  AND status = 'transferred'
GROUP BY month, transferType
ORDER BY month DESC, transferType;
```

### 2. تقرير أكثر الكيانات تعاملاً
```sql
SELECT 
    CASE targetType
        WHEN 'unit' THEN CONCAT('وحدة: ', u.unitName)
        WHEN 'company' THEN CONCAT('مؤسسة: ', c.companyName)
    END AS targetEntity,
    COUNT(*) AS transactionCount,
    SUM(amount) AS totalAmount
FROM intermediate_account_transactions iat
LEFT JOIN units u ON iat.targetUnitId = u.id
LEFT JOIN companies c ON iat.targetCompanyId = c.id
WHERE iat.intermediateAccountId = 1900
  AND iat.status = 'transferred'
GROUP BY targetEntity
ORDER BY totalAmount DESC
LIMIT 10;
```

### 3. تقرير الأداء (متوسط وقت الترحيل)
```sql
SELECT 
    DATE_FORMAT(voucherDate, '%Y-%m') AS month,
    COUNT(*) AS totalTransactions,
    AVG(TIMESTAMPDIFF(HOUR, createdAt, transferredAt)) AS avgHoursToTransfer,
    MIN(TIMESTAMPDIFF(HOUR, createdAt, transferredAt)) AS minHoursToTransfer,
    MAX(TIMESTAMPDIFF(HOUR, createdAt, transferredAt)) AS maxHoursToTransfer
FROM intermediate_account_transactions
WHERE intermediateAccountId = 1900
  AND status = 'transferred'
  AND transferredAt IS NOT NULL
GROUP BY month
ORDER BY month DESC;
```

---

## ✅ الفوائد

| الميزة | الوصف |
|--------|-------|
| **تبسيط** | حساب وسيط واحد لكل وحدة/مؤسسة |
| **مرونة** | فلترة متقدمة حسب أي معيار |
| **وضوح** | تتبع كامل لجميع التفاصيل |
| **تقارير** | إحصائيات وتقارير شاملة |
| **أداء** | فهارس محسّنة لسرعة البحث |
| **ذكاء اصطناعي** | أوامر بسيطة ومباشرة |

---

## 🔄 التكامل مع النظام الحالي

### تحديث جدول `accounts`:

```sql
-- إضافة حقل لتحديد أن الحساب هو حساب وسيط
ALTER TABLE accounts 
ADD COLUMN isIntermediateAccount BOOLEAN DEFAULT FALSE COMMENT 'هل هذا حساب وسيط؟';

-- إضافة فهرس
CREATE INDEX idx_is_intermediate ON accounts(isIntermediateAccount);
```

### إنشاء الحسابات الوسيطة تلقائياً:

```sql
-- عند إنشاء وحدة جديدة
INSERT INTO accounts (
    accountCode, accountName, accountType, 
    companyId, isIntermediateAccount, createdBy
) VALUES (
    '1900', 'الحساب الوسيط - الوحدة الجديدة', 'current_asset',
    NULL, TRUE, 1
);

-- عند إنشاء مؤسسة جديدة
INSERT INTO accounts (
    accountCode, accountName, accountType, 
    companyId, isIntermediateAccount, createdBy
) VALUES (
    '1950', 'الحساب الوسيط - المؤسسة الجديدة', 'current_asset',
    :companyId, TRUE, 1
);
```

---

**تاريخ الإنشاء:** 2025-01-14  
**الإصدار:** 1.0  
**الحالة:** ✅ جاهز للتنفيذ

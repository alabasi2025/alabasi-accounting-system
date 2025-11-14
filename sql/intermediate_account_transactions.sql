-- =====================================================
-- جدول تفاصيل الحساب الوسيط (Intermediate Account Transactions)
-- نظام العباسي المحاسبي الموحد
-- =====================================================
-- الهدف: تخزين جميع التفاصيل لكل عملية تمر عبر الحساب الوسيط
--        مع نظام فلترة متقدم
-- التاريخ: 2025-01-14
-- الإصدار: 1.0
-- =====================================================

CREATE TABLE IF NOT EXISTS intermediate_account_transactions (
    -- ==========================================
    -- المعرف الفريد
    -- ==========================================
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'المعرف الفريد',
    
    -- ==========================================
    -- ربط بالحساب الوسيط
    -- ==========================================
    intermediateAccountId INT NOT NULL COMMENT 'معرف الحساب الوسيط',
    
    -- ==========================================
    -- ربط بالسند المعلق
    -- ==========================================
    pendingVoucherId INT NOT NULL COMMENT 'معرف السند المعلق',
    
    -- ==========================================
    -- معلومات السند
    -- ==========================================
    voucherType ENUM('payment', 'receipt', 'journal_entry') NOT NULL COMMENT 'نوع السند',
    transferType ENUM('unit_to_unit', 'company_to_company', 'unit_to_company', 'company_to_unit') NOT NULL COMMENT 'نوع الترحيل',
    voucherNumber VARCHAR(50) NOT NULL COMMENT 'رقم السند',
    voucherDate DATE NOT NULL COMMENT 'تاريخ السند',
    amount DECIMAL(15,2) NOT NULL COMMENT 'المبلغ',
    description TEXT NOT NULL COMMENT 'البيان',
    
    -- ==========================================
    -- معلومات الكيان المصدر
    -- ==========================================
    sourceType ENUM('unit', 'company') NOT NULL COMMENT 'نوع الكيان المصدر',
    sourceUnitId INT NULL COMMENT 'معرف الوحدة المصدر',
    sourceCompanyId INT NULL COMMENT 'معرف المؤسسة المصدر',
    sourceBranchId INT NULL COMMENT 'معرف الفرع المصدر',
    
    -- ==========================================
    -- معلومات الكيان الهدف
    -- ==========================================
    targetType ENUM('unit', 'company') NOT NULL COMMENT 'نوع الكيان الهدف',
    targetUnitId INT NULL COMMENT 'معرف الوحدة الهدف',
    targetCompanyId INT NULL COMMENT 'معرف المؤسسة الهدف',
    targetBranchId INT NULL COMMENT 'معرف الفرع الهدف',
    
    -- ==========================================
    -- معلومات الحسابات المستخدمة
    -- ==========================================
    cashAccountId INT NULL COMMENT 'الصندوق أو البنك المستخدم',
    cashAccountCode VARCHAR(20) NULL COMMENT 'كود الصندوق/البنك',
    cashAccountName VARCHAR(255) NULL COMMENT 'اسم الصندوق/البنك',
    
    targetAccountId INT NULL COMMENT 'الحساب النهائي في الطرف الآخر',
    targetAccountCode VARCHAR(20) NULL COMMENT 'كود الحساب النهائي',
    targetAccountName VARCHAR(255) NULL COMMENT 'اسم الحساب النهائي',
    
    -- ==========================================
    -- معلومات القيد المحاسبي
    -- ==========================================
    debitAccountId INT NOT NULL COMMENT 'الحساب المدين',
    debitAccountCode VARCHAR(20) NOT NULL COMMENT 'كود الحساب المدين',
    debitAccountName VARCHAR(255) NOT NULL COMMENT 'اسم الحساب المدين',
    
    creditAccountId INT NOT NULL COMMENT 'الحساب الدائن',
    creditAccountCode VARCHAR(20) NOT NULL COMMENT 'كود الحساب الدائن',
    creditAccountName VARCHAR(255) NOT NULL COMMENT 'اسم الحساب الدائن',
    
    -- ==========================================
    -- معلومات الترحيل
    -- ==========================================
    status ENUM('pending', 'transferred', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending' COMMENT 'حالة السند',
    isTransferred BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'هل تم الترحيل؟',
    transferredAt DATETIME NULL COMMENT 'تاريخ ووقت الترحيل',
    transferredBy INT NULL COMMENT 'المستخدم الذي قام بالترحيل',
    
    -- ==========================================
    -- معلومات إضافية
    -- ==========================================
    priority ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium' COMMENT 'الأولوية',
    notes TEXT NULL COMMENT 'ملاحظات',
    
    -- ==========================================
    -- Audit Trail
    -- ==========================================
    createdBy INT NOT NULL COMMENT 'المستخدم الذي أنشأ السجل',
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ الإنشاء',
    updatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'تاريخ آخر تعديل',
    
    -- ==========================================
    -- القيود (Constraints)
    -- ==========================================
    
    -- التحقق من أن المبلغ موجب
    CONSTRAINT chk_iat_amount_positive CHECK (amount > 0),
    
    -- التحقق من وجود معرف الكيان المصدر
    CONSTRAINT chk_iat_source_entity CHECK (
        (sourceType = 'unit' AND sourceUnitId IS NOT NULL) OR
        (sourceType = 'company' AND sourceCompanyId IS NOT NULL)
    ),
    
    -- التحقق من وجود معرف الكيان الهدف
    CONSTRAINT chk_iat_target_entity CHECK (
        (targetType = 'unit' AND targetUnitId IS NOT NULL) OR
        (targetType = 'company' AND targetCompanyId IS NOT NULL)
    ),
    
    -- ==========================================
    -- المفاتيح الخارجية (Foreign Keys)
    -- ==========================================
    
    CONSTRAINT fk_iat_intermediate_account 
        FOREIGN KEY (intermediateAccountId) REFERENCES accounts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_pending_voucher 
        FOREIGN KEY (pendingVoucherId) REFERENCES pending_vouchers(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_source_unit 
        FOREIGN KEY (sourceUnitId) REFERENCES units(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_source_company 
        FOREIGN KEY (sourceCompanyId) REFERENCES companies(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_source_branch 
        FOREIGN KEY (sourceBranchId) REFERENCES branches(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_target_unit 
        FOREIGN KEY (targetUnitId) REFERENCES units(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_target_company 
        FOREIGN KEY (targetCompanyId) REFERENCES companies(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_target_branch 
        FOREIGN KEY (targetBranchId) REFERENCES branches(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_cash_account 
        FOREIGN KEY (cashAccountId) REFERENCES accounts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_target_account 
        FOREIGN KEY (targetAccountId) REFERENCES accounts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_debit_account 
        FOREIGN KEY (debitAccountId) REFERENCES accounts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_credit_account 
        FOREIGN KEY (creditAccountId) REFERENCES accounts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_transferred_by 
        FOREIGN KEY (transferredBy) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_iat_created_by 
        FOREIGN KEY (createdBy) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='جدول تفاصيل الحساب الوسيط مع نظام فلترة متقدم';

-- ==========================================
-- الفهارس (Indexes)
-- ==========================================

-- فهارس أساسية
CREATE INDEX idx_iat_intermediate_account ON intermediate_account_transactions(intermediateAccountId);
CREATE INDEX idx_iat_pending_voucher ON intermediate_account_transactions(pendingVoucherId);
CREATE INDEX idx_iat_voucher_type ON intermediate_account_transactions(voucherType);
CREATE INDEX idx_iat_transfer_type ON intermediate_account_transactions(transferType);
CREATE INDEX idx_iat_voucher_number ON intermediate_account_transactions(voucherNumber);
CREATE INDEX idx_iat_voucher_date ON intermediate_account_transactions(voucherDate);
CREATE INDEX idx_iat_status ON intermediate_account_transactions(status);
CREATE INDEX idx_iat_is_transferred ON intermediate_account_transactions(isTransferred);
CREATE INDEX idx_iat_priority ON intermediate_account_transactions(priority);
CREATE INDEX idx_iat_amount ON intermediate_account_transactions(amount);

-- فهارس للكيانات
CREATE INDEX idx_iat_source_type ON intermediate_account_transactions(sourceType);
CREATE INDEX idx_iat_target_type ON intermediate_account_transactions(targetType);
CREATE INDEX idx_iat_source_unit ON intermediate_account_transactions(sourceUnitId);
CREATE INDEX idx_iat_source_company ON intermediate_account_transactions(sourceCompanyId);
CREATE INDEX idx_iat_target_unit ON intermediate_account_transactions(targetUnitId);
CREATE INDEX idx_iat_target_company ON intermediate_account_transactions(targetCompanyId);

-- فهارس للحسابات
CREATE INDEX idx_iat_cash_account ON intermediate_account_transactions(cashAccountId);
CREATE INDEX idx_iat_target_account ON intermediate_account_transactions(targetAccountId);

-- فهارس مركبة لتحسين الأداء
CREATE INDEX idx_iat_source_entity ON intermediate_account_transactions(sourceType, sourceUnitId, sourceCompanyId);
CREATE INDEX idx_iat_target_entity ON intermediate_account_transactions(targetType, targetUnitId, targetCompanyId);
CREATE INDEX idx_iat_transfer_status ON intermediate_account_transactions(status, isTransferred, transferredAt);
CREATE INDEX idx_iat_date_status ON intermediate_account_transactions(voucherDate, status);
CREATE INDEX idx_iat_account_date ON intermediate_account_transactions(intermediateAccountId, voucherDate);
CREATE INDEX idx_iat_account_status ON intermediate_account_transactions(intermediateAccountId, status);

-- فهرس للبحث الشامل
CREATE INDEX idx_iat_search ON intermediate_account_transactions(
    intermediateAccountId, 
    voucherType, 
    transferType, 
    status, 
    voucherDate
);

-- ==========================================
-- تحديث جدول accounts لإضافة حقل isIntermediateAccount
-- ==========================================

ALTER TABLE accounts 
ADD COLUMN IF NOT EXISTS isIntermediateAccount BOOLEAN DEFAULT FALSE COMMENT 'هل هذا حساب وسيط؟';

CREATE INDEX idx_is_intermediate ON accounts(isIntermediateAccount);

-- ==========================================
-- بيانات تجريبية (للاختبار فقط)
-- ==========================================

-- مثال: إنشاء حساب وسيط للوحدة 1
/*
INSERT INTO accounts (
    accountCode, accountName, accountType, 
    companyId, isIntermediateAccount, createdBy
) VALUES (
    '1900', 'الحساب الوسيط - الوحدة 1', 'current_asset',
    NULL, TRUE, 1
);
*/

-- مثال: إنشاء سجل في جدول تفاصيل الحساب الوسيط
/*
INSERT INTO intermediate_account_transactions (
    intermediateAccountId, pendingVoucherId,
    voucherType, transferType, voucherNumber, voucherDate, amount, description,
    sourceType, sourceUnitId,
    targetType, targetUnitId,
    cashAccountId, cashAccountCode, cashAccountName,
    debitAccountId, debitAccountCode, debitAccountName,
    creditAccountId, creditAccountCode, creditAccountName,
    status, priority, createdBy
) VALUES (
    1900, 1,
    'payment', 'unit_to_unit', 'SV-2025-001', '2025-01-15', 50000.00, 'دفع للوحدة 2',
    'unit', 1,
    'unit', 2,
    1010, '1010', 'الصندوق الرئيسي',
    1900, '1900', 'الحساب الوسيط - الوحدة 1',
    1010, '1010', 'الصندوق الرئيسي',
    'pending', 'high', 1
);
*/

-- ==========================================
-- استعلامات مفيدة
-- ==========================================

-- 1. عرض جميع العمليات عبر حساب وسيط معين
/*
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
*/

-- 2. إحصائيات حسب نوع الترحيل
/*
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
*/

-- 3. العمليات المعلقة حسب الأولوية
/*
SELECT 
    priority,
    COUNT(*) AS count,
    SUM(amount) AS totalAmount
FROM intermediate_account_transactions
WHERE intermediateAccountId = 1900
  AND status = 'pending'
GROUP BY priority
ORDER BY FIELD(priority, 'urgent', 'high', 'medium', 'low');
*/

-- ==========================================
-- ملاحظات مهمة
-- ==========================================
-- 1. هذا الجدول يعمل كـ "سجل تفصيلي" للحساب الوسيط
-- 2. يتم إنشاء سجل جديد تلقائياً عند إنشاء سند معلق
-- 3. الفهارس محسّنة للبحث والفلترة السريعة
-- 4. يمكن إضافة فلاتر إضافية حسب الحاجة

-- ==========================================
-- نهاية السكريبت
-- ==========================================

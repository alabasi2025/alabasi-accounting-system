-- =====================================================
-- جدول السندات المعلقة (Pending Vouchers)
-- نظام العباسي المحاسبي الموحد
-- =====================================================
-- الهدف: تخزين السندات التي تحتوي على حسابات وسيطة
--        والتي تحتاج إلى ترحيل بين الوحدات والمؤسسات
-- التاريخ: 2025-01-14
-- الإصدار: 1.0
-- =====================================================

-- حذف الجدول إذا كان موجوداً (للاختبار فقط)
-- DROP TABLE IF EXISTS pending_vouchers;

CREATE TABLE IF NOT EXISTS pending_vouchers (
    -- ==========================================
    -- الحقول الأساسية
    -- ==========================================
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'المعرف الفريد',
    voucherType ENUM('payment', 'receipt', 'journal_entry') NOT NULL COMMENT 'نوع السند: صرف/قبض/قيد يومية',
    transferType ENUM('unit_to_unit', 'company_to_company', 'unit_to_company', 'company_to_unit') NOT NULL COMMENT 'نوع الترحيل: بين الوحدات/بين المؤسسات/من وحدة لمؤسسة/من مؤسسة لوحدة',
    voucherNumber VARCHAR(50) NOT NULL COMMENT 'رقم السند الأصلي',
    voucherDate DATE NOT NULL COMMENT 'تاريخ السند',
    fiscalYearId INT NOT NULL COMMENT 'السنة المالية',
    amount DECIMAL(15,2) NOT NULL COMMENT 'المبلغ الإجمالي',
    description TEXT NOT NULL COMMENT 'البيان/الوصف',
    
    -- ==========================================
    -- معلومات الكيان المصدر
    -- ==========================================
    sourceType ENUM('unit', 'company') NOT NULL COMMENT 'نوع الكيان المصدر: وحدة/مؤسسة',
    sourceUnitId INT NULL COMMENT 'معرف الوحدة المصدر',
    sourceCompanyId INT NULL COMMENT 'معرف المؤسسة المصدر',
    sourceBranchId INT NULL COMMENT 'معرف الفرع المصدر',
    sourceVoucherId INT NOT NULL COMMENT 'معرف السند الأصلي في الجدول المصدر',
    sourceTableName VARCHAR(50) NOT NULL COMMENT 'اسم جدول السند الأصلي (payment_vouchers/receipt_vouchers/journal_entries)',
    
    -- ==========================================
    -- معلومات الكيان الهدف
    -- ==========================================
    targetType ENUM('unit', 'company') NOT NULL COMMENT 'نوع الكيان الهدف: وحدة/مؤسسة',
    targetUnitId INT NULL COMMENT 'معرف الوحدة الهدف',
    targetCompanyId INT NULL COMMENT 'معرف المؤسسة الهدف',
    targetBranchId INT NULL COMMENT 'معرف الفرع الهدف',
    
    -- ==========================================
    -- معلومات الحساب الوسيط
    -- ==========================================
    intermediateAccountId INT NOT NULL COMMENT 'معرف الحساب الوسيط',
    intermediateAccountCode VARCHAR(20) NOT NULL COMMENT 'كود الحساب الوسيط',
    intermediateAccountName VARCHAR(255) NOT NULL COMMENT 'اسم الحساب الوسيط',
    mappingId INT NOT NULL COMMENT 'معرف الربط من جدول intermediate_accounts_mapping',
    
    -- ==========================================
    -- معلومات الحساب النهائي في الهدف
    -- ==========================================
    targetAccountId INT NULL COMMENT 'معرف الحساب النهائي في الهدف (يُملأ عند الترحيل)',
    targetAccountCode VARCHAR(20) NULL COMMENT 'كود الحساب النهائي',
    targetAccountName VARCHAR(255) NULL COMMENT 'اسم الحساب النهائي',
    targetAccountType ENUM('debit', 'credit') NULL COMMENT 'نوع الحساب النهائي: مدين/دائن',
    
    -- ==========================================
    -- معلومات الترحيل
    -- ==========================================
    status ENUM('pending', 'transferred', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending' COMMENT 'حالة السند',
    isTransferred BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'هل تم الترحيل؟',
    transferredAt DATETIME NULL COMMENT 'تاريخ ووقت الترحيل',
    transferredBy INT NULL COMMENT 'المستخدم الذي قام بالترحيل',
    linkedVoucherId INT NULL COMMENT 'معرف السند المُنشأ في الهدف',
    linkedVoucherNumber VARCHAR(50) NULL COMMENT 'رقم السند المُنشأ في الهدف',
    linkedTableName VARCHAR(50) NULL COMMENT 'اسم جدول السند المُنشأ',
    
    -- ==========================================
    -- معلومات القيد المحاسبي الأصلي
    -- ==========================================
    debitAccountId INT NOT NULL COMMENT 'الحساب المدين في القيد الأصلي',
    debitAccountCode VARCHAR(20) NOT NULL COMMENT 'كود الحساب المدين',
    debitAccountName VARCHAR(255) NOT NULL COMMENT 'اسم الحساب المدين',
    creditAccountId INT NOT NULL COMMENT 'الحساب الدائن في القيد الأصلي',
    creditAccountCode VARCHAR(20) NOT NULL COMMENT 'كود الحساب الدائن',
    creditAccountName VARCHAR(255) NOT NULL COMMENT 'اسم الحساب الدائن',
    
    -- ==========================================
    -- معلومات إضافية
    -- ==========================================
    notes TEXT NULL COMMENT 'ملاحظات إضافية',
    attachments TEXT NULL COMMENT 'مرفقات (JSON format)',
    priority ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium' COMMENT 'الأولوية',
    dueDate DATE NULL COMMENT 'تاريخ الاستحقاق',
    rejectionReason TEXT NULL COMMENT 'سبب الرفض (إن وجد)',
    rejectedAt DATETIME NULL COMMENT 'تاريخ الرفض',
    rejectedBy INT NULL COMMENT 'المستخدم الذي رفض',
    
    -- ==========================================
    -- معلومات التدقيق (Audit Trail)
    -- ==========================================
    createdBy INT NOT NULL COMMENT 'المستخدم الذي أنشأ السند',
    createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاريخ ووقت الإنشاء',
    updatedBy INT NULL COMMENT 'آخر مستخدم قام بالتعديل',
    updatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'تاريخ آخر تعديل',
    
    -- ==========================================
    -- القيود (Constraints)
    -- ==========================================
    
    -- التحقق من وجود معرف الكيان المصدر
    CONSTRAINT chk_source_entity CHECK (
        (sourceType = 'unit' AND sourceUnitId IS NOT NULL) OR
        (sourceType = 'company' AND sourceCompanyId IS NOT NULL)
    ),
    
    -- التحقق من وجود معرف الكيان الهدف
    CONSTRAINT chk_target_entity CHECK (
        (targetType = 'unit' AND targetUnitId IS NOT NULL) OR
        (targetType = 'company' AND targetCompanyId IS NOT NULL)
    ),
    
    -- التحقق من أن المبلغ موجب
    CONSTRAINT chk_amount_positive CHECK (amount > 0),
    
    -- التحقق من أن تاريخ الترحيل بعد تاريخ الإنشاء
    CONSTRAINT chk_transferred_after_created CHECK (
        transferredAt IS NULL OR transferredAt >= createdAt
    ),
    
    -- التحقق من أن السند المُرحّل يحتوي على معلومات الترحيل
    CONSTRAINT chk_transferred_data CHECK (
        (status = 'transferred' AND isTransferred = TRUE AND transferredAt IS NOT NULL AND transferredBy IS NOT NULL AND linkedVoucherId IS NOT NULL) OR
        (status != 'transferred')
    ),
    
    -- التحقق من أن السند المرفوض يحتوي على سبب الرفض
    CONSTRAINT chk_rejected_reason CHECK (
        (status = 'rejected' AND rejectionReason IS NOT NULL AND rejectedAt IS NOT NULL AND rejectedBy IS NOT NULL) OR
        (status != 'rejected')
    ),
    
    -- ==========================================
    -- المفاتيح الخارجية (Foreign Keys)
    -- ==========================================
    
    CONSTRAINT fk_pending_fiscal_year 
        FOREIGN KEY (fiscalYearId) REFERENCES fiscal_years(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_source_unit 
        FOREIGN KEY (sourceUnitId) REFERENCES units(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_source_company 
        FOREIGN KEY (sourceCompanyId) REFERENCES companies(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_source_branch 
        FOREIGN KEY (sourceBranchId) REFERENCES branches(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_target_unit 
        FOREIGN KEY (targetUnitId) REFERENCES units(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_target_company 
        FOREIGN KEY (targetCompanyId) REFERENCES companies(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_target_branch 
        FOREIGN KEY (targetBranchId) REFERENCES branches(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_intermediate_account 
        FOREIGN KEY (intermediateAccountId) REFERENCES accounts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_mapping 
        FOREIGN KEY (mappingId) REFERENCES intermediate_accounts_mapping(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_target_account 
        FOREIGN KEY (targetAccountId) REFERENCES accounts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_debit_account 
        FOREIGN KEY (debitAccountId) REFERENCES accounts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_credit_account 
        FOREIGN KEY (creditAccountId) REFERENCES accounts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_transferred_by 
        FOREIGN KEY (transferredBy) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_rejected_by 
        FOREIGN KEY (rejectedBy) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_created_by 
        FOREIGN KEY (createdBy) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_pending_updated_by 
        FOREIGN KEY (updatedBy) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='جدول السندات المعلقة التي تحتاج إلى ترحيل بين الوحدات والمؤسسات';

-- ==========================================
-- الفهارس (Indexes)
-- ==========================================

-- فهارس أساسية
CREATE INDEX idx_voucher_number ON pending_vouchers(voucherNumber);
CREATE INDEX idx_voucher_date ON pending_vouchers(voucherDate);
CREATE INDEX idx_status ON pending_vouchers(status);
CREATE INDEX idx_transfer_type ON pending_vouchers(transferType);
CREATE INDEX idx_source_type ON pending_vouchers(sourceType);
CREATE INDEX idx_target_type ON pending_vouchers(targetType);
CREATE INDEX idx_is_transferred ON pending_vouchers(isTransferred);
CREATE INDEX idx_priority ON pending_vouchers(priority);
CREATE INDEX idx_due_date ON pending_vouchers(dueDate);

-- فهارس مركبة لتحسين الأداء
CREATE INDEX idx_source_entity ON pending_vouchers(sourceType, sourceUnitId, sourceCompanyId);
CREATE INDEX idx_target_entity ON pending_vouchers(targetType, targetUnitId, targetCompanyId);
CREATE INDEX idx_transfer_status ON pending_vouchers(status, isTransferred, transferredAt);
CREATE INDEX idx_created_date ON pending_vouchers(createdAt, status);
CREATE INDEX idx_priority_due ON pending_vouchers(priority, dueDate, status);
CREATE INDEX idx_voucher_lookup ON pending_vouchers(sourceTableName, sourceVoucherId);
CREATE INDEX idx_linked_voucher ON pending_vouchers(linkedTableName, linkedVoucherId);

-- فهرس للبحث السريع عن السندات المعلقة
CREATE INDEX idx_pending_search ON pending_vouchers(status, priority, voucherDate, amount);

-- فهرس للمستخدمين
CREATE INDEX idx_users ON pending_vouchers(createdBy, transferredBy, rejectedBy);

-- ==========================================
-- بيانات تجريبية (للاختبار فقط)
-- ==========================================

-- مثال 1: سند صرف من الوحدة 1 إلى الوحدة 2
/*
INSERT INTO pending_vouchers (
    voucherType, transferType, voucherNumber, voucherDate, fiscalYearId, amount, description,
    sourceType, sourceUnitId, sourceVoucherId, sourceTableName,
    targetType, targetUnitId,
    intermediateAccountId, intermediateAccountCode, intermediateAccountName, mappingId,
    debitAccountId, debitAccountCode, debitAccountName,
    creditAccountId, creditAccountCode, creditAccountName,
    status, priority, createdBy
) VALUES (
    'payment', 'unit_to_unit', 'SV-2025-001', '2025-01-15', 1, 50000.00, 'دفع للمورد أحمد - الوحدة 2',
    'unit', 1, 123, 'payment_vouchers',
    'unit', 2,
    1050, '1050', 'حساب وسيط - الوحدة 2', 5,
    1050, '1050', 'حساب وسيط - الوحدة 2',
    1010, '1010', 'الصندوق',
    'pending', 'high', 1
);
*/

-- مثال 2: سند قبض من المؤسسة 1 إلى المؤسسة 2
/*
INSERT INTO pending_vouchers (
    voucherType, transferType, voucherNumber, voucherDate, fiscalYearId, amount, description,
    sourceType, sourceCompanyId, sourceBranchId, sourceVoucherId, sourceTableName,
    targetType, targetCompanyId,
    intermediateAccountId, intermediateAccountCode, intermediateAccountName, mappingId,
    targetAccountCode, targetAccountName, targetAccountType,
    debitAccountId, debitAccountCode, debitAccountName,
    creditAccountId, creditAccountCode, creditAccountName,
    status, priority, dueDate, createdBy
) VALUES (
    'receipt', 'company_to_company', 'RV-2025-010', '2025-01-16', 1, 75000.00, 'تحصيل من العميل محمد - المؤسسة 2',
    'company', 1, 1, 456, 'receipt_vouchers',
    'company', 2,
    2050, '2050', 'حساب وسيط - المؤسسة 2', 8,
    '3010', 'العميل محمد', 'debit',
    1010, '1010', 'الصندوق',
    2050, '2050', 'حساب وسيط - المؤسسة 1',
    'pending', 'medium', '2025-01-20', 2
);
*/

-- ==========================================
-- ملاحظات مهمة
-- ==========================================
-- 1. يجب التأكد من وجود جميع الجداول المرتبطة قبل تنفيذ هذا السكريبت
-- 2. القيود (Constraints) تضمن سلامة البيانات
-- 3. الفهارس تحسن أداء الاستعلامات
-- 4. يمكن تعطيل بعض القيود في بيئة التطوير إذا لزم الأمر
-- 5. يُنصح بعمل نسخة احتياطية قبل التنفيذ في بيئة الإنتاج

-- ==========================================
-- نهاية السكريبت
-- ==========================================

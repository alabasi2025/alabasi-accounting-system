-- ========================================
-- نظام الدورات المحاسبية وإقفال الفترات
-- متوافق مع البنية الحالية لقاعدة alabasi_unified
-- تاريخ الإنشاء: 2025-01-14
-- ========================================

-- ========================================
-- 1. جدول الدورات المحاسبية
-- ========================================
CREATE TABLE IF NOT EXISTS account_cycles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    companyId INT NOT NULL COMMENT 'المؤسسة التابعة',
    name VARCHAR(255) NOT NULL COMMENT 'اسم الدورة: يناير 2025، الربع الأول 2025',
    nameEn VARCHAR(255) COMMENT 'January 2025, Q1 2025',
    type ENUM('monthly', 'quarterly', 'yearly') NOT NULL COMMENT 'نوع الدورة',
    startDate DATE NOT NULL COMMENT 'تاريخ بداية الدورة',
    endDate DATE NOT NULL COMMENT 'تاريخ نهاية الدورة',
    status ENUM('open', 'under_review', 'closed') DEFAULT 'open' COMMENT 'حالة الدورة',
    closedBy INT COMMENT 'المستخدم الذي أقفل الدورة',
    closedAt TIMESTAMP NULL COMMENT 'تاريخ الإقفال',
    closingJournalId INT COMMENT 'قيد الإقفال',
    notes TEXT COMMENT 'ملاحظات',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (companyId) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (closedBy) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY company_period_unique (companyId, startDate, endDate),
    INDEX idx_company_status (companyId, status),
    INDEX idx_dates (startDate, endDate),
    INDEX idx_type (type),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='جدول الدورات المحاسبية';

-- ========================================
-- 2. جدول سجل عمليات الدورات
-- ========================================
CREATE TABLE IF NOT EXISTS cycle_operations_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cycleId INT NOT NULL,
    operation ENUM('created', 'opened', 'closed', 'reopened', 'reviewed', 'modified', 'deleted') NOT NULL,
    performedBy INT NOT NULL,
    reason TEXT COMMENT 'سبب العملية',
    oldStatus ENUM('open', 'under_review', 'closed') COMMENT 'الحالة القديمة',
    newStatus ENUM('open', 'under_review', 'closed') COMMENT 'الحالة الجديدة',
    metadata JSON COMMENT 'بيانات إضافية',
    ipAddress VARCHAR(45) COMMENT 'عنوان IP',
    userAgent TEXT COMMENT 'معلومات المتصفح',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cycleId) REFERENCES account_cycles(id) ON DELETE CASCADE,
    FOREIGN KEY (performedBy) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_cycle (cycleId),
    INDEX idx_operation (operation),
    INDEX idx_date (createdAt),
    INDEX idx_user (performedBy)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='سجل عمليات الدورات المحاسبية';

-- ========================================
-- 3. جدول أرصدة نهاية الفترة
-- ========================================
CREATE TABLE IF NOT EXISTS period_balances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cycleId INT NOT NULL,
    accountId INT NOT NULL,
    openingBalance DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'الرصيد الافتتاحي',
    closingBalance DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'الرصيد الختامي',
    totalDebits DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'إجمالي المدين',
    totalCredits DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'إجمالي الدائن',
    transactionCount INT NOT NULL DEFAULT 0 COMMENT 'عدد المعاملات',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cycleId) REFERENCES account_cycles(id) ON DELETE CASCADE,
    FOREIGN KEY (accountId) REFERENCES accounts(id) ON DELETE CASCADE,
    
    UNIQUE KEY cycle_account_unique (cycleId, accountId),
    INDEX idx_cycle (cycleId),
    INDEX idx_account (accountId),
    INDEX idx_closing_balance (closingBalance)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='أرصدة نهاية الفترة (Snapshot)';

-- ========================================
-- 4. تعديل جدول القيود (journals)
-- ========================================
-- إضافة حقل cycleId إذا لم يكن موجوداً
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journals' AND COLUMN_NAME = 'cycleId');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE journals ADD COLUMN cycleId INT COMMENT "الدورة المحاسبية التابع لها القيد"', 
    'SELECT "Column cycleId already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- إضافة حقل isClosingEntry إذا لم يكن موجوداً
SET @col_exists2 = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journals' AND COLUMN_NAME = 'isClosingEntry');
SET @sql2 = IF(@col_exists2 = 0, 
    'ALTER TABLE journals ADD COLUMN isClosingEntry BOOLEAN DEFAULT FALSE COMMENT "هل هو قيد إقفال"', 
    'SELECT "Column isClosingEntry already exists"');
PREPARE stmt2 FROM @sql2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

-- إضافة Indexes
SET @idx_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journals' AND INDEX_NAME = 'idx_cycle');
SET @sql5 = IF(@idx_exists = 0,
    'CREATE INDEX idx_cycle ON journals(cycleId)',
    'SELECT "Index idx_cycle already exists"');
PREPARE stmt5 FROM @sql5; EXECUTE stmt5; DEALLOCATE PREPARE stmt5;

SET @idx_exists2 = (SELECT COUNT(*) FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journals' AND INDEX_NAME = 'idx_closing');
SET @sql6 = IF(@idx_exists2 = 0,
    'CREATE INDEX idx_closing ON journals(isClosingEntry)',
    'SELECT "Index idx_closing already exists"');
PREPARE stmt6 FROM @sql6; EXECUTE stmt6; DEALLOCATE PREPARE stmt6;

-- إضافة Foreign Key لـ closingJournalId في account_cycles
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'account_cycles' 
    AND CONSTRAINT_NAME = 'fk_account_cycles_closing_journal');
SET @sql3 = IF(@fk_exists = 0,
    'ALTER TABLE account_cycles ADD CONSTRAINT fk_account_cycles_closing_journal FOREIGN KEY (closingJournalId) REFERENCES journals(id) ON DELETE SET NULL',
    'SELECT "FK fk_account_cycles_closing_journal already exists"');
PREPARE stmt3 FROM @sql3; EXECUTE stmt3; DEALLOCATE PREPARE stmt3;

-- إضافة Foreign Key لـ cycleId في journals
SET @fk_exists2 = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'journals' 
    AND CONSTRAINT_NAME = 'fk_journals_cycle');
SET @sql4 = IF(@fk_exists2 = 0,
    'ALTER TABLE journals ADD CONSTRAINT fk_journals_cycle FOREIGN KEY (cycleId) REFERENCES account_cycles(id) ON DELETE SET NULL',
    'SELECT "FK fk_journals_cycle already exists"');
PREPARE stmt4 FROM @sql4; EXECUTE stmt4; DEALLOCATE PREPARE stmt4;

-- ========================================
-- 5. Views للإحصائيات
-- ========================================

-- View لعرض إحصائيات الدورات
CREATE OR REPLACE VIEW v_cycle_statistics AS
SELECT 
    ac.id,
    ac.companyId,
    ac.name,
    ac.nameEn,
    ac.type,
    ac.status,
    ac.startDate,
    ac.endDate,
    ac.notes,
    ac.closedAt,
    COUNT(DISTINCT j.id) as totalJournals,
    SUM(CASE WHEN j.isClosingEntry = 1 THEN 1 ELSE 0 END) as closingJournals,
    COUNT(DISTINCT pb.accountId) as accountsWithBalances,
    SUM(CASE WHEN j.status = 'posted' THEN 1 ELSE 0 END) as postedJournals,
    u.username as closedByUser,
    u.fullName as closedByName,
    c.nameAr as companyName,
    ac.createdAt,
    ac.updatedAt
FROM account_cycles ac
LEFT JOIN journals j ON j.cycleId = ac.id
LEFT JOIN period_balances pb ON pb.cycleId = ac.id
LEFT JOIN users u ON u.id = ac.closedBy
LEFT JOIN companies c ON c.id = ac.companyId
GROUP BY ac.id;

-- View لعرض الأرصدة بالتفصيل
CREATE OR REPLACE VIEW v_period_balances_detail AS
SELECT 
    pb.*,
    ac.name as cycleName,
    ac.nameEn as cycleNameEn,
    ac.type as cycleType,
    ac.startDate,
    ac.endDate,
    ac.status as cycleStatus,
    a.code as accountCode,
    a.nameAr as accountName,
    a.nameEn as accountNameEn,
    a.type as accountType,
    a.parentId as accountParentId,
    c.nameAr as companyName
FROM period_balances pb
JOIN account_cycles ac ON ac.id = pb.cycleId
JOIN accounts a ON a.id = pb.accountId
JOIN companies c ON c.id = ac.companyId;

-- View لعرض سجل العمليات مع التفاصيل
CREATE OR REPLACE VIEW v_cycle_operations_detail AS
SELECT 
    col.*,
    ac.name as cycleName,
    ac.type as cycleType,
    ac.status as currentStatus,
    u.username,
    u.fullName as performedByName,
    c.nameAr as companyName
FROM cycle_operations_log col
JOIN account_cycles ac ON ac.id = col.cycleId
JOIN users u ON u.id = col.performedBy
JOIN companies c ON c.id = ac.companyId
ORDER BY col.createdAt DESC;

-- ========================================
-- 6. Stored Procedures
-- ========================================

DELIMITER $$

-- إجراء لحساب أرصدة الفترة
DROP PROCEDURE IF EXISTS sp_calculate_period_balances$$

CREATE PROCEDURE sp_calculate_period_balances(IN p_cycleId INT)
BEGIN
    DECLARE v_startDate DATE;
    DECLARE v_endDate DATE;
    DECLARE v_companyId INT;
    DECLARE v_prevCycleId INT;
    DECLARE v_rowCount INT;
    
    -- الحصول على بيانات الدورة
    SELECT startDate, endDate, companyId 
    INTO v_startDate, v_endDate, v_companyId
    FROM account_cycles 
    WHERE id = p_cycleId;
    
    -- التحقق من وجود الدورة
    IF v_companyId IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'الدورة المحاسبية غير موجودة';
    END IF;
    
    -- الحصول على الدورة السابقة المقفلة
    SELECT id INTO v_prevCycleId
    FROM account_cycles
    WHERE companyId = v_companyId
    AND endDate < v_startDate
    AND status = 'closed'
    ORDER BY endDate DESC
    LIMIT 1;
    
    -- حذف الأرصدة القديمة إن وجدت
    DELETE FROM period_balances WHERE cycleId = p_cycleId;
    
    -- حساب الأرصدة لكل حساب
    INSERT INTO period_balances 
    (cycleId, accountId, openingBalance, closingBalance, totalDebits, totalCredits, transactionCount)
    SELECT 
        p_cycleId,
        a.id,
        -- الرصيد الافتتاحي من الدورة السابقة
        COALESCE(pb_prev.closingBalance, 0.00) as openingBalance,
        -- الرصيد الختامي = الرصيد الافتتاحي + (المدين - الدائن)
        COALESCE(pb_prev.closingBalance, 0.00) + 
        COALESCE(SUM(je.debit - je.credit), 0.00) as closingBalance,
        -- إجمالي المدين
        COALESCE(SUM(je.debit), 0.00) as totalDebits,
        -- إجمالي الدائن
        COALESCE(SUM(je.credit), 0.00) as totalCredits,
        -- عدد المعاملات
        COUNT(DISTINCT j.id) as transactionCount
    FROM accounts a
    LEFT JOIN period_balances pb_prev ON pb_prev.accountId = a.id AND pb_prev.cycleId = v_prevCycleId
    LEFT JOIN journalEntries je ON je.accountId = a.id
    LEFT JOIN journals j ON j.id = je.journalId 
        AND j.date BETWEEN v_startDate AND v_endDate
        AND j.status = 'posted'
        AND COALESCE(j.isClosingEntry, 0) = 0
    WHERE a.branchId IN (SELECT id FROM branches WHERE companyId = v_companyId)
    GROUP BY a.id;
    
    -- الحصول على عدد الحسابات المعالجة
    SET v_rowCount = ROW_COUNT();
    
    -- إرجاع النتيجة
    SELECT CONCAT('✅ تم حساب أرصدة ', v_rowCount, ' حساب للدورة ', p_cycleId) as result;
END$$

-- إجراء للحصول على الدورة النشطة لتاريخ معين
DROP PROCEDURE IF EXISTS sp_get_active_cycle$$

CREATE PROCEDURE sp_get_active_cycle(
    IN p_date DATE,
    IN p_companyId INT
)
BEGIN
    SELECT 
        id,
        name,
        type,
        status,
        startDate,
        endDate
    FROM account_cycles
    WHERE companyId = p_companyId
    AND p_date BETWEEN startDate AND endDate
    LIMIT 1;
END$$

-- إجراء لإنشاء دورات شهرية تلقائية لسنة كاملة
DROP PROCEDURE IF EXISTS sp_create_yearly_cycles$$

CREATE PROCEDURE sp_create_yearly_cycles(
    IN p_year INT,
    IN p_companyId INT
)
BEGIN
    DECLARE v_month INT DEFAULT 1;
    DECLARE v_startDate DATE;
    DECLARE v_endDate DATE;
    DECLARE v_monthName VARCHAR(50);
    DECLARE v_monthNameEn VARCHAR(50);
    DECLARE v_created INT DEFAULT 0;
    
    WHILE v_month <= 12 DO
        -- حساب تاريخ البداية والنهاية
        SET v_startDate = DATE(CONCAT(p_year, '-', LPAD(v_month, 2, '0'), '-01'));
        SET v_endDate = LAST_DAY(v_startDate);
        
        -- اسم الشهر بالعربية
        SET v_monthName = CASE v_month
            WHEN 1 THEN 'يناير'
            WHEN 2 THEN 'فبراير'
            WHEN 3 THEN 'مارس'
            WHEN 4 THEN 'أبريل'
            WHEN 5 THEN 'مايو'
            WHEN 6 THEN 'يونيو'
            WHEN 7 THEN 'يوليو'
            WHEN 8 THEN 'أغسطس'
            WHEN 9 THEN 'سبتمبر'
            WHEN 10 THEN 'أكتوبر'
            WHEN 11 THEN 'نوفمبر'
            WHEN 12 THEN 'ديسمبر'
        END;
        
        -- اسم الشهر بالإنجليزية
        SET v_monthNameEn = DATE_FORMAT(v_startDate, '%M');
        
        -- إدخال الدورة
        INSERT IGNORE INTO account_cycles 
        (companyId, name, nameEn, type, startDate, endDate)
        VALUES (
            p_companyId,
            CONCAT(v_monthName, ' ', p_year),
            CONCAT(v_monthNameEn, ' ', p_year),
            'monthly',
            v_startDate,
            v_endDate
        );
        
        IF ROW_COUNT() > 0 THEN
            SET v_created = v_created + 1;
        END IF;
        
        SET v_month = v_month + 1;
    END WHILE;
    
    SELECT CONCAT('✅ تم إنشاء ', v_created, ' دورة شهرية لعام ', p_year) as result;
END$$

DELIMITER ;

-- ========================================
-- 7. Functions مساعدة
-- ========================================

DELIMITER $$

-- دالة للحصول على حالة الدورة لتاريخ معين
DROP FUNCTION IF EXISTS fn_get_cycle_status$$

CREATE FUNCTION fn_get_cycle_status(
    p_date DATE,
    p_companyId INT
) RETURNS VARCHAR(20)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_status VARCHAR(20);
    
    SELECT status INTO v_status
    FROM account_cycles
    WHERE companyId = p_companyId
    AND p_date BETWEEN startDate AND endDate
    LIMIT 1;
    
    RETURN COALESCE(v_status, 'no_cycle');
END$$

-- دالة للتحقق من إمكانية التعديل على قيد
DROP FUNCTION IF EXISTS fn_can_modify_journal$$

CREATE FUNCTION fn_can_modify_journal(p_journalId INT) RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_cycleStatus VARCHAR(20);
    
    SELECT ac.status INTO v_cycleStatus
    FROM journals j
    LEFT JOIN account_cycles ac ON ac.id = j.cycleId
    WHERE j.id = p_journalId;
    
    -- إذا لم تكن هناك دورة أو الدورة مفتوحة، يمكن التعديل
    IF v_cycleStatus IS NULL OR v_cycleStatus = 'open' THEN
        RETURN TRUE;
    END IF;
    
    RETURN FALSE;
END$$

DELIMITER ;

-- ========================================
-- 8. بيانات تجريبية
-- ========================================

-- إنشاء دورة تجريبية لشهر يناير 2025
INSERT IGNORE INTO account_cycles (companyId, name, nameEn, type, startDate, endDate, notes)
SELECT 
    id,
    'يناير 2025',
    'January 2025',
    'monthly',
    '2025-01-01',
    '2025-01-31',
    'دورة تجريبية'
FROM companies
LIMIT 1;

-- ========================================
-- انتهى ملف SQL
-- ========================================

SELECT '✅ تم إنشاء جميع الجداول والـ Views والـ Stored Procedures بنجاح!' as status;
SELECT CONCAT('📊 تم إنشاء ', COUNT(*), ' جدول جديد') as tables_created 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME IN ('account_cycles', 'cycle_operations_log', 'period_balances');

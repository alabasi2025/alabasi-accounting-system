-- ========================================
-- نظام الدورات المحاسبية وإقفال الفترات
-- تاريخ الإنشاء: 2025-01-14
-- ========================================

-- ========================================
-- 1. جدول الدورات المحاسبية
-- ========================================
CREATE TABLE IF NOT EXISTS account_cycles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    organizationId INT NOT NULL,
    name VARCHAR(255) NOT NULL COMMENT 'اسم الدورة: يناير 2025، الربع الأول 2025',
    nameEn VARCHAR(255) COMMENT 'January 2025, Q1 2025',
    type ENUM('monthly', 'quarterly', 'yearly') NOT NULL COMMENT 'نوع الدورة',
    startDate DATE NOT NULL COMMENT 'تاريخ بداية الدورة',
    endDate DATE NOT NULL COMMENT 'تاريخ نهاية الدورة',
    status ENUM('open', 'under_review', 'closed') DEFAULT 'open' COMMENT 'حالة الدورة',
    closedBy INT COMMENT 'المستخدم الذي أقفل الدورة',
    closedAt TIMESTAMP NULL COMMENT 'تاريخ الإقفال',
    closingJournalEntryId INT COMMENT 'قيد الإقفال',
    notes TEXT COMMENT 'ملاحظات',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organizationId) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (closedBy) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY org_period_unique (organizationId, startDate, endDate),
    INDEX idx_org_status (organizationId, status),
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
    openingBalance INT NOT NULL DEFAULT 0 COMMENT 'الرصيد الافتتاحي بالقروش',
    closingBalance INT NOT NULL DEFAULT 0 COMMENT 'الرصيد الختامي بالقروش',
    totalDebits INT NOT NULL DEFAULT 0 COMMENT 'إجمالي المدين بالقروش',
    totalCredits INT NOT NULL DEFAULT 0 COMMENT 'إجمالي الدائن بالقروش',
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
-- 4. تعديل جدول القيود اليومية
-- ========================================
ALTER TABLE journalEntries 
ADD COLUMN IF NOT EXISTS cycleId INT COMMENT 'الدورة المحاسبية التابع لها القيد',
ADD COLUMN IF NOT EXISTS isClosingEntry BOOLEAN DEFAULT FALSE COMMENT 'هل هو قيد إقفال';

-- إضافة Foreign Key إذا لم يكن موجوداً
SET @fk_exists = (SELECT COUNT(*) 
                  FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'journalEntries' 
                  AND CONSTRAINT_NAME = 'journalEntries_ibfk_cycle');

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE journalEntries ADD FOREIGN KEY (cycleId) REFERENCES account_cycles(id) ON DELETE SET NULL',
    'SELECT "Foreign key already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- إضافة Indexes
ALTER TABLE journalEntries 
ADD INDEX IF NOT EXISTS idx_cycle (cycleId),
ADD INDEX IF NOT EXISTS idx_closing (isClosingEntry);

-- تحديث Foreign Key لـ closingJournalEntryId في account_cycles
SET @fk_exists2 = (SELECT COUNT(*) 
                   FROM information_schema.TABLE_CONSTRAINTS 
                   WHERE CONSTRAINT_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'account_cycles' 
                   AND CONSTRAINT_NAME = 'account_cycles_ibfk_closing');

SET @sql2 = IF(@fk_exists2 = 0,
    'ALTER TABLE account_cycles ADD CONSTRAINT account_cycles_ibfk_closing FOREIGN KEY (closingJournalEntryId) REFERENCES journalEntries(id) ON DELETE SET NULL',
    'SELECT "Foreign key already exists"');

PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- ========================================
-- 5. Views للإحصائيات
-- ========================================

-- View لعرض إحصائيات الدورات
CREATE OR REPLACE VIEW v_cycle_statistics AS
SELECT 
    ac.id,
    ac.organizationId,
    ac.name,
    ac.nameEn,
    ac.type,
    ac.status,
    ac.startDate,
    ac.endDate,
    ac.notes,
    ac.closedAt,
    COUNT(DISTINCT je.id) as totalEntries,
    SUM(CASE WHEN je.isClosingEntry = 1 THEN 1 ELSE 0 END) as closingEntries,
    COUNT(DISTINCT pb.accountId) as accountsWithBalances,
    u.username as closedByUser,
    u.fullName as closedByName,
    ac.createdAt,
    ac.updatedAt
FROM account_cycles ac
LEFT JOIN journalEntries je ON je.cycleId = ac.id
LEFT JOIN period_balances pb ON pb.cycleId = ac.id
LEFT JOIN users u ON u.id = ac.closedBy
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
    ca.code as accountCode,
    ca.nameAr as accountName,
    ca.nameEn as accountNameEn,
    ca.type as accountType,
    ca.parentId as accountParentId
FROM period_balances pb
JOIN account_cycles ac ON ac.id = pb.cycleId
JOIN accounts ca ON ca.id = pb.accountId;

-- View لعرض سجل العمليات مع التفاصيل
CREATE OR REPLACE VIEW v_cycle_operations_detail AS
SELECT 
    col.*,
    ac.name as cycleName,
    ac.type as cycleType,
    ac.status as currentStatus,
    u.username,
    u.fullName as performedByName
FROM cycle_operations_log col
JOIN account_cycles ac ON ac.id = col.cycleId
JOIN users u ON u.id = col.performedBy
ORDER BY col.createdAt DESC;

-- ========================================
-- 6. Stored Procedures
-- ========================================

-- إجراء لحساب أرصدة الفترة
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_calculate_period_balances$$

CREATE PROCEDURE sp_calculate_period_balances(IN p_cycleId INT)
BEGIN
    DECLARE v_startDate DATE;
    DECLARE v_endDate DATE;
    DECLARE v_orgId INT;
    DECLARE v_prevCycleId INT;
    DECLARE v_rowCount INT;
    
    -- الحصول على بيانات الدورة
    SELECT startDate, endDate, organizationId 
    INTO v_startDate, v_endDate, v_orgId
    FROM account_cycles 
    WHERE id = p_cycleId;
    
    -- التحقق من وجود الدورة
    IF v_orgId IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'الدورة المحاسبية غير موجودة';
    END IF;
    
    -- الحصول على الدورة السابقة
    SELECT id INTO v_prevCycleId
    FROM account_cycles
    WHERE organizationId = v_orgId
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
        ca.id,
        -- الرصيد الافتتاحي من الدورة السابقة
        COALESCE(pb_prev.closingBalance, 0) as openingBalance,
        -- الرصيد الختامي = الرصيد الافتتاحي + الحركة في الفترة
        COALESCE(pb_prev.closingBalance, 0) + 
        COALESCE(SUM(CASE WHEN jel.type = 'debit' THEN jel.amount ELSE -jel.amount END), 0) as closingBalance,
        -- إجمالي المدين
        COALESCE(SUM(CASE WHEN jel.type = 'debit' THEN jel.amount ELSE 0 END), 0) as totalDebits,
        -- إجمالي الدائن
        COALESCE(SUM(CASE WHEN jel.type = 'credit' THEN jel.amount ELSE 0 END), 0) as totalCredits,
        -- عدد المعاملات
        COUNT(DISTINCT je.id) as transactionCount
    FROM accounts ca
    LEFT JOIN period_balances pb_prev ON pb_prev.accountId = ca.id AND pb_prev.cycleId = v_prevCycleId
    LEFT JOIN journalEntryLines jel ON jel.accountId = ca.id
    LEFT JOIN journalEntries je ON je.id = jel.journalEntryId 
        AND je.entryDate BETWEEN v_startDate AND v_endDate
        AND je.organizationId = v_orgId
        AND je.isClosingEntry = 0
    WHERE ca.organizationId = v_orgId
    GROUP BY ca.id;
    
    -- الحصول على عدد الحسابات المعالجة
    SET v_rowCount = ROW_COUNT();
    
    -- إرجاع النتيجة
    SELECT CONCAT('✅ تم حساب أرصدة ', v_rowCount, ' حساب للدورة ', p_cycleId) as result;
END$$

-- إجراء للحصول على الدورة النشطة لتاريخ معين
DROP PROCEDURE IF EXISTS sp_get_active_cycle$$

CREATE PROCEDURE sp_get_active_cycle(
    IN p_date DATE,
    IN p_organizationId INT
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
    WHERE organizationId = p_organizationId
    AND p_date BETWEEN startDate AND endDate
    LIMIT 1;
END$$

-- إجراء لإنشاء دورات تلقائية لسنة كاملة
DROP PROCEDURE IF EXISTS sp_create_yearly_cycles$$

CREATE PROCEDURE sp_create_yearly_cycles(
    IN p_year INT,
    IN p_organizationId INT
)
BEGIN
    DECLARE v_month INT DEFAULT 1;
    DECLARE v_startDate DATE;
    DECLARE v_endDate DATE;
    DECLARE v_monthName VARCHAR(50);
    DECLARE v_monthNameEn VARCHAR(50);
    
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
        (organizationId, name, nameEn, type, startDate, endDate)
        VALUES (
            p_organizationId,
            CONCAT(v_monthName, ' ', p_year),
            CONCAT(v_monthNameEn, ' ', p_year),
            'monthly',
            v_startDate,
            v_endDate
        );
        
        SET v_month = v_month + 1;
    END WHILE;
    
    SELECT CONCAT('✅ تم إنشاء 12 دورة شهرية لعام ', p_year) as result;
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
    p_organizationId INT
) RETURNS VARCHAR(20)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_status VARCHAR(20);
    
    SELECT status INTO v_status
    FROM account_cycles
    WHERE organizationId = p_organizationId
    AND p_date BETWEEN startDate AND endDate
    LIMIT 1;
    
    RETURN COALESCE(v_status, 'no_cycle');
END$$

-- دالة للتحقق من إمكانية التعديل
DROP FUNCTION IF EXISTS fn_can_modify_entry$$

CREATE FUNCTION fn_can_modify_entry(p_entryId INT) RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_cycleStatus VARCHAR(20);
    
    SELECT ac.status INTO v_cycleStatus
    FROM journalEntries je
    LEFT JOIN account_cycles ac ON ac.id = je.cycleId
    WHERE je.id = p_entryId;
    
    -- إذا لم تكن هناك دورة أو الدورة مفتوحة، يمكن التعديل
    IF v_cycleStatus IS NULL OR v_cycleStatus = 'open' THEN
        RETURN TRUE;
    END IF;
    
    RETURN FALSE;
END$$

DELIMITER ;

-- ========================================
-- 8. Triggers
-- ========================================

DELIMITER $$

-- Trigger لتسجيل عملية الإنشاء تلقائياً
DROP TRIGGER IF EXISTS trg_cycle_after_insert$$

CREATE TRIGGER trg_cycle_after_insert
AFTER INSERT ON account_cycles
FOR EACH ROW
BEGIN
    -- تسجيل عملية الإنشاء (سيتم تحديث performedBy من PHP)
    -- هذا Trigger فقط للحالات الخاصة
    IF @disable_cycle_triggers IS NULL OR @disable_cycle_triggers = 0 THEN
        INSERT INTO cycle_operations_log 
        (cycleId, operation, performedBy, newStatus, reason)
        VALUES (
            NEW.id,
            'created',
            COALESCE(@current_user_id, 1),
            NEW.status,
            'إنشاء دورة جديدة تلقائياً'
        );
    END IF;
END$$

-- Trigger لتسجيل عمليات التحديث
DROP TRIGGER IF EXISTS trg_cycle_after_update$$

CREATE TRIGGER trg_cycle_after_update
AFTER UPDATE ON account_cycles
FOR EACH ROW
BEGIN
    IF @disable_cycle_triggers IS NULL OR @disable_cycle_triggers = 0 THEN
        -- تسجيل التغيير في الحالة
        IF OLD.status != NEW.status THEN
            INSERT INTO cycle_operations_log 
            (cycleId, operation, performedBy, oldStatus, newStatus, reason)
            VALUES (
                NEW.id,
                CASE NEW.status
                    WHEN 'closed' THEN 'closed'
                    WHEN 'open' THEN 'reopened'
                    ELSE 'modified'
                END,
                COALESCE(@current_user_id, 1),
                OLD.status,
                NEW.status,
                'تحديث حالة الدورة'
            );
        END IF;
    END IF;
END$$

DELIMITER ;

-- ========================================
-- 9. بيانات تجريبية (اختيارية)
-- ========================================

-- إدخال دورة تجريبية (يمكن حذفها لاحقاً)
-- INSERT INTO account_cycles (organizationId, name, nameEn, type, startDate, endDate, notes)
-- VALUES (1, 'يناير 2025', 'January 2025', 'monthly', '2025-01-01', '2025-01-31', 'دورة تجريبية');

-- ========================================
-- 10. منح الصلاحيات
-- ========================================

-- منح صلاحيات للمستخدم (إذا لزم الأمر)
-- GRANT SELECT, INSERT, UPDATE, DELETE ON account_cycles TO 'alabasi_user'@'localhost';
-- GRANT SELECT, INSERT ON cycle_operations_log TO 'alabasi_user'@'localhost';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON period_balances TO 'alabasi_user'@'localhost';
-- GRANT EXECUTE ON PROCEDURE sp_calculate_period_balances TO 'alabasi_user'@'localhost';
-- GRANT EXECUTE ON PROCEDURE sp_get_active_cycle TO 'alabasi_user'@'localhost';
-- GRANT EXECUTE ON PROCEDURE sp_create_yearly_cycles TO 'alabasi_user'@'localhost';

-- ========================================
-- انتهى ملف SQL
-- ========================================

SELECT '✅ تم إنشاء جميع الجداول والـ Views والـ Stored Procedures بنجاح!' as status;

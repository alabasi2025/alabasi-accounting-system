-- ============================================
-- نظام العباسي الموحد - قاعدة البيانات الأساسية
-- Alabasi Unified System - Basic Database
-- ============================================

-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS alabasi_unified 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE alabasi_unified;

-- ============================================
-- جدول المستخدمين
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nameAr VARCHAR(100) NOT NULL,
    nameEn VARCHAR(100),
    email VARCHAR(100),
    role ENUM('admin', 'accountant', 'user') NOT NULL DEFAULT 'user',
    isActive BOOLEAN NOT NULL DEFAULT TRUE,
    lastLogin DATETIME,
    createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    createdBy INT,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول الفروع
-- ============================================
CREATE TABLE IF NOT EXISTS branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    nameAr VARCHAR(200) NOT NULL,
    nameEn VARCHAR(200),
    address TEXT,
    phone VARCHAR(50),
    isActive BOOLEAN NOT NULL DEFAULT TRUE,
    createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    createdBy INT,
    INDEX idx_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول الحسابات
-- ============================================
CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    nameAr VARCHAR(200) NOT NULL,
    nameEn VARCHAR(200),
    type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    parentId INT,
    level INT NOT NULL DEFAULT 1,
    isActive BOOLEAN NOT NULL DEFAULT TRUE,
    createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    createdBy INT,
    FOREIGN KEY (parentId) REFERENCES accounts(id),
    INDEX idx_code (code),
    INDEX idx_type (type),
    INDEX idx_parent (parentId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول الحسابات التحليلية
-- ============================================
CREATE TABLE IF NOT EXISTS analyticalAccounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    nameAr VARCHAR(200) NOT NULL,
    nameEn VARCHAR(200),
    accountId INT NOT NULL,
    type ENUM('customer', 'supplier', 'employee', 'other') NOT NULL,
    phone VARCHAR(50),
    email VARCHAR(100),
    address TEXT,
    isActive BOOLEAN NOT NULL DEFAULT TRUE,
    createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    createdBy INT,
    FOREIGN KEY (accountId) REFERENCES accounts(id),
    INDEX idx_code (code),
    INDEX idx_type (type),
    INDEX idx_account (accountId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول القيود اليومية
-- ============================================
CREATE TABLE IF NOT EXISTS journals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journalNumber VARCHAR(50) NOT NULL UNIQUE,
    date DATE NOT NULL,
    description TEXT NOT NULL,
    totalDebit DECIMAL(15,2) NOT NULL DEFAULT 0,
    totalCredit DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('draft', 'posted') NOT NULL DEFAULT 'draft',
    postedAt DATETIME,
    postedBy INT,
    createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    createdBy INT,
    INDEX idx_number (journalNumber),
    INDEX idx_date (date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول تفاصيل القيود
-- ============================================
CREATE TABLE IF NOT EXISTS journalEntries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journalId INT NOT NULL,
    accountId INT NOT NULL,
    analyticalAccountId INT,
    debit DECIMAL(15,2) NOT NULL DEFAULT 0,
    credit DECIMAL(15,2) NOT NULL DEFAULT 0,
    description TEXT,
    FOREIGN KEY (journalId) REFERENCES journals(id) ON DELETE CASCADE,
    FOREIGN KEY (accountId) REFERENCES accounts(id),
    FOREIGN KEY (analyticalAccountId) REFERENCES analyticalAccounts(id),
    INDEX idx_journal (journalId),
    INDEX idx_account (accountId),
    INDEX idx_analytical (analyticalAccountId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- البيانات الأولية
-- ============================================

-- إضافة مستخدم افتراضي (admin / admin123)
INSERT INTO users (username, password, nameAr, nameEn, email, role, isActive) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'المدير العام', 'Admin', 'admin@alabasi.com', 'admin', 1);

-- إضافة فرع افتراضي
INSERT INTO branches (code, nameAr, nameEn, phone, isActive, createdBy) VALUES
('BR001', 'الفرع الرئيسي', 'Main Branch', '0123456789', 1, 1);

-- إضافة الحسابات الرئيسية
INSERT INTO accounts (code, nameAr, nameEn, type, level, isActive, createdBy) VALUES
('1', 'الأصول', 'Assets', 'asset', 1, 1, 1),
('2', 'الخصوم', 'Liabilities', 'liability', 1, 1, 1),
('3', 'حقوق الملكية', 'Equity', 'equity', 1, 1, 1),
('4', 'الإيرادات', 'Revenue', 'revenue', 1, 1, 1),
('5', 'المصروفات', 'Expenses', 'expense', 1, 1, 1);

-- الأصول المتداولة
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdBy) VALUES
('11', 'الأصول المتداولة', 'Current Assets', 'asset', 1, 2, 1, 1),
('111', 'النقدية', 'Cash', 'asset', 2, 3, 1, 1),
('112', 'البنوك', 'Banks', 'asset', 2, 3, 1, 1),
('113', 'العملاء', 'Accounts Receivable', 'asset', 2, 3, 1, 1),
('114', 'المخزون', 'Inventory', 'asset', 2, 3, 1, 1);

-- الأصول الثابتة
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdBy) VALUES
('12', 'الأصول الثابتة', 'Fixed Assets', 'asset', 1, 2, 1, 1),
('121', 'الأراضي', 'Land', 'asset', 7, 3, 1, 1),
('122', 'المباني', 'Buildings', 'asset', 7, 3, 1, 1),
('123', 'المعدات', 'Equipment', 'asset', 7, 3, 1, 1);

-- الخصوم المتداولة
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdBy) VALUES
('21', 'الخصوم المتداولة', 'Current Liabilities', 'liability', 2, 2, 1, 1),
('211', 'الموردون', 'Accounts Payable', 'liability', 11, 3, 1, 1),
('212', 'القروض قصيرة الأجل', 'Short-term Loans', 'liability', 11, 3, 1, 1);

-- حقوق الملكية
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdBy) VALUES
('31', 'رأس المال', 'Capital', 'equity', 3, 2, 1, 1),
('32', 'الأرباح المحتجزة', 'Retained Earnings', 'equity', 3, 2, 1, 1);

-- الإيرادات
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdBy) VALUES
('41', 'إيرادات المبيعات', 'Sales Revenue', 'revenue', 4, 2, 1, 1),
('42', 'إيرادات أخرى', 'Other Revenue', 'revenue', 4, 2, 1, 1);

-- المصروفات
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdBy) VALUES
('51', 'تكلفة المبيعات', 'Cost of Sales', 'expense', 5, 2, 1, 1),
('52', 'مصروفات إدارية', 'Administrative Expenses', 'expense', 5, 2, 1, 1),
('53', 'مصروفات تسويقية', 'Marketing Expenses', 'expense', 5, 2, 1, 1);

-- رسالة نجاح
SELECT '✅ تم إنشاء قاعدة البيانات والجداول بنجاح!' as status;
SELECT CONCAT('✅ تم إضافة ', COUNT(*), ' حساب') as accounts_status FROM accounts;
SELECT '✅ يمكنك الآن تسجيل الدخول بـ: admin / admin123' as login_info;

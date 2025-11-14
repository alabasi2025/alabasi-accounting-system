-- ===================================
-- قاعدة بيانات نظام الأباسي الكاملة
-- Alabasi Complete Database Schema
-- ===================================

-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS alabasi_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE alabasi_test;

-- ===================================
-- 1. جدول المستخدمين
-- ===================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nameAr VARCHAR(100),
    nameEn VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('admin', 'accountant', 'user') DEFAULT 'user',
    isActive BOOLEAN DEFAULT TRUE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 2. جدول الوحدات
-- ===================================
CREATE TABLE IF NOT EXISTS units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    nameAr VARCHAR(100) NOT NULL,
    nameEn VARCHAR(100),
    description TEXT,
    isActive BOOLEAN DEFAULT TRUE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 3. جدول المؤسسات
-- ===================================
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    nameAr VARCHAR(100) NOT NULL,
    nameEn VARCHAR(100),
    unitId INT,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    isActive BOOLEAN DEFAULT TRUE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unitId) REFERENCES units(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 4. جدول الفروع
-- ===================================
CREATE TABLE IF NOT EXISTS branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    nameAr VARCHAR(100) NOT NULL,
    nameEn VARCHAR(100),
    companyId INT,
    address TEXT,
    phone VARCHAR(20),
    isActive BOOLEAN DEFAULT TRUE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (companyId) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 5. جدول شجرة الحسابات
-- ===================================
CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    nameAr VARCHAR(200) NOT NULL,
    nameEn VARCHAR(200),
    parentId INT NULL,
    accountType ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    accountNature ENUM('debit', 'credit') NOT NULL,
    level INT DEFAULT 1,
    isParent BOOLEAN DEFAULT FALSE,
    isActive BOOLEAN DEFAULT TRUE,
    description TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parentId) REFERENCES accounts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 6. جدول القيود اليومية
-- ===================================
CREATE TABLE IF NOT EXISTS journals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    description TEXT,
    totalDebit DECIMAL(15,2) DEFAULT 0,
    totalCredit DECIMAL(15,2) DEFAULT 0,
    status ENUM('draft', 'posted') DEFAULT 'draft',
    voucherType ENUM('none', 'receipt', 'payment') DEFAULT 'none',
    voucherId INT NULL,
    createdBy INT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (createdBy) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 7. جدول تفاصيل القيود
-- ===================================
CREATE TABLE IF NOT EXISTS journal_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journalId INT NOT NULL,
    accountId INT NOT NULL,
    description TEXT,
    debit DECIMAL(15,2) DEFAULT 0,
    credit DECIMAL(15,2) DEFAULT 0,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (journalId) REFERENCES journals(id) ON DELETE CASCADE,
    FOREIGN KEY (accountId) REFERENCES accounts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 8. جدول سندات القبض
-- ===================================
CREATE TABLE IF NOT EXISTS receipt_vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucherNumber VARCHAR(50) NOT NULL UNIQUE,
    voucherDate DATE NOT NULL,
    receivedFrom VARCHAR(200) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    amountInWords TEXT,
    paymentMethod ENUM('cash', 'check', 'bank_transfer', 'other') DEFAULT 'cash',
    checkNumber VARCHAR(50),
    bankName VARCHAR(100),
    checkDate DATE,
    debitAccountId INT NOT NULL,
    creditAccountId INT NOT NULL,
    description TEXT,
    notes TEXT,
    status ENUM('draft', 'posted') DEFAULT 'draft',
    journalId INT,
    unitId INT,
    companyId INT,
    branchId INT,
    createdBy INT,
    postedBy INT,
    postedAt TIMESTAMP NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (debitAccountId) REFERENCES accounts(id),
    FOREIGN KEY (creditAccountId) REFERENCES accounts(id),
    FOREIGN KEY (journalId) REFERENCES journals(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (postedBy) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 9. جدول سندات الصرف
-- ===================================
CREATE TABLE IF NOT EXISTS payment_vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucherNumber VARCHAR(50) NOT NULL UNIQUE,
    voucherDate DATE NOT NULL,
    paidTo VARCHAR(200) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    amountInWords TEXT,
    paymentMethod ENUM('cash', 'check', 'bank_transfer', 'other') DEFAULT 'cash',
    checkNumber VARCHAR(50),
    bankName VARCHAR(100),
    checkDate DATE,
    debitAccountId INT NOT NULL,
    creditAccountId INT NOT NULL,
    description TEXT,
    notes TEXT,
    status ENUM('draft', 'posted') DEFAULT 'draft',
    journalId INT,
    unitId INT,
    companyId INT,
    branchId INT,
    createdBy INT,
    postedBy INT,
    postedAt TIMESTAMP NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (debitAccountId) REFERENCES accounts(id),
    FOREIGN KEY (creditAccountId) REFERENCES accounts(id),
    FOREIGN KEY (journalId) REFERENCES journals(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (postedBy) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- 10. جدول أرقام السندات التلقائية
-- ===================================
CREATE TABLE IF NOT EXISTS voucher_sequences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucherType ENUM('receipt', 'payment') NOT NULL,
    prefix VARCHAR(10) NOT NULL,
    currentNumber INT DEFAULT 1,
    year INT NOT NULL,
    UNIQUE KEY unique_type_year (voucherType, year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- إدراج بيانات تجريبية
-- ===================================

-- مستخدم تجريبي
INSERT INTO users (username, password, nameAr, nameEn, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'المدير', 'Admin', 'admin@alabasi.com', 'admin'),
('accountant', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'المحاسب', 'Accountant', 'accountant@alabasi.com', 'accountant');

-- وحدة تجريبية
INSERT INTO units (code, nameAr, nameEn) VALUES
('UNIT001', 'الوحدة الرئيسية', 'Main Unit');

-- مؤسسة تجريبية
INSERT INTO companies (code, nameAr, nameEn, unitId) VALUES
('COMP001', 'مؤسسة الأباسي', 'Alabasi Company', 1);

-- فرع تجريبي
INSERT INTO branches (code, nameAr, nameEn, companyId) VALUES
('BR001', 'الفرع الرئيسي', 'Main Branch', 1);

-- شجرة حسابات تجريبية
INSERT INTO accounts (code, nameAr, nameEn, parentId, accountType, accountNature, level, isParent) VALUES
-- الأصول
('1', 'الأصول', 'Assets', NULL, 'asset', 'debit', 1, TRUE),
('1-1', 'الأصول المتداولة', 'Current Assets', 1, 'asset', 'debit', 2, TRUE),
('1-1-1', 'الصندوق', 'Cash', 2, 'asset', 'debit', 3, FALSE),
('1-1-2', 'البنك', 'Bank', 2, 'asset', 'debit', 3, FALSE),
('1-1-3', 'العملاء', 'Customers', 2, 'asset', 'debit', 3, FALSE),

-- الخصوم
('2', 'الخصوم', 'Liabilities', NULL, 'liability', 'credit', 1, TRUE),
('2-1', 'الخصوم المتداولة', 'Current Liabilities', 6, 'liability', 'credit', 2, TRUE),
('2-1-1', 'الموردون', 'Suppliers', 7, 'liability', 'credit', 3, FALSE),

-- حقوق الملكية
('3', 'حقوق الملكية', 'Equity', NULL, 'equity', 'credit', 1, TRUE),
('3-1', 'رأس المال', 'Capital', 9, 'equity', 'credit', 2, FALSE),

-- الإيرادات
('4', 'الإيرادات', 'Revenue', NULL, 'revenue', 'credit', 1, TRUE),
('4-1', 'المبيعات', 'Sales', 11, 'revenue', 'credit', 2, FALSE),
('4-2', 'إيرادات أخرى', 'Other Revenue', 11, 'revenue', 'credit', 2, FALSE),

-- المصروفات
('5', 'المصروفات', 'Expenses', NULL, 'expense', 'debit', 1, TRUE),
('5-1', 'مصروفات التشغيل', 'Operating Expenses', 14, 'expense', 'debit', 2, TRUE),
('5-1-1', 'الرواتب', 'Salaries', 15, 'expense', 'debit', 3, FALSE),
('5-1-2', 'الإيجار', 'Rent', 15, 'expense', 'debit', 3, FALSE),
('5-1-3', 'الكهرباء والماء', 'Utilities', 15, 'expense', 'debit', 3, FALSE);

-- ===================================
-- تم إنشاء قاعدة البيانات بنجاح
-- ===================================

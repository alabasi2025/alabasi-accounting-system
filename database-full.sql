-- ============================================
-- قاعدة بيانات نظام العباسي الموحد - كاملة
-- ============================================

-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS alabasi_unified CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE alabasi_unified;

SET FOREIGN_KEY_CHECKS = 0;

-- حذف الجداول القديمة
DROP TABLE IF EXISTS journalEntries;
DROP TABLE IF EXISTS journals;
DROP TABLE IF EXISTS analyticalAccounts;
DROP TABLE IF EXISTS accounts;
DROP TABLE IF EXISTS warehouses;
DROP TABLE IF EXISTS branches;
DROP TABLE IF EXISTS companies;
DROP TABLE IF EXISTS modules;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS userPermissions;
DROP TABLE IF EXISTS settings;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- جدول الوحدات (Modules)
-- ============================================
CREATE TABLE modules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  nameAr VARCHAR(200) NOT NULL,
  nameEn VARCHAR(200),
  description TEXT,
  icon VARCHAR(50),
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  displayOrder INT NOT NULL DEFAULT 0,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول المؤسسات (Companies)
-- ============================================
CREATE TABLE companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  nameAr VARCHAR(200) NOT NULL,
  nameEn VARCHAR(200),
  taxNumber VARCHAR(50),
  commercialRegister VARCHAR(50),
  phone VARCHAR(50),
  email VARCHAR(100),
  website VARCHAR(200),
  address TEXT,
  city VARCHAR(100),
  country VARCHAR(100) DEFAULT 'السعودية',
  postalCode VARCHAR(20),
  logo VARCHAR(255),
  fiscalYearStart DATE,
  fiscalYearEnd DATE,
  currency VARCHAR(10) DEFAULT 'SAR',
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول الفروع (Branches)
-- ============================================
CREATE TABLE branches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  companyId INT NOT NULL,
  code VARCHAR(50) NOT NULL UNIQUE,
  nameAr VARCHAR(200) NOT NULL,
  nameEn VARCHAR(200),
  phone VARCHAR(50),
  email VARCHAR(100),
  address TEXT,
  city VARCHAR(100),
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (companyId) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول المستخدمين (Users)
-- ============================================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  fullName VARCHAR(200) NOT NULL,
  email VARCHAR(100),
  phone VARCHAR(50),
  branchId INT,
  role ENUM('admin', 'manager', 'accountant', 'user') NOT NULL DEFAULT 'user',
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  lastLogin TIMESTAMP NULL,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (branchId) REFERENCES branches(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول دليل الحسابات (Accounts)
-- ============================================
CREATE TABLE accounts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  nameAr VARCHAR(200) NOT NULL,
  nameEn VARCHAR(200),
  parentId INT,
  type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
  level INT NOT NULL DEFAULT 1,
  isParent BOOLEAN NOT NULL DEFAULT FALSE,
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  allowPosting BOOLEAN NOT NULL DEFAULT TRUE,
  description TEXT,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  createdBy INT,
  FOREIGN KEY (parentId) REFERENCES accounts(id) ON DELETE RESTRICT,
  FOREIGN KEY (createdBy) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول الحسابات التحليلية (Analytical Accounts)
-- ============================================
CREATE TABLE analyticalAccounts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  accountId INT NOT NULL,
  code VARCHAR(50) NOT NULL UNIQUE,
  nameAr VARCHAR(200) NOT NULL,
  nameEn VARCHAR(200),
  type ENUM('customer', 'supplier', 'employee', 'project', 'cost_center', 'other') NOT NULL,
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  contactInfo JSON,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (accountId) REFERENCES accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول القيود اليومية (Journals)
-- ============================================
CREATE TABLE journals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  journalNumber VARCHAR(50) NOT NULL UNIQUE,
  date DATE NOT NULL,
  type ENUM('manual', 'sales', 'purchase', 'payment', 'receipt', 'opening', 'closing') NOT NULL DEFAULT 'manual',
  description TEXT,
  totalDebit DECIMAL(15,2) NOT NULL DEFAULT 0,
  totalCredit DECIMAL(15,2) NOT NULL DEFAULT 0,
  status ENUM('draft', 'posted', 'cancelled') NOT NULL DEFAULT 'draft',
  branchId INT,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  createdBy INT,
  postedAt TIMESTAMP NULL,
  postedBy INT,
  FOREIGN KEY (branchId) REFERENCES branches(id) ON DELETE SET NULL,
  FOREIGN KEY (createdBy) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (postedBy) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول تفاصيل القيود (Journal Entries)
-- ============================================
CREATE TABLE journalEntries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  journalId INT NOT NULL,
  accountId INT NOT NULL,
  analyticalAccountId INT,
  debit DECIMAL(15,2) NOT NULL DEFAULT 0,
  credit DECIMAL(15,2) NOT NULL DEFAULT 0,
  description TEXT,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (journalId) REFERENCES journals(id) ON DELETE CASCADE,
  FOREIGN KEY (accountId) REFERENCES accounts(id) ON DELETE RESTRICT,
  FOREIGN KEY (analyticalAccountId) REFERENCES analyticalAccounts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول المخازن (Warehouses)
-- ============================================
CREATE TABLE warehouses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  nameAr VARCHAR(200) NOT NULL,
  nameEn VARCHAR(200),
  branchId INT NOT NULL,
  address TEXT,
  isActive BOOLEAN NOT NULL DEFAULT TRUE,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (branchId) REFERENCES branches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول الصلاحيات (Permissions)
-- ============================================
CREATE TABLE permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  nameAr VARCHAR(200) NOT NULL,
  nameEn VARCHAR(200),
  moduleCode VARCHAR(50),
  description TEXT,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (moduleCode) REFERENCES modules(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول صلاحيات المستخدمين (User Permissions)
-- ============================================
CREATE TABLE userPermissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  userId INT NOT NULL,
  permissionId INT NOT NULL,
  granted BOOLEAN NOT NULL DEFAULT TRUE,
  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (permissionId) REFERENCES permissions(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_permission (userId, permissionId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- جدول الإعدادات (Settings)
-- ============================================
CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  keyName VARCHAR(100) NOT NULL UNIQUE,
  keyValue TEXT,
  category VARCHAR(50),
  description TEXT,
  updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updatedBy INT,
  FOREIGN KEY (updatedBy) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- إدراج البيانات الأولية
-- ============================================

-- الوحدات
INSERT INTO modules (code, nameAr, nameEn, icon, displayOrder) VALUES
('accounting', 'المحاسبة', 'Accounting', '📊', 1),
('inventory', 'المخزون', 'Inventory', '📦', 2),
('sales', 'المبيعات', 'Sales', '💰', 3),
('purchases', 'المشتريات', 'Purchases', '🛒', 4),
('hr', 'الموارد البشرية', 'Human Resources', '👥', 5),
('reports', 'التقارير', 'Reports', '📈', 6),
('settings', 'الإعدادات', 'Settings', '⚙️', 7);

-- مؤسسة افتراضية
INSERT INTO companies (code, nameAr, nameEn, taxNumber, phone, email, city, country, currency, fiscalYearStart, fiscalYearEnd) VALUES
('COMP001', 'شركة العباسي', 'Al-Abasi Company', '300000000000003', '0500000000', 'info@alabasi.com', 'الرياض', 'السعودية', 'SAR', '2025-01-01', '2025-12-31');

-- فرع افتراضي
INSERT INTO branches (companyId, code, nameAr, nameEn, city) VALUES
(1, 'BR001', 'الفرع الرئيسي', 'Main Branch', 'الرياض');

-- مستخدم افتراضي (admin/admin123)
INSERT INTO users (username, password, fullName, email, branchId, role) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd2ALwuQdO.iYCGGp4VLD/nFHEEJNfkRh4Lw6qpPO', 'المدير العام', 'admin@alabasi.com', 1, 'admin');

-- دليل الحسابات
INSERT INTO accounts (code, nameAr, nameEn, type, level, isParent, allowPosting) VALUES
-- الأصول (1)
('1', 'الأصول', 'Assets', 'asset', 1, TRUE, FALSE),
('11', 'الأصول المتداولة', 'Current Assets', 'asset', 2, TRUE, FALSE),
('111', 'النقدية وما في حكمها', 'Cash and Cash Equivalents', 'asset', 3, TRUE, FALSE),
('1111', 'الصندوق', 'Cash on Hand', 'asset', 4, FALSE, TRUE),
('1112', 'البنك', 'Bank', 'asset', 4, FALSE, TRUE),
('112', 'العملاء', 'Accounts Receivable', 'asset', 3, TRUE, FALSE),
('1121', 'عملاء محليون', 'Local Customers', 'asset', 4, FALSE, TRUE),
('113', 'المخزون', 'Inventory', 'asset', 3, TRUE, FALSE),
('1131', 'مخزون بضاعة', 'Merchandise Inventory', 'asset', 4, FALSE, TRUE),
('12', 'الأصول الثابتة', 'Fixed Assets', 'asset', 2, TRUE, FALSE),
('121', 'الأراضي', 'Land', 'asset', 3, FALSE, TRUE),
('122', 'المباني', 'Buildings', 'asset', 3, FALSE, TRUE),

-- الخصوم (2)
('2', 'الخصوم', 'Liabilities', 'liability', 1, TRUE, FALSE),
('21', 'الخصوم المتداولة', 'Current Liabilities', 'liability', 2, TRUE, FALSE),
('211', 'الموردون', 'Accounts Payable', 'liability', 3, TRUE, FALSE),
('2111', 'موردون محليون', 'Local Suppliers', 'liability', 4, FALSE, TRUE),

-- حقوق الملكية (3)
('3', 'حقوق الملكية', 'Equity', 'equity', 1, TRUE, FALSE),
('31', 'رأس المال', 'Capital', 'equity', 2, FALSE, TRUE),
('32', 'الأرباح المحتجزة', 'Retained Earnings', 'equity', 2, FALSE, TRUE),

-- الإيرادات (4)
('4', 'الإيرادات', 'Revenue', 'revenue', 1, TRUE, FALSE),
('41', 'إيرادات المبيعات', 'Sales Revenue', 'revenue', 2, TRUE, FALSE),
('411', 'مبيعات بضاعة', 'Merchandise Sales', 'revenue', 3, FALSE, TRUE),

-- المصروفات (5)
('5', 'المصروفات', 'Expenses', 'expense', 1, TRUE, FALSE),
('51', 'تكلفة المبيعات', 'Cost of Sales', 'expense', 2, FALSE, TRUE),
('52', 'مصروفات إدارية', 'Administrative Expenses', 'expense', 2, TRUE, FALSE),
('521', 'رواتب وأجور', 'Salaries and Wages', 'expense', 3, FALSE, TRUE),
('522', 'إيجارات', 'Rent', 'expense', 3, FALSE, TRUE);

-- مخزن افتراضي
INSERT INTO warehouses (code, nameAr, nameEn, branchId) VALUES
('WH001', 'المخزن الرئيسي', 'Main Warehouse', 1);

-- الصلاحيات
INSERT INTO permissions (code, nameAr, nameEn, moduleCode) VALUES
('accounts.view', 'عرض الحسابات', 'View Accounts', 'accounting'),
('accounts.add', 'إضافة حسابات', 'Add Accounts', 'accounting'),
('accounts.edit', 'تعديل حسابات', 'Edit Accounts', 'accounting'),
('accounts.delete', 'حذف حسابات', 'Delete Accounts', 'accounting'),
('journals.view', 'عرض القيود', 'View Journals', 'accounting'),
('journals.add', 'إضافة قيود', 'Add Journals', 'accounting'),
('journals.edit', 'تعديل قيود', 'Edit Journals', 'accounting'),
('journals.delete', 'حذف قيود', 'Delete Journals', 'accounting'),
('reports.view', 'عرض التقارير', 'View Reports', 'reports'),
('settings.manage', 'إدارة الإعدادات', 'Manage Settings', 'settings');

-- إعدادات النظام
INSERT INTO settings (keyName, keyValue, category, description) VALUES
('system_name', 'نظام العباسي الموحد', 'general', 'اسم النظام'),
('default_currency', 'SAR', 'general', 'العملة الافتراضية'),
('date_format', 'Y-m-d', 'general', 'صيغة التاريخ'),
('fiscal_year_start', '2025-01-01', 'accounting', 'بداية السنة المالية'),
('fiscal_year_end', '2025-12-31', 'accounting', 'نهاية السنة المالية');

-- رسالة نجاح
SELECT '✅ تم إنشاء قاعدة البيانات والجداول بنجاح!' AS status;
SELECT CONCAT('✅ تم إضافة ', COUNT(*), ' حساب') AS accounts_status FROM accounts;
SELECT '✅ يمكنك تسجيل الدخول بـ: admin / admin123' AS login_info;

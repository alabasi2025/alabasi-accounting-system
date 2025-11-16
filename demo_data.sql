-- ============================================
-- بيانات تجريبية شاملة - نظام الأباسي المحاسبي
-- Comprehensive Demo Data - Alabasi Accounting System
-- ============================================
-- هذا الملف يحتوي على بيانات تجريبية كاملة لعرض جميع وظائف النظام
-- ============================================

USE u306850950_alabasi;

-- تعطيل فحص المفاتيح الخارجية مؤقتاً
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- 1. المستخدمين (Users)
-- ============================================

-- حذف البيانات القديمة
TRUNCATE TABLE users;

-- إضافة مستخدمين تجريبيين
-- كلمة المرور لجميع المستخدمين: admin123
INSERT INTO users (username, password, nameAr, nameEn, email, role, isActive, createdAt) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'المدير العام', 'System Administrator', 'admin@alabasi.es', 'admin', 1, NOW()),
('accountant1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'أحمد محمود', 'Ahmed Mahmoud', 'ahmed@alabasi.es', 'accountant', 1, NOW()),
('accountant2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'فاطمة علي', 'Fatima Ali', 'fatima@alabasi.es', 'accountant', 1, NOW()),
('user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'محمد خالد', 'Mohammed Khaled', 'mohammed@alabasi.es', 'user', 1, NOW()),
('user2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'سارة أحمد', 'Sarah Ahmed', 'sarah@alabasi.es', 'user', 1, NOW());

-- ============================================
-- 2. الفروع (Branches)
-- ============================================

-- حذف البيانات القديمة
TRUNCATE TABLE branches;

-- إضافة فروع تجريبية
INSERT INTO branches (code, nameAr, nameEn, address, phone, isActive, createdAt, createdBy) VALUES
('BR001', 'الفرع الرئيسي', 'Main Branch', 'شارع الملك فهد، الرياض، المملكة العربية السعودية', '+966112345678', 1, NOW(), 1),
('BR002', 'فرع جدة', 'Jeddah Branch', 'شارع الأمير محمد، جدة، المملكة العربية السعودية', '+966122345678', 1, NOW(), 1),
('BR003', 'فرع الدمام', 'Dammam Branch', 'شارع الخليج، الدمام، المملكة العربية السعودية', '+966132345678', 1, NOW(), 1),
('BR004', 'فرع مكة', 'Makkah Branch', 'شارع إبراهيم الخليل، مكة المكرمة، المملكة العربية السعودية', '+966122456789', 1, NOW(), 1);

-- ============================================
-- 3. الحسابات الرئيسية (Main Accounts)
-- ============================================

-- حذف البيانات القديمة
TRUNCATE TABLE accounts;

-- إضافة الحسابات الرئيسية (المستوى الأول)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('1', 'الأصول', 'Assets', 'asset', NULL, 1, 1, NOW(), 1),
('2', 'الخصوم', 'Liabilities', 'liability', NULL, 1, 1, NOW(), 1),
('3', 'حقوق الملكية', 'Equity', 'equity', NULL, 1, 1, NOW(), 1),
('4', 'الإيرادات', 'Revenue', 'revenue', NULL, 1, 1, NOW(), 1),
('5', 'المصروفات', 'Expenses', 'expense', NULL, 1, 1, NOW(), 1);

-- الأصول المتداولة (المستوى الثاني)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('11', 'الأصول المتداولة', 'Current Assets', 'asset', 1, 2, 1, NOW(), 1),
('12', 'الأصول الثابتة', 'Fixed Assets', 'asset', 1, 2, 1, NOW(), 1),
('13', 'الأصول الأخرى', 'Other Assets', 'asset', 1, 2, 1, NOW(), 1);

-- حسابات الأصول المتداولة (المستوى الثالث)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('111', 'النقدية', 'Cash', 'asset', 6, 3, 1, NOW(), 1),
('1111', 'الصندوق الرئيسي', 'Main Cash Box', 'asset', 9, 4, 1, NOW(), 1),
('1112', 'صندوق الفرع - جدة', 'Jeddah Branch Cash', 'asset', 9, 4, 1, NOW(), 1),
('1113', 'صندوق الفرع - الدمام', 'Dammam Branch Cash', 'asset', 9, 4, 1, NOW(), 1),

('112', 'البنوك', 'Banks', 'asset', 6, 3, 1, NOW(), 1),
('1121', 'البنك الأهلي - حساب جاري', 'Al Ahli Bank - Current Account', 'asset', 13, 4, 1, NOW(), 1),
('1122', 'بنك الراجحي - حساب جاري', 'Al Rajhi Bank - Current Account', 'asset', 13, 4, 1, NOW(), 1),
('1123', 'بنك الرياض - حساب توفير', 'Riyadh Bank - Savings Account', 'asset', 13, 4, 1, NOW(), 1),

('113', 'العملاء', 'Accounts Receivable', 'asset', 6, 3, 1, NOW(), 1),
('114', 'أوراق القبض', 'Notes Receivable', 'asset', 6, 3, 1, NOW(), 1),
('115', 'المخزون', 'Inventory', 'asset', 6, 3, 1, NOW(), 1),
('1151', 'مخزون المواد الخام', 'Raw Materials Inventory', 'asset', 18, 4, 1, NOW(), 1),
('1152', 'مخزون البضاعة الجاهزة', 'Finished Goods Inventory', 'asset', 18, 4, 1, NOW(), 1),
('1153', 'مخزون قطع الغيار', 'Spare Parts Inventory', 'asset', 18, 4, 1, NOW(), 1);

-- حسابات الأصول الثابتة (المستوى الثالث)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('121', 'الأراضي', 'Land', 'asset', 7, 3, 1, NOW(), 1),
('122', 'المباني', 'Buildings', 'asset', 7, 3, 1, NOW(), 1),
('123', 'الأثاث والمعدات المكتبية', 'Furniture & Office Equipment', 'asset', 7, 3, 1, NOW(), 1),
('124', 'السيارات', 'Vehicles', 'asset', 7, 3, 1, NOW(), 1),
('125', 'الأجهزة والمعدات', 'Machinery & Equipment', 'asset', 7, 3, 1, NOW(), 1),
('126', 'مجمع الإهلاك', 'Accumulated Depreciation', 'asset', 7, 3, 1, NOW(), 1);

-- الخصوم (المستوى الثاني)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('21', 'الخصوم المتداولة', 'Current Liabilities', 'liability', 2, 2, 1, NOW(), 1),
('22', 'الخصوم طويلة الأجل', 'Long-term Liabilities', 'liability', 2, 2, 1, NOW(), 1);

-- حسابات الخصوم المتداولة (المستوى الثالث)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('211', 'الموردون', 'Accounts Payable', 'liability', 29, 3, 1, NOW(), 1),
('212', 'أوراق الدفع', 'Notes Payable', 'liability', 29, 3, 1, NOW(), 1),
('213', 'القروض قصيرة الأجل', 'Short-term Loans', 'liability', 29, 3, 1, NOW(), 1),
('214', 'الرواتب المستحقة', 'Salaries Payable', 'liability', 29, 3, 1, NOW(), 1),
('215', 'الضرائب المستحقة', 'Taxes Payable', 'liability', 29, 3, 1, NOW(), 1);

-- حسابات الخصوم طويلة الأجل (المستوى الثالث)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('221', 'القروض طويلة الأجل', 'Long-term Loans', 'liability', 30, 3, 1, NOW(), 1),
('222', 'السندات', 'Bonds Payable', 'liability', 30, 3, 1, NOW(), 1);

-- حقوق الملكية (المستوى الثاني)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('31', 'رأس المال', 'Capital', 'equity', 3, 2, 1, NOW(), 1),
('32', 'الأرباح المحتجزة', 'Retained Earnings', 'equity', 3, 2, 1, NOW(), 1),
('33', 'الاحتياطيات', 'Reserves', 'equity', 3, 2, 1, NOW(), 1),
('34', 'أرباح العام الحالي', 'Current Year Profit', 'equity', 3, 2, 1, NOW(), 1);

-- الإيرادات (المستوى الثاني)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('41', 'إيرادات المبيعات', 'Sales Revenue', 'revenue', 4, 2, 1, NOW(), 1),
('42', 'إيرادات الخدمات', 'Service Revenue', 'revenue', 4, 2, 1, NOW(), 1),
('43', 'إيرادات أخرى', 'Other Revenue', 'revenue', 4, 2, 1, NOW(), 1);

-- حسابات الإيرادات (المستوى الثالث)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('411', 'مبيعات محلية', 'Local Sales', 'revenue', 41, 3, 1, NOW(), 1),
('412', 'مبيعات تصدير', 'Export Sales', 'revenue', 41, 3, 1, NOW(), 1),
('413', 'خصم مسموح به', 'Sales Discounts', 'revenue', 41, 3, 1, NOW(), 1),
('414', 'مردودات المبيعات', 'Sales Returns', 'revenue', 41, 3, 1, NOW(), 1);

-- المصروفات (المستوى الثاني)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('51', 'تكلفة المبيعات', 'Cost of Goods Sold', 'expense', 5, 2, 1, NOW(), 1),
('52', 'مصروفات إدارية', 'Administrative Expenses', 'expense', 5, 2, 1, NOW(), 1),
('53', 'مصروفات تسويقية', 'Marketing Expenses', 'expense', 5, 2, 1, NOW(), 1),
('54', 'مصروفات مالية', 'Financial Expenses', 'expense', 5, 2, 1, NOW(), 1);

-- حسابات المصروفات الإدارية (المستوى الثالث)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('521', 'الرواتب والأجور', 'Salaries & Wages', 'expense', 50, 3, 1, NOW(), 1),
('522', 'الإيجارات', 'Rent Expense', 'expense', 50, 3, 1, NOW(), 1),
('523', 'الكهرباء والماء', 'Utilities', 'expense', 50, 3, 1, NOW(), 1),
('524', 'الاتصالات', 'Communications', 'expense', 50, 3, 1, NOW(), 1),
('525', 'القرطاسية', 'Stationery', 'expense', 50, 3, 1, NOW(), 1),
('526', 'الصيانة', 'Maintenance', 'expense', 50, 3, 1, NOW(), 1),
('527', 'التأمينات', 'Insurance', 'expense', 50, 3, 1, NOW(), 1);

-- حسابات المصروفات التسويقية (المستوى الثالث)
INSERT INTO accounts (code, nameAr, nameEn, type, parentId, level, isActive, createdAt, createdBy) VALUES
('531', 'الإعلانات', 'Advertising', 'expense', 51, 3, 1, NOW(), 1),
('532', 'العمولات', 'Commissions', 'expense', 51, 3, 1, NOW(), 1),
('533', 'مصاريف السفر', 'Travel Expenses', 'expense', 51, 3, 1, NOW(), 1);

-- ============================================
-- 4. الحسابات التحليلية (Analytical Accounts)
-- ============================================

-- حذف البيانات القديمة
TRUNCATE TABLE analyticalAccounts;

-- إضافة عملاء تجريبيين
INSERT INTO analyticalAccounts (code, nameAr, nameEn, accountId, type, phone, email, address, isActive, createdAt, createdBy) VALUES
('C001', 'شركة النجاح التجارية', 'Al-Najah Trading Company', 17, 'customer', '+966501234567', 'info@alnajah.com', 'الرياض، المملكة العربية السعودية', 1, NOW(), 1),
('C002', 'مؤسسة الأمل للمقاولات', 'Al-Amal Contracting Est.', 17, 'customer', '+966502234567', 'contact@alamal.com', 'جدة، المملكة العربية السعودية', 1, NOW(), 1),
('C003', 'شركة الفجر للتجارة', 'Al-Fajr Trading Co.', 17, 'customer', '+966503234567', 'sales@alfajr.com', 'الدمام، المملكة العربية السعودية', 1, NOW(), 1),
('C004', 'مؤسسة البناء الحديث', 'Modern Construction Est.', 17, 'customer', '+966504234567', 'info@modernbuild.com', 'الرياض، المملكة العربية السعودية', 1, NOW(), 1),
('C005', 'شركة التقنية المتقدمة', 'Advanced Tech Company', 17, 'customer', '+966505234567', 'contact@advtech.com', 'جدة، المملكة العربية السعودية', 1, NOW(), 1);

-- إضافة موردين تجريبيين
INSERT INTO analyticalAccounts (code, nameAr, nameEn, accountId, type, phone, email, address, isActive, createdAt, createdBy) VALUES
('S001', 'شركة المواد الأولية المحدودة', 'Raw Materials Ltd.', 31, 'supplier', '+966511234567', 'sales@rawmaterials.com', 'الرياض، المملكة العربية السعودية', 1, NOW(), 1),
('S002', 'مؤسسة الإمدادات الصناعية', 'Industrial Supplies Est.', 31, 'supplier', '+966512234567', 'info@indsupplies.com', 'جدة، المملكة العربية السعودية', 1, NOW(), 1),
('S003', 'شركة المعدات والأدوات', 'Equipment & Tools Co.', 31, 'supplier', '+966513234567', 'contact@equiptools.com', 'الدمام، المملكة العربية السعودية', 1, NOW(), 1),
('S004', 'مؤسسة التوريدات العامة', 'General Supplies Est.', 31, 'supplier', '+966514234567', 'sales@gensupplies.com', 'الرياض، المملكة العربية السعودية', 1, NOW(), 1),
('S005', 'شركة الاستيراد والتصدير', 'Import Export Company', 31, 'supplier', '+966515234567', 'info@impexp.com', 'جدة، المملكة العربية السعودية', 1, NOW(), 1);

-- إضافة موظفين تجريبيين
INSERT INTO analyticalAccounts (code, nameAr, nameEn, accountId, type, phone, email, address, isActive, createdAt, createdBy) VALUES
('E001', 'أحمد محمد العلي', 'Ahmed Mohammed Al-Ali', 34, 'employee', '+966521234567', 'ahmed.ali@alabasi.es', 'الرياض، المملكة العربية السعودية', 1, NOW(), 1),
('E002', 'فاطمة خالد السعيد', 'Fatima Khaled Al-Saeed', 34, 'employee', '+966522234567', 'fatima.saeed@alabasi.es', 'جدة، المملكة العربية السعودية', 1, NOW(), 1),
('E003', 'محمد عبدالله الأحمد', 'Mohammed Abdullah Al-Ahmad', 34, 'employee', '+966523234567', 'mohammed.ahmad@alabasi.es', 'الدمام، المملكة العربية السعودية', 1, NOW(), 1),
('E004', 'سارة علي المحمد', 'Sarah Ali Al-Mohammed', 34, 'employee', '+966524234567', 'sarah.mohammed@alabasi.es', 'الرياض، المملكة العربية السعودية', 1, NOW(), 1),
('E005', 'خالد أحمد الخالد', 'Khaled Ahmed Al-Khaled', 34, 'employee', '+966525234567', 'khaled.khaled@alabasi.es', 'جدة، المملكة العربية السعودية', 1, NOW(), 1);

-- ============================================
-- 5. القيود اليومية (Journal Entries)
-- ============================================

-- حذف البيانات القديمة
TRUNCATE TABLE journalEntries;
TRUNCATE TABLE journals;

-- قيد افتتاحي - رأس المال
INSERT INTO journals (journalNumber, date, description, totalDebit, totalCredit, status, postedAt, postedBy, createdAt, createdBy) VALUES
('JE-2025-001', '2025-01-01', 'قيد افتتاحي - رأس المال', 1000000.00, 1000000.00, 'posted', NOW(), 1, NOW(), 1);

INSERT INTO journalEntries (journalId, accountId, debit, credit, description) VALUES
(1, 10, 500000.00, 0.00, 'رصيد افتتاحي - الصندوق الرئيسي'),
(1, 14, 300000.00, 0.00, 'رصيد افتتاحي - البنك الأهلي'),
(1, 15, 200000.00, 0.00, 'رصيد افتتاحي - بنك الراجحي'),
(1, 38, 0.00, 1000000.00, 'رأس المال الافتتاحي');

-- قيد شراء أصول ثابتة
INSERT INTO journals (journalNumber, date, description, totalDebit, totalCredit, status, postedAt, postedBy, createdAt, createdBy) VALUES
('JE-2025-002', '2025-01-05', 'شراء أثاث مكتبي', 50000.00, 50000.00, 'posted', NOW(), 1, NOW(), 1);

INSERT INTO journalEntries (journalId, accountId, debit, credit, description) VALUES
(2, 25, 50000.00, 0.00, 'شراء أثاث مكتبي'),
(2, 14, 0.00, 50000.00, 'دفع من البنك الأهلي');

-- قيد شراء بضاعة
INSERT INTO journals (journalNumber, date, description, totalDebit, totalCredit, status, postedAt, postedBy, createdAt, createdBy) VALUES
('JE-2025-003', '2025-01-10', 'شراء بضاعة من شركة المواد الأولية', 150000.00, 150000.00, 'posted', NOW(), 1, NOW(), 1);

INSERT INTO journalEntries (journalId, accountId, analyticalAccountId, debit, credit, description) VALUES
(3, 20, NULL, 150000.00, 0.00, 'شراء مواد خام'),
(3, 31, 6, 0.00, 150000.00, 'شراء على الحساب من شركة المواد الأولية');

-- قيد بيع بضاعة نقداً
INSERT INTO journals (journalNumber, date, description, totalDebit, totalCredit, status, postedAt, postedBy, createdAt, createdBy) VALUES
('JE-2025-004', '2025-01-15', 'بيع بضاعة نقداً', 200000.00, 200000.00, 'posted', NOW(), 1, NOW(), 1);

INSERT INTO journalEntries (journalId, accountId, debit, credit, description) VALUES
(4, 10, 200000.00, 0.00, 'تحصيل نقدي من مبيعات'),
(4, 45, 0.00, 200000.00, 'مبيعات محلية');

-- قيد تكلفة البضاعة المباعة
INSERT INTO journals (journalNumber, date, description, totalDebit, totalCredit, status, postedAt, postedBy, createdAt, createdBy) VALUES
('JE-2025-005', '2025-01-15', 'تكلفة البضاعة المباعة', 120000.00, 120000.00, 'posted', NOW(), 1, NOW(), 1);

INSERT INTO journalEntries (journalId, accountId, debit, credit, description) VALUES
(5, 49, 120000.00, 0.00, 'تكلفة البضاعة المباعة'),
(5, 20, 0.00, 120000.00, 'خصم من المخزون');

-- قيد بيع على الحساب
INSERT INTO journals (journalNumber, date, description, totalDebit, totalCredit, status, postedAt, postedBy, createdAt, createdBy) VALUES
('JE-2025-006', '2025-01-20', 'بيع بضاعة على الحساب لشركة النجاح', 180000.00, 180000.00, 'posted', NOW(), 1, NOW(), 1);

INSERT INTO journalEntries (journalId, accountId, analyticalAccountId, debit, credit, description) VALUES
(6, 17, 1, 180000.00, 0.00, 'مبيعات آجلة - شركة النجاح'),
(6, 45, NULL, 0.00, 180000.00, 'مبيعات محلية');

-- قيد تحصيل من عميل
INSERT INTO journals (journalNumber, date, description, totalDebit, totalCredit, status, postedAt, postedBy, createdAt, createdBy) VALUES
('JE-2025-007', '2025-01-25', 'تحصيل من شركة النجاح', 100000.00, 100000.00, 'posted', NOW(), 1, NOW(), 1);

INSERT INTO journalEntries (journalId, accountId, analyticalAccountId, debit, credit, description) VALUES
(7, 14, NULL, 100000.00, 0.00, 'إيداع في البنك الأهلي'),
(7, 17, 1, 0.00, 100000.00, 'تحصيل من شركة النجاح');

-- قيد دفع رواتب
INSERT INTO journals (journalNumber, date, description, totalDebit, totalCredit, status, postedAt, postedBy, createdAt, createdBy) VALUES
('JE-2025-008', '2025-01-31', 'دفع رواتب شهر يناير', 85000.00, 85000.00, 'posted', NOW(), 1, NOW(), 1);

INSERT INTO journalEntries (journalId, accountId, debit, credit, description) VALUES
(8, 54, 85000.00, 0.00, 'رواتب وأجور شهر يناير'),
(8, 14, 0.00, 85000.00, 'دفع من البنك الأهلي');

-- قيد مصروفات إدارية
INSERT INTO journals (journalNumber, date, description, totalDebit, totalCredit, status, postedAt, postedBy, createdAt, createdBy) VALUES
('JE-2025-009', '2025-01-31', 'مصروفات إدارية متنوعة', 25000.00, 25000.00, 'posted', NOW(), 1, NOW(), 1);

INSERT INTO journalEntries (journalId, accountId, debit, credit, description) VALUES
(9, 55, 8000.00, 0.00, 'إيجار شهر يناير'),
(9, 56, 5000.00, 0.00, 'كهرباء وماء'),
(9, 57, 3000.00, 0.00, 'اتصالات'),
(9, 58, 2000.00, 0.00, 'قرطاسية'),
(9, 59, 4000.00, 0.00, 'صيانة'),
(9, 60, 3000.00, 0.00, 'تأمينات'),
(9, 10, 0.00, 25000.00, 'دفع نقدي من الصندوق');

-- قيد دفع لمورد
INSERT INTO journals (journalNumber, date, description, totalDebit, totalCredit, status, postedAt, postedBy, createdAt, createdBy) VALUES
('JE-2025-010', '2025-02-05', 'دفع لشركة المواد الأولية', 75000.00, 75000.00, 'posted', NOW(), 1, NOW(), 1);

INSERT INTO journalEntries (journalId, accountId, analyticalAccountId, debit, credit, description) VALUES
(10, 31, 6, 75000.00, 0.00, 'سداد جزء من المديونية'),
(10, 15, NULL, 0.00, 75000.00, 'دفع من بنك الراجحي');

-- ============================================
-- تفعيل فحص المفاتيح الخارجية
-- ============================================
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- رسائل النجاح
-- ============================================
SELECT '✅ تم إضافة البيانات التجريبية بنجاح!' as status;
SELECT CONCAT('✅ عدد المستخدمين: ', COUNT(*), ' مستخدم') as users_count FROM users;
SELECT CONCAT('✅ عدد الفروع: ', COUNT(*), ' فرع') as branches_count FROM branches;
SELECT CONCAT('✅ عدد الحسابات: ', COUNT(*), ' حساب') as accounts_count FROM accounts;
SELECT CONCAT('✅ عدد الحسابات التحليلية: ', COUNT(*), ' حساب') as analytical_count FROM analyticalAccounts;
SELECT CONCAT('✅ عدد القيود: ', COUNT(*), ' قيد') as journals_count FROM journals;
SELECT CONCAT('✅ عدد سطور القيود: ', COUNT(*), ' سطر') as entries_count FROM journalEntries;
SELECT '✅ يمكنك الآن استعراض النظام بالبيانات التجريبية الكاملة!' as final_message;

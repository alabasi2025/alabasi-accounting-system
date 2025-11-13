-- إضافة جدول الوحدات
CREATE TABLE IF NOT EXISTS units (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    nameAr VARCHAR(255) NOT NULL,
    nameEn VARCHAR(255),
    description TEXT,
    isActive BOOLEAN DEFAULT 1,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    FOREIGN KEY (createdBy) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إضافة عمود unitId لجدول companies
ALTER TABLE companies ADD COLUMN unitId INT AFTER id;
ALTER TABLE companies ADD FOREIGN KEY (unitId) REFERENCES units(id);

-- إدراج وحدة افتراضية
INSERT INTO units (code, nameAr, nameEn, description, isActive, createdBy) 
VALUES ('UNIT001', 'الوحدة الرئيسية', 'Main Unit', 'الوحدة الافتراضية للنظام', 1, 1);

-- تحديث المؤسسات الموجودة لربطها بالوحدة الافتراضية
UPDATE companies SET unitId = 1 WHERE unitId IS NULL;

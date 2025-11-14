-- إضافة جداول المخزون للنظام القديم

-- جدول فئات الأصناف
CREATE TABLE IF NOT EXISTS `item_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `parentId` INT DEFAULT NULL,
  `isActive` BOOLEAN DEFAULT TRUE,
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`parentId`) REFERENCES `item_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الأصناف
CREATE TABLE IF NOT EXISTS `items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) UNIQUE NOT NULL,
  `nameAr` VARCHAR(200) NOT NULL,
  `nameEn` VARCHAR(200),
  `categoryId` INT,
  `unit` VARCHAR(50),
  `purchasePrice` DECIMAL(15,2) DEFAULT 0,
  `salePrice` DECIMAL(15,2) DEFAULT 0,
  `reorderLevel` INT DEFAULT 0,
  `minStock` INT DEFAULT 0,
  `isActive` BOOLEAN DEFAULT TRUE,
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`categoryId`) REFERENCES `item_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول حركات المخزون
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `movementNumber` VARCHAR(50) UNIQUE NOT NULL,
  `movementDate` DATE NOT NULL,
  `movementType` ENUM('in', 'out', 'transfer') NOT NULL,
  `itemId` INT NOT NULL,
  `warehouseId` INT NOT NULL,
  `quantity` INT NOT NULL,
  `unitPrice` DECIMAL(15,2) DEFAULT 0,
  `totalValue` DECIMAL(15,2) DEFAULT 0,
  `notes` TEXT,
  `createdBy` INT,
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`itemId`) REFERENCES `items`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`warehouseId`) REFERENCES `warehouses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`createdBy`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول رصيد المخزون
CREATE TABLE IF NOT EXISTS `stock_balance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `itemId` INT NOT NULL,
  `warehouseId` INT NOT NULL,
  `quantity` INT DEFAULT 0,
  `averageCost` DECIMAL(15,2) DEFAULT 0,
  `totalValue` DECIMAL(15,2) DEFAULT 0,
  `lastUpdated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `item_warehouse` (`itemId`, `warehouseId`),
  FOREIGN KEY (`itemId`) REFERENCES `items`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`warehouseId`) REFERENCES `warehouses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدراج فئات تجريبية
INSERT IGNORE INTO `item_categories` (`id`, `name`, `description`) VALUES
(1, 'مواد خام', 'المواد الخام الأساسية'),
(2, 'منتجات تامة', 'المنتجات الجاهزة للبيع'),
(3, 'قطع غيار', 'قطع الغيار والصيانة');

SELECT '✅ تم إضافة جداول المخزون بنجاح!' AS result;

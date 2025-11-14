-- ============================================
-- المخطط الموسع - الوحدات الجديدة
-- Expanded Schema - New Modules
-- 21 جدول جديد + 3 جداول موجودة
-- ============================================

-- ============================================
-- 1. وحدة إدارة الطاقة (Energy Management Module)
-- 5 جداول
-- ============================================

-- محطات الطاقة
CREATE TABLE stations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    nameAr VARCHAR(200) NOT NULL,
    nameEn VARCHAR(200),
    stationType ENUM('electric', 'water', 'gas', 'solar') NOT NULL,
    location VARCHAR(500),
    capacity DECIMAL(15,2),
    branchId INT,
    isActive BOOLEAN DEFAULT TRUE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (branchId) REFERENCES branches(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_station_type (stationType),
    INDEX idx_station_branch (branchId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- الاشتراكات
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscriptionNumber VARCHAR(50) NOT NULL UNIQUE,
    stationId INT NOT NULL,
    customerId INT,
    customerName VARCHAR(200),
    customerPhone VARCHAR(20),
    customerAddress TEXT,
    subscriptionType ENUM('residential', 'commercial', 'industrial', 'government') NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE,
    status ENUM('active', 'suspended', 'terminated') DEFAULT 'active',
    meterNumber VARCHAR(50),
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (stationId) REFERENCES stations(id),
    FOREIGN KEY (customerId) REFERENCES customers(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_subscription_station (stationId),
    INDEX idx_subscription_customer (customerId),
    INDEX idx_subscription_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- قراءات العدادات
CREATE TABLE meter_readings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscriptionId INT NOT NULL,
    readingDate DATE NOT NULL,
    previousReading DECIMAL(15,2) NOT NULL DEFAULT 0,
    currentReading DECIMAL(15,2) NOT NULL,
    consumption DECIMAL(15,2) GENERATED ALWAYS AS (currentReading - previousReading) STORED,
    readingType ENUM('manual', 'automatic', 'estimated') DEFAULT 'manual',
    readerName VARCHAR(100),
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (subscriptionId) REFERENCES subscriptions(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_reading_subscription (subscriptionId),
    INDEX idx_reading_date (readingDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- فواتير الطاقة
CREATE TABLE energy_invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoiceNumber VARCHAR(50) NOT NULL UNIQUE,
    subscriptionId INT NOT NULL,
    meterReadingId INT,
    invoiceDate DATE NOT NULL,
    dueDate DATE,
    consumption DECIMAL(15,2) NOT NULL,
    unitPrice DECIMAL(15,4) NOT NULL,
    consumptionAmount DECIMAL(15,2) GENERATED ALWAYS AS (consumption * unitPrice) STORED,
    fixedCharges DECIMAL(15,2) DEFAULT 0,
    taxes DECIMAL(15,2) DEFAULT 0,
    totalAmount DECIMAL(15,2) NOT NULL,
    paidAmount DECIMAL(15,2) DEFAULT 0,
    remainingAmount DECIMAL(15,2) GENERATED ALWAYS AS (totalAmount - paidAmount) STORED,
    status ENUM('draft', 'issued', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    paymentDate DATE,
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (subscriptionId) REFERENCES subscriptions(id),
    FOREIGN KEY (meterReadingId) REFERENCES meter_readings(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_energy_invoice_subscription (subscriptionId),
    INDEX idx_energy_invoice_status (status),
    INDEX idx_energy_invoice_date (invoiceDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- تقارير الاستهلاك
CREATE TABLE consumption_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reportType ENUM('daily', 'weekly', 'monthly', 'yearly') NOT NULL,
    stationId INT,
    subscriptionId INT,
    reportDate DATE NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE NOT NULL,
    totalConsumption DECIMAL(15,2) NOT NULL,
    totalAmount DECIMAL(15,2) NOT NULL,
    averageConsumption DECIMAL(15,2),
    peakConsumption DECIMAL(15,2),
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    createdBy INT,
    FOREIGN KEY (stationId) REFERENCES stations(id),
    FOREIGN KEY (subscriptionId) REFERENCES subscriptions(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    INDEX idx_report_type (reportType),
    INDEX idx_report_station (stationId),
    INDEX idx_report_date (reportDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. وحدة الفوترة (Billing Module)
-- 4 جداول
-- ============================================

-- دورات الفوترة
CREATE TABLE billing_cycles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    nameAr VARCHAR(200) NOT NULL,
    nameEn VARCHAR(200),
    cycleType ENUM('monthly', 'quarterly', 'semi-annual', 'annual') NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE NOT NULL,
    dueDate DATE NOT NULL,
    status ENUM('open', 'closed', 'processing') DEFAULT 'open',
    totalInvoices INT DEFAULT 0,
    totalAmount DECIMAL(15,2) DEFAULT 0,
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_cycle_type (cycleType),
    INDEX idx_cycle_status (status),
    INDEX idx_cycle_dates (startDate, endDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- الفواتير العامة (مختلفة عن فواتير المبيعات/المشتريات)
CREATE TABLE general_invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoiceNumber VARCHAR(50) NOT NULL UNIQUE,
    invoiceType ENUM('service', 'subscription', 'rental', 'utility', 'other') NOT NULL,
    billingCycleId INT,
    customerId INT,
    customerName VARCHAR(200),
    invoiceDate DATE NOT NULL,
    dueDate DATE NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    taxAmount DECIMAL(15,2) DEFAULT 0,
    discountAmount DECIMAL(15,2) DEFAULT 0,
    totalAmount DECIMAL(15,2) NOT NULL,
    paidAmount DECIMAL(15,2) DEFAULT 0,
    remainingAmount DECIMAL(15,2) GENERATED ALWAYS AS (totalAmount - paidAmount) STORED,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    paymentMethod ENUM('cash', 'check', 'bank_transfer', 'credit_card', 'online') NULL,
    paymentDate DATE,
    description TEXT,
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (billingCycleId) REFERENCES billing_cycles(id),
    FOREIGN KEY (customerId) REFERENCES customers(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_general_invoice_type (invoiceType),
    INDEX idx_general_invoice_customer (customerId),
    INDEX idx_general_invoice_status (status),
    INDEX idx_general_invoice_date (invoiceDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- المدفوعات العامة
CREATE TABLE general_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paymentNumber VARCHAR(50) NOT NULL UNIQUE,
    invoiceId INT,
    invoiceType ENUM('sales', 'purchase', 'general', 'energy') NOT NULL,
    customerId INT,
    paymentDate DATE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    paymentMethod ENUM('cash', 'check', 'bank_transfer', 'credit_card', 'online') NOT NULL,
    checkNumber VARCHAR(50),
    bankName VARCHAR(200),
    transactionReference VARCHAR(100),
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (customerId) REFERENCES customers(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_payment_invoice (invoiceId),
    INDEX idx_payment_customer (customerId),
    INDEX idx_payment_date (paymentDate),
    INDEX idx_payment_method (paymentMethod)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إشعارات الفوترة
CREATE TABLE billing_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notificationType ENUM('invoice_due', 'payment_received', 'overdue_reminder', 'payment_failed') NOT NULL,
    invoiceId INT,
    customerId INT,
    recipientEmail VARCHAR(320),
    recipientPhone VARCHAR(20),
    subject VARCHAR(500),
    message TEXT,
    sentDate TIMESTAMP,
    status ENUM('pending', 'sent', 'failed', 'read') DEFAULT 'pending',
    deliveryMethod ENUM('email', 'sms', 'both') NOT NULL,
    attempts INT DEFAULT 0,
    lastAttempt TIMESTAMP,
    errorMessage TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    createdBy INT,
    FOREIGN KEY (customerId) REFERENCES customers(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    INDEX idx_notification_type (notificationType),
    INDEX idx_notification_customer (customerId),
    INDEX idx_notification_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. وحدة IoT (IoT Module)
-- 4 جداول
-- ============================================

-- أجهزة IoT
CREATE TABLE iot_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deviceCode VARCHAR(50) NOT NULL UNIQUE,
    deviceName VARCHAR(200) NOT NULL,
    deviceType ENUM('sensor', 'meter', 'controller', 'gateway', 'camera', 'other') NOT NULL,
    manufacturer VARCHAR(200),
    model VARCHAR(200),
    serialNumber VARCHAR(100) UNIQUE,
    macAddress VARCHAR(50),
    ipAddress VARCHAR(50),
    location VARCHAR(500),
    stationId INT,
    subscriptionId INT,
    status ENUM('active', 'inactive', 'maintenance', 'offline') DEFAULT 'active',
    lastOnline TIMESTAMP,
    installationDate DATE,
    warrantyExpiry DATE,
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (stationId) REFERENCES stations(id),
    FOREIGN KEY (subscriptionId) REFERENCES subscriptions(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_device_type (deviceType),
    INDEX idx_device_status (status),
    INDEX idx_device_station (stationId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- قراءات الأجهزة
CREATE TABLE device_readings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deviceId INT NOT NULL,
    readingTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    readingType VARCHAR(50) NOT NULL,
    readingValue DECIMAL(15,4) NOT NULL,
    unit VARCHAR(20),
    qualityIndicator ENUM('good', 'fair', 'poor', 'error') DEFAULT 'good',
    rawData JSON,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deviceId) REFERENCES iot_devices(id),
    INDEX idx_reading_device (deviceId),
    INDEX idx_reading_time (readingTime),
    INDEX idx_reading_type (readingType)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- تنبيهات الأجهزة
CREATE TABLE device_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deviceId INT NOT NULL,
    alertType ENUM('threshold_exceeded', 'device_offline', 'low_battery', 'malfunction', 'security', 'other') NOT NULL,
    severity ENUM('info', 'warning', 'critical', 'emergency') NOT NULL,
    alertMessage TEXT NOT NULL,
    alertTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    acknowledgedBy INT,
    acknowledgedAt TIMESTAMP,
    resolvedBy INT,
    resolvedAt TIMESTAMP,
    status ENUM('new', 'acknowledged', 'in_progress', 'resolved', 'ignored') DEFAULT 'new',
    resolution TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deviceId) REFERENCES iot_devices(id),
    FOREIGN KEY (acknowledgedBy) REFERENCES users(id),
    FOREIGN KEY (resolvedBy) REFERENCES users(id),
    INDEX idx_alert_device (deviceId),
    INDEX idx_alert_type (alertType),
    INDEX idx_alert_severity (severity),
    INDEX idx_alert_status (status),
    INDEX idx_alert_time (alertTime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- أوامر الأجهزة
CREATE TABLE device_commands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deviceId INT NOT NULL,
    commandType ENUM('read', 'write', 'reset', 'calibrate', 'update', 'reboot', 'custom') NOT NULL,
    commandName VARCHAR(100) NOT NULL,
    commandParameters JSON,
    sentAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    executedAt TIMESTAMP,
    status ENUM('pending', 'sent', 'executed', 'failed', 'timeout') DEFAULT 'pending',
    responseData JSON,
    errorMessage TEXT,
    sentBy INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deviceId) REFERENCES iot_devices(id),
    FOREIGN KEY (sentBy) REFERENCES users(id),
    INDEX idx_command_device (deviceId),
    INDEX idx_command_type (commandType),
    INDEX idx_command_status (status),
    INDEX idx_command_sent (sentAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. وحدة الاتصالات (Communications Module)
-- 4 جداول
-- ============================================

-- الرسائل
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    messageType ENUM('internal', 'customer', 'supplier', 'broadcast') NOT NULL,
    fromUserId INT,
    toUserId INT,
    toCustomerId INT,
    toSupplierId INT,
    subject VARCHAR(500),
    messageBody TEXT NOT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    status ENUM('draft', 'sent', 'delivered', 'read', 'failed') DEFAULT 'draft',
    sentAt TIMESTAMP,
    deliveredAt TIMESTAMP,
    readAt TIMESTAMP,
    attachments JSON,
    parentMessageId INT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    createdBy INT,
    FOREIGN KEY (fromUserId) REFERENCES users(id),
    FOREIGN KEY (toUserId) REFERENCES users(id),
    FOREIGN KEY (toCustomerId) REFERENCES customers(id),
    FOREIGN KEY (toSupplierId) REFERENCES suppliers(id),
    FOREIGN KEY (parentMessageId) REFERENCES messages(id),
    FOREIGN KEY (createdBy) REFERENCES users(id),
    INDEX idx_message_type (messageType),
    INDEX idx_message_from (fromUserId),
    INDEX idx_message_to (toUserId),
    INDEX idx_message_status (status),
    INDEX idx_message_sent (sentAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- الإشعارات
CREATE TABLE system_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notificationType ENUM('system', 'transaction', 'reminder', 'alert', 'approval', 'info') NOT NULL,
    userId INT NOT NULL,
    title VARCHAR(500) NOT NULL,
    message TEXT NOT NULL,
    actionUrl VARCHAR(500),
    actionLabel VARCHAR(100),
    priority ENUM('low', 'normal', 'high') DEFAULT 'normal',
    status ENUM('unread', 'read', 'archived') DEFAULT 'unread',
    readAt TIMESTAMP,
    expiresAt TIMESTAMP,
    metadata JSON,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES users(id),
    INDEX idx_notification_user (userId),
    INDEX idx_notification_type (notificationType),
    INDEX idx_notification_status (status),
    INDEX idx_notification_created (createdAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- سجل البريد الإلكتروني
CREATE TABLE email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emailType ENUM('transactional', 'marketing', 'notification', 'alert', 'report') NOT NULL,
    fromEmail VARCHAR(320) NOT NULL,
    toEmail VARCHAR(320) NOT NULL,
    ccEmails TEXT,
    bccEmails TEXT,
    subject VARCHAR(500) NOT NULL,
    bodyHtml TEXT,
    bodyText TEXT,
    attachments JSON,
    status ENUM('queued', 'sent', 'delivered', 'bounced', 'failed', 'spam') DEFAULT 'queued',
    sentAt TIMESTAMP,
    deliveredAt TIMESTAMP,
    openedAt TIMESTAMP,
    clickedAt TIMESTAMP,
    bouncedAt TIMESTAMP,
    errorMessage TEXT,
    provider VARCHAR(100),
    messageId VARCHAR(200),
    attempts INT DEFAULT 0,
    lastAttempt TIMESTAMP,
    metadata JSON,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    createdBy INT,
    FOREIGN KEY (createdBy) REFERENCES users(id),
    INDEX idx_email_type (emailType),
    INDEX idx_email_to (toEmail),
    INDEX idx_email_status (status),
    INDEX idx_email_sent (sentAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- سجل الرسائل النصية
CREATE TABLE sms_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    smsType ENUM('transactional', 'marketing', 'notification', 'alert', 'otp') NOT NULL,
    toPhone VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('queued', 'sent', 'delivered', 'failed', 'rejected') DEFAULT 'queued',
    sentAt TIMESTAMP,
    deliveredAt TIMESTAMP,
    errorMessage TEXT,
    provider VARCHAR(100),
    messageId VARCHAR(200),
    cost DECIMAL(10,4),
    attempts INT DEFAULT 0,
    lastAttempt TIMESTAMP,
    metadata JSON,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    createdBy INT,
    FOREIGN KEY (createdBy) REFERENCES users(id),
    INDEX idx_sms_type (smsType),
    INDEX idx_sms_phone (toPhone),
    INDEX idx_sms_status (status),
    INDEX idx_sms_sent (sentAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. وحدة الخرائط (Maps Module)
-- 4 جداول
-- ============================================

-- المواقع الجغرافية
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    locationCode VARCHAR(50) NOT NULL UNIQUE,
    locationName VARCHAR(200) NOT NULL,
    locationType ENUM('branch', 'warehouse', 'station', 'customer', 'supplier', 'asset', 'other') NOT NULL,
    referenceId INT,
    referenceType VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    region VARCHAR(100),
    country VARCHAR(100) DEFAULT 'Yemen',
    postalCode VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    altitude DECIMAL(10,2),
    accuracy DECIMAL(10,2),
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_location_type (locationType),
    INDEX idx_location_reference (referenceType, referenceId),
    INDEX idx_location_coordinates (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- طبقات الخريطة
CREATE TABLE map_layers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    layerName VARCHAR(200) NOT NULL,
    layerType ENUM('markers', 'polygons', 'routes', 'heatmap', 'custom') NOT NULL,
    description TEXT,
    layerData JSON NOT NULL,
    isVisible BOOLEAN DEFAULT TRUE,
    displayOrder INT DEFAULT 0,
    style JSON,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_layer_type (layerType),
    INDEX idx_layer_visible (isVisible),
    INDEX idx_layer_order (displayOrder)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- الحدود الجغرافية (Geofences)
CREATE TABLE geo_fences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fenceName VARCHAR(200) NOT NULL,
    fenceType ENUM('circle', 'polygon', 'route') NOT NULL,
    centerLatitude DECIMAL(10,8),
    centerLongitude DECIMAL(11,8),
    radius DECIMAL(10,2),
    polygonCoordinates JSON,
    description TEXT,
    isActive BOOLEAN DEFAULT TRUE,
    alertOnEntry BOOLEAN DEFAULT FALSE,
    alertOnExit BOOLEAN DEFAULT FALSE,
    alertRecipients JSON,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdBy INT,
    updatedBy INT,
    FOREIGN KEY (createdBy) REFERENCES users(id),
    FOREIGN KEY (updatedBy) REFERENCES users(id),
    INDEX idx_fence_type (fenceType),
    INDEX idx_fence_active (isActive),
    INDEX idx_fence_center (centerLatitude, centerLongitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- سجل التتبع
CREATE TABLE tracking_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trackingType ENUM('vehicle', 'asset', 'person', 'device') NOT NULL,
    referenceId INT NOT NULL,
    referenceType VARCHAR(50) NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    altitude DECIMAL(10,2),
    speed DECIMAL(10,2),
    heading DECIMAL(5,2),
    accuracy DECIMAL(10,2),
    trackingTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    batteryLevel INT,
    signalStrength INT,
    additionalData JSON,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tracking_type (trackingType),
    INDEX idx_tracking_reference (referenceType, referenceId),
    INDEX idx_tracking_time (trackingTime),
    INDEX idx_tracking_coordinates (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. وحدة الذكاء الاصطناعي (AI Module)
-- 3 جداول (موجودة بالفعل في بعض المشاريع)
-- ============================================

-- محادثات الذكاء الاصطناعي
CREATE TABLE IF NOT EXISTS ai_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    conversationTitle VARCHAR(500),
    startTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    endTime TIMESTAMP,
    status ENUM('active', 'completed', 'abandoned') DEFAULT 'active',
    totalMessages INT DEFAULT 0,
    context JSON,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES users(id),
    INDEX idx_conversation_user (userId),
    INDEX idx_conversation_status (status),
    INDEX idx_conversation_start (startTime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- سجل الأوامر
CREATE TABLE IF NOT EXISTS command_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversationId INT,
    userId INT NOT NULL,
    commandText TEXT NOT NULL,
    commandType VARCHAR(100),
    responseText TEXT,
    executionTime DECIMAL(10,3),
    success BOOLEAN DEFAULT TRUE,
    errorMessage TEXT,
    metadata JSON,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversationId) REFERENCES ai_conversations(id),
    FOREIGN KEY (userId) REFERENCES users(id),
    INDEX idx_command_conversation (conversationId),
    INDEX idx_command_user (userId),
    INDEX idx_command_type (commandType),
    INDEX idx_command_created (createdAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- الأنماط المتعلمة
CREATE TABLE IF NOT EXISTS learned_patterns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patternType ENUM('user_behavior', 'transaction', 'query', 'error', 'optimization') NOT NULL,
    patternName VARCHAR(200) NOT NULL,
    patternData JSON NOT NULL,
    frequency INT DEFAULT 1,
    confidence DECIMAL(5,4) DEFAULT 0,
    lastOccurrence TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    isActive BOOLEAN DEFAULT TRUE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pattern_type (patternType),
    INDEX idx_pattern_active (isActive),
    INDEX idx_pattern_frequency (frequency),
    INDEX idx_pattern_confidence (confidence)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- النهاية - 21 جدول جديد + 3 جداول AI
-- ============================================

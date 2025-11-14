<?php
/**
 * ملف تجريبي لاختبار نظام التحديثات
 * Test File for Updates System
 * 
 * هذا الملف تم إنشاؤه لاختبار نظام التحديث التلقائي من GitHub
 * This file was created to test the automatic update system from GitHub
 */

echo "<!DOCTYPE html>";
echo "<html lang='ar' dir='rtl'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>اختبار نظام التحديثات - نظام الأباسي</title>";
echo "    <style>";
echo "        body {";
echo "            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;";
echo "            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);";
echo "            min-height: 100vh;";
echo "            display: flex;";
echo "            justify-content: center;";
echo "            align-items: center;";
echo "            margin: 0;";
echo "            padding: 20px;";
echo "        }";
echo "        .container {";
echo "            background: white;";
echo "            border-radius: 20px;";
echo "            padding: 40px;";
echo "            box-shadow: 0 10px 30px rgba(0,0,0,0.3);";
echo "            text-align: center;";
echo "            max-width: 600px;";
echo "        }";
echo "        h1 {";
echo "            color: #667eea;";
echo "            margin-bottom: 20px;";
echo "        }";
echo "        .success-icon {";
echo "            font-size: 80px;";
echo "            color: #28a745;";
echo "            margin-bottom: 20px;";
echo "        }";
echo "        .info {";
echo "            background: #f8f9fa;";
echo "            border-radius: 10px;";
echo "            padding: 20px;";
echo "            margin: 20px 0;";
echo "            text-align: right;";
echo "        }";
echo "        .info-item {";
echo "            margin: 10px 0;";
echo "            padding: 10px;";
echo "            border-right: 4px solid #667eea;";
echo "        }";
echo "        .btn {";
echo "            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);";
echo "            color: white;";
echo "            padding: 12px 30px;";
echo "            border: none;";
echo "            border-radius: 10px;";
echo "            font-size: 16px;";
echo "            cursor: pointer;";
echo "            text-decoration: none;";
echo "            display: inline-block;";
echo "            margin: 10px;";
echo "        }";
echo "        .btn:hover {";
echo "            transform: translateY(-2px);";
echo "            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);";
echo "        }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "    <div class='container'>";
echo "        <div class='success-icon'>✅</div>";
echo "        <h1>نظام التحديثات يعمل بنجاح!</h1>";
echo "        <p>تم تحديث النظام بنجاح من GitHub</p>";
echo "        ";
echo "        <div class='info'>";
echo "            <div class='info-item'>";
echo "                <strong>📅 تاريخ التحديث:</strong> " . date('Y-m-d H:i:s') . "";
echo "            </div>";
echo "            <div class='info-item'>";
echo "                <strong>🔢 رقم الإصدار:</strong> v1.0.1 (تجريبي)";
echo "            </div>";
echo "            <div class='info-item'>";
echo "                <strong>📝 نوع التحديث:</strong> تحديث تجريبي من GitHub";
echo "            </div>";
echo "            <div class='info-item'>";
echo "                <strong>✨ الميزات الجديدة:</strong>";
echo "                <ul style='text-align: right; margin-top: 10px;'>";
echo "                    <li>إصلاح مشكلة النسخ الاحتياطي</li>";
echo "                    <li>تحسين نظام التحديثات</li>";
echo "                    <li>إضافة ملف اختبار التحديثات</li>";
echo "                </ul>";
echo "            </div>";
echo "        </div>";
echo "        ";
echo "        <a href='dashboard.php' class='btn'>العودة للرئيسية</a>";
echo "        <a href='backup-manager.php' class='btn'>إدارة التحديثات</a>";
echo "    </div>";
echo "</body>";
echo "</html>";
?>

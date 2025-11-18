<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار تسجيل الدخول</title>
</head>
<body>
    <h1>اختبار تسجيل الدخول</h1>
    
    <h2>دخول للقاعدة المركزية:</h2>
    <form method="POST" action="/login-process">
        @csrf
        <input type="hidden" name="unit_id" value="main">
        <button type="submit">دخول للقاعدة المركزية</button>
    </form>
    
    <hr>
    
    <h2>دخول لوحدة الحديدة - أعمال الموظفين:</h2>
    <form method="POST" action="/login-process">
        @csrf
        <input type="hidden" name="unit_id" value="1">
        <input type="hidden" name="company_id" value="1">
        <button type="submit">دخول لأعمال الموظفين</button>
    </form>
</body>
</html>

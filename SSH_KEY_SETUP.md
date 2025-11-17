# 🔑 تفعيل مفتاح SSH للنشر التلقائي

## الخطوات:

### 1. تسجيل الدخول إلى السيرفر
```bash
ssh -p 65002 alabasiu@alabasi.es
```

### 2. إنشاء مجلد .ssh (إذا لم يكن موجوداً)
```bash
mkdir -p ~/.ssh
chmod 700 ~/.ssh
```

### 3. إضافة المفتاح العام إلى authorized_keys
```bash
echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQC8ZAM7kwgsYzfAQWjZ1IH+ESiExm3DDC8tSj6bAVF2BDj6euDzTLdiFjlY93tu9B/PncBwa2XFrUOHUT24mIDkBJ3CccuzeCGK/K0B36m3woahv2xyEK+YeVI1VYVL2ji/L4YtJZkQX/SB6quPO2pT51ilHfcznOIR3K8gODV7/K3yt8O2ULgTMnl+sCIr4HO7KMoviIpHh5uRLZv74amxUUxewz8Wog+NQP7EHaUQE29V5HGbleIErq+K5JBzDdUlExA3mof+EHSRMONiP+K3Z4HhQECMco1lgOyW/cxOpF2tMz3Vi8Er2WEw6wheWGr6flNLe+TsvYD+E59H1QltJvd1//7a26fbGLbkjYainACboKHGHfJC6Kzv76qcG92aJ6C+15OYXzMI4HRsSybxoDx5fx64HzjD593s3Ijzf3sr5yfybY64PggzWtvbzcAEWCx7Vh4LYsfkTjaokDLaE+OSwR/gvMjj6OkuDAsC5EgXnJGsf3FTVddjDBnhFKKqsgERcx3XGLYfzVsWxgaPC7bDBKFqcATQwbjSbZAKTRR9aGo8WVEoodVPThHJsq9v1GktbbnzxKKZH7DCOBHlhg5fo+IJJZjA8FOP7ri9UD/cZHEiYlJIriQKDSdOGvjGr1bVg3CORk+/wngdMRnhU9ABSOmzs2m/QrVBeEdEUQ== manus-deploy@alabasi.es" >> ~/.ssh/authorized_keys
```

### 4. ضبط الصلاحيات
```bash
chmod 600 ~/.ssh/authorized_keys
```

### 5. التحقق من التفعيل
اخرج من السيرفر وحاول الاتصال مرة أخرى:
```bash
exit
ssh -i /home/ubuntu/.ssh/manus_alabasi_deploy -p 65002 alabasiu@alabasi.es
```

إذا نجح الاتصال بدون طلب كلمة مرور، فالمفتاح مفعّل! ✅

---

## بعد تفعيل المفتاح:

يمكنك نشر التحديثات تلقائياً بأمر واحد:
```bash
ssh -i /home/ubuntu/.ssh/manus_alabasi_deploy -p 65002 alabasiu@alabasi.es "cd domains/alabasi.es/public_html && git pull origin master && php artisan migrate --force && php artisan cache:clear"
```

---

## ملاحظات:
- تأكد من أن السيرفر يسمح بـ SSH Key Authentication
- إذا كنت تستخدم cPanel، يمكنك إضافة المفتاح من: **SSH Access → Manage SSH Keys**

FROM php:8.1-apache

# تثبيت الإضافات المطلوبة
RUN docker-php-ext-install pdo pdo_mysql mysqli

# تفعيل mod_rewrite
RUN a2enmod rewrite

# نسخ ملفات التطبيق
COPY . /var/www/html/

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# إنشاء مجلدات الرفع
RUN mkdir -p /var/www/html/uploads \
    && mkdir -p /var/www/html/backups \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/backups

# تكوين Apache
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/alabasi.conf \
    && a2enconf alabasi

EXPOSE 80

CMD ["apache2-foreground"]

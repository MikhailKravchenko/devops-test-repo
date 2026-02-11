FROM php:8.2-apache

# Устанавливаем необходимые PHP-расширения
RUN docker-php-ext-install pdo pdo_mysql

# Настраиваем рабочую директорию
WORKDIR /var/www/html

# Копируем исходный код приложения
COPY . /var/www/html

# Открываем стандартный HTTP-порт
EXPOSE 80


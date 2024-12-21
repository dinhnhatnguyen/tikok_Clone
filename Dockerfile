FROM php:apache

COPY php.ini /usr/local/etc/php/

# cài đặt thư viện để tạo ảnh thumbnail từ video
RUN apt-get update && apt-get install -y ffmpeg \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# cài đặt thư viện để sử dụng db Mysql
RUN docker-php-ext-install pdo pdo_mysql
FROM toasterlint/php-apache-mysql:7.4-nowarn
#FROM php:apache
#ARG USER_ID
#ARG GROUP_ID
#RUN groupadd -f -g $GROUP_ID user
#RUN useradd -u $USER_ID -g $GROUP_ID user
RUN apt-get update && apt-get upgrade -y
RUN apt-get install wget -y
RUN docker-php-ext-install pdo pdo_mysql calendar
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
#USER user

RUN echo "file_uploads = On\n" \
         "memory_limit = 500M\n" \
         "upload_max_filesize = 500M\n" \
         "post_max_size = 500M\n" \
         "max_execution_time = 600\n" \
         > /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html


COPY default.conf /etc/apache2/sites-enabled/000-default.conf

#RUN composer install

#CMD bash -c "composer install"


EXPOSE 80

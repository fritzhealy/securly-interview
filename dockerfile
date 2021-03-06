FROM php:7.0-apache
RUN apt-get update \
&& echo 'deb http://packages.dotdeb.org jessie all' >> /etc/apt/sources.list \
&& echo 'deb-src http://packages.dotdeb.org jessie all' >> /etc/apt/sources.list \
&& apt-get install -y wget \
&& wget https://www.dotdeb.org/dotdeb.gpg \
&& apt-key add dotdeb.gpg \
&& apt-get update \
&& apt-get install -y php7.0-mysql \
&& docker-php-ext-install pdo_mysql
RUN a2enmod rewrite
RUN echo 'INCLUDE /etc/apache2/httpd.conf ' >> /etc/apache2/apache2.conf
RUN touch /etc/apache2/httpd.conf
RUN echo '<Directory /var/www/html/>' >> /etc/apache2/httpd.conf
RUN echo 'AllowOverride All' >> /etc/apache2/httpd.conf
RUN echo '</Directory>' >> /etc/apache2/httpd.conf
RUN service apache2 restart
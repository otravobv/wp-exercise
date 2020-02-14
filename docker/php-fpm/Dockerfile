FROM php:7.3-fpm

# Install MySQLi extension
RUN docker-php-ext-install mysqli

# Install Redis extension
RUN pecl install redis \
	&& rm -rf /tmp/pear \
	&& docker-php-ext-enable redis

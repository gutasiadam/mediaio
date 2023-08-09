    FROM php:7.3-fpm  
	RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
	RUN apt-get update; apt-get install -y libmagickwand-dev; pecl install imagick; docker-php-ext-enable imagick;
	RUN apt-get update && apt-get install -y zip
	
	
	RUN apt-get update && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev && docker-php-ext-configure gd && docker-php-ext-install -j$(nproc) gd
	RUN docker-php-ext-install exif
	
	
	RUN apt-get install -y libicu-dev && docker-php-ext-configure intl && docker-php-ext-install intl
	
	# Clear cache
	RUN apt-get clean && rm -rf /var/lib/apt/lists/*
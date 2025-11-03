FROM php:7.4-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libgd-dev \
    jpegoptim optipng pngquant gifsicle \
    zip \
    unzip

# Install nodejs
#RUN apt-get install -y git-core curl build-essential openssl libssl-dev \
# && git clone https://github.com/nodejs/node.git \
# && cd node \
# && ./configure \
# && make \
# && sudo make install

RUN apt-get update && apt-get install -y \
    software-properties-common \
    npm
RUN npm install npm@latest -g && \
    npm install n -g && \
    n latest
    
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# GD
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg && docker-php-ext-install -j$(nproc) gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

RUN export NODE_OPTIONS=--openssl-legacy-provider

# Set working directory
WORKDIR /var/www

USER $user

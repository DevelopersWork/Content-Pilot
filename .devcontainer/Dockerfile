# Check the ARG build arguments for WordPress and PHP versions
ARG WORDPRESS_VERSION
ARG PHP_VERSION

# Extend the official WordPress image with specified versions
FROM wordpress:${WORDPRESS_VERSION}-php${PHP_VERSION}-apache

# Set the working directory in the container
WORKDIR /var/www/html/wp-content/plugins/content-pilot

# Install development tools
RUN apt-get update && \
    apt-get install -y \
    git \
    xvfb \
    zip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# Install utility libraries
RUN apt-get update && \
    apt-get install -y \
    libnss3 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libgtk-3-0 \
    libgbm1 \
    libasound2 \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# Download the expected checksum for Composer
RUN EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"

# Download the Composer installer script
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Calculate the actual checksum for Composer
RUN ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

# Verify the checksum and install Composer
RUN if [[ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]]; then \
        >&2 echo 'ERROR: Invalid installer checksum' && \
        rm composer-setup.php && \
        exit 1; \
    fi 
RUN php composer-setup.php --install-dir=/bin --filename=composer --quiet
RUN php -r "unlink('composer-setup.php');"

# Install Node Version Manager (NVM)
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/master/install.sh | bash

# Reload bash so that NVM can be used
SHELL ["/bin/bash", "--login", "-c"]

# Install LTS Node.js version using NVM
RUN nvm install 'lts/*' --reinstall-packages-from=current

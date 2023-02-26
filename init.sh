if [ "$ENV" != "development" ]
then
    exit 1
fi

apt-get update && apt-get install git xvfb libnss3 libatk1.0-0 libatk-bridge2.0-0 libgtk-3-0 libgbm1 libasound2 -y

###################################
# Installing Node Version Manager #
###################################
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash

###########################
# Installing PHP Composer #
###########################
EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --install-dir=/bin --filename=composer --quiet
php -r "unlink('composer-setup.php');"

# Restart terminal to use the installed commands
source ~/.bashrc

# Installs the provided node in .nvmrc
nvm install

# Installs node packages from package.json
npm install

# Installs php composer packages from composer.json
php /bin/composer install

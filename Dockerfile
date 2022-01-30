FROM ghcr.io/developerswork/wordpress:release-master

ADD . "/var/www/html/wp-content/plugins/content-pilot_developerswork"

RUN cd /var/www/html/wp-content/plugins/content-pilot_developerswork && composer install
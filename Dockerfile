FROM ubuntu:latest

RUN apt-get update
RUN apt-get -y upgrade
#RUN DEBIAN_FRONTEND=noninteractive apt-get -y install curl nginx \
#                                              supervisor vim git bzip2 wget mysql-client

RUN DEBIAN_FRONTEND=noninteractive apt-get -y install curl nginx php5-cli php5-fpm php5-intl php5-curl \
                                              php5-mysql  supervisor vim git bzip2 wget mysql-client

RUN echo "\ndaemon off;" >> /etc/nginx/nginx.conf && \
    chown -R www-data:www-data /var/lib/nginx

COPY docker/www.conf /etc/php/fpm/pool.d/www.conf
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/fastcgi_params /etc/nginx/fastcgi_params
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

COPY ./ /var/www/
WORKDIR /var/www/
#
RUN composer install
##RUN chmod +rwx ./install/pullStatic.sh && ./install/pullStatic.sh

CMD ["/usr/bin/supervisord"]

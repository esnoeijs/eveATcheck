#!/usr/bin/env bash
mysql -h mysql -u root -pderp < /var/www/install/install.sql
if [ ! -f /tmp/mysql-latest.tar.bz2 ]
then
    curl -o /tmp/mysql-latest.tar.bz2 https://www.fuzzwork.co.uk/dump/mysql-latest.tar.bz2
fi
tar -xjOf /tmp/mysql-latest.tar.bz2 | mysql -h mysql -u root -pderp eveATcheck
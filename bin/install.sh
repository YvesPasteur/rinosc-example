#!/bin/sh

composer install

mysql -u root < ./bin/install.sql
mysql -u root < ./bin/create_user.sql

#!/bin/bash

mv /var/www/html/londonbloggers /var/www/html/londonbloggers.iamcal.com
sed -i 's/\/londonbloggers\//\/londonbloggers.iamcal.com\//' /var/www/html/londonbloggers.iamcal.com/db/.git

cd /var/www/html/londonbloggers.iamcal.com
(< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-32};echo) > secrets/session_crypto_key
(< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-40};echo) > secrets/duo_app_key
(< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-32};echo) > secrets/crumb_secret

ln -s /var/www/html/londonbloggers.iamcal.com/site.conf /etc/apache2/sites-available/londonbloggers.iamcal.com.conf
a2ensite londonbloggers.iamcal.com
service apache2 reload

chgrp www-data templates_c
chmod g+w templates_c

composer install

cd db
./init_db.sh


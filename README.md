# London Bloggers

In the summer of 2002, while working in london, I started a directory for London blogs - blogs that
were written by people who lived and worked in Greater London. There were a few hundred at the time 
and we would periodically meet up in London to talk about the web. Since I was moderately obsessed 
with the tube map (and still am, only more so), the directory was based on it, with blogs arranged 
by station. The station, line and connection data is included in the <code>data/</code> dir.

Fast forward more than 8 years and the site is getting pretty old. This is my project to rebuild it
from scratch with a nice slippy map and maintainable code. It's based on
<a href="https://github.com/exflickr/flamework">flamework</a>, an open source PHP un-framework. There's 
no sensible reason to run this code yourself, but it's an example of a small flamework app. The public 
nature also encourages me to make is neat. In theory.

Once it's finished, it'll be live at
<a href="http://londonbloggers.iamcal.com">http://londonbloggers.iamcal.com</a>.


## Installation

    cd /var/www/html
    git clone --recursive git@github.com:iamcal/londonbloggers.git londonbloggers.iamcal.com
    cd londonbloggers.iamcal.com
    (< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-32};echo) > secrets/session_crypto_key
    (< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-40};echo) > secrets/duo_app_key
    ln -s /var/www/html/londonbloggers.iamcal.com/site.conf /etc/apache2/sites-available/londonbloggers.iamcal.com.conf
    a2ensite londonbloggers.iamcal.com
    service apache2 reload
    chgrp www-data templates_c
    chmod g+w templates_c
    cd db
    ./init_db.sh

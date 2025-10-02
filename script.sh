#!/bin/bash
(crontab -l | grep -v "/usr/bin/php /opt/lampp/htdocs/go/artisan dm:disbursement") | crontab -
(crontab -l ; echo "09 16 * * 1 /usr/bin/php /opt/lampp/htdocs/go/artisan dm:disbursement") | crontab -
(crontab -l | grep -v "/usr/bin/php /opt/lampp/htdocs/go/artisan restaurant:disbursement") | crontab -
(crontab -l ; echo "09 15 * * 3 /usr/bin/php /opt/lampp/htdocs/go/artisan restaurant:disbursement") | crontab -

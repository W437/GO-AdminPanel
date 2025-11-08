#!/bin/bash
(crontab -l | grep -v "/usr/bin/php /opt/lampp/htdocs/StackFood-Admin/artisan dm:disbursement") | crontab -

(crontab -l | grep -v "/usr/bin/php /opt/lampp/htdocs/StackFood-Admin/artisan restaurant:disbursement") | crontab -


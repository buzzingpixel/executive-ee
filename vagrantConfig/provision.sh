#!/bin/sh
# ---------------------------------------------------------------------------
# Copyright 2017, BuzzingPixel, LLC
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the Apache License 2.0.
# http://www.apache.org/licenses/LICENSE-2.0
# ---------------------------------------------------------------------------





###################################
# Prepare things
###################################

# Remove all virtual hosts
sudo rm -rf /etc/nginx/sites-available/*;
sudo rm -rf /etc/nginx/sites-enabled/*;





###################################
# Run PHP Provisioning Script
###################################

#!/bin/sh
sudo php /vagrant/vagrantConfig/provision.php;





###################################
# 30 min database backup cron
###################################

# Set up DB Backups cron for every 30 minutes if it has not been setup
if [ ! -f /var/log/cronSetup ]; then
    # Make sure script is executable
    chmod +x /vagrant/vagrantConfig/dbBackups.sh;

    # Echo out the cron command
    echo "*/30 * * * * /vagrant/vagrantConfig/dbBackups.sh >/dev/null 2>&1" >> cron;

    # Set the cron file as a cron job
    crontab cron;

    # remove the cron file
    rm cron;

    # Write the file cronSetup so we know it's already been setup
    echo 'cron setup' > /var/log/cronSetup;
fi





###################################
# Restart services
###################################

# Restart apache
sudo service php-5.3.29-fpm restart
sudo service php-5.6.30-fpm restart
sudo service php-7.0.18-fpm restart
sudo service php-7.1.3-fpm restart
sudo service nginx restart;

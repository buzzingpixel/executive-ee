#!/bin/bash

## Update this path for your project on your server
PATH_TO_EXECUTIVE_CLI_EE_FILE=/path/to/projet/ee;

################################################################################
## About this script
# Continuously runs the Executive queue until the process is stopped.
# This script should be placed on your server OUTSIDE OF YOUR PROJECT

## Supervisor Config file example /etc/supervisor/conf.d/name_of_my_running_script.conf
# [program:name_of_my_running_script]
# command=/path/to/queueRunner.sh
# autostart=true
# autorestart=true
# stderr_logfile=/var/log/name_of_my_running_script.err.log
# stdout_logfile=/var/log/name_of_my_running_script.out.log

## Supervisor commands
# sudo supervisorctl (places user in the supervisor command line)
#     stop name_of_my_running_script
#     start name_of_my_running_script
# sudo service supervisor restart (does not re-read config)
# sudo supervisorctl reread (reads config)
# sudo supervisorctl update (after rereading config above, restarts apps scripts where config has changed)
################################################################################

# Run the queue every second infinitely
while true; do
    php ${PATH_TO_EXECUTIVE_CLI_EE_FILE} executive runQueue;
    sleep 1;
done

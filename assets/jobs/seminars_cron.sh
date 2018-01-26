#!/bin/bash


# To automate this, set up a cron job, similar to
# PATH=/usr/sbin:/usr/bin:$PATH
# */15 * * * * /PATH_TO_WEB_DIRECTORY/assets/jobs/seminars_cron.sh >> /PATH_TO_WEB_DIRECTORY/assets/jobs/log/cron.log

# which will run every 15 minutes.

my_dir=$(cd `dirname $0` && pwd)
cd $my_dir
touch log/mail.log log/update.log log/cron.log log/logrotate.log
php mailer.job.php >> log/mail.log
php runupdate.job.php >> log/update.log
logrotate -s log/logrotate.log logrotate.conf

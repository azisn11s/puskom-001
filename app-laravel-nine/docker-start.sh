#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-app3}
env=${APP_ENV:-production}

# if [ "$env" != "local" ]; then
#     echo "Caching configuration..."
#     (cd /var/www && php artisan config:cache && php artisan route:cache && php artisan view:cache)
# fi

if [ "$role" = "app3" ]; then

    exec php-fpm

elif [ "$role" = "queue" ]; then

    echo "Executing queue..."
	sleep 60
    echo "Running the queue..."
    php /var/www/app3/artisan queue:work redis --verbose --daemon

elif [ "$role" = "scheduler" ]; then

    while [ true ]
    do
	  now=$(date +"%Y-%m-%d %T")
	  echo "[$now] Executing cron..."
      php /var/www/app3/artisan schedule:run --verbose --no-interaction &
      sleep $((60 - $(date +%s) % 60))
    done

# elif [ "$role" = "broadcaster" ]; then

#     while [ true ]
#     do
# 	  now=$(date +"%Y-%m-%d %T")
# 	  echo "[$now] Executing broadcaster..."
#       php /var/www/artisan websockets:serve --verbose --no-interaction
#     done

else
    echo "Could not match the container role \"$role\""
    exit 1
fi

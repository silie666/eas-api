web: vendor/bin/heroku-php-nginx -C nginx.conf public/
release: php artisan queue:work redis --queue=listen-course --timeout=1200 --sleep=3 --tries=1 --memory=1024 &
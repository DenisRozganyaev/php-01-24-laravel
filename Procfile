web: vendor/bin/heroku-php-apache2 public/
tasks: npm install && npm run build && php artisan migrate --force && php artisan queue:listen --queue=default,wishilist-notify,order-created

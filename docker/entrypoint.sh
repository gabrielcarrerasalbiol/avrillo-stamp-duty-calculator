#!/bin/sh
set -eu

cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ -f .env ] && ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force --no-interaction
fi

if [ "${DB_CONNECTION:-}" = "mysql" ]; then
    php -r '
        $host = getenv("DB_HOST") ?: "db";
        $port = getenv("DB_PORT") ?: "3306";
        $database = getenv("DB_DATABASE") ?: "stamp_duty";
        $username = getenv("DB_USERNAME") ?: "stamp_duty";
        $password = getenv("DB_PASSWORD") ?: "stamp_duty";

        $attempts = 30;

        while ($attempts-- > 0) {
            try {
                new PDO("mysql:host={$host};port={$port};dbname={$database}", $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                exit(0);
            } catch (Throwable $exception) {
                fwrite(STDOUT, "Waiting for database...\n");
                usleep(2000000);
            }
        }

        fwrite(STDERR, "Database connection could not be established.\n");
        exit(1);
    ' 

    php artisan migrate --force --no-interaction
fi

exec "$@"
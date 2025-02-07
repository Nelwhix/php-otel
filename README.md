# PHP Otel
A demo project for my phparch article on distributed
tracing using OpenTelemetry

## Usage
1. Clone the project
2. Run `docker compose up -d`
3. Run migrations and seeders with `docker compose run --rm order-service php artisan migrate --seed`
# Stamp Duty Calculator

This repository contains a Laravel application for the SDLT calculator task. The project is set up to run locally in Docker with an app container and a local MariaDB database.

## What it does

The app calculates residential Stamp Duty Land Tax for England using:

- standard residential rates
- first-time buyer relief
- higher rates for additional properties

The calculation logic lives in a dedicated Laravel service and the rate tables live in [config/sdlt.php](config/sdlt.php).

## Run locally with Docker

1. Start the stack:

```bash
docker compose up --build
```

2. Open the app at `http://localhost:18080`.

3. The database is available on `localhost:33061` with these defaults:

```text
Database: stamp_duty
Username: stamp_duty
Password: stamp_duty
Root password: root
```

The app container will automatically:

- copy `.env.example` to `.env` if needed
- install Composer dependencies if `vendor/` is missing
- generate an application key if one has not been set
- wait for the MariaDB container to become reachable
- run Laravel migrations

If you need different host ports, change them in `docker-compose.yml`.

## Useful commands

Run tests inside Docker:

```bash
docker compose exec app php artisan test
```

Stop and remove containers:

```bash
docker compose down
```

Stop and remove containers and the local database volume:

```bash
docker compose down -v
```

## Run tests

From the host machine:

```bash
php artisan test
```

Inside Docker:

```bash
docker compose exec app php artisan test
```

## Example inputs and outputs

These are useful quick checks against the calculator:

| Scenario | Inputs | Expected SDLT |
| --- | --- | --- |
| Standard residential | Price `295000`, standard buyer, not an additional property | `£4,750.00` |
| First-time buyer relief | Price `425000`, all buyers are first-time buyers, not an additional property | `£6,250.00` |
| Additional property | Price `300000`, standard buyer, additional property checkbox ticked | `£20,000.00` |
| Threshold boundary | Price `250000`, standard buyer, not an additional property | `£2,500.00` |

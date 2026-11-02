# Multi-Tenant REST API (Symfony 7.4)

## Tech Stack
- **PHP**: 8.4
- **Framework**: Symfony 7.4
- **Database**: PostgreSQL 16
- **Environment**: Docker & Docker Compose (Nginx + PHP-FPM + Postgres)

## Getting Started

1. Clone the repository and navigate to the project directory.
2. Build and start the Docker containers:
   ```bash
   docker compose up --build
3. Access the application locally via
    ```bash
    https://localhost
4. For loading fixtures use:
    ```bash
    docker exec -it symfony-multitenant-auth-php-fpm-1 php bin/console foundry:load-fixtures main
5. To log in as users use ```password```
6. Generate the JWT cryptographic keys:
    ```bash
    docker exec -it symfony-multitenant-auth-php-fpm-1 php bin/console lexik:jwt:generate-keypair

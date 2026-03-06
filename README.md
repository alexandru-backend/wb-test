# WB-API Analytics Integration Test

This project is a technical assessment consisting of a Laravel-based integration with a Russian Wildberries (WB) external API to fetch and store Sales, Orders, Stocks, and Incomes data.

## 🚀 Tech Stack
- **Framework:** Laravel 10
- **Language:** PHP 8.2
- **Database:** MySQL (Hosted on **Railway**)
- **Containerization:** Docker & Docker Compose
- **Web Server:** Nginx / PHP-FPM

## 📊 Database Access (Railway)
*As requested, the database has been deployed on a free hosting service (Railway).*

- **Host:** [crossover.proxy.rlwy.net]
- **Port:** [47418]
- **User:** [root]
- **Password:** [penWblGSBNChgTHdKLsVqvlnoznziRIf]
- **Database Name:** railway

## 🛠️ Data Synchronization (Artisan Commands)
To fetch data from the external API (109.73.206.144:6969) and sync it with the database, run the following commands inside the container:

```bash
docker-compose exec app php artisan fetch:sales
docker-compose exec app php artisan fetch:orders
docker-compose exec app php artisan fetch:stocks
docker-compose exec app php artisan fetch:incomes
```
Note: By default, commands are configured to fetch the first page of results for a quick evaluation.

📡 API Endpoints (Local)
The local API provides access to the synchronized data. All requests require the key parameter.

Sales: GET /api/sales?key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie&dateFrom=2024-01-01

Orders: GET /api/orders?key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie

Stocks: GET /api/stocks?key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie&dateFrom=2026-03-06

Incomes: GET /api/incomes?key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie

⚙️ Installation & Setup
Clone the repository.

Ensure your .env file is configured with the Railway DB credentials provided above.

Start the environment: docker-compose up -d

Run migrations: docker-compose exec app php artisan migrate

📝 Technical Note (Octane)
The project was initially structured for Laravel Octane. However, to ensure 100% stability during this evaluation, the standard PHP-FPM/Nginx stack is being used.
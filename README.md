# Stock Performance Dashboard

A Laravel web application for the WizGlobal technical assignment. Users can log in, upload stock price files (CSV, XLS, XLSX, or ODS), and view a chart of the **top 5 best performers** for the selected period.

Performance is calculated as the **highest price gain during the period**: for each stock, the system finds the maximum value of `current price − lowest price seen so far` across all dates in the file.

---

## Screenshots

### Login page

Sign in with the seeded demo credentials to access the dashboard.

![Login page](docs/screenshots/login.png)

**Demo credentials:** `admin@gmail.com` / `Admin@123!`

### Dashboard

Upload a stock price file and view the top 5 performers as a histogram, ranked by highest price gain during the period.

![Stock Performance Dashboard](docs/screenshots/dashboard.png)

---

## Tech stack

| Item | Version / package |
|------|-------------------|
| **PHP** | `^8.3` (8.3 or higher) |
| **Laravel** | `^13.8` (installed: **13.18.1**) |
| **Database** | SQLite (default) or MySQL |
| **Excel/CSV parsing** | [maatwebsite/excel](https://github.com/SpartnerNL/Laravel-Excel) `^3.1` |
| **Charts** | [Chart.js](https://www.chartjs.org/) 4.x (CDN) |
| **Frontend build** | Vite 8, Tailwind CSS 4 (optional for asset pipeline) |

### Main Composer packages

```json
"php": "^8.3",
"laravel/framework": "^13.8",
"laravel/tinker": "^3.0",
"maatwebsite/excel": "^3.1"
```

---

## Prerequisites

Before you begin, make sure you have:

- **Git**
- **PHP 8.3+** with extensions: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`
- **Composer** 2.x
- **Node.js** 18+ and **npm** (optional; only needed if you run the Vite asset pipeline)
- **MySQL 8+** (optional; SQLite works out of the box)

Check your versions:

```bash
php -v
composer -V
node -v    
mysql --version   
```

---

## Installation (from git clone to running app)

### 1. Clone the repository

```bash
git clone https://github.com/Mitchellesweetie/technical_assignmet2.git
cd technical_assignmet2
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Create the environment file

```bash
cp .env.example .env
```

### 4. Generate the application key

```bash
php artisan key:generate
```

### 5. Configure environment variables

Open `.env` and set the values below.

#### Application

```env
APP_NAME="Stock Performance Dashboard"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```


#### DATABASE MySQL

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=technical_assignment2
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database in MySQL before migrating:

```sql
php artisan migrate will create by default 
CREATE DATABASE technical_assignment2;
```

#### Session and cache (recommended defaults)

These are already set in `.env.example` and should work after migration:

```env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 6. Run database migrations

```bash
php artisan migrate
```

This creates:

- `users` — login accounts
- `stock_uploads` — uploaded file metadata
- `stock_prices` — parsed stock price rows
- `sessions`, `cache`, `jobs` — Laravel system tables

### 7. Seed the database

Seed demo users with:

```bash
php artisan db:seed
```

Or seed only users:

```bash
php artisan db:seed --class=UserSeeder
```

#### Seeded accounts

| Name | Email | Password |
|------|-------|----------|
| Admin User | `admin@gmail.com` | `Admin@123!` |
| Student User | `student@gmail.com` | `Student@123!` |

The seeder uses `updateOrCreate`, so it is safe to run multiple times.

### 8. Start the application

```bash
php artisan serve
```

Open in your browser:

**http://localhost:8000**

---

## Quick setup (one command)

If dependencies are not installed yet, you can use the Composer setup script:

```bash
composer run setup
php artisan db:seed
php artisan serve
```

`composer run setup` runs: `composer install`, copies `.env`, generates the app key, runs migrations, and builds frontend assets.

---

## How to use the application

1. Go to **http://localhost:8000**
2. Log in with one of the seeded accounts (see table above)
3. On the dashboard, upload a stock price file (CSV, XLS, XLSX, or ODS)
4. View the **Top 5 performers** histogram and summary cards

### Expected file format

The file must contain three columns: **stock**, **price**, and **date**.

```csv
stock,price,date
Eaagads Ltd,14.5,2019-01-02
Limuru Tea,500,2019-01-03
EA Breweries,173.5,2019-01-02
```

- Column headers are optional
- Supported date format: `YYYY-MM-DD` (Excel serial dates are also supported for spreadsheet uploads)
- Maximum upload size: 10 MB

---

## Performance calculation

For each company (`stock_name`):

1. Prices are sorted by date
2. A running minimum price is tracked
3. Daily gain = `current price − running minimum`
4. **Max price gain** = highest daily gain during the period
5. Companies are ranked by max price gain; the top 5 are shown on the chart

Example: if prices go `100 → 80 → 120`, the highest gain is **40** (buy at 80, sell at 120).

---

## Useful Artisan commands

| Command | Description |
|---------|-------------|
| `php artisan migrate` | Run database migrations |
| `php artisan migrate:fresh` | Drop all tables and re-run migrations |
| `php artisan db:seed` | Seed demo users |
| `php artisan migrate:fresh --seed` | Reset DB and seed in one step |
| `php artisan serve` | Start local dev server |
| `php artisan config:clear` | Clear cached config after `.env` changes |

---

## Project structure

```
app/
├── Http/Controllers/
│   ├── AuthenticationController.php   # Login & logout
│   └── StockController.php            # Upload & dashboard
├── Models/
│   ├── StockUpload.php
│   └── StockPrice.php
└── Services/
    ├── StockFileParser.php            # CSV / Excel / ODS parsing
    └── TopPerformersAnalyzer.php      # Top 5 ranking & chart data

database/
├── migrations/                        # users, stock_uploads, stock_prices
└── seeders/
    ├── DatabaseSeeder.php
    └── UserSeeder.php                 # Demo login accounts

resources/views/
├── login.blade.php
└── dashboard.blade.php                # Upload form + Chart.js histogram

routes/web.php                         # Application routes
```

---

## Routes

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/` | Login page |
| POST | `/auth/login` | Submit login |
| POST | `/auth/logout` | Log out |
| GET | `/dashboard` | Dashboard (auth required) |
| POST | `/upload` | Upload stock file (auth required) |

---

## Troubleshooting

### `SQLSTATE[HY000] [2002] Connection refused`

MySQL is not running or `.env` database settings are wrong. Either:

- Start MySQL and verify `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, or
- Switch to SQLite (see **Option A** above)

### `Base table or view not found`

Run migrations:

```bash
php artisan migrate
```

### Login fails after fresh install

Seed the users:

```bash
php artisan db:seed
```

### Changes to `.env` not applied

```bash
php artisan config:clear
```

---

## License

This project is built on the [Laravel](https://laravel.com) framework, which is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

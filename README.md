# Modestik BD

Modestik BD is an enterprise-grade e-commerce and retail operations management platform built with Laravel 12. The application provides an end-to-end shopping experience tailored for fashion, lifestyle, and modest apparel retail, coupled with back-office operations including courier logistics, abandoned cart recovery, and a complete double-entry business accounting system.

---

## Key Feature Modules

### 1. Storefront and Customer Experience
- Modern, responsive storefront optimized for desktop, tablet, and mobile devices.
- Multi-level category hierarchy and brand-based browsing.
- Product catalog with real-time attribute filters (size, color, fabric, price range).
- Live AJAX search bar with instant search suggestions.
- Interactive cart drawer and dedicated cart page.
- Persistent customer wishlist supporting both guest and authenticated sessions with automatic merge on login.
- Flash sales module with countdown timers and promotional pricing.
- Customer product review system with star ratings and photo uploads.
- Dynamic CMS pages, FAQ accordions, and an integrated blog engine.

### 2. Cart, Checkout, and Lead Capture
- Frictionless single-page checkout optimized for high conversion.
- Full Bangladesh geographic dataset integration (Divisions, Districts, Thanas / Upazilas).
- Real-time lead capture: automatically records customer information as fields are typed before order completion.
- Coupon engine supporting percentage discounts, fixed discounts, minimum spend thresholds, and expiry dates.
- Seamless guest checkout with automatic account association upon customer registration.
- Cash on Delivery (COD) and manual payment method support with transaction verification (bKash / Nagad / Bank).

### 3. Courier Logistics and Shipping Automation
- Integrated Steadfast Courier API for parcel fulfillment.
- One-click consignment dispatch directly from order details.
- Real-time parcel tracking and status synchronization.
- Automated generation of consignment notes and tracking IDs.
- Courier settlement and COD remittance reconciliation workflow.
- Customizable shipping zones with independent delivery fee structures (Inside Dhaka, Sub-Dhaka, Outside Dhaka).

### 4. Abandoned Cart Recovery Console
- Dedicated dashboard tracking incomplete checkouts and dropped carts.
- Real-time cart activity logs (items added, checkout started, shipping details entered).
- Direct conversion recovery actions:
  - Automated or manual SMS notifications to customers.
  - Email recovery sequences with direct cart restoration links.
  - Manual one-click conversion of abandoned carts into confirmed orders.
- Abandoned cart analytics reporting recovery rates and recovered revenue.

### 5. Double-Entry Accounting and Financial Management
- Complete business accounting module built specifically for e-commerce workflows.
- Money In and Money Out transaction tracking with double-entry classification.
- Categorized operational expense management.
- Supplier management: purchase order entry, supplier dues tracking, and supplier payment logs.
- Customer receivables tracking with direct due collection logging.
- Multi-account financial ledger (Cash, Bank Accounts, Mobile Financial Services).
- Owner capital investment and personal equity withdrawal tracking.
- Payroll management with staff salary disbursements.
- Courier COD remittance settlement and fee deduction ledger.
- Automated calculation of Gross Profit, Net Profit, and Operating Expenses.
- Monthly closing reports and balance sheet generation.
- One-click export of accounting ledgers and financial statements to Excel and PDF.
- Transaction reversal capabilities with audit trails.

### 6. Inventory and Catalog Management
- Support for simple products and complex variable products with SKU, barcode, and variant attributes.
- Multi-image gallery upload with primary image designation and variant-specific image mapping.
- Product cloning utility for fast addition of similar catalog items.
- Real-time inventory tracking with stock-in and stock-out audit logs.
- Supplier restocking records with unit purchase cost tracking.
- Live inventory valuation reporting based on current purchase cost and retail price.
- Low-stock and out-of-stock threshold alerts.

### 7. Order Fulfillment and Invoicing
- Centralized order management dashboard with filtering by status, date, and courier.
- Order statuses: Pending, Processing, Ready to Ship, Shipped, Delivered, Cancelled, Returned.
- In-house Point of Sale (POS) and manual order entry for phone and social media orders.
- Bulk order status updates and bulk deletion.
- Comprehensive order status change history with staff attribution.
- Professional PDF invoice generation powered by DomPDF and mPDF.
- Customer self-service order tracking by phone number and order ID.

### 8. Marketing, Analytics, and SEO
- Dynamic homepage hero slider and promotional banner manager.
- Campaign manager for promotional events and seasonal discounts.
- Newsletter subscription management with subscriber export to CSV.
- Tracking configuration for Facebook Pixel, Conversions API (CAPI), and Google Analytics 4.
- SEO metadata fields (meta title, description, keywords, Open Graph tags) across all products and static pages.

### 9. System Administration and Security
- Role-Based Access Control (RBAC) via Spatie Laravel Permission.
- Granular permissions for products, orders, accounting, settings, and reports.
- Comprehensive activity log system auditing administrative actions.
- Centralized site configuration (general settings, contact info, social links, tax settings).
- Media library with automated cleanup of unreferenced image files.
- Database backup manager with one-click creation, download, restore, and safe reset.

---

## Technology Stack

- Backend: PHP 8.2+
- Framework: Laravel 12.x
- Frontend: Blade Templates, Vanilla CSS, JavaScript, Vite
- Database: MySQL 8.0+ / MariaDB 10.4+
- PDF Generation: DomPDF, mPDF
- Spreadsheet Export: Maatwebsite Excel (PhpSpreadsheet)
- Image Processing: Intervention Image Laravel
- Permissions: Spatie Laravel Permission
- Authentication: Laravel Session Auth & Laravel Sanctum
- CSS/Asset Bundler: Vite

---

## System Requirements

- PHP >= 8.2
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- GD / Imagick PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Composer 2.x
- Node.js >= 18.x and NPM

---

## Installation Guide

Follow these steps to configure and run Modestik BD locally:

### 1. Clone Repository
```bash
git clone git@github.com:EngrSaad2/Modestik-BD.git
cd Modestik-BD
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
Copy the environment template and generate application key:
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Open `.env` and configure your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=modestik
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations and Seeders
Run database migrations along with predefined roles, permissions, administrative accounts, and initial seed data:
```bash
php artisan migrate --seed
```

### 6. Create Storage Symlink
Link the public storage directory to the application storage:
```bash
php artisan storage:link
```

### 7. Compile Frontend Assets
For local development:
```bash
npm run dev
```

For production build:
```bash
npm run build
```

### 8. Start Local Development Server
```bash
php artisan serve
```
Access the application at `http://localhost:8000`.

---

## Default Administrative Credentials

After running `php artisan db:seed`, the default super administrator account is:

- URL: `http://localhost:8000/login`
- Email: `modestik@gmail.com`
- Password: `123456`

Change these credentials immediately upon deploying to any staging or production environment.

---

## Directory Structure Overview

```
modestik/
|-- app/
|   |-- Http/
|   |   |-- Controllers/
|   |   |   |-- Admin/           # Admin dashboard, inventory, orders, accounting
|   |   |   |-- Auth/            # Authentication controllers
|   |   |   `-- Frontend/        # Shop, cart, checkout, customer portal
|   |   `-- Middleware/          # Auth, Admin, Guest role guards
|   |-- Models/                  # Eloquent models and business logic
|   |-- Services/
|   |   |-- Accounting/          # Financial ledger and report services
|   |   |-- Cart/                # Cart calculation and abandoned lead tracker
|   |   `-- Courier/             # Steadfast API and consignment dispatch
|   |-- Support/                 # Bangladesh geographic datasets and utilities
|   `-- Traits/                  # Reusable guest data merging traits
|-- config/                      # Application, auth, database, services configs
|-- database/
|   |-- factories/               # Model factories
|   |-- migrations/              # Database schema migrations
|   `-- seeders/                 # Database seeders
|-- public/                      # Static assets, uploads, and compiled Vite assets
|-- resources/
|   |-- css/                     # Frontend and admin stylesheets
|   |-- js/                      # JavaScript modules
|   `-- views/
|       |-- admin/               # Admin panel Blade views
|       |-- customer/            # Customer account Blade views
|       |-- frontend/            # Public storefront Blade views
|       `-- layouts/             # Master Blade layouts
|-- routes/
|   |-- admin.php                # Admin route group
|   |-- console.php              # Scheduled console commands
|   `-- web.php                  # Public storefront and customer routes
`-- storage/                     # Logs, cache, and public uploads
```

---

## Production Deployment

A PowerShell deployment automation script is provided in `.agents/skills/update-server/deploy.ps1`.

### Deploy to Remote Host
```powershell
powershell -File .agents/skills/update-server/deploy.ps1
```

### Run Remote Migrations (SSH)
```bash
ssh -i "C:\Users\Admin\.ssh\modestik_nopw" modestik@209.42.27.61 "cd public_html && php artisan migrate --force"
```

---

## License

This project is proprietary software developed for Modestik BD. All rights reserved.

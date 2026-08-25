# SafeVoice Platform 🛡️
Secure and Anonymous Abuse Reporting System

**SafeVoice** is a comprehensive system built with **Laravel 13** and **Livewire 3**. It is designed for companies and organizations to allow employees or clients to submit abuse reports (such as financial fraud, harassment, privacy violations, and safety hazards) completely securely and anonymously.

---

## ✨ Key Features

### 1. Public Portal
- **Anonymous Reporting**: Anyone can submit a report without needing to register or log in.
- **Evidence Attachments**: Securely upload files and images as evidence.
- **Report Tracking**: Reporters receive a unique "Tracking Code" upon submission, allowing them to track the status of their report later via the `/track` page.
- **Privacy First**: Sensitive data and internal investigator notes are never exposed to the reporter. Only the current status (e.g., Pending, Investigating, Closed) is visible.

### 2. Admin Portal
- **Report Triage**: A dedicated dashboard to filter incoming reports, update their statuses, and assign investigators.
- **Encrypted Internal Notes**: Investigators can write private notes on specific reports. These notes are **encrypted** in the database and remain completely hidden from reporters.
- **Category Management**: Add, edit, or disable violation categories (e.g., Financial Fraud, Sexual Misconduct).
- **Role-Based Access Control (RBAC)**:
  - `Super Admin`: Full system access, including category and user management.
  - `Admin`: Compliance manager with report triage privileges.
  - `Investigator`: A user dedicated to investigating specific assigned reports.
- **Audit Logs**: Track all actions performed by admins and investigators for transparency and compliance.
- **Toast Notifications**: Fast and responsive UI feedback powered by Alpine.js and Livewire.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 13, PHP 8.4, MySQL / TiDB
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS, Vite
- **Authentication**: Laravel Fortify
- **Security**: Sensitive fields (like `internal_notes`) are encrypted at rest using Eloquent's encrypted casting.
- **Infrastructure**: Docker, Apache (Production-Ready configuration).

---

## 🚀 Local Development Setup

### Prerequisites:
- PHP 8.4 or higher
- Composer
- Node.js & NPM
- MySQL or SQLite

### Steps:
1. Clone the repository.
2. Install PHP and Node dependencies:
   ```bash
   composer install
   npm install
   ```
3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```
4. Generate the application key:
   ```bash
   php artisan key:generate
   ```
5. Create the storage symlink (required for evidence uploads):
   ```bash
   php artisan storage:link
   ```
6. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
   *(Note: The seeder will create the default admin accounts and categories).*
7. Start the development servers:
   ```bash
   php artisan serve
   npm run dev
   ```
---

## 🐳 Production Deployment (Docker)

This project includes a complete, production-ready Docker setup suitable for PaaS providers like Render, Railway, or AWS.

The setup features:
- **Multi-stage Build**: Separates the `Vite/Node` build process from the final lightweight `PHP/Apache` image.
- **Automated Entrypoint (`docker-entrypoint.sh`)**: Automatically performs the following tasks on container startup:
  1. Clears old caches.
  2. Runs Migrations (if `RUN_MIGRATIONS=true`).
  3. Runs Seeders (if `RUN_SEEDERS=true`).
  4. Automatically creates the storage symlink.
  5. Caches config, routes, and views for optimal performance.

### Production Environment Variables (`.env`):
Ensure the following variables are set in your hosting provider's dashboard:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Automated Deployment Settings
RUN_MIGRATIONS=true
RUN_SEEDERS=true

# Database (Automatically supports secure SSL/TLS connections for TiDB Serverless)
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=safevoice
DB_USERNAME=root
DB_PASSWORD=secret
```
*Note:* The system is programmed to explicitly trust proxies and force `HTTPS` for all assets and URLs when `APP_ENV=production`.

---

## 📁 Core Directory Structure

- `app/Livewire/Admin/`: Contains all Livewire components for the Admin Portal.
- `resources/views/layouts/`:
  - `app.blade.php`: The layout for the public-facing portal.
  - `admin.blade.php`: The layout for the Admin Portal dashboard.
- `database/seeders/`: Contains initial configuration data. Uses `firstOrCreate` and `updateOrCreate` to ensure production safety and prevent data duplication.

---

## 🔒 Security & Compliance
Security is a top priority in this architecture:
1. **No Sensitive Data Leaks**: The public tracking page only exposes the report status, nothing more.
2. **Encrypted Connections**: The system fully supports and manages secure SSL/TLS connections to cloud databases like TiDB by default.
3. **Encryption at Rest**: Internal investigator notes are automatically encrypted before being saved to the database.

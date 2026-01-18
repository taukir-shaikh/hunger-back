# Hunger: Food Ordering Platform

## Overview
Hunger is a full-stack web application for food ordering, featuring a Laravel (PHP) backend and a React (Vite) frontend. It allows users to browse restaurants, place orders, and manage their accounts, while restaurants can manage their menus and orders.

---

## Features
- User registration, login, and email OTP verification
- Restaurant listing and menu management
- Order placement, status tracking, and history
- Payment integration (structure ready)
- Role-based access (users, restaurants, admins)
- RESTful API endpoints for frontend-backend communication

---

## Backend (Laravel)
- **Framework:** Laravel 12.x
- **Database:** MySQL (default, configurable)
- **Key Directories:**
  - `app/Models/`: Eloquent models (e.g., `TbUsers`, `TbOrders`)
  - `app/Services/`: Business logic (e.g., `OrderService`, `EmailOtpService`)
  - `app/Repositories/`: Data access abstraction
  - `routes/`: API and web route definitions
  - `database/migrations/`: Database schema
  - `database/seeders/`: Initial data population
- **Authentication:** Laravel Sanctum (API tokens)
- **Email OTP:** Secure email verification with OTPs
- **Order Management:** Order creation, itemization, and status transitions

### Setup
1. Install dependencies:
   ```bash
   composer install
   npm install
   ```
2. Copy environment file and set credentials:
   ```bash
   cp .env.example .env
   # Edit .env for DB, mail, etc.
   ```
3. Generate app key:
   ```bash
   php artisan key:generate
   ```
4. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
5. Start the backend server:
   ```bash
   php artisan serve
   ```

---

## Frontend (React + Vite)
- **Framework:** React 19.x
- **Build Tool:** Vite
- **Styling:** Tailwind CSS, Chakra UI
- **Routing:** React Router
- **API Calls:** Axios

### Setup
1. Install dependencies:
   ```bash
   npm install
   ```
2. Start the development server:
   ```bash
   npm run dev
   ```

---

## API Structure
- All API endpoints are prefixed with `/api` (see `routes/api.php`).
- Auth endpoints: registration, login, OTP verification
- Restaurant endpoints: list, details, menu
- Order endpoints: create, update, status, history

---

## Database Structure (Key Tables)
- `tb_users`: User accounts
- `tb_restaurants`: Restaurant profiles
- `tb_orders`: Orders placed by users
- `tb_order_items`: Items within each order
- `tb_email_otps`: Email OTP records

---

## Development & Contribution
- Follow PSR-12 coding standards (PHP)
- Use Eloquent models for DB access where possible
- Use services for business logic, repositories for data access
- PRs and issues welcome!

---

## License
MIT

---

## Authors
- Taukir Shaikh
- Contributors welcome!

---

## Contact
For support or questions, open an issue or contact the maintainer.

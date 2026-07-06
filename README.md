# SMAPS - Smart Multi-channel Queue & Appointment System

Backend API for SMAPS. Built with Laravel 11 and SQLite.

## How to Run

### Option 1: Docker (Recommended)

1. Build and run:
   ```bash
   docker compose up --build
   ```
2. API running on: http://localhost:8069
3. Health check: http://localhost:8069/api/health

### Option 2: Local PHP

1. Copy env file:
   ```bash
   copy .env.example .env
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Generate application key:
   ```bash
   php artisan key:generate
   ```
4. Create SQLite DB and run migrations & seeds:
   ```bash
   copy nul database\database.sqlite
   php artisan migrate --seed
   ```
5. Run server:
   ```bash
   php artisan serve --port=8000
   ```

---

## Demo Accounts

All accounts use password: `password`

| Role | Name | Email |
|------|------|-------|
| Admin | Admin SMAPS | `admin@smaps.test` |
| Doctor (A) | Dr. Andi Pratama | `dr.andi@smaps.test` |
| Doctor (B) | Dr. Siti Rahayu | `dr.siti@smaps.test` |
| Doctor (C) | Dr. Budi Santoso | `dr.budi@smaps.test` |
| Patient | Budi | `budi@example.com` |

---

## Key API Endpoints

### Authentication
- `POST /api/register`
- `POST /api/login`
- `POST /api/logout` (auth required)
- `GET /api/me` (auth required)

### Queue Operations
- `GET /api/queues` (auth required)
- `POST /api/queues` (auth required - book appointment)
- `GET /api/display` (public - live queue monitor)
- `GET /api/check-queue` (public - check single queue details)
- `POST /api/queues/{queue}/call` (admin/doctor)
- `POST /api/queues/{queue}/serve` (admin/doctor)
- `POST /api/queues/{queue}/complete` (admin/doctor)
- `POST /api/queues/{queue}/skip` (admin/doctor)

### Doctor Queue Actions
- `POST /api/doctors/{doctor}/call-next` (admin/doctor - call next patient directly)
- `GET /api/doctors/{doctor}/summary` (auth required)

### Doctor Management (Admin only)
- `POST /api/doctors`
- `PUT /api/doctors/{doctor}`
- `DELETE /api/doctors/{doctor}`

---

## Interactive Documentation
Open http://localhost:8069/docs in your browser to view the OpenAPI specification.

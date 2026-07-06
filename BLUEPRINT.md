# 🏥 Sistem Manajemen Antrean & Pendaftaran Pasien (Skala Kecil) — SMAPS

> **Blueprint Proyek — Versi 1.0**  
> Tanggal: Juli 2026  
> Arsitektur: Decoupled (Laravel REST API + JavaScript SPA + Tailwind CSS)

---

# PART 1: PRODUCT REQUIREMENT DOCUMENT (PRD)

---

## 1.1 Project Overview & Objectives

### Deskripsi Singkat

SMAPS (Sistem Manajemen Antrean & Pendaftaran Pasien Skala Kecil) adalah aplikasi berbasis web yang dirancang untuk mendigitalisasi proses pendaftaran pasien dan manajemen antrean di klinik atau rumah sakit skala kecil. Sistem ini menggantikan alur manual (pendaftaran kertas, pemanggilan lisan) dengan alur digital yang transparan dan efisien.

### Objectives

| # | Tujuan | Indikator Keberhasilan |
|---|--------|----------------------|
| 1 | Mempercepat proses pendaftaran pasien | Pasien dapat mendaftar online < 2 menit |
| 2 | Mengurangi kerumunan di area pendaftaran | Pasien tahu estimasi giliran secara real-time |
| 3 | Memberikan transparansi antrean | Nomor antrean aktif ditampilkan di dashboard publik |
| 4 | Memudahkan admin/suster mengelola alur pasien | Admin punya panel untuk panggil, skip, selesaikan antrean |
| 5 | Menyediakan data operasional klinik | Statistik harian: jumlah pasien, waktu tunggu rata-rata |

### Scope (MVP)

- **Dalam Scope:** Pendaftaran pasien, pemilihan dokter/jadwal, nomor antrean otomatis, dashboard admin, display antrean publik.
- **Di Luar Scope (Future):** Pembayaran online, rekam medis elektronik, integrasi BPJS, notifikasi SMS/WhatsApp, multi-cabang.

---

## 1.2 User Personas & Roles

### Persona 1: Pasien

| Atribut | Detail |
|---------|--------|
| **Nama Representatif** | Ibu Sari, 35 tahun |
| **Kebutuhan** | Mendaftar antrean dari rumah atau saat di klinik, memilih dokter favorit, mengetahui estimasi giliran |
| **Pain Point** | Harus datang pagi-pagi untuk ambil nomor antrean, tidak tahu kapan dipanggil |
| **Device** | Smartphone (browser mobile) |
| **Role dalam Sistem** | `patient` — Akses terbatas: daftar antrean, lihat status antrean sendiri |

### Persona 2: Admin / Suster

| Atribut | Detail |
|---------|--------|
| **Nama Representatif** | Suster Dewi, 28 tahun |
| **Kebutuhan** | Mengelola jadwal dokter, memanggil nomor antrean secara berurutan, menandai pasien selesai/skip |
| **Pain Point** | Sulit mengatur urutan pasien saat ramai, tidak ada catatan digital |
| **Device** | Desktop/Tablet di meja pendaftaran |
| **Role dalam Sistem** | `admin` — Akses penuh: kelola dokter, jadwal, panggil antrean, lihat statistik |

---

## 1.3 Core Features List

### MVP 1 — Inti Antrean

| # | Fitur | Role | Prioritas |
|---|-------|------|-----------|
| F1 | Registrasi & Login (Email/Password) | Semua | 🔴 Wajib |
| F2 | Pendaftaran antrean baru (pilih dokter + jadwal) | Pasien | 🔴 Wajib |
| F3 | Generate nomor antrean otomatis (per dokter per hari) | Sistem | 🔴 Wajib |
| F4 | Dashboard Admin — daftar antrean hari ini | Admin | 🔴 Wajib |
| F5 | Panggil antrean berikutnya | Admin | 🔴 Wajib |
| F6 | Update status antrean (waiting → called → serving → done / skipped) | Admin | 🔴 Wajib |
| F7 | Display antrean publik (tanpa login) | Publik | 🔴 Wajib |
| F8 | CRUD Dokter | Admin | 🔴 Wajib |
| F9 | CRUD Jadwal Dokter | Admin | 🔴 Wajib |

### MVP 2 — Peningkatan (Future)

| # | Fitur | Role | Prioritas |
|---|-------|------|-----------|
| F10 | Statistik harian (jumlah pasien, avg waktu tunggu) | Admin | 🟡 Penting |
| F11 | Riwayat kunjungan pasien | Pasien/Admin | 🟡 Penting |
| F12 | Notifikasi browser (antrean hampir dipanggil) | Pasien | 🟢 Nice-to-have |
| F13 | Print nomor antrean (thermal printer) | Admin | 🟢 Nice-to-have |

---

## 1.4 User Flow

### Alur Pasien (Happy Path)

```
┌─────────────────────────────────────────────────────────────────┐
│                        ALUR PASIEN                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Buka Aplikasi Web                                          │
│       │                                                         │
│       ▼                                                         │
│  2. Register / Login                                           │
│       │                                                         │
│       ▼                                                         │
│  3. Halaman Pendaftaran Antrean                                │
│       ├── Pilih Dokter (lihat daftar dokter & spesialisasi)    │
│       ├── Pilih Jadwal (tanggal + slot waktu tersedia)         │
│       └── Isi Keluhan Singkat (opsional)                       │
│       │                                                         │
│       ▼                                                         │
│  4. Konfirmasi Pendaftaran                                     │
│       │                                                         │
│       ▼                                                         │
│  5. Dapat Nomor Antrean (misal: A-007)                         │
│       │                                                         │
│       ▼                                                         │
│  6. Lihat Status Antrean Real-time                             │
│       ├── Posisi saat ini dalam antrean                        │
│       ├── Nomor yang sedang dilayani                           │
│       └── Estimasi waktu tunggu                                │
│       │                                                         │
│       ▼                                                         │
│  7. Dipanggil → Menuju Ruang Periksa                           │
│       │                                                         │
│       ▼                                                         │
│  8. Selesai Dilayani (status: done)                            │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Alur Admin/Suster

```
┌─────────────────────────────────────────────────────────────────┐
│                      ALUR ADMIN/SUSTER                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Login sebagai Admin                                        │
│       │                                                         │
│       ▼                                                         │
│  2. Dashboard Admin                                            │
│       ├── Lihat antrean hari ini (per dokter)                  │
│       ├── Filter berdasarkan status                            │
│       └── Lihat statistik ringkas                              │
│       │                                                         │
│       ▼                                                         │
│  3. Kelola Antrean                                             │
│       ├── Klik "Panggil Berikutnya"                            │
│       │     └── Status pasien berubah: waiting → called        │
│       ├── Klik "Mulai Layani"                                  │
│       │     └── Status pasien berubah: called → serving        │
│       ├── Klik "Selesai"                                       │
│       │     └── Status pasien berubah: serving → done          │
│       └── Klik "Skip"                                          │
│             └── Status pasien berubah: → skipped               │
│       │                                                         │
│       ▼                                                         │
│  4. Kelola Data Master                                         │
│       ├── CRUD Dokter (nama, spesialisasi, foto)               │
│       └── CRUD Jadwal (dokter, hari, jam mulai, jam selesai)   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### State Machine — Status Antrean

```
  ┌──────────┐    panggil     ┌──────────┐    layani    ┌──────────┐
  │ WAITING  │ ─────────────▶ │  CALLED  │ ──────────▶ │ SERVING  │
  └──────────┘                └──────────┘              └──────────┘
       │                           │                         │
       │         skip              │        skip             │ selesai
       │◄──────────────────────────┘                         │
       │                                                     ▼
       │                                               ┌──────────┐
       └──────────────────────────────────────────────▶│   DONE   │
                          skip                         └──────────┘
                                                            ▲
                    ┌──────────┐                            │
                    │ SKIPPED  │ (bisa dipanggil ulang)     │
                    └──────────┘                            │
                         │          panggil ulang           │
                         └──────────────────────────────────┘
```

---
---

# PART 2: ARCHITECTURE & DATABASE DESIGN

---

## 2.1 Arsitektur Terpisah (Decoupled Architecture)

### Diagram Arsitektur

```
┌─────────────────────────┐          HTTP/HTTPS           ┌─────────────────────────┐
│                         │    (JSON REST API Calls)      │                         │
│   FRONTEND (SPA)        │ ◄──────────────────────────▶  │   BACKEND (API)         │
│                         │                               │                         │
│   - HTML/CSS/JS         │     Authorization:            │   - Laravel 11          │
│   - Tailwind CSS        │     Bearer <token>            │   - PHP 8.2+            │
│   - Fetch API/Axios     │                               │   - Laravel Sanctum     │
│                         │                               │   - PostgreSQL/MySQL    │
│   Served by:            │                               │                         │
│   - Nginx / Vite Dev    │                               │   Served by:            │
│   - Port 5173 (dev)     │                               │   - Nginx + PHP-FPM     │
│   - Port 80 (prod)      │                               │   - Port 8000 (dev)     │
│                         │                               │   - Port 80/443 (prod)  │
└─────────────────────────┘                               └──────────┬──────────────┘
                                                                     │
                                                                     │ Eloquent ORM
                                                                     ▼
                                                          ┌─────────────────────────┐
                                                          │                         │
                                                          │   DATABASE              │
                                                          │   PostgreSQL / MySQL    │
                                                          │                         │
                                                          │   Tables:               │
                                                          │   - users               │
                                                          │   - doctors             │
                                                          │   - schedules           │
                                                          │   - patients            │
                                                          │   - queues              │
                                                          │                         │
                                                          └─────────────────────────┘
```

### Mekanisme Autentikasi — Laravel Sanctum (SPA Mode)

Laravel Sanctum digunakan dalam mode **SPA Authentication** untuk frontend yang berada di domain/subdomain yang sama, atau mode **API Token** untuk client terpisah.

#### Flow Autentikasi:

```
┌──────────┐                                    ┌──────────┐
│ Frontend │                                    │ Backend  │
└────┬─────┘                                    └────┬─────┘
     │                                               │
     │  1. GET /sanctum/csrf-cookie                  │
     │  ─────────────────────────────────────────▶   │
     │                                               │
     │  2. Set-Cookie: XSRF-TOKEN=xxx               │
     │  ◄─────────────────────────────────────────   │
     │                                               │
     │  3. POST /api/v1/auth/login                   │
     │     Headers: X-XSRF-TOKEN: xxx               │
     │     Body: { email, password }                 │
     │  ─────────────────────────────────────────▶   │
     │                                               │
     │  4. Response: { user, token }                 │
     │     Set-Cookie: laravel_session=yyy           │
     │  ◄─────────────────────────────────────────   │
     │                                               │
     │  5. Subsequent API Calls                      │
     │     Headers: Authorization: Bearer <token>    │
     │  ─────────────────────────────────────────▶   │
     │                                               │
```

#### Konfigurasi CORS (Backend `config/cors.php`):

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')],
    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];
```

#### Konfigurasi Sanctum (`config/sanctum.php`):

```php
return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,localhost:5173,127.0.0.1')),
    'expiration' => 60 * 24, // Token berlaku 24 jam
];
```

---

## 2.2 Database Schema

### Entity Relationship Diagram (ERD)

```
┌──────────────┐       ┌──────────────┐       ┌──────────────┐
│    users     │       │   doctors    │       │  schedules   │
├──────────────┤       ├──────────────┤       ├──────────────┤
│ id (PK)      │       │ id (PK)      │       │ id (PK)      │
│ name         │       │ name         │       │ doctor_id(FK)│──┐
│ email (UQ)   │       │ specialization│      │ day_of_week  │  │
│ password     │       │ phone        │       │ start_time   │  │
│ role         │       │ photo        │       │ end_time     │  │
│ created_at   │       │ is_active    │       │ max_patients │  │
│ updated_at   │       │ created_at   │       │ created_at   │  │
└──────┬───────┘       │ updated_at   │       │ updated_at   │  │
       │               └──────┬───────┘       └──────────────┘  │
       │                      │                                  │
       │                      └──────────────────────────────────┘
       │
       │         ┌──────────────┐       ┌──────────────────┐
       │         │   patients   │       │     queues       │
       │         ├──────────────┤       ├──────────────────┤
       │         │ id (PK)      │       │ id (PK)          │
       └────────▶│ user_id (FK) │       │ patient_id (FK)  │──▶ patients.id
                 │ nik          │       │ doctor_id (FK)   │──▶ doctors.id
                 │ name         │       │ schedule_id (FK) │──▶ schedules.id
                 │ phone        │       │ queue_number     │
                 │ birth_date   │       │ queue_date       │
                 │ gender       │       │ status           │
                 │ address      │       │ complaint        │
                 │ created_at   │       │ called_at        │
                 │ updated_at   │       │ served_at        │
                 └──────────────┘       │ completed_at     │
                                        │ created_at       │
                                        │ updated_at       │
                                        └──────────────────┘
```

### DDL SQL (PostgreSQL Compatible)

```sql
-- ============================================
-- TABLE: users
-- Deskripsi: Akun pengguna sistem (pasien & admin)
-- ============================================
CREATE TABLE users (
    id              BIGSERIAL       PRIMARY KEY,
    name            VARCHAR(100)    NOT NULL,
    email           VARCHAR(150)    NOT NULL UNIQUE,
    email_verified_at TIMESTAMP     NULL,
    password        VARCHAR(255)    NOT NULL,
    role            VARCHAR(20)     NOT NULL DEFAULT 'patient'
                                    CHECK (role IN ('admin', 'patient')),
    remember_token  VARCHAR(100)    NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- ============================================
-- TABLE: doctors
-- Deskripsi: Data dokter yang tersedia di klinik
-- ============================================
CREATE TABLE doctors (
    id              BIGSERIAL       PRIMARY KEY,
    name            VARCHAR(100)    NOT NULL,
    specialization  VARCHAR(100)    NOT NULL,
    phone           VARCHAR(20)     NULL,
    photo           VARCHAR(255)    NULL,
    is_active       BOOLEAN         NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_doctors_active ON doctors(is_active);

-- ============================================
-- TABLE: schedules
-- Deskripsi: Jadwal praktik dokter per hari
-- ============================================
CREATE TABLE schedules (
    id              BIGSERIAL       PRIMARY KEY,
    doctor_id       BIGINT          NOT NULL,
    day_of_week     SMALLINT        NOT NULL CHECK (day_of_week BETWEEN 0 AND 6),
                                    -- 0=Minggu, 1=Senin, ..., 6=Sabtu
    start_time      TIME            NOT NULL,
    end_time        TIME            NOT NULL,
    max_patients    INTEGER         NOT NULL DEFAULT 30,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_schedules_doctor
        FOREIGN KEY (doctor_id) REFERENCES doctors(id)
        ON DELETE CASCADE,

    CONSTRAINT uq_schedule_doctor_day
        UNIQUE (doctor_id, day_of_week),

    CONSTRAINT chk_schedule_time
        CHECK (end_time > start_time)
);

CREATE INDEX idx_schedules_doctor ON schedules(doctor_id);
CREATE INDEX idx_schedules_day ON schedules(day_of_week);

-- ============================================
-- TABLE: patients
-- Deskripsi: Data profil pasien (terhubung ke user)
-- ============================================
CREATE TABLE patients (
    id              BIGSERIAL       PRIMARY KEY,
    user_id         BIGINT          NULL,
    nik             VARCHAR(16)     NULL UNIQUE,
    name            VARCHAR(100)    NOT NULL,
    phone           VARCHAR(20)     NULL,
    birth_date      DATE            NULL,
    gender          VARCHAR(10)     NULL CHECK (gender IN ('male', 'female')),
    address         TEXT            NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_patients_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
);

CREATE INDEX idx_patients_user ON patients(user_id);
CREATE INDEX idx_patients_nik ON patients(nik);

-- ============================================
-- TABLE: queues
-- Deskripsi: Antrean pasien harian
-- ============================================
CREATE TABLE queues (
    id              BIGSERIAL       PRIMARY KEY,
    patient_id      BIGINT          NOT NULL,
    doctor_id       BIGINT          NOT NULL,
    schedule_id     BIGINT          NOT NULL,
    queue_number    VARCHAR(10)     NOT NULL,      -- Format: "A-001", "B-012"
    queue_date      DATE            NOT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'waiting'
                                    CHECK (status IN ('waiting', 'called', 'serving', 'done', 'skipped')),
    complaint       TEXT            NULL,
    called_at       TIMESTAMP       NULL,
    served_at       TIMESTAMP       NULL,
    completed_at    TIMESTAMP       NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_queues_patient
        FOREIGN KEY (patient_id) REFERENCES patients(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_queues_doctor
        FOREIGN KEY (doctor_id) REFERENCES doctors(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_queues_schedule
        FOREIGN KEY (schedule_id) REFERENCES schedules(id)
        ON DELETE CASCADE,

    CONSTRAINT uq_queue_number_date_doctor
        UNIQUE (queue_number, queue_date, doctor_id)
);

CREATE INDEX idx_queues_date ON queues(queue_date);
CREATE INDEX idx_queues_status ON queues(status);
CREATE INDEX idx_queues_doctor_date ON queues(doctor_id, queue_date);
CREATE INDEX idx_queues_patient ON queues(patient_id);
```

### Catatan Relasi

| Relasi | Tipe | Deskripsi |
|--------|------|-----------|
| `users` → `patients` | One-to-One | Satu akun user punya satu profil pasien |
| `doctors` → `schedules` | One-to-Many | Satu dokter bisa punya banyak jadwal (per hari berbeda) |
| `doctors` → `queues` | One-to-Many | Satu dokter bisa punya banyak antrean |
| `patients` → `queues` | One-to-Many | Satu pasien bisa punya banyak antrean (kunjungan berbeda) |
| `schedules` → `queues` | One-to-Many | Satu jadwal bisa punya banyak antrean |

---
---

# PART 3: BACKEND API SPESIFIKASI (LARAVEL)

---

## 3.1 Struktur Folder Laravel (API-Only)

```
smaps-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── V1/
│   │   │           ├── AuthController.php
│   │   │           ├── DoctorController.php
│   │   │           ├── ScheduleController.php
│   │   │           ├── PatientController.php
│   │   │           ├── QueueController.php
│   │   │           └── DashboardController.php
│   │   ├── Middleware/
│   │   │   ├── EnsureIsAdmin.php
│   │   │   └── ForceJsonResponse.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Doctor/
│   │   │   │   ├── StoreDoctorRequest.php
│   │   │   │   └── UpdateDoctorRequest.php
│   │   │   ├── Schedule/
│   │   │   │   ├── StoreScheduleRequest.php
│   │   │   │   └── UpdateScheduleRequest.php
│   │   │   └── Queue/
│   │   │       ├── StoreQueueRequest.php
│   │   │       └── UpdateQueueStatusRequest.php
│   │   └── Resources/
│   │       ├── DoctorResource.php
│   │       ├── ScheduleResource.php
│   │       ├── PatientResource.php
│   │       ├── QueueResource.php
│   │       └── UserResource.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Doctor.php
│   │   ├── Schedule.php
│   │   ├── Patient.php
│   │   └── Queue.php
│   ├── Services/
│   │   ├── QueueService.php          # Logika bisnis antrean
│   │   └── QueueNumberGenerator.php  # Generate nomor antrean
│   └── Enums/
│       ├── UserRole.php
│       ├── QueueStatus.php
│       └── Gender.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2024_01_01_000001_create_doctors_table.php
│   │   ├── 2024_01_01_000002_create_schedules_table.php
│   │   ├── 2024_01_01_000003_create_patients_table.php
│   │   └── 2024_01_01_000004_create_queues_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── AdminSeeder.php
│   │   ├── DoctorSeeder.php
│   │   └── ScheduleSeeder.php
│   └── factories/
│       ├── DoctorFactory.php
│       ├── PatientFactory.php
│       └── QueueFactory.php
├── routes/
│   ├── api.php                       # Semua API routes di sini
│   └── web.php                       # Kosong / hanya redirect
├── config/
│   ├── cors.php
│   ├── sanctum.php
│   └── ...
├── tests/
│   └── Feature/
│       ├── Auth/
│       │   ├── LoginTest.php
│       │   └── RegisterTest.php
│       ├── QueueTest.php
│       └── DoctorTest.php
├── .env.example
├── composer.json
└── README.md
```

---

## 3.2 API Endpoints Table

### Prefix: `/api/v1`

#### 🔐 Authentication

| Method | Endpoint | Auth | Role | Deskripsi |
|--------|----------|------|------|-----------|
| `GET` | `/sanctum/csrf-cookie` | ❌ | — | Ambil CSRF token untuk SPA |
| `POST` | `/api/v1/auth/register` | ❌ | — | Registrasi akun pasien baru |
| `POST` | `/api/v1/auth/login` | ❌ | — | Login & dapatkan token |
| `POST` | `/api/v1/auth/logout` | ✅ | Semua | Logout & revoke token |
| `GET` | `/api/v1/auth/me` | ✅ | Semua | Profil user yang sedang login |

#### 👨‍⚕️ Doctors

| Method | Endpoint | Auth | Role | Deskripsi |
|--------|----------|------|------|-----------|
| `GET` | `/api/v1/doctors` | ❌ | — | Daftar dokter aktif (publik) |
| `GET` | `/api/v1/doctors/{id}` | ❌ | — | Detail dokter + jadwal |
| `POST` | `/api/v1/doctors` | ✅ | Admin | Tambah dokter baru |
| `PUT` | `/api/v1/doctors/{id}` | ✅ | Admin | Update data dokter |
| `DELETE` | `/api/v1/doctors/{id}` | ✅ | Admin | Hapus (soft) dokter |

#### 📅 Schedules

| Method | Endpoint | Auth | Role | Deskripsi |
|--------|----------|------|------|-----------|
| `GET` | `/api/v1/doctors/{doctorId}/schedules` | ❌ | — | Jadwal dokter tertentu |
| `POST` | `/api/v1/schedules` | ✅ | Admin | Tambah jadwal |
| `PUT` | `/api/v1/schedules/{id}` | ✅ | Admin | Update jadwal |
| `DELETE` | `/api/v1/schedules/{id}` | ✅ | Admin | Hapus jadwal |

#### 🎫 Queues

| Method | Endpoint | Auth | Role | Deskripsi |
|--------|----------|------|------|-----------|
| `GET` | `/api/v1/queues/today` | ❌ | — | Antrean hari ini (display publik) |
| `GET` | `/api/v1/queues/today?doctor_id=1` | ❌ | — | Antrean hari ini per dokter |
| `GET` | `/api/v1/queues/my` | ✅ | Pasien | Antrean milik pasien yang login |
| `POST` | `/api/v1/queues` | ✅ | Pasien | Daftar antrean baru |
| `PATCH` | `/api/v1/queues/{id}/call` | ✅ | Admin | Panggil antrean (→ called) |
| `PATCH` | `/api/v1/queues/{id}/serve` | ✅ | Admin | Mulai layani (→ serving) |
| `PATCH` | `/api/v1/queues/{id}/complete` | ✅ | Admin | Selesai (→ done) |
| `PATCH` | `/api/v1/queues/{id}/skip` | ✅ | Admin | Skip antrean (→ skipped) |
| `PATCH` | `/api/v1/queues/{id}/recall` | ✅ | Admin | Panggil ulang yang skipped |

#### 📊 Dashboard

| Method | Endpoint | Auth | Role | Deskripsi |
|--------|----------|------|------|-----------|
| `GET` | `/api/v1/dashboard/stats` | ✅ | Admin | Statistik antrean hari ini |

---

### Detail Request & Response

#### `POST /api/v1/auth/register`

**Request Body:**
```json
{
    "name": "Sari Wulandari",
    "email": "sari@email.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "081234567890",
    "nik": "3201234567890001",
    "birth_date": "1990-05-15",
    "gender": "female",
    "address": "Jl. Merdeka No. 10, Jakarta"
}
```

**Response Success (201):**
```json
{
    "success": true,
    "message": "Registrasi berhasil.",
    "data": {
        "user": {
            "id": 5,
            "name": "Sari Wulandari",
            "email": "sari@email.com",
            "role": "patient"
        },
        "token": "3|abc123def456..."
    }
}
```

**Response Error (422):**
```json
{
    "success": false,
    "message": "Validasi gagal.",
    "errors": {
        "email": ["Email sudah terdaftar."],
        "nik": ["NIK harus 16 digit angka."]
    }
}
```

---

#### `POST /api/v1/auth/login`

**Request Body:**
```json
{
    "email": "sari@email.com",
    "password": "password123"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Login berhasil.",
    "data": {
        "user": {
            "id": 5,
            "name": "Sari Wulandari",
            "email": "sari@email.com",
            "role": "patient"
        },
        "token": "3|abc123def456..."
    }
}
```

**Response Error (401):**
```json
{
    "success": false,
    "message": "Email atau password salah."
}
```

---

#### `POST /api/v1/queues` — Daftar Antrean Baru

**Request Body:**
```json
{
    "doctor_id": 2,
    "schedule_id": 4,
    "complaint": "Demam dan batuk 3 hari"
}
```

**Response Success (201):**
```json
{
    "success": true,
    "message": "Pendaftaran antrean berhasil.",
    "data": {
        "id": 45,
        "queue_number": "B-007",
        "queue_date": "2026-07-06",
        "status": "waiting",
        "complaint": "Demam dan batuk 3 hari",
        "position": 7,
        "doctor": {
            "id": 2,
            "name": "dr. Ahmad Pratama",
            "specialization": "Umum"
        },
        "schedule": {
            "day_of_week": 1,
            "start_time": "08:00",
            "end_time": "12:00"
        },
        "created_at": "2026-07-06T08:30:00.000000Z"
    }
}
```

**Response Error (422):**
```json
{
    "success": false,
    "message": "Validasi gagal.",
    "errors": {
        "doctor_id": ["Dokter tidak ditemukan."],
        "schedule_id": ["Jadwal tidak tersedia untuk hari ini."]
    }
}
```

**Response Error (409):**
```json
{
    "success": false,
    "message": "Anda sudah terdaftar di antrean dokter ini untuk hari ini."
}
```

---

#### `GET /api/v1/queues/today`

**Query Parameters:** `?doctor_id=2&status=waiting`

**Response Success (200):**
```json
{
    "success": true,
    "message": "Data antrean hari ini.",
    "data": {
        "date": "2026-07-06",
        "current_serving": {
            "queue_number": "B-003",
            "patient_name": "Budi Santoso",
            "doctor_name": "dr. Ahmad Pratama"
        },
        "queues": [
            {
                "id": 41,
                "queue_number": "B-004",
                "status": "waiting",
                "patient_name": "Rina Dewi",
                "doctor_name": "dr. Ahmad Pratama",
                "complaint": "Sakit kepala",
                "created_at": "2026-07-06T07:15:00.000000Z"
            },
            {
                "id": 42,
                "queue_number": "B-005",
                "status": "waiting",
                "patient_name": "Joko Widodo",
                "doctor_name": "dr. Ahmad Pratama",
                "complaint": "Kontrol rutin",
                "created_at": "2026-07-06T07:20:00.000000Z"
            }
        ],
        "summary": {
            "total": 12,
            "waiting": 5,
            "called": 1,
            "serving": 1,
            "done": 4,
            "skipped": 1
        }
    }
}
```

---

#### `PATCH /api/v1/queues/{id}/call`

**Request Body:** _(kosong)_

**Response Success (200):**
```json
{
    "success": true,
    "message": "Pasien B-004 dipanggil.",
    "data": {
        "id": 41,
        "queue_number": "B-004",
        "status": "called",
        "patient_name": "Rina Dewi",
        "called_at": "2026-07-06T09:15:00.000000Z"
    }
}
```

**Response Error (400):**
```json
{
    "success": false,
    "message": "Antrean tidak bisa dipanggil. Status saat ini: serving."
}
```

---

## 3.3 Contoh Kode Controller — Daftar Antrean

### File: `app/Http/Requests/Queue/StoreQueueRequest.php`

```php
<?php

namespace App\Http\Requests\Queue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQueueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Sudah dijaga oleh middleware auth:sanctum
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'doctor_id' => [
                'required',
                'integer',
                Rule::exists('doctors', 'id')->where('is_active', true),
            ],
            'schedule_id' => [
                'required',
                'integer',
                Rule::exists('schedules', 'id')->where('doctor_id', $this->input('doctor_id')),
            ],
            'complaint' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'doctor_id.required'  => 'Dokter wajib dipilih.',
            'doctor_id.exists'    => 'Dokter tidak ditemukan atau tidak aktif.',
            'schedule_id.required' => 'Jadwal wajib dipilih.',
            'schedule_id.exists'  => 'Jadwal tidak tersedia untuk dokter ini.',
            'complaint.max'       => 'Keluhan maksimal 500 karakter.',
        ];
    }
}
```

### File: `app/Services/QueueNumberGenerator.php`

```php
<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\Doctor;

class QueueNumberGenerator
{
    /**
     * Generate nomor antrean unik per dokter per hari.
     *
     * Format: {Prefix}-{3-digit nomor urut}
     * Contoh: A-001, A-002, B-001
     *
     * Prefix ditentukan dari urutan alfabet berdasarkan ID dokter:
     * Doctor ID 1 = A, Doctor ID 2 = B, dst.
     */
    public function generate(int $doctorId, string $date): string
    {
        // Tentukan prefix (A, B, C, ...)
        $doctor = Doctor::findOrFail($doctorId);
        $prefix = chr(64 + (($doctor->id - 1) % 26) + 1); // A=65 dalam ASCII

        // Hitung antrean terakhir untuk dokter ini hari ini
        $lastQueue = Queue::where('doctor_id', $doctorId)
            ->where('queue_date', $date)
            ->orderByDesc('id')
            ->first();

        if ($lastQueue) {
            // Ekstrak nomor dari format "A-007"
            $lastNumber = (int) substr($lastQueue->queue_number, strpos($lastQueue->queue_number, '-') + 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('%s-%03d', $prefix, $nextNumber);
    }
}
```

### File: `app/Services/QueueService.php`

```php
<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\Patient;
use App\Models\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class QueueService
{
    public function __construct(
        private QueueNumberGenerator $numberGenerator
    ) {}

    /**
     * Buat antrean baru untuk pasien.
     *
     * @throws ValidationException
     */
    public function createQueue(int $userId, array $data): Queue
    {
        $today = Carbon::today()->toDateString();
        $dayOfWeek = Carbon::today()->dayOfWeek; // 0=Minggu, 1=Senin, ...

        // 1. Pastikan pasien punya profil
        $patient = Patient::where('user_id', $userId)->first();
        if (!$patient) {
            throw ValidationException::withMessages([
                'patient' => ['Profil pasien belum lengkap. Silakan lengkapi data diri.'],
            ]);
        }

        // 2. Validasi jadwal sesuai hari ini
        $schedule = Schedule::where('id', $data['schedule_id'])
            ->where('doctor_id', $data['doctor_id'])
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule) {
            throw ValidationException::withMessages([
                'schedule_id' => ['Jadwal dokter tidak tersedia untuk hari ini.'],
            ]);
        }

        // 3. Cek apakah sudah melebihi kapasitas
        $currentCount = Queue::where('doctor_id', $data['doctor_id'])
            ->where('queue_date', $today)
            ->count();

        if ($currentCount >= $schedule->max_patients) {
            throw ValidationException::withMessages([
                'doctor_id' => ['Kuota antrean dokter ini untuk hari ini sudah penuh.'],
            ]);
        }

        // 4. Cek duplikasi (pasien sudah daftar ke dokter yang sama hari ini)
        $existing = Queue::where('patient_id', $patient->id)
            ->where('doctor_id', $data['doctor_id'])
            ->where('queue_date', $today)
            ->whereNotIn('status', ['done', 'skipped'])
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'doctor_id' => ['Anda sudah terdaftar di antrean dokter ini untuk hari ini.'],
            ]);
        }

        // 5. Generate nomor antrean
        $queueNumber = $this->numberGenerator->generate($data['doctor_id'], $today);

        // 6. Simpan antrean
        return Queue::create([
            'patient_id'  => $patient->id,
            'doctor_id'   => $data['doctor_id'],
            'schedule_id' => $data['schedule_id'],
            'queue_number' => $queueNumber,
            'queue_date'  => $today,
            'status'      => 'waiting',
            'complaint'   => $data['complaint'] ?? null,
        ]);
    }
}
```

### File: `app/Http/Controllers/Api/V1/QueueController.php`

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Queue\StoreQueueRequest;
use App\Http\Resources\QueueResource;
use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class QueueController extends Controller
{
    public function __construct(
        private QueueService $queueService
    ) {}

    /**
     * GET /api/v1/queues/today
     * Antrean hari ini (publik, bisa filter per dokter).
     */
    public function today(Request $request): JsonResponse
    {
        $today = Carbon::today()->toDateString();

        $query = Queue::with(['patient', 'doctor'])
            ->where('queue_date', $today)
            ->orderBy('queue_number');

        // Filter per dokter (opsional)
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        // Filter per status (opsional)
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $queues = $query->get();

        // Cari antrean yang sedang dilayani
        $currentServing = Queue::with(['patient', 'doctor'])
            ->where('queue_date', $today)
            ->where('status', 'serving')
            ->when($request->has('doctor_id'), function ($q) use ($request) {
                $q->where('doctor_id', $request->input('doctor_id'));
            })
            ->first();

        // Hitung summary
        $allQueues = Queue::where('queue_date', $today)
            ->when($request->has('doctor_id'), function ($q) use ($request) {
                $q->where('doctor_id', $request->input('doctor_id'));
            });

        $summary = [
            'total'   => (clone $allQueues)->count(),
            'waiting' => (clone $allQueues)->where('status', 'waiting')->count(),
            'called'  => (clone $allQueues)->where('status', 'called')->count(),
            'serving' => (clone $allQueues)->where('status', 'serving')->count(),
            'done'    => (clone $allQueues)->where('status', 'done')->count(),
            'skipped' => (clone $allQueues)->where('status', 'skipped')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Data antrean hari ini.',
            'data' => [
                'date'            => $today,
                'current_serving' => $currentServing ? [
                    'queue_number' => $currentServing->queue_number,
                    'patient_name' => $currentServing->patient->name,
                    'doctor_name'  => $currentServing->doctor->name,
                ] : null,
                'queues'  => QueueResource::collection($queues),
                'summary' => $summary,
            ],
        ]);
    }

    /**
     * GET /api/v1/queues/my
     * Antrean milik pasien yang sedang login.
     */
    public function my(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Profil pasien belum ditemukan.',
            ], 404);
        }

        $queues = Queue::with(['doctor', 'schedule'])
            ->where('patient_id', $patient->id)
            ->orderByDesc('queue_date')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat antrean Anda.',
            'data'    => QueueResource::collection($queues),
        ]);
    }

    /**
     * POST /api/v1/queues
     * Daftar antrean baru.
     */
    public function store(StoreQueueRequest $request): JsonResponse
    {
        $queue = $this->queueService->createQueue(
            $request->user()->id,
            $request->validated()
        );

        $queue->load(['patient', 'doctor', 'schedule']);

        // Hitung posisi dalam antrean
        $position = Queue::where('doctor_id', $queue->doctor_id)
            ->where('queue_date', $queue->queue_date)
            ->where('status', 'waiting')
            ->where('id', '<=', $queue->id)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran antrean berhasil.',
            'data'    => array_merge(
                (new QueueResource($queue))->toArray($request),
                ['position' => $position]
            ),
        ], 201);
    }

    /**
     * PATCH /api/v1/queues/{id}/call
     * Admin memanggil antrean.
     */
    public function call(Queue $queue): JsonResponse
    {
        if (!in_array($queue->status, ['waiting', 'skipped'])) {
            return response()->json([
                'success' => false,
                'message' => "Antrean tidak bisa dipanggil. Status saat ini: {$queue->status}.",
            ], 400);
        }

        $queue->update([
            'status'    => 'called',
            'called_at' => now(),
        ]);

        $queue->load(['patient', 'doctor']);

        return response()->json([
            'success' => true,
            'message' => "Pasien {$queue->queue_number} dipanggil.",
            'data'    => new QueueResource($queue),
        ]);
    }

    /**
     * PATCH /api/v1/queues/{id}/serve
     * Admin mulai melayani.
     */
    public function serve(Queue $queue): JsonResponse
    {
        if ($queue->status !== 'called') {
            return response()->json([
                'success' => false,
                'message' => "Antrean harus berstatus 'called' untuk mulai dilayani.",
            ], 400);
        }

        $queue->update([
            'status'    => 'serving',
            'served_at' => now(),
        ]);

        $queue->load(['patient', 'doctor']);

        return response()->json([
            'success' => true,
            'message' => "Pasien {$queue->queue_number} sedang dilayani.",
            'data'    => new QueueResource($queue),
        ]);
    }

    /**
     * PATCH /api/v1/queues/{id}/complete
     * Admin menyelesaikan layanan.
     */
    public function complete(Queue $queue): JsonResponse
    {
        if ($queue->status !== 'serving') {
            return response()->json([
                'success' => false,
                'message' => "Antrean harus berstatus 'serving' untuk diselesaikan.",
            ], 400);
        }

        $queue->update([
            'status'       => 'done',
            'completed_at' => now(),
        ]);

        $queue->load(['patient', 'doctor']);

        return response()->json([
            'success' => true,
            'message' => "Pasien {$queue->queue_number} selesai dilayani.",
            'data'    => new QueueResource($queue),
        ]);
    }

    /**
     * PATCH /api/v1/queues/{id}/skip
     * Admin skip antrean.
     */
    public function skip(Queue $queue): JsonResponse
    {
        if (!in_array($queue->status, ['waiting', 'called'])) {
            return response()->json([
                'success' => false,
                'message' => "Antrean tidak bisa di-skip. Status saat ini: {$queue->status}.",
            ], 400);
        }

        $queue->update([
            'status' => 'skipped',
        ]);

        $queue->load(['patient', 'doctor']);

        return response()->json([
            'success' => true,
            'message' => "Pasien {$queue->queue_number} di-skip.",
            'data'    => new QueueResource($queue),
        ]);
    }
}
```

### File: `routes/api.php`

```php
<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DoctorController;
use App\Http\Controllers\Api\V1\ScheduleController;
use App\Http\Controllers\Api\V1\QueueController;
use App\Http\Controllers\Api\V1\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Authentication ──────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    // ── Public Routes ───────────────────────────────────────────
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);
    Route::get('/doctors/{doctor}/schedules', [ScheduleController::class, 'byDoctor']);
    Route::get('/queues/today', [QueueController::class, 'today']);

    // ── Authenticated Routes ────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Pasien
        Route::post('/queues', [QueueController::class, 'store']);
        Route::get('/queues/my', [QueueController::class, 'my']);

        // Admin Only
        Route::middleware('ensure.admin')->group(function () {
            // Doctors CRUD
            Route::post('/doctors', [DoctorController::class, 'store']);
            Route::put('/doctors/{doctor}', [DoctorController::class, 'update']);
            Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy']);

            // Schedules CRUD
            Route::post('/schedules', [ScheduleController::class, 'store']);
            Route::put('/schedules/{schedule}', [ScheduleController::class, 'update']);
            Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy']);

            // Queue Management
            Route::patch('/queues/{queue}/call', [QueueController::class, 'call']);
            Route::patch('/queues/{queue}/serve', [QueueController::class, 'serve']);
            Route::patch('/queues/{queue}/complete', [QueueController::class, 'complete']);
            Route::patch('/queues/{queue}/skip', [QueueController::class, 'skip']);
            Route::patch('/queues/{queue}/recall', [QueueController::class, 'call']); // reuse call

            // Dashboard
            Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        });
    });
});
```

### File: `app/Http/Middleware/EnsureIsAdmin.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang diizinkan.',
            ], 403);
        }

        return $next($request);
    }
}
```

### File: `app/Http/Resources/QueueResource.php`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'queue_number'  => $this->queue_number,
            'queue_date'    => $this->queue_date,
            'status'        => $this->status,
            'complaint'     => $this->complaint,
            'called_at'     => $this->called_at,
            'served_at'     => $this->served_at,
            'completed_at'  => $this->completed_at,
            'created_at'    => $this->created_at,
            'patient_name'  => $this->whenLoaded('patient', fn() => $this->patient->name),
            'doctor'        => $this->whenLoaded('doctor', fn() => [
                'id'             => $this->doctor->id,
                'name'           => $this->doctor->name,
                'specialization' => $this->doctor->specialization,
            ]),
            'schedule'      => $this->whenLoaded('schedule', fn() => [
                'day_of_week' => $this->schedule->day_of_week,
                'start_time'  => $this->schedule->start_time,
                'end_time'    => $this->schedule->end_time,
            ]),
        ];
    }
}
```

---
---

# PART 4: FRONTEND IMPLEMENTATION (JAVASCRIPT)

---

## 4.1 Struktur Folder Frontend

```
smaps-frontend/
├── index.html                    # Entry point
├── package.json                  # (opsional, jika pakai npm/build tool)
├── tailwind.config.js            # Konfigurasi Tailwind CSS
├── postcss.config.js             # PostCSS config
├── vite.config.js                # Vite config (opsional)
│
├── public/
│   ├── favicon.ico
│   └── assets/
│       └── images/
│           └── logo.png
│
├── src/
│   ├── css/
│   │   └── app.css               # Import Tailwind + custom styles
│   │
│   ├── js/
│   │   ├── app.js                # Entry point JavaScript
│   │   ├── router.js             # Simple SPA router (hash-based)
│   │   │
│   │   ├── config/
│   │   │   └── api.js            # Base URL, default headers
│   │   │
│   │   ├── services/
│   │   │   ├── auth.service.js   # Login, register, logout API calls
│   │   │   ├── doctor.service.js # CRUD dokter API calls
│   │   │   ├── queue.service.js  # Antrean API calls
│   │   │   └── http.js           # HTTP client wrapper (Fetch/Axios)
│   │   │
│   │   ├── pages/
│   │   │   ├── LoginPage.js
│   │   │   ├── RegisterPage.js
│   │   │   ├── PatientDashboard.js
│   │   │   ├── QueueRegistration.js
│   │   │   ├── AdminDashboard.js
│   │   │   ├── DoctorManagement.js
│   │   │   ├── ScheduleManagement.js
│   │   │   └── PublicQueueDisplay.js
│   │   │
│   │   ├── components/
│   │   │   ├── Navbar.js
│   │   │   ├── QueueCard.js
│   │   │   ├── QueueTable.js
│   │   │   ├── DoctorCard.js
│   │   │   ├── StatusBadge.js
│   │   │   ├── Modal.js
│   │   │   ├── Alert.js
│   │   │   └── Loader.js
│   │   │
│   │   └── utils/
│   │       ├── storage.js        # LocalStorage helpers (token, user)
│   │       ├── formatter.js      # Format tanggal, waktu, dll.
│   │       └── validators.js     # Validasi form client-side
│   │
│   └── templates/
│       └── ... (opsional, jika pakai template HTML terpisah)
│
└── dist/                         # Output build (production)
    ├── index.html
    └── assets/
        ├── app.css
        └── app.js
```

---

## 4.2 API Service Integration

### File: `src/js/config/api.js`

```javascript
/**
 * Konfigurasi API
 */
const API_CONFIG = {
    BASE_URL: 'http://localhost:8000/api/v1',
    TIMEOUT: 15000, // 15 detik
};

export default API_CONFIG;
```

### File: `src/js/services/http.js`

```javascript
/**
 * HTTP Client Wrapper menggunakan Fetch API
 * Menangani autentikasi token, error handling, dan response parsing.
 */
import API_CONFIG from '../config/api.js';

class HttpClient {
    constructor() {
        this.baseURL = API_CONFIG.BASE_URL;
    }

    /**
     * Ambil token dari localStorage
     */
    getToken() {
        return localStorage.getItem('auth_token');
    }

    /**
     * Set default headers (JSON + Authorization)
     */
    getHeaders(customHeaders = {}) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...customHeaders,
        };

        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        return headers;
    }

    /**
     * Handle response: parse JSON, throw on error
     */
    async handleResponse(response) {
        const data = await response.json();

        if (!response.ok) {
            const error = new Error(data.message || 'Terjadi kesalahan.');
            error.status = response.status;
            error.data = data;
            throw error;
        }

        return data;
    }

    /**
     * GET request
     */
    async get(endpoint, params = {}) {
        const url = new URL(`${this.baseURL}${endpoint}`);
        Object.keys(params).forEach(key => {
            if (params[key] !== undefined && params[key] !== null) {
                url.searchParams.append(key, params[key]);
            }
        });

        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: this.getHeaders(),
        });

        return this.handleResponse(response);
    }

    /**
     * POST request
     */
    async post(endpoint, body = {}) {
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            method: 'POST',
            headers: this.getHeaders(),
            body: JSON.stringify(body),
        });

        return this.handleResponse(response);
    }

    /**
     * PUT request
     */
    async put(endpoint, body = {}) {
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            method: 'PUT',
            headers: this.getHeaders(),
            body: JSON.stringify(body),
        });

        return this.handleResponse(response);
    }

    /**
     * PATCH request
     */
    async patch(endpoint, body = {}) {
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            method: 'PATCH',
            headers: this.getHeaders(),
            body: JSON.stringify(body),
        });

        return this.handleResponse(response);
    }

    /**
     * DELETE request
     */
    async delete(endpoint) {
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            method: 'DELETE',
            headers: this.getHeaders(),
        });

        return this.handleResponse(response);
    }
}

// Singleton instance
const http = new HttpClient();
export default http;
```

### File: `src/js/services/queue.service.js`

```javascript
/**
 * Queue Service
 * Integrasi API untuk fitur antrean pasien.
 */
import http from './http.js';

const QueueService = {

    /**
     * GET — Mengambil daftar antrean hari ini
     * Endpoint: GET /api/v1/queues/today
     *
     * @param {Object} filters - { doctor_id, status } (opsional)
     * @returns {Promise<Object>} Response berisi data antrean hari ini
     *
     * Contoh penggunaan:
     *   const result = await QueueService.getTodayQueues({ doctor_id: 2 });
     *   console.log(result.data.queues);
     *   console.log(result.data.summary);
     *   console.log(result.data.current_serving);
     */
    async getTodayQueues(filters = {}) {
        try {
            const data = await http.get('/queues/today', filters);
            return data;
        } catch (error) {
            console.error('Gagal mengambil antrean hari ini:', error);
            throw error;
        }
    },

    /**
     * POST — Melakukan pendaftaran antrean baru
     * Endpoint: POST /api/v1/queues
     *
     * @param {Object} payload - { doctor_id, schedule_id, complaint }
     * @returns {Promise<Object>} Response berisi data antrean yang baru dibuat
     *
     * Contoh penggunaan:
     *   const result = await QueueService.registerQueue({
     *       doctor_id: 2,
     *       schedule_id: 4,
     *       complaint: 'Demam 3 hari'
     *   });
     *   console.log(result.data.queue_number); // "B-007"
     *   console.log(result.data.position);     // 7
     */
    async registerQueue(payload) {
        try {
            const data = await http.post('/queues', payload);
            return data;
        } catch (error) {
            console.error('Gagal mendaftarkan antrean:', error);
            throw error;
        }
    },

    /**
     * GET — Mengambil antrean milik pasien yang login
     * Endpoint: GET /api/v1/queues/my
     */
    async getMyQueues() {
        try {
            const data = await http.get('/queues/my');
            return data;
        } catch (error) {
            console.error('Gagal mengambil antrean saya:', error);
            throw error;
        }
    },

    /**
     * PATCH — Panggil antrean (Admin)
     * Endpoint: PATCH /api/v1/queues/{id}/call
     */
    async callQueue(queueId) {
        return http.patch(`/queues/${queueId}/call`);
    },

    /**
     * PATCH — Mulai layani (Admin)
     * Endpoint: PATCH /api/v1/queues/{id}/serve
     */
    async serveQueue(queueId) {
        return http.patch(`/queues/${queueId}/serve`);
    },

    /**
     * PATCH — Selesaikan layanan (Admin)
     * Endpoint: PATCH /api/v1/queues/{id}/complete
     */
    async completeQueue(queueId) {
        return http.patch(`/queues/${queueId}/complete`);
    },

    /**
     * PATCH — Skip antrean (Admin)
     * Endpoint: PATCH /api/v1/queues/{id}/skip
     */
    async skipQueue(queueId) {
        return http.patch(`/queues/${queueId}/skip`);
    },
};

export default QueueService;
```

### File: `src/js/services/auth.service.js`

```javascript
/**
 * Auth Service
 * Integrasi API untuk autentikasi pengguna.
 */
import http from './http.js';

const AuthService = {

    /**
     * Login
     */
    async login(email, password) {
        const data = await http.post('/auth/login', { email, password });

        // Simpan token & user ke localStorage
        if (data.success && data.data.token) {
            localStorage.setItem('auth_token', data.data.token);
            localStorage.setItem('auth_user', JSON.stringify(data.data.user));
        }

        return data;
    },

    /**
     * Register
     */
    async register(formData) {
        const data = await http.post('/auth/register', formData);

        if (data.success && data.data.token) {
            localStorage.setItem('auth_token', data.data.token);
            localStorage.setItem('auth_user', JSON.stringify(data.data.user));
        }

        return data;
    },

    /**
     * Logout
     */
    async logout() {
        try {
            await http.post('/auth/logout');
        } finally {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
        }
    },

    /**
     * Get current user from localStorage
     */
    getCurrentUser() {
        const user = localStorage.getItem('auth_user');
        return user ? JSON.parse(user) : null;
    },

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return !!localStorage.getItem('auth_token');
    },

    /**
     * Check if current user is admin
     */
    isAdmin() {
        const user = this.getCurrentUser();
        return user?.role === 'admin';
    },
};

export default AuthService;
```

---

## 4.3 Contoh Penggunaan di Halaman

### Contoh: Halaman Pendaftaran Antrean (Pasien)

```javascript
// src/js/pages/QueueRegistration.js
import QueueService from '../services/queue.service.js';
import http from '../services/http.js';

async function renderQueueRegistration() {
    const container = document.getElementById('app');

    // 1. Ambil daftar dokter
    const doctorsResponse = await http.get('/doctors');
    const doctors = doctorsResponse.data;

    // 2. Render form
    container.innerHTML = `
        <div class="max-w-lg mx-auto mt-10 bg-white rounded-xl shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Daftar Antrean</h2>

            <div id="alert-container"></div>

            <form id="queue-form" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Dokter</label>
                    <select id="doctor_id" name="doctor_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Dokter --</option>
                        ${doctors.map(d => `
                            <option value="${d.id}">${d.name} — ${d.specialization}</option>
                        `).join('')}
                    </select>
                </div>

                <div id="schedule-container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jadwal</label>
                    <select id="schedule_id" name="schedule_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Jadwal --</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keluhan (opsional)</label>
                    <textarea id="complaint" name="complaint" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ceritakan keluhan Anda secara singkat..."></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">
                    Daftar Antrean
                </button>
            </form>

            <div id="result-container" class="hidden mt-6"></div>
        </div>
    `;

    // 3. Event: Saat pilih dokter → load jadwal
    document.getElementById('doctor_id').addEventListener('change', async (e) => {
        const doctorId = e.target.value;
        const scheduleContainer = document.getElementById('schedule-container');
        const scheduleSelect = document.getElementById('schedule_id');

        if (!doctorId) {
            scheduleContainer.classList.add('hidden');
            return;
        }

        const schedulesResponse = await http.get(`/doctors/${doctorId}/schedules`);
        const schedules = schedulesResponse.data;
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        scheduleSelect.innerHTML = `
            <option value="">-- Pilih Jadwal --</option>
            ${schedules.map(s => `
                <option value="${s.id}">${dayNames[s.day_of_week]} | ${s.start_time} - ${s.end_time}</option>
            `).join('')}
        `;
        scheduleContainer.classList.remove('hidden');
    });

    // 4. Event: Submit form
    document.getElementById('queue-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
            doctor_id: parseInt(document.getElementById('doctor_id').value),
            schedule_id: parseInt(document.getElementById('schedule_id').value),
            complaint: document.getElementById('complaint').value || null,
        };

        try {
            const result = await QueueService.registerQueue(payload);
            const q = result.data;

            // Tampilkan hasil
            document.getElementById('queue-form').classList.add('hidden');
            document.getElementById('result-container').classList.remove('hidden');
            document.getElementById('result-container').innerHTML = `
                <div class="text-center bg-green-50 border border-green-200 rounded-xl p-8">
                    <div class="text-green-600 text-lg font-semibold mb-2">✅ Pendaftaran Berhasil!</div>
                    <div class="text-6xl font-bold text-blue-700 my-4">${q.queue_number}</div>
                    <div class="text-gray-600 space-y-1">
                        <p>Dokter: <strong>${q.doctor.name}</strong></p>
                        <p>Tanggal: <strong>${q.queue_date}</strong></p>
                        <p>Posisi Antrean: <strong>#${q.position}</strong></p>
                    </div>
                    <p class="mt-4 text-sm text-gray-500">Silakan tunggu sampai nomor Anda dipanggil.</p>
                </div>
            `;
        } catch (error) {
            const alertContainer = document.getElementById('alert-container');
            const errors = error.data?.errors;
            let errorHtml = error.data?.message || 'Terjadi kesalahan.';

            if (errors) {
                errorHtml += '<ul class="list-disc list-inside mt-2">';
                Object.values(errors).flat().forEach(msg => {
                    errorHtml += `<li>${msg}</li>`;
                });
                errorHtml += '</ul>';
            }

            alertContainer.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    ${errorHtml}
                </div>
            `;
        }
    });
}

export default renderQueueRegistration;
```

---

## 4.4 UI/UX Component Blueprint — Dashboard Admin "Panggil Antrean"

### Wireframe Struktur

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  NAVBAR  [Logo SMAPS]              [Admin: Suster Dewi]  [Logout]         │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌─── SUMMARY CARDS ──────────────────────────────────────────────────┐    │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────┐│    │
│  │  │ Total    │  │ Menunggu │  │ Dipanggil│  │ Selesai  │  │ Skip ││    │
│  │  │   25     │  │   12     │  │    1     │  │   10     │  │   2  ││    │
│  │  │ 📊       │  │ 🕐       │  │ 📢       │  │ ✅       │  │ ⏭️   ││    │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘  └──────┘│    │
│  └────────────────────────────────────────────────────────────────────┘    │
│                                                                             │
│  ┌─── SEDANG DILAYANI (HIGHLIGHT) ────────────────────────────────────┐    │
│  │                                                                     │    │
│  │   🔔  Nomor Antrean Saat Ini:    B-003                            │    │
│  │       Pasien: Budi Santoso                                         │    │
│  │       Dokter: dr. Ahmad Pratama — Umum                            │    │
│  │                                                                     │    │
│  │   [🟢 Mulai Layani]   [✅ Selesai]   [⏭️ Skip]                    │    │
│  │                                                                     │    │
│  └─────────────────────────────────────────────────────────────────────┘    │
│                                                                             │
│  ┌─── FILTER ─────────────────────────────────────────────────────────┐    │
│  │  Dokter: [▼ Semua Dokter]    Status: [▼ Semua Status]   [🔍 Cari]│    │
│  └─────────────────────────────────────────────────────────────────────┘    │
│                                                                             │
│  ┌─── TABEL ANTREAN ─────────────────────────────────────────────────┐    │
│  │  # │ No. Antrean │ Nama Pasien   │ Dokter         │ Status  │ Aksi│    │
│  │────┼─────────────┼───────────────┼────────────────┼─────────┼─────│    │
│  │  1 │   B-004     │ Rina Dewi     │ dr. Ahmad      │🟡 Wait  │ 📢 │    │
│  │  2 │   B-005     │ Joko W.       │ dr. Ahmad      │🟡 Wait  │ 📢 │    │
│  │  3 │   B-006     │ Sari W.       │ dr. Ahmad      │🟡 Wait  │ 📢 │    │
│  │  4 │   A-003     │ Ani S.        │ dr. Maya       │🟢 Serve │ ✅  │    │
│  │  5 │   B-001     │ Dedi P.       │ dr. Ahmad      │🔵 Done  │ —  │    │
│  │  6 │   B-002     │ Lina K.       │ dr. Ahmad      │🔴 Skip  │ 🔄 │    │
│  └─────────────────────────────────────────────────────────────────────┘    │
│                                                                             │
│  ┌─── TOMBOL AKSI UTAMA ─────────────────────────────────────────────┐    │
│  │                                                                     │    │
│  │           [ 📢 PANGGIL ANTREAN BERIKUTNYA ]                        │    │
│  │                                                                     │    │
│  └─────────────────────────────────────────────────────────────────────┘    │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Kode HTML + Tailwind CSS

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — SMAPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- ========================================= -->
    <!-- NAVBAR                                     -->
    <!-- ========================================= -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">🏥</span>
                    <span class="text-xl font-bold text-gray-800">SMAPS</span>
                    <span class="text-sm text-gray-500">| Dashboard Admin</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">👤 Suster Dewi</span>
                    <button id="btn-logout"
                        class="text-sm text-red-600 hover:text-red-800 font-medium">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========================================= -->
    <!-- MAIN CONTENT                               -->
    <!-- ========================================= -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Judul Halaman -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Antrean Hari Ini</h1>
            <p class="text-gray-500 mt-1" id="current-date">Senin, 6 Juli 2026</p>
        </div>

        <!-- ======= SUMMARY CARDS ======= -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8" id="summary-cards">
            <!-- Card: Total -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-3xl font-bold text-gray-900" id="stat-total">25</p>
                    </div>
                    <span class="text-3xl">📊</span>
                </div>
            </div>
            <!-- Card: Menunggu -->
            <div class="bg-yellow-50 rounded-xl shadow-sm border border-yellow-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-700">Menunggu</p>
                        <p class="text-3xl font-bold text-yellow-800" id="stat-waiting">12</p>
                    </div>
                    <span class="text-3xl">🕐</span>
                </div>
            </div>
            <!-- Card: Dipanggil -->
            <div class="bg-blue-50 rounded-xl shadow-sm border border-blue-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-700">Dipanggil</p>
                        <p class="text-3xl font-bold text-blue-800" id="stat-called">1</p>
                    </div>
                    <span class="text-3xl">📢</span>
                </div>
            </div>
            <!-- Card: Selesai -->
            <div class="bg-green-50 rounded-xl shadow-sm border border-green-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-700">Selesai</p>
                        <p class="text-3xl font-bold text-green-800" id="stat-done">10</p>
                    </div>
                    <span class="text-3xl">✅</span>
                </div>
            </div>
            <!-- Card: Skip -->
            <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-700">Dilewati</p>
                        <p class="text-3xl font-bold text-red-800" id="stat-skipped">2</p>
                    </div>
                    <span class="text-3xl">⏭️</span>
                </div>
            </div>
        </div>

        <!-- ======= CURRENT SERVING (HIGHLIGHT) ======= -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-lg p-8 mb-8 text-white"
             id="current-serving-card">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-blue-200 text-sm font-medium uppercase tracking-wide mb-1">
                        Sedang Dilayani
                    </p>
                    <p class="text-5xl md:text-6xl font-extrabold tracking-tight" id="serving-number">
                        B-003
                    </p>
                    <div class="mt-3 space-y-1">
                        <p class="text-blue-100">
                            Pasien: <strong class="text-white" id="serving-patient">Budi Santoso</strong>
                        </p>
                        <p class="text-blue-100">
                            Dokter: <strong class="text-white" id="serving-doctor">dr. Ahmad Pratama — Umum</strong>
                        </p>
                    </div>
                </div>
                <div class="mt-6 md:mt-0 flex flex-wrap gap-3">
                    <button onclick="handleServe()"
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors shadow-md">
                        🟢 Mulai Layani
                    </button>
                    <button onclick="handleComplete()"
                        class="bg-white hover:bg-gray-100 text-blue-700 px-6 py-3 rounded-lg font-semibold transition-colors shadow-md">
                        ✅ Selesai
                    </button>
                    <button onclick="handleSkip()"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors shadow-md">
                        ⏭️ Skip
                    </button>
                </div>
            </div>
        </div>

        <!-- ======= FILTER BAR ======= -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Filter Dokter</label>
                    <select id="filter-doctor"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Dokter</option>
                        <option value="1">dr. Maya Sari — Anak</option>
                        <option value="2">dr. Ahmad Pratama — Umum</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Filter Status</label>
                    <select id="filter-status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="waiting">🟡 Menunggu</option>
                        <option value="called">📢 Dipanggil</option>
                        <option value="serving">🟢 Dilayani</option>
                        <option value="done">🔵 Selesai</option>
                        <option value="skipped">🔴 Dilewati</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="loadQueues()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                        🔍 Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- ======= TABEL ANTREAN ======= -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Daftar Antrean</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">No. Antrean</th>
                            <th class="px-6 py-3">Nama Pasien</th>
                            <th class="px-6 py-3">Dokter</th>
                            <th class="px-6 py-3">Keluhan</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="queue-table-body">
                        <!-- Row: Waiting -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-500">1</td>
                            <td class="px-6 py-4">
                                <span class="text-lg font-bold text-gray-800">B-004</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">Rina Dewi</td>
                            <td class="px-6 py-4 text-sm text-gray-600">dr. Ahmad Pratama</td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-[200px] truncate">Sakit kepala</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    🟡 Menunggu
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg font-medium transition-colors"
                                    onclick="handleCall(41)">
                                    📢 Panggil
                                </button>
                            </td>
                        </tr>
                        <!-- Row: Called -->
                        <tr class="hover:bg-gray-50 transition-colors bg-blue-50">
                            <td class="px-6 py-4 text-sm text-gray-500">2</td>
                            <td class="px-6 py-4">
                                <span class="text-lg font-bold text-blue-700">B-003</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">Budi Santoso</td>
                            <td class="px-6 py-4 text-sm text-gray-600">dr. Ahmad Pratama</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Demam</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    📢 Dipanggil
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center space-x-1">
                                <button class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded-lg font-medium transition-colors"
                                    onclick="handleServe(40)">
                                    🟢 Layani
                                </button>
                                <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg font-medium transition-colors"
                                    onclick="handleSkip(40)">
                                    ⏭️
                                </button>
                            </td>
                        </tr>
                        <!-- Row: Done -->
                        <tr class="hover:bg-gray-50 transition-colors opacity-60">
                            <td class="px-6 py-4 text-sm text-gray-500">3</td>
                            <td class="px-6 py-4">
                                <span class="text-lg font-bold text-gray-400">B-001</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">Dedi Prasetyo</td>
                            <td class="px-6 py-4 text-sm text-gray-400">dr. Ahmad Pratama</td>
                            <td class="px-6 py-4 text-sm text-gray-400">Kontrol rutin</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Selesai
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-gray-400 text-sm">—</span>
                            </td>
                        </tr>
                        <!-- Row: Skipped -->
                        <tr class="hover:bg-gray-50 transition-colors opacity-60">
                            <td class="px-6 py-4 text-sm text-gray-500">4</td>
                            <td class="px-6 py-4">
                                <span class="text-lg font-bold text-red-400">B-002</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">Lina Kusuma</td>
                            <td class="px-6 py-4 text-sm text-gray-400">dr. Ahmad Pratama</td>
                            <td class="px-6 py-4 text-sm text-gray-400">Batuk</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    🔴 Dilewati
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button class="bg-orange-500 hover:bg-orange-600 text-white text-xs px-3 py-1.5 rounded-lg font-medium transition-colors"
                                    onclick="handleRecall(39)">
                                    🔄 Panggil Ulang
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ======= TOMBOL PANGGIL BERIKUTNYA ======= -->
        <div class="text-center">
            <button onclick="handleCallNext()"
                class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-lg font-bold px-12 py-4 rounded-2xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                📢 PANGGIL ANTREAN BERIKUTNYA
            </button>
        </div>

    </main>

    <!-- ========================================= -->
    <!-- FOOTER                                     -->
    <!-- ========================================= -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-4 text-center text-sm text-gray-500">
            © 2026 SMAPS — Sistem Manajemen Antrean & Pendaftaran Pasien
        </div>
    </footer>

    <!-- ========================================= -->
    <!-- JAVASCRIPT (Inline untuk demonstrasi)      -->
    <!-- ========================================= -->
    <script type="module">
        import QueueService from './src/js/services/queue.service.js';

        // Auto-refresh antrean setiap 10 detik
        async function loadQueues() {
            try {
                const doctorId = document.getElementById('filter-doctor').value || undefined;
                const status = document.getElementById('filter-status').value || undefined;

                const result = await QueueService.getTodayQueues({
                    doctor_id: doctorId,
                    status: status,
                });

                // Update summary cards
                const s = result.data.summary;
                document.getElementById('stat-total').textContent = s.total;
                document.getElementById('stat-waiting').textContent = s.waiting;
                document.getElementById('stat-called').textContent = s.called;
                document.getElementById('stat-done').textContent = s.done;
                document.getElementById('stat-skipped').textContent = s.skipped;

                // Update current serving
                const serving = result.data.current_serving;
                if (serving) {
                    document.getElementById('serving-number').textContent = serving.queue_number;
                    document.getElementById('serving-patient').textContent = serving.patient_name;
                    document.getElementById('serving-doctor').textContent = serving.doctor_name;
                }

                // Update table (simplified — in production, render dynamically)
                console.log('Antrean loaded:', result.data.queues);

            } catch (error) {
                console.error('Gagal load antrean:', error);
            }
        }

        // Expose functions to global scope for onclick handlers
        window.loadQueues = loadQueues;
        window.handleCall = async (id) => {
            if (confirm('Panggil pasien ini?')) {
                await QueueService.callQueue(id);
                loadQueues();
            }
        };
        window.handleServe = async (id) => {
            await QueueService.serveQueue(id);
            loadQueues();
        };
        window.handleComplete = async (id) => {
            await QueueService.completeQueue(id);
            loadQueues();
        };
        window.handleSkip = async (id) => {
            if (confirm('Skip pasien ini?')) {
                await QueueService.skipQueue(id);
                loadQueues();
            }
        };
        window.handleRecall = async (id) => {
            await QueueService.callQueue(id);
            loadQueues();
        };
        window.handleCallNext = async () => {
            // Logika: ambil antrean waiting pertama, lalu panggil
            const result = await QueueService.getTodayQueues({ status: 'waiting' });
            const nextQueue = result.data.queues[0];
            if (nextQueue) {
                await QueueService.callQueue(nextQueue.id);
                loadQueues();
            } else {
                alert('Tidak ada antrean yang menunggu.');
            }
        };

        // Initial load
        loadQueues();

        // Auto-refresh setiap 10 detik
        setInterval(loadQueues, 10000);
    </script>

</body>
</html>
```

---
---

# APPENDIX

## A. Status Badge Color Guide (Tailwind CSS)

| Status | Background | Text | Contoh |
|--------|-----------|------|--------|
| `waiting` | `bg-yellow-100` | `text-yellow-800` | 🟡 Menunggu |
| `called` | `bg-blue-100` | `text-blue-800` | 📢 Dipanggil |
| `serving` | `bg-indigo-100` | `text-indigo-800` | 🟢 Dilayani |
| `done` | `bg-green-100` | `text-green-800` | ✅ Selesai |
| `skipped` | `bg-red-100` | `text-red-800` | 🔴 Dilewati |

## B. Environment Variables (.env)

```env
# Backend (.env)
APP_NAME=SMAPS
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smaps
DB_USERNAME=postgres
DB_PASSWORD=secret

SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost
SESSION_DOMAIN=localhost
```

```env
# Frontend (.env)
VITE_API_BASE_URL=http://localhost:8000/api/v1
```

## C. Quick Start Commands

```bash
# Backend
cd smaps-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

# Frontend
cd smaps-frontend
npm install
npm run dev
```

---

> **Dokumen ini bersifat living document.** Akan diperbarui seiring perkembangan proyek.  
> Disiapkan sebagai blueprint teknis untuk implementasi langsung ke kode program.

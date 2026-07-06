
Repositori ini menyediakan API backend lengkap beserta dokumentasi Swagger UI interaktif. Berikut langkah-langkah untuk menjalankan pengujian dan mencoba API.

---

## 1. Menjalankan Server & Database
Pastikan server lokal berjalan:
```bash
# Jalankan server
php artisan serve
```
Secara default, server berjalan di `http://localhost:8000`.

---

## 2. Mengakses Dokumentasi Swagger UI
Anda dapat membuka Swagger UI langsung melalui browser untuk mencoba setiap API:
* **URL Swagger UI:** [http://localhost:8000/docs/index.html](http://localhost:8000/docs/index.html)
* **URL OpenAPI Specification (YAML):** [http://localhost:8000/docs/openapi.yaml](http://localhost:8000/docs/openapi.yaml)

### Menggunakan Swagger UI untuk Endpoint Terproteksi:
1. Lakukan **Login** dengan mengirimkan request ke `POST /api/login`.
2. Salin token dari `token` di dalam response JSON (contoh: `1|laravel_sanctum_token...`).
3. Klik tombol **Authorize** di pojok kanan atas Swagger UI.
4. Masukkan token tersebut (cukup paste tokennya langsung) lalu klik **Authorize**.

---

## 3. Akun Bawaan (Seeder)
Database telah di-seed dengan akun percobaan berikut:
* **Admin:**
  * Email: `admin@smaps.test`
  * Password: `password`
* **Dokter (Dr. Andi Pratama):**
  * Email: `drandipratama@smaps.test`
  * Password: `password`
* **Pasien (Budi Santoso):**
  * Email: `budisantoso@smaps.test`
  * Password: `password`

---

## 4. Alur Skenario Pengujian (Manual via Swagger / Postman)

### Skenario A: Pasien Mendaftar Antrean
1. **Login** sebagai Pasien (`budisantoso@smaps.test`). Salin tokennya.
2. Set token di **Authorize** header.
3. Kirim request ke `GET /api/doctors` untuk mendapatkan ID dokter (misal ID: `1`, prefix antrean `A`).
4. Daftarkan antrean melalui `POST /api/queues` dengan request body:
   ```json
   {
     "doctor_id": 1
   }
   ```
5. Response akan mengembalikan nomor antrean (misal: `A-001`).

### Skenario B: Dokter Memanggil Antrean
1. **Login** sebagai Dokter (`drandipratama@smaps.test`) atau Admin (`admin@smaps.test`). Salin tokennya.
2. Set token di **Authorize** header.
3. Ubah status antrean pasien (misal ID antrean: `1`) ke **called** via `PUT /api/queues/1/status` dengan body:
   ```json
   {
     "status": "called"
   }
   ```
4. Lakukan hal yang sama untuk memajukan status ke `serving` lalu `done`.

### Skenario C: Memantau Antrean Realtime (Display/Pasien)
* **Lihat seluruh antrean aktif (untuk display rumah sakit):**
  `GET /api/queues/status` (Tanpa Auth)
* **Cek estimasi waktu tunggu spesifik:**
  `GET /api/queues/1/status` (Tanpa Auth)
* **Cek ringkasan antrean dokter:**
  `GET /api/doctors/1/summary` (Tanpa Auth)

---

## 5. Menjalankan Automated Tests (Pest)
Anda dapat menjalankan suite pengujian otomatis (Feature & Unit Tests) menggunakan Pest dengan perintah:
```bash
php artisan test --compact
```

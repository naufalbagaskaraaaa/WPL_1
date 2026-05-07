# Software Development Workshop - TI B4 (WPL_1)

![Laravel Version](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel)
![PHP Version](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php)
![Database](https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql)
![Midtrans](https://img.shields.io/badge/Midtrans-Payment_Gateway-0096e6?style=for-the-badge)

Proyek ini adalah implementasi sistem dari modul **Web Programming Workshop (WPL_1)**. Aplikasi ini dirancang untuk mendemonstrasikan berbagai konsep pengembangan web modern dari sisi frontend dan backend, serta interaksi basis data tingkat lanjut, API pihak ketiga (OAuth & Payment Gateway), administrasi file, dan sistem transaksi multi-role.

---

## 🌐 Demo & Credentials

**Live Demo URL:** [https://collage-nopal.user.cloudjkt01.com](https://collage-nopal.user.cloudjkt01.com)

**Hak Akses Admin:**
- **Email:** `admin@mail.com`
- **Password:** `123456`

---

## 🚀 Fitur Utama & Modul

Aplikasi ini memiliki beberapa modul pembelajaran yang saling terintegrasi:

1. **Sistem Autentikasi Lanjutan**
   - Standard Login & Register dengan verifikasi OTP (One-Time Password) via Email.
   - Login menggunakan **Google OAuth** (Laravel Socialite).

2. **Master Data & Datatables**
   - Manajemen Buku dan Kategori.
   - Manajemen Barang menggunakan AJAX (jQuery & Axios), serta integrasi dengan **Yajra DataTables**.

3. **Sistem Point of Sale (POS) / Kasir**
   - Antarmuka Kasir dinamis.
   - Pencarian barang dengan Barcode Scanner.
   - Pencatatan transaksi real-time menggunakan Axios & jQuery.

4. **Sistem Pemesanan (Foodcourt / Tenant)**
   - Manajemen Menu oleh Vendor.
   - Antarmuka pemesanan untuk pelanggan (Customer).
   - Integrasi sistem pembayaran **Midtrans** (termasuk webhook notifikasi otomatis).

5. **Pengelolaan File, Dokumen, dan Media**
   - Upload & penyimpanan gambar (Mendukung mode simpan File & Database BLOB).
   - Output dokumen berformat PDF (Landscape & Portrait/Undangan) menggunakan DomPDF.
   - Pembuatan dan pemrosesan QR Code & Barcode.

6. **Filter Pencarian Wilayah Dinamis**
   - Dependent dropdown tingkat Provinsi -> Kota/Kabupaten -> Kecamatan -> Desa (Desa/Kelurahan) via AJAX.

---

## 🛠 Tech Stack & Dependencies

- **Framework:** Laravel 12 / PHP 8.4
- **Database:** PostgreSQL (dapat berjalan di MySQL dengan beberapa penyesuaian)
- **Frontend Toolkit:** Bootstrap 5 (Purple Admin Template), jQuery, Axios
- **Pustaka Utama (Vendor):**
  - `midtrans/midtrans-php`: API Payment Gateway Midtrans.
  - `laravel/socialite`: Google Auth.
  - `barryvdh/laravel-dompdf`: Generator PDF.
  - `yajra/laravel-datatables-oracle`: Tabel dinamis server-side.
  - `picqer/php-barcode-generator` & `endroid/qr-code`: Generator QR dan Barcode.

---

## ⚙️ Panduan Instalasi (Development)

Ikuti langkah-langkah berikut untuk menjalankan project ini di lingkungan lokal Anda:

1. **Kloning Repositori:**
   ```bash
   git clone <url-repo-anda>
   cd WPL_1
   ```

2. **Install Dependensi PHP & Node:**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Konfigurasi Environment:**
   - Salin file konfigurasi `.env.example` ke `.env`:
     ```bash
     cp .env.example .env
     ```
   - Sesuaikan konfigurasi database (DB_CONNECTION, DB_HOST, DB_PORT, dsb).
   - Pastikan telah menambahkan API keys untuk **Google OAuth** dan **Midtrans** di dalam `.env` sebelum menjalankan fitur terkait.

4. **Generate Key & Database Migration:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

5. **Start Development Server:**
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan di `http://127.0.0.1:8000`

---

## 📸 Screenshots

- **Login Page** - [Lihat Gambar](https://drive.google.com/file/d/1BAUr0WPQmwFSmmoZZqntgyz7Xhb-JOAP/view?usp=sharing)
- **Dashboard Admin** - [Lihat Gambar](https://drive.google.com/file/d/1pAFV5wo-jrXVj7FSpz4Gr9BOsN-NVZq2/view?usp=sharing)
- **Menu Kategori** - [Lihat Gambar](https://drive.google.com/file/d/1g19rfXMQac-C-Kj_UVlHKvJ5xqLNDCm6/view?usp=sharing)
- **Menu Buku** - [Lihat Gambar](https://drive.google.com/file/d/1elVgqphRG4gQPtYxAnySyULcwoLvlmOW/view?usp=sharing)

---
*Dibuat untuk keperluan edukasi dan praktek Software Development Workshop.*
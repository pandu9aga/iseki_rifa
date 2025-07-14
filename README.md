# 📦 Laravel Reporting App

## 📋 Deskripsi
Aplikasi pelaporan berbasis Laravel untuk mencatat dan mengelola data perizinan pegawai secara efisien.

## ✨ Fitur

### 1. Modul Report
- Tambah data perizinan
- Laporan harian (teks)
- Laporan bulanan (format `.xlsx`)
- Menampilkan seluruh data perizinan dengan fitur filter
- Edit dan hapus data perizinan

### 2. Modul Pegawai
- Tambah data pegawai
- Menampilkan seluruh data pegawai dengan fitur filter
- Edit dan hapus data pegawai

## 🚀 Langkah Instalasi

### 1. Clone atau Extract
Extract file ZIP ke direktori lokal.

### 2. Install Dependency
```bash
composer install
```

### 3. Setup Database

* Buat database baru bernama `reportabsendb`
* Import file `backup.sql` ke dalam database tersebut

### 4. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Lalu ubah bagian koneksi database:

```env
DB_DATABASE=reportabsendb
DB_USERNAME=root
DB_PASSWORD=  # isi jika ada password
```

### 5. Generate Key

```bash
php artisan key:generate
```

### 6. Jalankan Server

```bash
php artisan serve
```

---

## ✅ Catatan

* Folder `vendor/` dan file `.env` tidak disertakan dalam ZIP, pastikan langkah instalasi dilakukan dengan benar.
* Bahasa lokal: Indonesia (`APP_LOCALE=id`)

---

# 👑 Raja POS — Modern Retail & Enterprise Management System

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire 3](https://img.shields.io/badge/Livewire-3.x-4E5BA6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Tests](https://img.shields.io/badge/Tests-42%20Passed%20(163%20Assertions)-10B981?style=for-the-badge&logo=githubactions&logoColor=white)](#-automated-testing)
[![Enterprise Ready](https://img.shields.io/badge/Enterprise-Read--Replica%20%26%20Redis%20Ready-3F7A5D?style=for-the-badge)](#-enterprise-architecture)

---

## 📌 Overview

**Raja POS** adalah sistem Kasir (Point of Sale) & Manajemen Ritel modern berbasis web yang dirancang khusus untuk toko fisik, toko aksesori, dan layanan transaksi digital/PPOB. Sistem ini menggabungkan integritas transaksi tingkat tinggi (*ACID-compliant*, *Row Locking*), manajemen stok barang fisik otomatis, pemantauan saldo mutasi toko, hingga dukungan arsitektur **Enterprise Grade** (Caching Layer & Queue Workers).

---

## ✨ Key Features

### 🛒 1. Modern POS Kasir & Transaksi
- **Kasir Cepat & Responsif**: Interface kasir serba guna mendukung scanning barcode, pencarian produk instan, serta pemisahan jenis stok (Fisik, Digital, & Layanan).
- **Multi-Payment & Split Account**: Pembayaran fleksibel kombinasi Tunai, Transfer Bank, QRIS, & E-Wallet dalam 1 transaksi belanja.
- **Strict Stock Guard**: Mencegah transaksi kasir jika stok fisik di lokasi toko kosong atau kurang (*zero negative stock*).
- **Open-Nominal Layanan**: Penanganan khusus produk PPOB/Jasa (PLN, Pulsa, Transfer Bank) dengan proteksi *non-negative margin* dan batas minimum nominal Rp 1.000.
- **Cetak Struk Thermal**: Generasi struk belanja kasir ukuran 80mm/58mm thermal dan struk digital.

### 📦 2. Manajemen Stok & Inventaris
- **Master Produk & Katalog**: Pengelompokan Kategori, Subtipe/Jenis, dan Brand/Merk.
- **Stok Barang Real-Time**: Sinkronisasi otomatis stok fisik di seluruh lokasi toko.
- **Stock Opname & Adjustments**: Penyesuaian fisik stok toko dengan persetujuan (*approval flow*) dan audit log pergerakan stok.
- **Deduplikasi & Normalisasi Import**: Fitur impor masal via formatted Excel/CSV tanpa menambah stok awal secara ganda pada produk yang sudah ada.

### 💰 3. Keuangan & Mutasi Saldo
- **Akun Saldo Toko**: Pemisahan pencatatan saldo Tunai (*CASH*), Rekening Bank, QRIS, & Provider E-Wallet.
- **Laporan Laba-Rugi & Mutasi**: Pencatatan otomatis jurnal transaksi masuk/keluar, biaya operasional, dan margin keuntungan (*gross profit*).

### ♻️ 4. Pembatalan Transaksi & Sampah Transaksi
- **30-Day Trash Retention**: Penanganan pembatalan transaksi oleh kasir/admin dengan pengembalian stok barang fisik dan pengembalian saldo secara otomatis (*atomic reversal*).
- **Auto Delete**: Transaksi dibatalkan otomatis terhapus permanen setelah 30 hari retensi.

### ⚡ 5. Arsitektur Enterprise Grade
- **High-Performance Caching Layer (`CatalogCacheService`)**: Menyajikan katalog produk kasir dari Redis/Memory Cache dengan pembersihan otomatis (*invalidation*) saat terjadi perubahan data.
- **Async Queue Background Workers**: Pengolahan histori pergerakan stok (`RecordInventoryMovementJob`) dan audit log (`ProcessAuditLogJob`) melalui antrean pekerja (*Laravel Queue*).
- **Multi-Database Read/Write Replicas**: Konfigurasi terpisah antara Write DB (master transaksi) dan Read Replica DB (laporan & analitik).

---

## 🛠️ Tech Stack

- **Framework**: [Laravel 12](https://laravel.com)
- **Frontend / Reactive UI**: [Livewire 3](https://livewire.laravel.com), [Alpine.js](https://alpinejs.dev)
- **Styling**: [Tailwind CSS 3](https://tailwindcss.com) (Palet EMCO Emerald `#3F7A5D` & Ochre `#C2AC7C`)
- **Database**: MySQL 8.x (Engine InnoDB, Foreign Keys, Read/Write Replica Support)
- **Excel Processor**: [OpenSpout](https://github.com/openspout/openspout) (Fast XLSX/CSV Parser)
- **Testing**: PHPUnit / Pest Framework

---

## 🚀 Quick Start / Local Installation

### Requirements
- PHP `>= 8.2` (Extensions: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`)
- MySQL `>= 8.0` / MariaDB `>= 10.4`
- Composer `>= 2.x`

### Step 1: Clone Repository
```bash
git clone https://github.com/Anantarf/Raja-POS.git
cd POS
```

### Step 2: Environment Setup
Duplikasi file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

Sesuaikan konfigurasi database MySQL di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=raja_pos
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3: Install Dependencies & Generate Key
```bash
composer install
php artisan key:generate
```

### Step 4: Database Migration & Seeding
Jalankan migrasi database dan seeder bawaan (Superadmin User & Master Data):
```bash
php artisan migrate --seed
```

### Step 5: Start Local Server
```bash
php artisan serve
```
Akses aplikasi di browser pada: `http://127.0.0.1:8000`

---

## 🔑 Default Credentials

| Role | Username / Email | Password | Akses |
|---|---|---|---|
| **Superadmin (Owner)** | `superadmin` / `admin@rajapos.com` | `password` | Akses Penuh All Features |
| **Kasir (Cashier)** | `kasir` | `password` | POS Checkout & Struk |

---

## 🧪 Automated Testing

Proyek ini dilengkapi dengan suite pengujian otomatis menyeluruh mencakup POS Checkout, Pembayaran Campuran, Pemulihan Sampah, Pengurutan Tabel, dan Arsitektur Enterprise:

```bash
php artisan test
```

**Hasil Pengujian:**
```text
  Tests:    42 passed (163 assertions)
  Duration: 8.60s
```

---

## 🛡️ License & Credits

- **Developer**: Google Deepmind / Antigravity Engineering Team & User Pair
- **License**: Proprietary / Commercial License for Raja Aksesoris Retail Network.

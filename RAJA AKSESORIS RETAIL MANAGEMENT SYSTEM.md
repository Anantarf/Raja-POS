# RAJA AKSESORIS RETAIL MANAGEMENT SYSTEM
## Product Requirements Document, Design System & Database Architecture
**Version:** 1.0  
**Platform:** Web Application  
**Final Stack:** Laravel 12 + Livewire + MySQL 8 + Tailwind CSS + Filament

**Implementation Decision:** Custom Livewire digunakan untuk layar POS. Filament digunakan untuk admin panel, CRUD master data, inventory, user, dan laporan operasional.

---

# 1. PRODUCT OVERVIEW

## 1.1 Product Name

**Raja Aksesoris Retail Management System**

Short name:

**Raja POS**

## 1.2 Product Description

Raja POS adalah sistem operasional toko yang mengintegrasikan:

- Point of Sale
- Inventory barang fisik
- Produk digital
- Saldo provider dan rekening
- Pembayaran
- Pergerakan stok
- Laporan penjualan
- Monitoring profit
- User & access management

Sistem dibangun untuk menggantikan proses operasional yang saat ini masih berbasis spreadsheet.

Workbook Raja Aksesoris saat ini sudah memiliki pencatatan transaksi dengan informasi kode produk, produk/layanan, kategori, jenis, merk, QTY, harga modal, sumber modal, harga jual, metode pembayaran, dan status transaksi.

Untuk inventory fisik juga sudah tersedia atribut kode produk, kategori, nama barang, merk, jenis, status stok, jumlah stok, harga modal, harga jual, serta margin.

Produk digital memiliki struktur serupa dengan kode produk, kategori, nama layanan, provider/merk, harga modal, harga jual, margin, dan status.

---

# 2. PRODUCT GOALS

## Primary Goals

1. Mempercepat proses transaksi kasir.
2. Mengurangi pencatatan manual.
3. Menjaga sinkronisasi stok dengan transaksi.
4. Memisahkan produk fisik dan produk digital secara tepat.
5. Mengetahui omzet dan profit secara real-time.
6. Memantau saldo kas, bank, e-wallet, dan provider.
7. Menyediakan audit trail terhadap perubahan penting.
8. Mempermudah owner memonitor toko dari HP maupun komputer.
9. Mengurangi risiko kesalahan formula spreadsheet.
10. Menjadi single source of truth untuk operasional Raja Aksesoris.

---

# 3. NON-GOALS - VERSION 1

Fitur berikut tidak menjadi prioritas MVP:

- E-commerce customer-facing
- Marketplace integration
- Multi-warehouse kompleks
- Accounting double-entry penuh
- Payroll
- CRM kompleks
- Loyalty points
- API distributor otomatis
- WhatsApp automation
- AI forecasting
- Mobile Android/iOS native

Fitur tersebut dapat dikembangkan setelah sistem inti stabil.

---

# 4. TARGET USERS

## 4.1 Owner

Membutuhkan akses penuh terhadap:

- Dashboard
- Penjualan
- Profit
- Harga modal
- Inventory
- Saldo
- Laporan
- User
- Audit logs

## 4.2 Admin

Bertugas mengelola:

- Produk
- Inventory
- Harga
- Saldo
- Stock adjustment
- Stock opname
- Laporan operasional

Admin tidak harus memiliki akses ke pengaturan sistem tingkat owner.

## 4.3 Kasir

Bertugas melakukan:

- Transaksi POS
- Transaksi produk digital
- Penerimaan pembayaran
- Cetak struk
- Melihat transaksi sendiri

Kasir secara default tidak perlu melihat:

- Harga modal
- Margin global
- Laporan profit
- Pengaturan user

---

# 5. CORE MODULES

## 5.1 Authentication

### Features

- Login
- Logout
- Session management
- Role-based access control

### Roles

- Owner
- Admin
- Cashier

---

# 6. DASHBOARD

Dashboard menjadi halaman utama setelah login.

## KPI Cards

### Owner

- Penjualan hari ini
- Profit hari ini
- Jumlah transaksi
- Average transaction value
- Stok menipis
- Stok habis
- Saldo akun
- Produk terlaris

Workbook saat ini sudah memiliki laporan harian yang mencakup total transaksi, total modal, total penjualan, total margin, serta breakdown pembayaran seperti Cash, Dana, Transfer BCA dan QRIS.

Data tersebut nantinya dihitung langsung dari database.

## Dashboard Chart

### Sales Trend

Filter:

- Hari ini
- 7 hari
- 30 hari
- Bulan ini
- Custom date

### Revenue by Payment Method

Contoh:

- Cash
- QRIS
- BANK BCA
- DANA

### Best Selling Products

Menampilkan:

- Produk
- Quantity sold
- Revenue
- Gross profit

---

# 7. PRODUCT MANAGEMENT

## 7.1 Unified Product Master

Produk fisik dan digital menggunakan satu master produk.

### Product Type

- PHYSICAL
- DIGITAL
- SERVICE

## Product Attributes

- Product code / SKU
- Barcode internal boleh kosong; jika kosong, gunakan product code sebagai barcode internal
- Product name
- Category
- Brand
- Product type
- Product subtype
- Cost price
- Selling price
- Status
- Barcode
- Product image
- Minimum stock
- Description

### Product Status

- Active
- Inactive
- Discontinued

### Price Status

- COMPLETE
- INCOMPLETE

Produk dengan harga modal atau harga jual 0 tetap boleh disimpan, tetapi tidak boleh checkout sampai harga dilengkapi.


## Barcode Strategy

Barcode asli pabrik tidak wajib untuk MVP.

Aturan:

- `products.code` menjadi identitas utama produk.
- `products.barcode` boleh kosong.
- Jika `products.barcode` kosong, sistem memakai `products.code` sebagai barcode internal.
- POS search harus mendukung pencarian berdasarkan code, barcode, dan nama produk.
- Cetak label barcode internal tidak masuk MVP.

## Product Image Strategy

Produk boleh memiliki gambar agar POS catalog lebih mudah discan seperti arah visual blueprint.

Aturan:

- `products.image_path` boleh kosong.
- Produk tanpa gambar tetap aktif dan tetap bisa checkout.
- Jika gambar kosong, UI menampilkan placeholder berdasarkan kategori atau icon produk.
- Gambar tampil sebagai thumbnail kecil di POS catalog dan product admin.
- Upload gambar dilakukan manual dari halaman product form.
- Import Excel tidak wajib membawa gambar produk.
- Storage awal: `storage/app/public/products`.
- Format upload: `jpg`, `jpeg`, `png`, `webp`.
- Maksimal ukuran upload: 2 MB per gambar.
- Gambar tidak menjadi sumber identitas produk; identitas tetap `products.code`.

---

# 8. PHYSICAL INVENTORY

Produk fisik memiliki quantity aktual.

## Inventory Information

- Current stock
- Minimum stock
- Stock status
- Last stock movement
- Last cost price

## Stock Status

Status tidak disimpan manual sebagai business truth.

Status dihitung otomatis:

```text
stock = 0
-> HABIS

stock > 0 AND stock <= minimum_stock
-> MENIPIS

stock > minimum_stock
-> TERSEDIA
```

---

# 9. INVENTORY MOVEMENT

Semua perubahan stok harus memiliki histori.

## Movement Types MVP

- SALE
- TRASH_RESTORE
- ADJUSTMENT_IN
- ADJUSTMENT_OUT
- DAMAGE
- STOCK_OPNAME

`PURCHASE`, `RETURN_IN`, dan `RETURN_OUT` tidak dipakai di MVP karena purchase/supplier management ditunda.

Contoh:

```text
Product: Kabel Type-C

+20 Adjustment In - Stock Awal Import
 -2 Sale INV-1001
 -1 Damage
-----------------
 17 Current Stock
```

Dengan demikian sistem tidak hanya mengetahui stok akhir, tetapi juga dapat menjelaskan penyebab perubahannya.

---

# 10. DIGITAL PRODUCTS

Digital product tidak memiliki stock quantity seperti barang fisik.

Contoh:

- Pulsa
- Paket data
- Token
- Game voucher
- E-wallet
- Digital services

Produk digital Raja Aksesoris saat ini sudah mencakup berbagai paket berdasarkan provider dengan harga modal dan harga jual yang berbeda.

## Digital Product Sales

Untuk MVP, produk digital tidak memakai status provider terpisah. Jika checkout selesai, produk digital dianggap terjual.

Aturan:

- Tidak mengurangi inventory quantity.
- Tetap menyimpan snapshot nama, kode, harga modal, dan harga jual di `sale_items`.
- Produk digital provider memakai akun modal utama `MULTI`.
- Produk service seperti transfer atau tarik tunai memakai `product_type = SERVICE` dan akun modal sesuai jenis layanan.

---

# 11. POINT OF SALE

## POS Main Flow

```text
Login
->
New Transaction
->
Search / Scan Product
->
Add to Cart
->
Adjust Quantity
->
Select Payment Method
->
Receive Payment
->
Complete Transaction
->
Update Stock / Balance
->
Generate Receipt
```

---

# 12. CART

Setiap transaksi dapat memiliki banyak item.

Contoh:

```text
INV-20260903-001

Tempered Glass      x2
Kabel Type-C        x1
TWS ACOME           x1
Pulsa XL            x1
```

Hal ini merupakan perubahan arsitektur penting dibanding model spreadsheet flat transaction.

---

# 13. CHECKOUT

Checkout menampilkan:

- Subtotal
- Grand total
- Payment method
- Amount paid
- Change

## Payment Methods

Payment method aplikasi:

- CASH
- QRIS
- TRANSFER
- E_WALLET

Balance account tujuan dipilih terpisah dari payment method.
Contoh: payment method `TRANSFER` dapat masuk ke `BANK BCA` atau `BANK MAS`; payment method `E_WALLET` dapat masuk ke `DANA`.

---

# 14. RECEIPT

Support MVP:

- Thermal 58 mm
- Thermal 80 mm
- Browser print dialog sebagai fallback

PDF receipt bersifat opsional untuk fase berikutnya.

Aturan thermal print:

- Nama toko pada struk: Raja Aksesoris.
- Layout struk harus hemat lebar dan readable untuk kertas 58 mm.
- Ukuran 80 mm boleh memakai spacing lebih lega.
- Tombol print tersedia setelah transaksi selesai.
- Jika printer thermal belum terdeteksi browser/OS, sistem tetap menampilkan printable receipt.
- Integrasi awal menggunakan browser print agar kompatibel dengan printer thermal yang sudah terpasang di toko.

Receipt berisi:

```text
Raja Aksesoris

INV-20260903-0012
03 September 2026 14:32

Kasir: Nama Kasir

Kabel Type-C
2 x Rp25.000        Rp50.000

Tempered Glass
1 x Rp60.000        Rp60.000

---------------------------
TOTAL               Rp110.000
BAYAR               Rp150.000
KEMBALI              Rp40.000
---------------------------

Payment: Cash

Terima Kasih
```

---

# 15. BALANCE MANAGEMENT

Raja Aksesoris memiliki kebutuhan yang lebih kompleks daripada POS biasa karena operasional juga melibatkan beberapa akun saldo.

Spreadsheet saat ini sudah memonitor akun seperti MULTI, DANA, BANK MAS, WAHANA, QRIS, dan BANK BCA menggunakan saldo masuk, saldo keluar, serta saldo aktual.

## Balance Accounts

Contoh:

- CASH
- MULTI
- DANA
- BANK MAS
- WAHANA
- QRIS
- BANK BCA

## Balance Transaction

Mencatat:

- Date
- Transaction name
- Source account
- Destination account
- Transaction type
- Amount
- Notes

Struktur tersebut juga sesuai dengan form Input Saldo yang saat ini sudah mencatat tanggal, nama transaksi, akun tujuan, akun asal, tipe transaksi, dan nominal saldo.

---

# 16. BALANCE TRANSACTION TYPES

Enum MVP:

```text
DEPOSIT
WITHDRAWAL
TRANSFER
SALE_RECEIPT
DIGITAL_COST
ADJUSTMENT
EXPENSE
TRASH_REVERSAL
RESTORE_REVERSAL
```

Future enum, tidak dipakai di MVP:

```text
REFUND
```

MVP memakai Sampah Transaksi, bukan refund parsial.

Contoh:

```text
Transfer

FROM:
BANK BCA

TO:
MULTI

Rp2.000.000
```

Result:

```text
BANK BCA -2.000.000
MULTI +2.000.000
```

---

# 17. SALES HISTORY

Filter:

- Invoice
- Date
- Cashier
- Product
- Category
- Payment
- Transaction status

Columns:

- Invoice
- Date
- Cashier
- Items
- Cost
- Revenue
- Profit
- Payment
- Status

Harga modal dan profit hanya ditampilkan berdasarkan permission.

---

# 18. TRANSACTION STATUS

Sales transaction:

```text
DRAFT
COMPLETED
TRASHED
DELETED
```

Transaksi COMPLETED tidak boleh diedit bebas.

Kesalahan transaksi MVP ditangani menggunakan Sampah Transaksi.

## Sampah Transaksi

Sampah Transaksi adalah tempat penampungan transaksi yang dibatalkan sebelum disembunyikan dari daftar operasional.

Aturan:

- Hanya Owner/Admin yang boleh memindahkan transaksi ke Sampah Transaksi.
- Wajib isi alasan pembatalan.
- Transaksi yang masuk Sampah Transaksi tidak dihitung sebagai penjualan aktif.
- Stok fisik dari transaksi tersebut otomatis dikembalikan.
- Transaksi dapat di-restore selama belum melewati masa retensi.
- Jika di-restore, stok fisik dikurangi kembali sesuai item transaksi.
- Transaksi di Sampah Transaksi otomatis masuk status `DELETED` setelah 30 hari.
- Data transaksi tidak dihapus fisik dari database; UI biasa menyembunyikan status `DELETED`.
- Perubahan ke status `DELETED` tetap dicatat di audit log.

Status aplikasi:

```text
COMPLETED -> TRASHED -> DELETED
TRASHED -> RESTORED -> COMPLETED
```

Refund parsial dan adjustment detail ditunda untuk fase berikutnya.

Hal ini menjaga operasional tetap sederhana tanpa menghilangkan kontrol audit.

---

# 19. STOCK OPNAME

Admin dapat membuat sesi stock opname.

Workflow MVP sederhana:

```text
Buat Sesi
->
Input Stok Fisik
->
Sistem Hitung Selisih
->
Approve Owner/Admin
->
Buat Movement STOCK_OPNAME
->
Selesai
```

Approval cukup Owner/Admin dan tidak membutuhkan multi-step review.

Example:

```text
System Stock     12
Physical Stock   10
Difference       -2
```

System otomatis membuat:

```text
STOCK_OPNAME_ADJUSTMENT -2
```

---

# 20. REPORTING

Laporan MVP mengikuti kebutuhan Excel saat ini dan dihitung langsung dari database.

## Sales Report

Filter wajib:

- Hari ini
- 7 hari
- 30 hari
- Bulan ini
- Custom date range
- Cashier
- Payment method

Kolom/metric wajib:

- Revenue
- COGS
- Gross Profit
- Transactions
- Average order value
- Breakdown per payment method
- Breakdown per category besar: Aksesoris, Multi, Transfer, Lain-lain

## Product Report

Kolom/metric wajib:

- Top-selling products
- Slow-moving products
- Highest revenue products
- Highest profit products
- Produk dengan harga incomplete
- Produk fisik stok menipis/habis

## Inventory Report

Kolom/metric wajib:

- Current stock
- Low stock
- Out of stock
- Stock valuation berdasarkan cost price
- Stock movement history
- Stock opname result

## Payment Report

Kolom/metric wajib:

- Cash
- QRIS
- Transfer per destination account
- E-wallet per account
- Multi-payment transactions
- Cash change keluar
- Cash minus warning

## Balance Report

Kolom/metric wajib:

- Saldo awal periode
- Total pemasukan
- Total pengeluaran
- Total mutasi
- Saldo akhir periode
- Account: CASH, QRIS, BANK BCA, BANK MAS, DANA, MULTI, WAHANA

## Digital Product Report

Untuk MVP, laporan digital fokus pada penjualan dan margin, bukan status provider.

Kolom/metric wajib:

- Provider/brand
- Transaction amount
- Revenue
- Cost
- Margin

`Failed transaction` dan `Success rate` ditunda sampai fase tracking provider tersedia.

---

# 21. AUDIT LOG

Audit log wajib untuk aktivitas sensitif.

Log:

- Login
- Product price change
- Cost price change
- Stock adjustment
- Transaction cancellation
- Sampah Transaksi
- Balance adjustment
- User permission change

Example:

```text
03 Sep 2026
14:23

Admin Fiya changed
RJA-ACOM-0001

Selling price:
200,000 -> 220,000
```

---

# 22. IMPORT EXISTING DATA

Sistem harus menyediakan migrasi data dari Excel.

## Import

### Products

- Physical products
- Digital products

### Opening Balance

- Inventory quantity
- Balance accounts

### Not Included In MVP

- Historical transactions

Import harus melalui validation preview sebelum commit.
Historical transactions tidak diimport pada MVP agar data awal bersih dan implementasi tidak melebar.

---

# 23. EXCEL SOURCE FINDINGS

Catatan dari workbook `Raja Aksesoris Operasional (4).xlsx` yang wajib diikuti saat implementasi dan import:

- Sheet sumber utama: `Stok Fisik`, `Stok Digital`, `Saldo`, `Transaksi Harian`, `Saldo (Transaksi)`, `Laporan Harian`, dan `Laporan Bulanan`.
- Sheet `Form Responses 1` tersembunyi dan tidak dipakai untuk MVP.
- `Stok Fisik` memiliki 1142 produk dengan kode unik; nama produk boleh duplikat.
- `Stok Digital` memiliki 1026 produk dengan kode unik.
- Banyak baris template kosong di Excel tetap memiliki formula/harga 0; import harus hanya mengambil baris dengan `Kode Produk` terisi.
- Produk dengan `Kode Produk` terisi tetap diimport walaupun harga modal atau harga jual bernilai 0. Produk tersebut diberi tanda `price_status = INCOMPLETE` agar harga dilengkapi sebelum dijual.
- Status stok fisik mengikuti formula Excel: `stock <= 0` = Habis, `stock <= 3` = Menipis, `stock > 3` = Tersedia.
- Validasi transaksi Excel memakai payment method: `CASH`, `QRIS`, `TRANSFER`.
- Validasi modal via Excel memakai: `MULTI`, `DANA`, `BANK MAS`, `WAHANA`, `BANK BCA`, `Tidak pakai Saldo`, `CASH`.
- Validasi saldo transaksi Excel memakai jenis: `Pemasukan`, `Pengeluaran`, `Mutasi`.
- Akun saldo transaksi Excel memakai: `CASH`, `QRIS`, `BANK BCA`, `MULTI`, `DANA`, `BANK MAS`, `WAHANA`, dan `-`.
- Laporan harian menghitung total transaksi, total modal, total penjualan, total margin, serta breakdown Cash, Dana, Transfer, dan QRIS.
- Laporan bulanan membagi penjualan per kategori besar: `Aksesoris`, `Multi`, `Transfer`, `Lain-lain`, dan `Margin Penjualan`.
- Import normalisasi kategori/brand/jenis wajib menampilkan preview jika perubahan tidak sekadar kapitalisasi, spasi, atau typo nyata.

# 24. MVP SCOPE

MVP fokus pada operasional toko yang paling sering dipakai: master produk, stok fisik, transaksi kasir, pembayaran, struk, dan ringkasan penjualan. Fitur yang belum wajib untuk transaksi harian dipindahkan ke fase berikutnya.

## Phase 1 - Core POS & Inventory

### Authentication & Authorization

- Login
- Logout
- Role dasar: Owner, Admin, Cashier
- Login menggunakan username dan password
- Permission minimum untuk membatasi akses harga modal, profit, user, dan laporan

### Product Master

- Product CRUD
- Category CRUD
- Brand CRUD
- Product type: PHYSICAL, DIGITAL, SERVICE
- Harga modal dan harga jual
- Status produk

### Physical Inventory

- Current stock per product dan location
- Stock movement otomatis dari checkout
- Stock adjustment manual dengan alasan
- Stock opname sederhana
- Low-stock dan out-of-stock alert

### POS Transaction

- Product search
- Cart
- Quantity update
- Discount ditunda dari MVP awal
- Checkout atomic
- Multi-payment checkout
- Thermal receipt 58 mm / 80 mm

### Balance Minimal

- Balance account aktif: CASH, QRIS, BANK BCA, BANK MAS, DANA, MULTI, WAHANA
- Balance transaction otomatis dari payment, kembalian cash, service, dan penjualan digital
- CASH boleh minus dengan catatan operasional

### Transactions

- Sales history
- Sales detail
- Sampah Transaksi dengan alasan pembatalan
- Profit hanya terlihat oleh role yang punya permission

### Dashboard Basic

- Sales hari ini
- Profit hari ini
- Jumlah transaksi hari ini
- Stok menipis
- Produk terlaris sederhana

## Explicitly Deferred From Phase 1

- Produk digital dengan lifecycle provider lengkap
- Discount transaksi dan discount item
- Balance transfer dan balance journal penuh
- Stock opname approval flow lanjutan
- Refund parsial
- Audit log lengkap untuk semua entity
- Excel import historis

---

# 25. PHASE 2

- Digital product lifecycle provider lengkap
- Balance transfer manual
- Balance journal lanjutan
- Stock opname approval flow lanjutan
- Refund parsial
- Audit log lengkap untuk semua entity

---

# 26. PHASE 3

- Barcode scanner
- Thermal printing polish lanjutan
- Purchase management
- Supplier management
- Advanced analytics
- Multi-store support
- Backup management

---

# 27. SUCCESS METRICS

Setelah implementation:

### Operational

- >=95% transaksi dicatat melalui POS
- Stock discrepancy turun
- Tidak ada transaksi tanpa payment method
- Laporan harian tidak lagi direkap manual

### Performance

Target:

```text
POS page load     < 2 sec
Product search    < 500 ms
Checkout          < 2 sec
Dashboard         < 3 sec
```

---

# 28. NON-FUNCTIONAL REQUIREMENTS

## Security

- Password hashing
- CSRF protection
- Role permission
- Server-side authorization
- Rate limiting
- Audit trail
- Secure session

## Reliability

Transaksi penting menggunakan database transaction.

Contoh checkout:

```text
BEGIN

Create sale
Create sale items
Reduce inventory
Update balance
Create payment

COMMIT
```

Jika salah satu proses gagal:

```text
ROLLBACK
```

Tidak boleh terjadi transaksi tersimpan tetapi stock gagal berubah.

---

# 29. VISUAL REFERENCE

Dokumen visual `docs/raja-pos-blueprint.png` digunakan sebagai referensi utama untuk arah layout, density, visual feel, sitemap, user flow, dashboard, POS screen, checkout, sales detail, dan gambaran arsitektur.

Posisi gambar:

- Gambar menjadi acuan layout dan rasa visual aplikasi.
- Dashboard, POS, checkout, sales detail, balance accounts, sitemap, dan table-heavy admin mengikuti arah gambar.
- Business rule, database schema, MVP scope, validasi, dan flow transaksi tetap mengikuti dokumen MD ini.
- Jika ada konflik antara gambar dan MD, MD yang menang.
- Label mobile pada gambar diabaikan untuk scope awal; aplikasi tetap web application.

## Blueprint Coverage Decision

Elemen blueprint yang diikuti untuk MVP:

```text
Dashboard compact
POS new transaction
Checkout panel
Sales detail
Balance accounts
Product catalog thumbnail
Stock opname flow sederhana
Sidebar navigation
Thermal receipt action
Security basics
```

Elemen blueprint yang tidak dibuat sebagai modul terpisah pada MVP:

```text
Digital Transactions -> memakai Sales dengan filter product_type DIGITAL/SERVICE
Balance Transfers    -> ditunda sampai balance transfer manual masuk fase lanjutan
Import Data          -> hanya import preview master produk, stok awal, dan saldo awal
System Settings      -> minimal store, receipt, stock default, dan account/payment seeds
Mobile label        -> diabaikan; aplikasi tetap web application
```

---

# 30. DESIGN SYSTEM

# 30.1 Design Principle

UI Raja POS harus:

- cepat
- bersih
- minim distraksi
- nyaman digunakan berjam-jam
- data-dense tetapi tetap mudah dibaca
- mobile responsive
- touch-friendly

Tidak menggunakan gaya landing page marketing untuk halaman operasional.

---

# 31. VISUAL DIRECTION

Style:

**Modern Retail Operations Dashboard**

Karakter:

- Clean
- Professional
- Compact
- Functional
- High information density
- Sidebar gelap seperti blueprint
- Panel putih dengan border tipis
- Action utama berwarna biru
- Aksen brand emas dipakai hemat untuk identitas Raja, bukan dekorasi penuh

---

# 32. COLOR SYSTEM

Final token awal mengikuti arah visual blueprint dengan penyesuaian brand Raja Aksesoris.

## Brand Navy

```text
Navy 50     #EEF4FF
Navy 100    #DCE8F8
Navy 500    #1E3A8A
Navy 700    #172554
Navy 900    #0F172A
```

Brand Navy digunakan untuk:

- Sidebar
- Topbar tertentu
- Heading penting
- Active menu pada navigasi gelap
- Elemen identitas Raja POS

## Primary Blue

```text
Blue 50      #EFF6FF
Blue 100     #DBEAFE
Blue 500     #2563EB
Blue 600     #1D4ED8
Blue 700     #1E40AF
```

Primary Blue digunakan untuk:

- CTA utama
- Links
- Selected states
- Focus ring
- Button `Bayar`, `Simpan`, `Konfirmasi`

## Brand Gold

```text
Gold 50      #FFFBEB
Gold 100     #FEF3C7
Gold 500     #D4A017
Gold 600     #B8860B
Gold 700     #92400E
```

Brand Gold digunakan hemat untuk:

- Logo/accent Raja
- Highlight KPI tertentu
- Icon kecil atau divider aksen
- State penting yang bukan warning/error

Penggunaan warna harus hemat. UI operasional tidak boleh didominasi satu warna; warna dipakai untuk membantu scan, bukan dekorasi.

## Neutral

```text
Neutral 50    #FAFAFA
Neutral 100   #F5F5F5
Neutral 200   #E5E5E5
Neutral 500   #737373
Neutral 700   #404040
Neutral 900   #171717
```

## Semantic

```text
Success
#16A34A

Warning
#D97706

Danger
#DC2626

Info
#0284C7
```

## Status Color Mapping

```text
ACTIVE / TERSEDIA      Success
INACTIVE               Neutral
DISCONTINUED           Neutral 500
LOW_STOCK / MENIPIS    Warning
OUT_OF_STOCK / HABIS   Danger
DRAFT                  Neutral
COMPLETED              Success
TRASHED                Warning
DELETED                Neutral 500
INCOMPLETE PRICE       Danger
CASH MINUS             Warning
```

## Surface Tokens

```text
App background      Neutral 50
Main surface        White
Sidebar background  Navy 900
Sidebar text        Neutral 100
Panel border        Neutral 200
Table header        Neutral 50
Hover row           Blue 50
Focus ring          Blue 500
```

## Filament Theme Rule

Admin panel Filament wajib mengikuti token warna yang sama:

- Sidebar memakai Navy 900.
- Primary action memakai Blue 600.
- Accent brand memakai Gold 500 secara hemat.
- Badge status memakai mapping semantic, bukan warna default acak.
- POS Livewire boleh lebih custom dan lebih padat daripada halaman Filament.
- Halaman Filament tetap compact, table-first, dan tidak memakai tampilan marketing.

---

# 33. TYPOGRAPHY

Recommended font:

**Inter**

Fallback:

```text
Inter,
system-ui,
sans-serif
```

## Type Scale

```text
Display      30px / 36px / 700
H1           24px / 32px / 700
H2           20px / 28px / 600
H3           16px / 24px / 600
Body         14px / 20px / 400
Small        12px / 16px / 400
Label        12px / 16px / 500
```

Untuk POS dan dashboard, ukuran 14px menjadi default agar cukup compact.

Aturan tipografi UI:

- Gunakan satu font family saja.
- Tidak memakai display font untuk label, button, tabel, atau data angka.
- Heading dashboard ringkas dan tidak hero-sized.
- Angka uang memakai tabular number jika tersedia.

---

# 34. SPACING SYSTEM

Gunakan kelipatan 4px.

```text
1  = 4px
2  = 8px
3  = 12px
4  = 16px
5  = 20px
6  = 24px
8  = 32px
10 = 40px
12 = 48px
```

---

# 35. BORDER RADIUS

```text
Small       6px
Default     8px
Card       12px
Modal      16px
Pill       9999px
```

POS tidak perlu terlalu rounded.

Prioritaskan readability. Card operasional maksimal 12px, input/button default 8px, dan hindari nested card.

---

# 36. COMPONENT LIBRARY

Wajib tersedia:

## Navigation

- Sidebar
- Topbar
- Breadcrumb
- Tabs

## Input

- Text input
- Search
- Number input
- Currency
- Select
- Combobox
- Date picker
- Checkbox
- Radio
- Switch

## Actions

- Primary button
- Secondary button
- Ghost button
- Danger button
- Icon button

## Data

- Table
- Pagination
- Badge
- KPI card
- Chart
- Empty state
- Skeleton loader

## Overlay

- Modal
- Confirmation dialog
- Drawer
- Toast

## Component States

Setiap komponen interaktif wajib punya state:

- Default
- Hover
- Focus
- Active
- Disabled
- Loading
- Error jika input/aksi bisa gagal

Untuk loading data, gunakan skeleton pada area konten. Hindari spinner tunggal di tengah halaman kecuali untuk proses singkat.

## POS Interaction Requirements

- Search product harus menjadi fokus utama layar POS.
- Cart selalu terlihat di desktop.
- Total bayar dan kembalian harus visual paling kuat di checkout.
- Payment split harus mudah ditambah/hapus tanpa membuka modal berlapis.
- Tombol `Bayar` hanya aktif jika cart valid, harga lengkap, dan payment cukup.
- Produk `INCOMPLETE` harus terlihat jelas dan tidak bisa masuk checkout.
- Cash minus harus tampil sebagai warning, bukan error blocking.

---

# 37. BUTTON HIERARCHY

### Primary

Untuk tindakan utama:

```text
+ Tambah Produk
Bayar
Simpan
Konfirmasi
```

### Secondary

```text
Edit
Export
Print
```

### Ghost

```text
Detail
Cancel
Back
```

### Danger

```text
Delete
Pindahkan ke Sampah Transaksi
```

---

# 38. STATUS BADGES

## Product

```text
ACTIVE
INACTIVE
DISCONTINUED
```

Label UI dapat ditampilkan sebagai:

```text
AKTIF
INAKTIF
DIHENTIKAN
```

## Inventory

```text
AVAILABLE
LOW_STOCK
OUT_OF_STOCK
```

Label UI dapat ditampilkan sebagai:

```text
TERSEDIA
MENIPIS
HABIS
```

## Sales Transaction

```text
DRAFT
COMPLETED
TRASHED
DELETED
```

`TRASHED` digunakan untuk Sampah Transaksi. `DELETED` digunakan sebagai status arsip audit setelah transaksi disembunyikan dari daftar operasional oleh sistem.


---

# 39. TABLE DESIGN

Tables merupakan komponen utama.

Table harus memiliki:

- Sticky header
- Search
- Filtering
- Sort
- Pagination
- Bulk selection
- Responsive horizontal scroll

Example:

```text
Kode          Produk           Stok    Harga       Status
---------------------------------------------------------
RJA-ACOM-01   ACOME Mic        2       220.000     Menipis
RJA-SPEA-01   Speaker V1       10      100.000     Tersedia
```

---

# 40. POS LAYOUT

Desktop mengikuti arah blueprint: search di atas, product catalog dominan, cart dan checkout selalu terlihat di sisi kanan.

```text
+------------------------------------------------+
| Search Product                                 |
+--------------------------------+---------------+
|                                |               |
| PRODUCT CATALOG                | CART          |
|                                |               |
|                                |               |
|                                |               |
+--------------------------------+               |
| Categories                     | CHECKOUT      |
+--------------------------------+---------------+
```

Ratio:

```text
Product area: 65%
Cart:         35%
```

---

# 41. RESPONSIVE BREAKPOINT

```text
Mobile      < 640px
Tablet      640-1024px
Desktop     > 1024px
```

Untuk mobile POS:

```text
Products
->
Floating Cart Button
->
Cart Drawer
```

Aturan responsive POS:

- Desktop: sidebar + product catalog + cart/checkout panel.
- Tablet: product catalog dan cart boleh stacked, checkout tetap mudah dijangkau.
- Mobile: product list penuh, cart memakai drawer/floating button.
- Table admin memakai horizontal scroll, bukan mengecilkan teks berlebihan.

---

# 42. DATABASE ARCHITECTURE

Database:

**MySQL 8**

Naming convention:

```text
snake_case
plural table names
BIGINT primary keys
```

---

# 43. EXCEL TO DATABASE MAPPING

Mapping struktur Excel ke database:

```text
Stok Fisik.Kode Produk    -> products.code
Stok Fisik.Kategori       -> categories.name
Stok Fisik.Nama Barang    -> products.name
Stok Fisik.Merk           -> brands.name
Stok Fisik.Jenis          -> products.product_subtype
Stok Fisik.Status         -> calculated inventory status
Stok Fisik.Stok           -> inventories.quantity
Stok Fisik.Harga Modal    -> products.cost_price
Stok Fisik.Harga Jual     -> products.selling_price
Stok Fisik.Margin         -> calculated, not stored as source of truth
Stok Fisik.Margin (%)     -> calculated, not stored as source of truth

Stok Digital.Kode Produk  -> products.code
Stok Digital.Kategori     -> categories.name
Stok Digital.Nama Layanan -> products.name
Stok Digital.Merk         -> brands.name
Stok Digital.Jenis        -> products.product_subtype / provider account hint
Stok Digital.Status       -> products.status
Stok Digital.Harga Modal  -> products.cost_price
Stok Digital.Harga Jual   -> products.selling_price
Stok Digital.Margin       -> calculated, not stored as source of truth
Stok Digital.Margin (%)   -> calculated, not stored as source of truth
```

Margin tidak menjadi input manual utama. Sistem menghitung margin dari snapshot `cost_price` dan `selling_price` saat transaksi.

## Import Preview Decision

Import Excel wajib memakai preview sebelum commit.

Aturan preview:

- Baris tanpa `Kode Produk` tidak diimport.
- Produk dengan harga modal atau harga jual 0 tetap masuk preview dan akan diimport sebagai `price_status = INCOMPLETE`.
- Produk `INCOMPLETE` tidak bisa checkout sampai harga dilengkapi.
- Duplicate `products.code` menjadi error dan wajib diselesaikan sebelum import commit.
- Nama produk duplicate boleh, selama code berbeda.
- Normalisasi kategori, brand, jenis, dan provider hanya boleh otomatis untuk perbedaan kapitalisasi, spasi berlebih, dan typo yang jelas.
- Normalisasi yang meragukan wajib ditampilkan sebagai pilihan di preview dan menunggu ACC user.
- Margin dari Excel tidak diimport sebagai nilai utama; sistem menghitung ulang dari harga modal dan harga jual.
- Import saldo awal memakai sheet saldo yang paling akhir/valid sebagai opening balance.
- Historical transaction import ditunda; MVP hanya import master produk, stok awal, dan saldo awal.


---

# 44. CORE ENTITY RELATIONSHIP

```text
users
  |
  +---- sales
  |
  +---- inventory_movements
  |
  +---- audit_logs

categories
  |
  +---- products
          |
          +---- inventory
          |
          +---- sale_items
          |
          +---- inventory_movements

sales
  |
  +---- sale_items
  |
  +---- payments

balance_accounts
  |
  +---- payments
  |
  +---- balance_transactions
```

---

# 45. TABLE: users

```text
id
name
username
password
role_id
status
last_login_at
created_at
updated_at
```

---

# 46. TABLE: roles

```text
id
name
created_at
updated_at
```

Examples:

```text
OWNER
ADMIN
CASHIER
```

---

# 47. TABLE: permissions

```text
id
name
description
created_at
updated_at
```

Example:

```text
product.view
product.create
product.update

sales.create
sales.view

cost_price.view

report.profit.view
```

---

# 48. TABLE: permission_role

Pivot many-to-many roles dan permissions.

```text
id
role_id
permission_id
created_at
updated_at
```

Unique constraint:

```text
role_id + permission_id
```

---

# 49. MINIMUM PERMISSION MATRIX

Permission MVP dikunci granular agar implementasi role tidak melebar.

```text
Permission                  Owner  Admin  Cashier
----------------------------------------------------
dashboard.view              yes    yes    yes
product.view                yes    yes    yes
product.create              yes    yes    no
product.update              yes    yes    no
product.delete              yes    no     no
cost_price.view             yes    yes    no
inventory.view              yes    yes    yes
inventory.adjust            yes    yes    no
stock_opname.view           yes    yes    no
stock_opname.create         yes    yes    no
stock_opname.approve        yes    yes    no
sales.create                yes    yes    yes
sales.view_all              yes    yes    no
sales.view_own              yes    yes    yes
sales.trash                 yes    yes    no
sales.restore               yes    yes    no
payment.view                yes    yes    yes
balance.view                yes    yes    no
balance.adjust              yes    yes    no
report.sales.view           yes    yes    no
report.profit.view          yes    no     no
report.inventory.view       yes    yes    no
report.payment.view         yes    yes    no
report.balance.view         yes    yes    no
user.manage                 yes    no     no
role_permission.manage      yes    no     no
audit_log.view              yes    no     no
settings.manage             yes    no     no
excel_import.preview        yes    yes    no
excel_import.commit         yes    no     no
```

Aturan akses:

- Kasir hanya melihat transaksi sendiri dan tidak melihat harga modal, saldo global, atau profit.
- Admin boleh melihat harga modal untuk kebutuhan produk, stok, dan laporan operasional.
- Admin boleh memindahkan transaksi ke Sampah Transaksi dan restore dengan alasan wajib.
- Hanya Owner yang boleh commit import Excel setelah preview disetujui.
- Hanya Owner yang boleh mengubah role, permission, dan pengaturan sistem.

---

# 50. TABLE: categories

```text
id
name
slug
status
created_at
updated_at
```

---

# 51. TABLE: brands

```text
id
name
slug
status
created_at
updated_at
```

---

# 52. TABLE: products

```text
id

code
barcode
image_path nullable

name

category_id
brand_id

product_type
product_subtype

default_balance_account_id nullable

cost_price
selling_price

minimum_stock

status

description
price_status

created_at
updated_at
deleted_at
```

Enum:

```text
product_type

PHYSICAL
DIGITAL
SERVICE
```

---

# 53. TABLE: inventories

Satu produk fisik memiliki satu inventory record per location.

```text
id
product_id
location_id

quantity
reserved_quantity

last_stock_at

created_at
updated_at
```

---

# 54. TABLE: inventory_movements

```text
id

product_id
location_id

movement_type

quantity_before
quantity_change
quantity_after

reference_type
reference_id

notes

created_by

created_at
```

Enum MVP:

```text
SALE
TRASH_RESTORE
ADJUSTMENT_IN
ADJUSTMENT_OUT
DAMAGE
STOCK_OPNAME
```

---

# 55. TABLE: sales

```text
id

invoice_number

customer_id nullable
# no foreign key in MVP; customer master is not created yet
cashier_id

transaction_date

subtotal
discount_amount default 0
# fixed 0 in MVP; discount feature disabled
total_amount
amount_paid
change_amount

trash_reason nullable
trashed_by nullable
trashed_at nullable
deleted_at nullable

restored_by nullable
restored_at nullable

total_cost
gross_profit

status

notes

created_at
updated_at
```

Invoice:

```text
INV-20260903-000001
```

---

# 56. TABLE: sale_items

```text
id
sale_id
product_id

product_name_snapshot
product_code_snapshot
product_type_snapshot
product_subtype_snapshot
modal_account_snapshot nullable

quantity

cost_price
selling_price

discount_amount default 0
# fixed 0 in MVP; discount feature disabled
subtotal

created_at
```

Snapshot nama produk penting karena master product dapat berubah setelah transaksi.

Historical invoice tidak boleh berubah.

---

# 57. TABLE: payments

```text
id

sale_id

payment_method_id
balance_account_id nullable

amount
change_amount default 0
# informational only; sales.change_amount is the source of truth for total change

reference_number nullable

status

paid_at

created_at
```

Satu sale dapat memiliki lebih dari satu payment.

Contoh:

```text
Total Rp500.000

Cash   Rp300.000
QRIS   Rp200.000
```

---

# 58. TABLE: payment_methods

```text
id
name
code
type
status
```

Examples:

```text
CASH
QRIS
TRANSFER
E_WALLET
```

---

# 59. FUTURE TABLE: digital_transaction_details - NOT MVP

Tabel ini tidak dibuat pada migration MVP karena produk digital dianggap terjual saat checkout selesai tanpa status provider terpisah.

Jika fase lanjutan membutuhkan tracking provider, tabel ini dapat ditambahkan kembali dengan relasi ke `sale_items`.

---

# 60. TABLE: balance_accounts

```text
id

name
code

account_type

current_balance

status

created_at
updated_at
```

Examples:

```text
CASH
QRIS
BANK BCA
BANK MAS
DANA
MULTI
WAHANA
```

---

# 61. TABLE: balance_transactions

```text
id

transaction_number

transaction_type

source_account_id nullable
destination_account_id nullable

amount
balance_before nullable
balance_after nullable

reference_type
reference_id

description

created_by

transaction_date
created_at
```

---

# 62. BALANCE ACCOUNT RULE

Jangan hanya update:

```text
current_balance
```

tanpa history.

Setiap saldo berubah wajib memiliki:

```text
balance_transactions
```

`current_balance` dapat dipertahankan sebagai cached balance untuk performance.

---

# 63. TABLE: stock_opnames

```text
id

opname_number
location_id

status

started_at
completed_at

created_by
approved_by nullable

created_at
updated_at
```

---

# 64. TABLE: stock_opname_items

```text
id

stock_opname_id
product_id

system_quantity
physical_quantity
difference

notes

created_at
```

---

# 65. TABLE: customers - NOT MVP

Customer master tidak dibuat pada MVP.

Alasan:

- Operasional toko tidak membutuhkan customer tracking untuk transaksi harian.
- Checkout harus tetap cepat untuk kasir.
- `sales.customer_id` tetap nullable sebagai kolom future-proof, tetapi tidak ditampilkan di UI MVP dan tidak wajib memiliki foreign key aktif sampai tabel customers dibuat pada fase lanjutan.

```text
id
name
phone
created_at
updated_at
```

---

# 66. TABLE: locations

Walaupun awalnya hanya satu toko, tabel location tetap dibuat.

```text
id
name
code
address
status
```

Initial:

```text
RAJA-BANGO
```

Dengan ini sistem lebih mudah dikembangkan menjadi multi-store.

---

# 67. TABLE: audit_logs

```text
id

user_id

action
entity_type
entity_id

old_values JSON
new_values JSON

ip_address
user_agent

created_at
```

---

# 68. RELATIONAL ARCHITECTURE

```text
roles
  1
  |
  many
users
  |
  +--------------------+
  |                    |
  many                 many
sales            audit_logs
  |
  +---------------+
  |               |
  many            many
sale_items      payments
  |
  many
products
  |
  +---------------+
  |               |
  1               many
inventory    inventory_movements
```

---

# 69. EXTENDED ERD

```text
CATEGORY
   |
   +----< PRODUCT >---- BRAND
              |
              +---- INVENTORY
              |
              +---- INVENTORY_MOVEMENT
              |
              +---- SALE_ITEM
                        |
                       SALE
                        |
                        +---- PAYMENT
                        |
                        +---- USER / CASHIER

BALANCE_ACCOUNT
       |
       +---- PAYMENT
       |
       +---- BALANCE_TRANSACTION
```

---

# 70. RELATIONAL INTEGRITY RULES

Aturan relasional yang wajib diikuti saat migration:

- `users.role_id` wajib reference ke `roles.id`.
- `permission_role.role_id` reference ke `roles.id` dan `permission_role.permission_id` reference ke `permissions.id`.
- `products.category_id`, `products.brand_id`, dan `products.default_balance_account_id` memakai foreign key nullable sesuai kebutuhan import.
- `inventories.product_id + inventories.location_id` harus unique agar satu produk fisik hanya punya satu stok per lokasi.
- `inventory_movements.reference_type + reference_id` menyimpan asal perubahan stok seperti sale, stock opname, atau adjustment.
- `sales.cashier_id`, `sales.trashed_by`, dan `sales.restored_by` reference ke `users.id`.
- `sale_items.sale_id` reference ke `sales.id`; `sale_items.product_id` nullable-safe untuk histori jika master produk berubah/terhapus soft delete.
- `payments.sale_id` reference ke `sales.id`, `payments.payment_method_id` reference ke `payment_methods.id`, dan `payments.balance_account_id` reference ke `balance_accounts.id` jika payment masuk akun saldo.
- `balance_transactions.source_account_id` dan `destination_account_id` reference ke `balance_accounts.id` dan boleh nullable sesuai jenis transaksi.
- `stock_opname_items.stock_opname_id + product_id` harus unique dalam satu sesi.
- Semua uang disimpan sebagai integer rupiah atau DECIMAL tanpa floating point.
- Semua operasi checkout, Sampah Transaksi, restore, stock adjustment, stock opname approval, dan balance movement wajib memakai database transaction.

---

# 71. OPERATIONAL DECISIONS

Keputusan operasional yang mengikuti praktik toko dan workbook Excel:

- Default minimum stock fisik adalah 3, mengikuti formula Excel: `stock <= 3` berarti Menipis.
- Produk dengan stok 0 tetap diimport dan status stok dihitung sebagai Habis.
- Produk digital dapat dijual dari awal; jika transaksi selesai, item dianggap terjual tanpa status provider terpisah.
- Produk digital provider menggunakan akun modal utama MULTI.
- Produk service seperti transfer atau tarik tunai dapat memakai akun modal sesuai jenis layanan.
- DANA diklasifikasikan sebagai e-wallet, bukan transfer bank.
- WAHANA dipakai sebagai akun saldo terpisah untuk memisahkan pemasukan tertentu agar tidak tercampur dengan uang toko lain.
- Payment method TRANSFER tetap metode pembayaran; rekening tujuan dicatat melalui balance account seperti BANK BCA atau BANK MAS.
- Kode produk dan nama produk dari Excel dipertahankan. Kategori/brand/jenis boleh dinormalisasi jika hanya beda kapitalisasi, spasi, atau typo nyata. Perubahan normalisasi yang tidak jelas wajib masuk preview import dan menunggu ACC sebelum commit.
- Akun Owner awal menggunakan username dan password. Email tidak digunakan.
- Seed akun Owner awal:

```text
username: superadmin
password: password
role: OWNER
```

- Password default wajib diganti setelah setup pertama.

# 72. CHECKOUT RULES

Aturan checkout yang wajib dikunci sebelum implementasi:

- Nomor invoice dibuat server-side dan harus unique.
- Produk fisik tidak boleh checkout jika stok tersedia kurang dari quantity cart.
- Produk dengan `price_status = INCOMPLETE` tidak boleh checkout sampai harga modal dan harga jual dilengkapi.
- Produk digital dan service boleh checkout tanpa inventory quantity, lalu dianggap terjual saat transaksi selesai.
- Harga modal dan harga jual wajib disimpan sebagai snapshot di `sale_items`.
- Diskon ditunda dari MVP awal agar checkout lebih sederhana.
- Checkout MVP mendukung multi-payment.
- Total seluruh payment boleh sama atau lebih besar dari grand total.
- Selisih lebih bayar dicatat sebagai cash change.
- Overpayment dari QRIS, TRANSFER, atau payment non-cash lain tetap valid jika customer meminta kembalian tunai.
- Kembalian selalu keluar dari akun CASH.
- Saldo CASH boleh minus jika kembalian sementara memakai uang Owner/Admin. Sistem wajib menampilkan catatan saldo cash minus agar dapat diganti dari bank atau e-wallet toko.
- Setiap payment wajib memiliki payment method.
- Payment method TRANSFER wajib memilih balance account tujuan, misalnya BANK BCA atau BANK MAS.
- Transaksi `COMPLETED` tidak diedit langsung; pembatalan dilakukan dengan memindahkan transaksi ke Sampah Transaksi.
- Transaksi di Sampah Transaksi dapat di-restore sebelum 30 hari.
- Transaksi di Sampah Transaksi otomatis masuk status `DELETED` setelah 30 hari.
- Semua perubahan stok dan saldo dari checkout, Sampah Transaksi, restore, dan auto-delete status harus berada dalam database transaction.

Multi-payment dan overpayment dengan kembalian cash masuk MVP karena kasus pembayaran gabungan dan customer meminta kembalian tunai sering terjadi di operasional UMKM.

# 73. CHECKOUT TRANSACTION ARCHITECTURE

Checkout harus atomic.

Pseudo-flow:

```text
BEGIN DATABASE TRANSACTION

1. Create sale

2. Create sale_items

3. FOR EACH physical item:
      lock inventory row
      validate stock
      decrease stock
      create inventory movement

4. FOR EACH digital item:
      mark item as sold without stock deduction

5. Create payments

6. Update balances per payment account

7. Create balance transactions per payment

8. Calculate COGS

9. Calculate profit

10. Mark sale COMPLETED

COMMIT
```

Jika terjadi error:

```text
ROLLBACK
```

---

# 74. STOCK CONCURRENCY

Saat checkout:

```text
SELECT inventory
FOR UPDATE
```

Tujuannya mencegah dua kasir menjual unit terakhir secara bersamaan.

Contoh:

```text
Stock = 1

Cashier A
Cashier B
```

Tanpa locking keduanya dapat menganggap stock tersedia.

Dengan row lock hanya satu transaksi berhasil.

---

# 75. FINANCIAL DATA STRATEGY

Data transaksi harus menggunakan nilai snapshot.

Contoh product saat ini:

```text
Cost Price    50,000
Selling Price 75,000
```

Jika besok harga berubah:

```text
Cost Price    55,000
Selling Price 80,000
```

Transaksi kemarin tetap:

```text
50,000
75,000
```

Karena `sale_items` menyimpan:

```text
cost_price
selling_price
```

pada saat transaksi.

---

# 76. SOFT DELETE

Master data:

- products
- categories
- brands

menggunakan soft delete.

Historical transactions:

**tidak dihapus fisik dari database.**

Gunakan:

```text
TRASHED
DELETED
```

`TRASHED` berarti masuk Sampah Transaksi. `DELETED` berarti disembunyikan dari daftar operasional setelah retensi 30 hari.

---

# 77. INDEXING

Indexes wajib:

```text
products.code unique
products.barcode
products.name
products.category_id
products.brand_id
products.default_balance_account_id

sales.invoice_number unique
sales.transaction_date
sales.cashier_id
sales.status
sales.trashed_at

sale_items.product_id
sale_items.sale_id

inventory_movements.product_id

balance_transactions.transaction_date
balance_transactions.source_account_id
balance_transactions.destination_account_id
permission_role.role_id + permission_role.permission_id unique
inventories.product_id + inventories.location_id unique
```

---

# 78. ARCHITECTURE LAYER

Recommended Laravel architecture:

```text
HTTP
|
+-- Controllers / Livewire Components
|
v
Application Services
|
+-- SaleService
+-- InventoryService
+-- PaymentService
+-- BalanceService
+-- ReportingService
|
v
Domain Models
|
v
Database
```

Jangan menaruh seluruh business logic di Controller.

---

# 79. APPLICATION STRUCTURE

```text
app/

Models/
  Product.php
  Sale.php
  SaleItem.php
  Inventory.php
  Payment.php
  BalanceAccount.php

Services/
  PosService.php
  InventoryService.php
  BalanceService.php
  ReportingService.php

Enums/
  ProductType.php
  SaleStatus.php
  StockMovementType.php

Policies/
  ProductPolicy.php
  SalePolicy.php
  ReportPolicy.php

Livewire/
  Pos/
  Products/
  Inventory/
  Reports/
```

---

# 80. RECOMMENDED SIDEBAR

```text
RAJA POS

Dashboard

POS

Transactions
- Sales

Products
- All Products
- Physical
- Digital
- Categories
- Brands

Inventory
- Current Stock
- Stock Movements
- Stock Opname

Balance
- Accounts
- Transactions

Reports
- Sales
- Profit
- Product
- Inventory
- Payment

Users

Sampah Transaksi

Audit Logs

Settings
- Store Settings
- Receipt Settings
- Payment Methods
- Locations
- Roles & Permissions
```


## Page Layout Decision

Layout halaman MVP dikunci sebagai berikut:

```text
Dashboard       -> KPI compact + chart + table ringkas
POS             -> Custom Livewire full-screen operational layout
Transactions    -> Filament table + detail drawer/page
Products        -> Filament CRUD table-first
Inventory       -> Filament table + adjustment action
Stock Opname    -> Filament wizard/action flow sederhana
Balance         -> Filament account summary + transaction table
Reports         -> Filament report pages dengan filter date range
Users/Settings  -> Filament resource terbatas untuk Owner
Digital Tx      -> bukan menu terpisah; gunakan filter pada Sales
```

Aturan layout:

- POS tidak memakai layout CRUD Filament standar; POS dibuat custom supaya cepat untuk kasir.
- Halaman admin memakai tabel sebagai pusat kerja, bukan card-grid besar.
- Detail transaksi harus printable dan readable untuk audit.
- Filter tanggal dan search selalu dekat dengan tabel utama.
- Action destruktif memakai confirmation dialog.

---

# 81. DEVELOPMENT PRIORITY

Urutan development terbaik:

```text
1. Authentication

2. Roles & permissions

3. Product master

4. Physical inventory

5. Inventory movement

6. POS cart

7. Checkout

8. Payment

9. Receipt

10. Transaction history

11. Dashboard

12. Reports

13. Digital product

14. Balance management

15. Stock opname

16. Audit log

17. Excel import

```

---

# 82. BUG-PRONE FLOW NOTES

Catatan ini wajib dipakai sebagai checklist saat implementasi dan testing agar flow tidak bocor.

## Checkout Atomicity

Celah bug:

- Sale tersimpan tetapi stok gagal berkurang.
- Payment tersimpan tetapi saldo gagal berubah.
- Invoice duplicate saat dua kasir checkout bersamaan.
- Item physical dan digital tercampur lalu semua dianggap punya stok.

Pencegahan:

- Checkout wajib memakai satu database transaction.
- Invoice dibuat server-side di dalam transaction dan memiliki unique index.
- Inventory row physical wajib di-lock sebelum validasi stok.
- Produk digital/service dilewati dari proses pengurangan inventory.
- Jika satu langkah gagal, seluruh checkout rollback.

## Multi-Payment & Change

Celah bug:

- Total payment kurang tetapi transaksi tetap completed.
- Overpayment non-cash ditolak padahal customer minta kembalian cash.
- Kembalian tercatat pada payment method yang salah.
- CASH balance tidak berkurang saat kembalian keluar.
- Cash minus dianggap error blocking.

Pencegahan:

- `sum(payments.amount) >= sales.total_amount` wajib sebelum completed.
- `sales.change_amount = sum(payments.amount) - sales.total_amount`.
- `sales.change_amount` adalah source of truth total kembalian.
- `payments.change_amount` hanya informasi UI/detail dan tidak boleh dijumlahkan lagi ke laporan.
- Kembalian selalu membuat balance transaction keluar dari CASH.
- CASH minus boleh, tetapi wajib tampil sebagai warning operasional.

## Balance Consistency

Celah bug:

- `balance_accounts.current_balance` berubah tanpa history.
- Payment TRANSFER masuk tanpa destination account.
- Payment E_WALLET masuk ke rekening bank.
- Reversal Sampah Transaksi hanya mengubah sales status tanpa membalik saldo.

Pencegahan:

- Semua perubahan saldo wajib membuat `balance_transactions`.
- `current_balance` dihitung/diupdate dari service yang sama dengan history.
- Payment method menentukan valid account type.
- Sampah Transaksi dan restore wajib membuat reversing balance transaction per payment dan per cash change.
- Auto-delete 30 hari tidak boleh membalik stok/saldo lagi karena reversal sudah terjadi saat masuk Sampah Transaksi.

## Stock Consistency

Celah bug:

- Stok minus karena checkout paralel.
- Restore transaksi mengurangi stok tanpa validasi.
- Sampah Transaksi mengembalikan stok digital/service.
- Stock opname mengganti stok tanpa movement.

Pencegahan:

- Physical inventory lock memakai `SELECT ... FOR UPDATE` saat checkout dan restore.
- Restore wajib validasi stok cukup sebelum mengembalikan transaksi ke COMPLETED.
- Digital/service tidak membuat inventory movement.
- Semua adjustment dan stock opname wajib membuat inventory movement.

## Sampah Transaksi

Celah bug:

- Kasir bisa membatalkan transaksi sendiri tanpa izin.
- Transaksi TRASHED masih masuk laporan sales/profit.
- Transaksi DELETED hilang dari database.
- Restore setelah 30 hari tetap diizinkan.
- Reason kosong sehingga audit tidak berguna.

Pencegahan:

- Hanya Owner/Admin bisa trash dan restore.
- `trash_reason` wajib diisi.
- Query laporan default hanya menghitung `COMPLETED`.
- `DELETED` hanya status arsip/hidden, bukan hard delete.
- Restore hanya boleh sebelum retensi 30 hari dan sebelum status DELETED.

## Import Excel

Celah bug:

- Row template kosong ikut terimport sebagai produk.
- Harga 0 dianggap produk valid untuk checkout.
- Duplicate code menimpa produk lama diam-diam.
- Normalisasi typo terlalu agresif dan mengubah data asli tanpa ACC.
- Saldo awal double dihitung setelah import ulang.

Pencegahan:

- Import hanya row dengan `Kode Produk` terisi.
- Harga 0 masuk sebagai `price_status = INCOMPLETE` dan blocked dari checkout.
- Duplicate code menjadi blocking error di preview.
- Normalisasi meragukan wajib menunggu ACC user.
- Import harus idempotent atau punya mode clear/re-import yang eksplisit.

## Receipt & Print

Celah bug:

- Struk bisa dicetak sebelum transaksi committed.
- Receipt menampilkan total berbeda dari sales snapshot.
- Layout 58 mm kepotong karena teks terlalu panjang.
- Payment split tidak muncul di struk.

Pencegahan:

- Tombol print muncul hanya setelah checkout committed.
- Receipt membaca dari sales, sale_items, dan payments snapshot.
- Nama item panjang wajib wrap atau truncate rapi untuk 58 mm.
- Struk wajib menampilkan payment split dan change amount.

## Permission Leak

Celah bug:

- Kasir melihat harga modal dari response Livewire/API walaupun UI menyembunyikan kolom.
- Admin bisa mengubah role/permission lewat route tersembunyi.
- Report profit bisa diakses langsung via URL.

Pencegahan:

- Authorization dicek di backend policy/service, bukan hanya tampilan UI.
- Field harga modal/profit tidak dikirim ke client jika user tidak punya permission.
- Semua route/resource sensitif wajib memakai policy atau middleware permission.

---

# 83. DEVELOPMENT ACCEPTANCE CRITERIA

Development dianggap sesuai MD jika skenario berikut lulus.

## Authentication & Role

- User login memakai username dan password, tanpa email.
- Seed `superadmin / password` dibuat sebagai Owner awal.
- Kasir tidak bisa melihat harga modal, profit, saldo global, user management, dan audit log.
- Admin bisa mengelola produk, stok, stock opname, saldo operasional, dan Sampah Transaksi.
- Owner bisa mengakses semua fitur dan commit import Excel.

## Product & Import

- Produk fisik, digital, dan service berada di satu master `products`.
- Produk tanpa barcode tetap bisa dicari dengan `products.code`.
- Produk tanpa gambar tetap tampil dengan placeholder dan tetap bisa checkout.
- Produk dengan gambar tampil sebagai thumbnail di POS dan product admin.
- Produk harga 0 tetap terimport sebagai `price_status = INCOMPLETE`.
- Produk `INCOMPLETE` tampil di master produk tetapi tidak bisa checkout.
- Import Excel menampilkan preview sebelum commit.
- Duplicate code menghentikan commit import.
- Duplicate name boleh selama code berbeda.

## Inventory

- Produk fisik memiliki stok per location.
- Produk digital dan service tidak membuat inventory quantity.
- Checkout produk fisik mengurangi stok dan membuat inventory movement.
- Stok tidak boleh minus karena penjualan produk fisik.
- Stock status mengikuti rule: `stock <= 0` Habis, `stock <= minimum_stock` Menipis, selain itu Tersedia.
- Stock opname approved membuat movement `STOCK_OPNAME`.

## Checkout & Payment

- Checkout berjalan atomic dalam database transaction.
- Invoice dibuat server-side dan unique.
- Satu sale bisa memiliki lebih dari satu payment.
- Total payment boleh lebih besar dari total transaksi.
- Selisih lebih bayar dicatat sebagai `change_amount`.
- Kembalian selalu mengurangi akun CASH.
- Overpayment non-cash tetap valid jika customer meminta kembalian cash.
- CASH boleh minus dan wajib menampilkan warning.
- Harga modal, harga jual, nama produk, code, type, dan subtype tersimpan sebagai snapshot di `sale_items`.

## Balance

- Setiap saldo berubah wajib membuat `balance_transactions`.
- `current_balance` hanya cached balance, bukan satu-satunya sumber histori.
- Payment CASH masuk akun CASH.
- Payment QRIS masuk akun QRIS.
- Payment TRANSFER wajib memilih BANK BCA atau BANK MAS.
- Payment E_WALLET wajib memilih akun e-wallet seperti DANA.
- Akun WAHANA tetap terpisah dari saldo toko lain.

## Sampah Transaksi

- Transaksi COMPLETED tidak diedit langsung.
- Owner/Admin bisa memindahkan transaksi ke Sampah Transaksi dengan alasan wajib.
- Transaksi TRASHED tidak dihitung sebagai sales aktif.
- Saat masuk Sampah Transaksi, stok fisik dikembalikan dan saldo dibalik sesuai transaksi.
- Restore sebelum 30 hari mengurangi stok lagi dan mengembalikan saldo sesuai transaksi. Restore wajib ditolak jika stok fisik saat ini tidak cukup.
- Setelah 30 hari, transaksi masuk status DELETED dan tersembunyi dari UI operasional.
- Data transaksi tidak hard delete dari database.

## Receipt & Thermal Print

- Struk memakai nama toko `Raja Aksesoris`.
- Format struk readable di thermal 58 mm dan 80 mm.
- Tombol print tersedia setelah transaksi selesai.
- Browser printable receipt tetap tersedia sebagai fallback.
- Struk menampilkan item, qty, harga, total, payment split, dan kembalian.

## Reporting

- Sales report dapat difilter per tanggal, cashier, dan payment method.
- Profit hanya tampil untuk role yang punya permission.
- Product report menampilkan top-selling, slow-moving, highest revenue, highest profit, harga incomplete, dan stok menipis/habis.
- Payment report menampilkan multi-payment dan cash change keluar.
- Balance report menampilkan saldo awal, pemasukan, pengeluaran, mutasi, dan saldo akhir per account.

## UI & Design System

- Visual direction mengikuti `docs/raja-pos-blueprint.png`.
- POS dibuat custom Livewire, bukan CRUD Filament standar.
- Admin panel Filament mengikuti token Navy, Blue, Gold, Neutral, dan Semantic.
- Layout admin table-first, compact, dan tidak bergaya landing page.
- Action destruktif memakai confirmation dialog.
- Loading data memakai skeleton pada area konten.
- Status tidak hanya warna; harus ada label teks.

---

# 84. SEED DATA FINAL

Seed data berikut wajib tersedia setelah fresh install.

## Roles

```text
OWNER
ADMIN
CASHIER
```

## Initial User

```text
name: Superadmin
username: superadmin
password: password
role: OWNER
```

Password default wajib diganti setelah setup pertama.

## Payment Methods

```text
CASH       type: CASH
QRIS       type: QRIS
TRANSFER   type: TRANSFER
E_WALLET   type: E_WALLET
```

## Balance Accounts

```text
CASH       account_type: CASH
QRIS       account_type: QRIS
BANK BCA   account_type: BANK
BANK MAS   account_type: BANK
DANA       account_type: E_WALLET
MULTI      account_type: PROVIDER
WAHANA     account_type: PROVIDER
```

## Location

```text
name: Raja Aksesoris Bango
code: RAJA-BANGO
status: ACTIVE
```

## Default Settings

```text
store_name: Raja Aksesoris
receipt_paper_width: 58mm
minimum_stock_default: 3
currency: IDR
timezone: Asia/Jakarta
```

---

# 85. MVP TEST MATRIX

Test matrix ini wajib dipakai saat development dan sebelum dianggap selesai.

## Auth & Permission

```text
AUTH-01  Login superadmin berhasil dengan username/password.
AUTH-02  Login tanpa email berjalan.
AUTH-03  Cashier tidak melihat harga modal dan profit.
AUTH-04  Cashier tidak bisa akses URL user/settings/audit langsung.
AUTH-05  Owner bisa akses semua menu MVP.
```

## Product & Import

```text
IMP-01   Import preview hanya membaca row dengan Kode Produk terisi.
IMP-02   Produk harga 0 masuk sebagai INCOMPLETE.
IMP-03   Produk INCOMPLETE tidak bisa checkout.
IMP-04   Duplicate code menjadi blocking error.
IMP-05   Duplicate name tetap boleh jika code berbeda.
IMP-06   Normalisasi meragukan menunggu ACC sebelum commit.
IMP-07   Produk tanpa gambar tetap tampil dengan placeholder.
IMP-08   Upload gambar produk jpg/png/webp maksimal 2 MB berhasil.
```

## Inventory

```text
INV-01   Produk fisik membuat inventory per location.
INV-02   Produk digital/service tidak membuat inventory quantity.
INV-03   Checkout fisik mengurangi stok dan membuat movement SALE.
INV-04   Checkout stok kurang ditolak dan tidak membuat sale/payment.
INV-05   Stock opname approved membuat movement STOCK_OPNAME.
INV-06   Dua checkout paralel pada stok terakhir hanya satu yang berhasil.
```

## Checkout & Payment

```text
POS-01   Checkout cash exact amount berhasil.
POS-02   Checkout cash lebih bayar mencatat change_amount.
POS-03   Checkout multi-payment cash + QRIS berhasil.
POS-04   Checkout QRIS/transfer lebih bayar dengan kembalian cash berhasil.
POS-05   Payment kurang ditolak.
POS-06   TRANSFER wajib memilih BANK BCA/BANK MAS.
POS-07   E_WALLET wajib memilih DANA atau akun e-wallet valid.
POS-08   Sale item menyimpan snapshot harga dan nama produk.
```

## Balance

```text
BAL-01   Payment cash menambah CASH.
BAL-02   Payment QRIS menambah QRIS.
BAL-03   Payment transfer menambah akun bank tujuan.
BAL-04   Kembalian mengurangi CASH.
BAL-05   CASH minus tidak blocking dan menampilkan warning.
BAL-06   Setiap perubahan saldo memiliki balance_transactions.
```

## Sampah Transaksi

```text
TRX-01   Cashier tidak bisa memindahkan transaksi ke Sampah Transaksi.
TRX-02   Owner/Admin wajib isi alasan trash.
TRX-03   Trash transaksi membalik stok fisik dan saldo.
TRX-04   Transaksi TRASHED tidak masuk sales report aktif.
TRX-05   Restore transaksi mengurangi stok dan mengembalikan saldo.
TRX-06   Restore setelah DELETED ditolak.
TRX-07   Auto-delete 30 hari hanya mengubah status, bukan hard delete.
```

## Receipt & UI

```text
RCT-01   Receipt hanya bisa dicetak setelah checkout committed.
RCT-02   Receipt 58mm menampilkan item, qty, total, payment split, dan kembalian.
RCT-03   POS desktop menampilkan product catalog, cart, dan checkout sekaligus.
RCT-04   POS catalog menampilkan thumbnail produk atau placeholder.
RCT-05   Mobile POS memakai cart drawer/floating cart.
RCT-06   Loading list/table memakai skeleton.
RCT-07   Status badge selalu punya teks, bukan warna saja.
```

---

# 86. MILESTONE DEFINITION OF DONE

Setiap milestone dianggap selesai hanya jika migration, seed, UI dasar, permission, dan test relevan sudah lulus.

## Milestone 1 - Foundation

Done jika:

- Laravel project berjalan.
- Auth username/password berjalan.
- Role dan permission seed tersedia.
- Superadmin seed tersedia.
- Filament panel aktif dengan theme dasar Raja POS.

## Milestone 2 - Master Data

Done jika:

- Product, category, brand, location, payment method, dan balance account tersedia.
- Product type PHYSICAL/DIGITAL/SERVICE berjalan.
- Price status COMPLETE/INCOMPLETE berjalan.
- Kasir tidak bisa melihat cost price.

## Milestone 3 - Inventory

Done jika:

- Inventory per product/location berjalan.
- Stock movement dibuat dari adjustment dan sale.
- Stock status otomatis sesuai rule Excel.
- Stock opname MVP bisa dibuat dan approved.

## Milestone 4 - POS Checkout

Done jika:

- POS custom Livewire bisa search, add cart, update qty, dan checkout.
- Multi-payment berjalan.
- Cash change dan cash minus berjalan sesuai rule.
- Checkout atomic dan concurrency stock lolos test.

## Milestone 5 - Sampah Transaksi & Receipt

Done jika:

- Sampah Transaksi, restore, dan auto-delete status berjalan.
- Stok dan saldo dibalik dengan benar saat trash/restore.
- Thermal receipt 58mm/80mm printable.
- Receipt memakai snapshot transaksi.

## Milestone 6 - Reports & Import

Done jika:

- Sales, product, inventory, payment, balance, dan digital report MVP tersedia.
- Profit hanya tampil sesuai permission.
- Import Excel preview berjalan.
- Commit import hanya Owner.
- Test matrix MVP lulus untuk flow utama.

---

# 87. FINAL PRODUCT POSITIONING

Raja POS sebaiknya diposisikan bukan sebagai:

> aplikasi kasir sederhana

tetapi sebagai:

> **Retail Operations Management System untuk bisnis aksesoris dan produk digital yang mengintegrasikan POS, inventory, digital products, payment channel, balance management, profit monitoring, dan reporting dalam satu sistem.**

Arsitektur ini tetap sederhana untuk dikembangkan sebagai MVP tetapi sudah memiliki fondasi yang memungkinkan pengembangan:

```text
Single Store
     ->
Multiple Cashiers
     ->
Multiple Locations
     ->
Central Inventory
     ->
Full Retail Management Platform
```

# SH Store

Platform penjualan produk digital berbasis web yang dibangun dengan Laravel 12 dan Livewire 3. Mendukung pembelian otomatis, pembayaran QRIS, deposit saldo, manajemen stok, voucher, reseller, dan dashboard admin lengkap.

---

## Daftar Isi

- [Fitur](#fitur)
- [Tech Stack](#tech-stack)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Struktur Database](#struktur-database)
- [Role & Permission](#role--permission)
- [Alur Aplikasi](#alur-aplikasi)
- [Struktur Direktori](#struktur-direktori)
- [Referensi URL](#referensi-url)
- [Perintah Artisan](#perintah-artisan)
- [Queue & Scheduler](#queue--scheduler)
- [Payment Gateway](#payment-gateway)

---

## Fitur

### Customer
- Register, login, forgot & reset password
- Dashboard — saldo, total order, riwayat transaksi
- Katalog produk dengan pencarian dan filter kategori
- Checkout dengan pembayaran via **Saldo** atau **QRIS**
- Countdown pembayaran otomatis (15 menit)
- Deposit saldo via QRIS
- Riwayat transaksi & deposit
- Detail transaksi — salin data produk (email/password) langsung

### Reseller
- Semua fitur customer
- Harga khusus reseller
- Harga bulk untuk pembelian massal

### Admin
- Dashboard — statistik lengkap, pendapatan, produk terlaris, stok hampir habis
- Kelola produk, kategori, stok (manual & import `.txt`)
- Kelola voucher diskon (nominal / persentase)
- Kelola user & reseller (ubah role, reset password)
- Riwayat deposit & transaksi (search, filter, export Excel)
- Pengaturan website (nama, logo, favicon, banner, kontak)
- Maintenance mode
- Backup database terjadwal & manual
- Log viewer sistem (`/admin/logs`)
- Activity log & audit log

### Sistem
- Auto cancel order expired (via scheduler)
- Lock stok anti double order
- Generate invoice otomatis
- Webhook payment gateway
- Queue untuk proses order & deposit
- Monitoring stok hampir habis

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Framework | Laravel 12 |
| Frontend | Livewire 3, Tailwind CSS, Alpine.js |
| Database | PostgreSQL |
| Auth | Laravel Starter Kit (Livewire) |
| Permission | Spatie Laravel Permission |
| Queue | Laravel Queue |
| Scheduler | Laravel Task Scheduler |
| Storage | Laravel Storage |
| Payment | QRIS Payment Gateway |
| Log Viewer | Opcodes Log Viewer |
| Export | Maatwebsite Excel |
| Backup | Spatie Laravel Backup |
| Dev Environment | Laravel Herd |

---

## Persyaratan Sistem

- PHP >= 8.2
- PostgreSQL >= 14
- Composer >= 2
- Node.js >= 18
- Laravel Herd (rekomendasi untuk lokal) atau alternatif: Nginx/Apache

---

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/username/sh-store.git
cd sh-store
```

### 2. Install dependensi PHP

```bash
composer install
```

### 3. Install dependensi Node

```bash
npm install
```

### 4. Salin file environment

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Konfigurasi database

Edit `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sh_store
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Buat database:

```bash
createdb sh_store
```

### 6. Jalankan migrasi & seeder

```bash
php artisan migrate
php artisan db:seed
```

### 7. Build assets

```bash
npm run build
# atau untuk development
npm run dev
```

### 8. Buat symlink storage

```bash
php artisan storage:link
```

### 9. Jalankan queue worker

```bash
php artisan queue:work
```

---

## Konfigurasi

### Environment Penting

```env
APP_NAME="SH Store"
APP_URL=http://localhost:8000

# Log
LOG_CHANNEL=daily

# Queue
QUEUE_CONNECTION=database

# Mail (untuk reset password)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=no-reply@shstore.com
MAIL_FROM_NAME="SH Store"

# Payment Gateway
PAYMENT_GATEWAY_KEY=your_key
PAYMENT_GATEWAY_SECRET=your_secret
PAYMENT_GATEWAY_BASE_URL=https://api.provider.com

# Backup
BACKUP_DISK=local
```

### Pengaturan Website

Setelah seeder dijalankan, pengaturan website dapat diubah melalui panel admin di `/admin/settings`. Nilai default dari seeder:

| Key | Default |
|---|---|
| `site_name` | SH Store |
| `site_logo` | null |
| `site_favicon` | null |
| `site_banner` | null |
| `admin_contact` | admin@shstore.com |
| `maintenance_mode` | false |

---

## Struktur Database

```
users
├── id, name, email, password
├── balance (decimal)
└── timestamps

categories
└── id, name, slug, timestamps

products
├── id, category_id
├── name, slug, description, thumbnail
├── price_customer, price_reseller, price_bulk
├── min_bulk_qty, status
└── timestamps

stocks
├── id, product_id
├── value (isi produk: email|password atau kode voucher)
├── status (available | sold)
├── sold_at
└── timestamps

vouchers
├── id, code, type (nominal | percentage)
├── value, min_purchase, max_usage, used_count
├── expired_at
└── timestamps

orders
├── id, user_id, product_id, stock_id, voucher_id
├── quantity, price, discount, total
├── payment_method (balance | qris)
├── payment_status (pending | paid | expired | cancelled)
├── status (pending | processing | completed | cancelled)
├── qris_reference, qris_expired_at
├── invoice_number, notes
└── timestamps

deposits
├── id, user_id, amount
├── payment_status (pending | paid | expired)
├── qris_reference, qris_expired_at
└── timestamps

website_settings
└── id, key, value, timestamps

activity_logs
└── id, user_id, action, description, ip_address, user_agent, timestamps

audit_logs
└── id, user_id, model_type, model_id, action, old_values, new_values, timestamps
```

---

## Role & Permission

Manajemen role dan permission menggunakan **Spatie Laravel Permission**.

### Role

| Role | Deskripsi |
|---|---|
| `admin` | Akses penuh ke seluruh sistem |
| `reseller` | Akses customer + harga khusus reseller |
| `customer` | Akses dasar — default saat register |

### Permission per Role

**customer**
```
view products · create order · create deposit · use voucher
view own orders · view own deposits · edit own profile
```

**reseller** (semua customer +)
```
view reseller price · view bulk price
```

**admin** (semua permission)
```
manage products · manage categories · manage stocks · import stocks
manage vouchers · manage users · manage resellers · manage deposits
manage orders · manage settings · manage backups
view logs · view activity logs · view audit logs · toggle maintenance mode
```

### Akun Default (dari Seeder)

| Nama | Email | Role | Password |
|---|---|---|---|
| Super Admin | admin@demo.com | admin | password |
| Budi Santoso | budi@demo.com | reseller | password |
| Siti Rahayu | siti@demo.com | customer | password |
| Andi Wijaya | andi@demo.com | customer | password |
| Dewi Lestari | dewi@demo.com | reseller | password |

> Ganti semua password default sebelum deploy ke production.

---

## Alur Aplikasi

### Pembelian Produk

```
Login → Pilih Produk → Checkout
  → Pilih Pembayaran:
      [Saldo]  → Validasi saldo → Potong saldo → Order Paid
      [QRIS]   → Generate QRIS → Bayar (maks 15 menit)
                 → Webhook diterima → Order Paid
  → Lock Stok → Ambil stok tersedia → Stok = Sold
  → Generate Invoice → Order Completed
  → Lihat di Riwayat Transaksi → Salin data produk
```

### Auto Cancel

```
Scheduler (tiap menit) → Cek order pending + qris_expired_at < now()
  → Status = Expired/Cancelled
  → Stok dikembalikan ke Available
```

### Deposit Saldo

```
Login → Menu Deposit → Input nominal → Generate QRIS
  → Bayar → Webhook → Saldo bertambah → Riwayat tersimpan
```

---

## Struktur Direktori

```
app/
├── Console/
│   └── Commands/
│       ├── AutoCancelExpiredOrdersCommand.php
│       ├── BackupDatabaseCommand.php
│       └── CheckLowStockCommand.php
├── Jobs/
│   ├── ProcessOrderJob.php
│   ├── HandlePaymentWebhookJob.php
│   ├── HandleDepositWebhookJob.php
│   └── GenerateInvoiceJob.php
├── Livewire/
│   ├── Landing/
│   │   └── HomePage.php
│   ├── Customer/
│   │   ├── Dashboard.php
│   │   ├── ProductList.php
│   │   ├── ProductDetail.php
│   │   ├── Checkout.php
│   │   ├── TransactionHistory.php
│   │   ├── TransactionDetail.php
│   │   ├── Deposit.php
│   │   ├── DepositHistory.php
│   │   └── EditProfile.php
│   └── Admin/
│       ├── Dashboard.php
│       ├── Products/
│       ├── Categories/
│       ├── Stocks/
│       ├── Vouchers/
│       ├── Users/
│       ├── Deposits/
│       ├── Orders/
│       ├── Settings/
│       ├── Backup/
│       └── Logs/
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Category.php
│   ├── Stock.php
│   ├── Order.php
│   ├── Deposit.php
│   ├── Voucher.php
│   ├── WebsiteSetting.php
│   ├── ActivityLog.php
│   └── AuditLog.php
├── Services/
│   ├── OrderService.php
│   ├── DepositService.php
│   ├── PaymentService.php
│   ├── StockService.php
│   ├── VoucherService.php
│   ├── InvoiceService.php
│   ├── ActivityLogService.php
│   └── AuditLogService.php
└── Http/
    └── Requests/

resources/views/
├── layouts/
│   ├── landing.blade.php
│   ├── app.blade.php
│   └── admin.blade.php
└── livewire/
    ├── landing/
    ├── customer/
    └── admin/

database/
├── migrations/
└── seeders/
    ├── DatabaseSeeder.php
    ├── RolePermissionSeeder.php
    ├── UserSeeder.php
    ├── CategorySeeder.php
    ├── ProductSeeder.php
    ├── StockSeeder.php
    ├── VoucherSeeder.php
    ├── OrderSeeder.php
    ├── DepositSeeder.php
    ├── WebsiteSettingSeeder.php
    ├── ActivityLogSeeder.php
    └── AuditLogSeeder.php
```

---

## Referensi URL

### Public
| URL | Keterangan |
|---|---|
| `/` | Landing page |
| `/login` | Halaman login |
| `/register` | Halaman registrasi |
| `/forgot-password` | Lupa password |

### Customer (auth)
| URL | Keterangan |
|---|---|
| `/dashboard` | Dashboard customer |
| `/products` | Katalog produk |
| `/products/{slug}` | Detail produk |
| `/checkout/{product}` | Halaman checkout |
| `/orders` | Riwayat transaksi |
| `/orders/{invoice}` | Detail transaksi |
| `/deposit` | Deposit saldo |
| `/deposit/history` | Riwayat deposit |
| `/profile` | Edit profil |

### Admin
| URL | Keterangan |
|---|---|
| `/admin` | Dashboard admin |
| `/admin/products` | Kelola produk |
| `/admin/categories` | Kelola kategori |
| `/admin/stocks` | Kelola stok |
| `/admin/vouchers` | Kelola voucher |
| `/admin/users` | Kelola user |
| `/admin/deposits` | Riwayat deposit |
| `/admin/orders` | Riwayat transaksi |
| `/admin/settings` | Pengaturan website |
| `/admin/backup` | Backup database |
| `/admin/logs` | Log viewer sistem |
| `/admin/activity-logs` | Activity log |
| `/admin/audit-logs` | Audit log |

### Webhook
| URL | Method | Keterangan |
|---|---|---|
| `/webhook/payment` | POST | Callback dari payment gateway |

---

## Perintah Artisan

```bash
# Migrasi & Seeder
php artisan migrate
php artisan migrate:fresh --seed

# Cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Queue
php artisan queue:work
php artisan queue:work --queue=webhooks,default

# Scheduler (jalankan manual untuk testing)
php artisan schedule:run

# Custom Commands
php artisan orders:cancel-expired
php artisan stock:check-low
php artisan backup:run

# Log Viewer
php artisan log-viewer:publish
```

---

## Queue & Scheduler

### Queue Jobs

| Job | Trigger | Fungsi |
|---|---|---|
| `ProcessOrderJob` | Setelah payment confirmed | Proses order, lock & ambil stok, generate invoice |
| `HandlePaymentWebhookJob` | Webhook masuk | Validasi & update status order |
| `HandleDepositWebhookJob` | Webhook masuk | Update saldo & status deposit |
| `GenerateInvoiceJob` | Order completed | Generate nomor invoice |

### Scheduled Tasks

| Command | Jadwal | Fungsi |
|---|---|---|
| `AutoCancelExpiredOrdersCommand` | Setiap menit | Cancel order QRIS yang expired |
| `CheckLowStockCommand` | Setiap 30 menit | Monitor stok < 5 |
| `BackupDatabaseCommand` | Setiap hari (02:00) | Backup database otomatis |

Pastikan cron job terdaftar di server:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Payment Gateway

### Flow QRIS

```
App → PaymentService::generateQris(amount, reference)
    → Provider API → QR image URL + expiry time
    → Tampilkan ke user (countdown Alpine.js)

Provider → POST /webhook/payment
         → Verifikasi signature
         → Dispatch HandlePaymentWebhookJob
         → Update order/deposit status
```

### Konfigurasi

```env
PAYMENT_GATEWAY_KEY=your_api_key
PAYMENT_GATEWAY_SECRET=your_secret
PAYMENT_GATEWAY_BASE_URL=https://api.provider.com
```

Webhook URL yang perlu didaftarkan ke provider:

```
https://yourdomain.com/webhook/payment
```

Route webhook dikecualikan dari CSRF verification di `bootstrap/app.php`.

---

## Keamanan

- Password di-hash dengan `bcrypt` via `Hash::make()`
- CSRF protection aktif di semua form (otomatis di Livewire)
- XSS protection via Blade `{{ }}` escaping
- SQL injection protection via Eloquent query builder
- Rate limiting pada route login, register, deposit, checkout
- Role & permission via Spatie (tidak ada hardcode string role)
- Webhook diverifikasi via signature sebelum diproses
- Data produk (email/password) hanya tampil di detail transaksi yang sudah `completed`
- Log viewer hanya dapat diakses oleh role `admin`

---

## Lisensi

Project ini dikembangkan untuk keperluan internal. Tidak untuk didistribusikan ulang tanpa izin.
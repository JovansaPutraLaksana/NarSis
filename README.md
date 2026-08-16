# NarSis — Sistem Informasi Sekolah Multi-School

NarSis adalah MVP Laravel 13 Blade untuk satu platform yang dapat digunakan banyak sekolah sekaligus. Data operasional dipisahkan dengan `school_id`, sedangkan hak akses dibatasi berdasarkan role dan tenant sekolah.

## Fitur yang sudah tersedia

- **Admin Website**: dashboard platform, tambah/edit/nonaktifkan sekolah, membuat Admin Sekolah pertama.
- **Admin Sekolah**: tahun ajaran & semester, guru, siswa, orang tua/wali, kelas, wali kelas, penempatan siswa, mata pelajaran, guru pengampu, jadwal dan validasi bentrok.
- **Guru**: dashboard jadwal, absensi hanya pada jam pelajaran, upload materi, membuat tugas, melihat pengumpulan, memberi nilai dan feedback.
- **Siswa**: jadwal hari ini, riwayat absensi per mata pelajaran, materi, tugas, upload/ganti pengumpulan sebelum tenggat, melihat nilai.
- **Orang Tua/Wali**: satu akun dapat terhubung ke beberapa siswa pada sekolah yang sama, melihat ringkasan absensi dan tugas anak.
- **File private**: development menggunakan `storage/app/private`; production dapat menggunakan Supabase Storage melalui REST server-side.

## Requirement

- PHP 8.3+ (disarankan PHP 8.4)
- Composer
- PostgreSQL / Supabase PostgreSQL
- Laravel 13

## 1. Instalasi lokal

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Di Supabase SQL Editor buat schema Laravel:

```sql
create schema if not exists laravel;
```

Isi `.env` menggunakan connection string Supabase:

```env
APP_URL=http://narsis.test
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=pgsql
DB_URL=postgresql://...
DB_SCHEMA=laravel
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

NARSIS_STORAGE_DRIVER=local
```

Kemudian:

```bash
php artisan migrate
php artisan db:seed
php artisan optimize:clear
```

Admin Website awal mengikuti `.env`:

```env
NARSIS_ADMIN_EMAIL=admin@narsis.test
NARSIS_ADMIN_PASSWORD=ChangeMe123!
```

**Ganti password tersebut sebelum production.**


## Demo cepat (opsional)

Setelah migration selesai, Anda dapat membuat satu sekolah demo lengkap tanpa menghapus data yang sudah ada:

```bash
php artisan db:seed --class=DemoSeeder
```

Akun demo:

| Role | Email | Password |
|---|---|---|
| Admin Sekolah | `schooladmin@narsis.test` | `password123` |
| Guru | `guru@narsis.test` | `password123` |
| Siswa | `siswa@narsis.test` | `password123` |
| Orang Tua | `orangtua@narsis.test` | `password123` |

Seeder juga membuat Tahun Ajaran 2026/2027, semester Ganjil aktif, kelas X IPA 1, Matematika, guru pengampu, jadwal demo pada hari saat seeder dijalankan (00:00–23:59), materi, dan tugas. Jalankan hanya pada environment development/demo.

## 2. Alur konfigurasi sekolah

Urutan penggunaan yang disarankan:

1. Login Admin Website → buat sekolah dan Admin Sekolah.
2. Login Admin Sekolah → buat Tahun Ajaran dan dua periode Semester → aktifkan semester berjalan.
3. Tambah Guru, Siswa dan Orang Tua/Wali.
4. Buat Kelas → tentukan Wali Kelas → masukkan siswa ke kelas.
5. Buat Mata Pelajaran.
6. Tetapkan Guru Pengampu per kelas/mapel/semester.
7. Buat Jadwal Pelajaran.
8. Guru dapat mulai absensi, materi dan tugas.

## 3. Supabase Storage untuk production

Buat **private bucket** pada Supabase Storage, misalnya `narsis-private`.

Karena autentikasi aplikasi menggunakan Laravel Auth, NarSis mengakses Storage hanya dari server dengan `service_role` key. Jangan pernah menaruh key ini di JavaScript atau repository.

```env
NARSIS_STORAGE_DRIVER=supabase
SUPABASE_URL=https://PROJECT_REF.supabase.co
SUPABASE_SERVICE_ROLE_KEY=YOUR_SERVER_ONLY_SERVICE_ROLE_KEY
SUPABASE_STORAGE_BUCKET=narsis-private
```

Jika `NARSIS_STORAGE_DRIVER=local`, upload tetap bekerja secara lokal pada `storage/app/private`.

## 4. Deployment Vercel

Project menyertakan `api/index.php` dan `vercel.json`. Konfigurasi menggunakan community PHP runtime `vercel-php@0.8.0` (PHP 8.4).

Set environment variables pada Vercel minimal:

```text
APP_NAME
APP_ENV=production
APP_KEY
APP_DEBUG=false
APP_URL
APP_TIMEZONE=Asia/Jakarta
DB_CONNECTION=pgsql
DB_URL=<Supabase Transaction Pooler URL>
DB_SCHEMA=laravel
DB_SSLMODE=require
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
NARSIS_STORAGE_DRIVER=supabase
SUPABASE_URL
SUPABASE_SERVICE_ROLE_KEY
SUPABASE_STORAGE_BUCKET
```

Untuk serverless, gunakan **Supabase Transaction Pooler**. Migration sebaiknya dijalankan dari komputer/admin pipeline, bukan dari request web:

```bash
php artisan migrate --force
```

## 5. Keamanan tenant

- `RoleMiddleware` membatasi area Admin Website, Admin Sekolah, Guru, Siswa dan Orang Tua.
- `EnsureActiveUser` memutus akses pada request berikutnya jika akun dinonaktifkan atau profil role tidak lagi tersedia.
- `EnsureSchoolTenant` memastikan akun sekolah memiliki `school_id` dan sekolah masih aktif.
- Model data akademik menggunakan `BelongsToSchool`, sehingga query web otomatis dibatasi ke sekolah pengguna.
- `school_id` tidak diambil dari form Admin Sekolah; nilai ditetapkan dari akun yang sedang login.
- Download file melewati controller Laravel dan dicek terhadap role/kelas/anak yang terhubung.

## 6. Catatan penting

- Akun orang tua saat ini berlaku dalam **satu sekolah**. Jika orang tua mempunyai anak di dua sekolah berbeda pada NarSis, gunakan akun per sekolah. Ini dapat dikembangkan menjadi identity lintas tenant pada versi berikutnya.
- Jadwal diasumsikan tidak melewati tengah malam.
- Absensi hanya dapat dibuat/disimpan antara `start_time` dan `end_time` pada hari jadwal.
- Default file upload dibatasi 4 MB (`NARSIS_UPLOAD_MAX_KB=4096`) agar aman terhadap limit request Vercel 4.5 MB. Jika hosting tidak memakai Vercel, batas ini dapat dinaikkan. Download Supabase menggunakan signed URL sehingga file tidak diproxy melalui Vercel.

## 7. Pemeriksaan setelah update dari versi lama

**Backup database Supabase terlebih dahulu.** Setelah mengganti source code lama dengan versi ini:

```bash
php artisan migrate
php artisan optimize:clear
php artisan route:list
```

Tidak perlu `migrate:fresh`, sehingga data sekolah/tahun ajaran yang sudah ada tetap dipertahankan.

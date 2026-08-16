# Security Notes — NarSis

## Prinsip utama

1. Seluruh data akademik tenant memiliki `school_id`.
2. Model tenant menggunakan `BelongsToSchool` untuk global scope berdasarkan sekolah user yang login.
3. Route sekolah dilindungi `auth`, `active.user`, `role:*`, dan `school.tenant`.
4. `school_id` untuk data operasional tidak diambil dari form Admin Sekolah.
5. Route model binding terhadap model tenant otomatis mengikuti global scope dan mengembalikan 404 untuk ID sekolah lain.
6. File materi/tugas/pengumpulan tidak disimpan pada public path dan download melewati pemeriksaan hak akses.

## Supabase

- Gunakan schema PostgreSQL khusus `laravel`.
- Bucket Storage harus **private**.
- `SUPABASE_SERVICE_ROLE_KEY` hanya boleh berada pada environment variable server; jangan pernah dikirim ke browser atau disimpan di repository.
- Untuk Vercel, gunakan Supabase Transaction Pooler pada koneksi database production.

## Operasional production

- `APP_DEBUG=false`.
- Gunakan password Admin Website yang kuat dan berbeda dari demo.
- Jangan menjalankan `DemoSeeder` di production.
- Backup database sebelum migration besar.
- Jalankan `php artisan migrate --force` melalui workstation/pipeline yang terkontrol, bukan endpoint web.
- Review user aktif secara berkala, terutama akun Admin Sekolah.

# ISCP Learning Management System

Information System Capstone Project (ISCP) — Sistem Manajemen Pembelajaran (Learning Management System) berbasis web yang dikembangkan menggunakan framework Laravel. Sistem ini dirancang untuk mendukung aktivitas pembelajaran digital dengan peran pengguna: admin

## Fitur Utama

**Manajemen Pengguna**
- Multi-role login 
- Manajemen akun guru dan siswa

**Manajemen Pembelajaran**
- Pengelolaan mata pelajaran, pelajaran, tema, dan subtema
- Pengelolaan materi pembelajaran
- Impor materi pembelajaran dari Excel

**Manajemen Evaluasi**
- Pengelolaan kompetensi dasar
- Pengelolaan tipe soal dan model soal
- Pengelolaan judul soal dan item soal
- Pengerjaan latihan/ujian oleh siswa

**Manajemen Kelas**
- Pengelolaan data kelas
- Pengelompokan siswa berdasarkan kelas

**Manajemen Produk**
- Pengelolaan produk pembelajaran
- Pengelolaan kode serial sebagai akses produk

## Tech Stack

- **Backend:** PHP, Laravel
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript, Bootstrap
- **Architecture:** MVC (Model-View-Controller)

## Catatan

Project ini merupakan versi awal pengembangan sistem manajemen pembelajaran yang dikerjakan sebagai bagian dari Information System Capstone Project. Beberapa proses pada sistem ini masih dilakukan secara manual oleh admin, di antaranya:

- Pengiriman informasi melalui email (belum otomatis)
- Tidak terdapat fitur chatbot untuk layanan pelanggan
- Tidak terdapat penjadwalan otomatis (scheduler)

Pengembangan lebih lanjut dari konsep sistem ini, termasuk penambahan fitur chatbot berbasis n8n dan otomatisasi notifikasi email, dilakukan pada proyek Tugas Akhir penulis.

## Instalasi

1. Clone repository:
```bash
   git clone https://github.com/JG1van/ISCP_LEARNING_MANAGEMENT_SYSTEM_LARAVEL.git
```

2. Masuk ke direktori project:
```bash
   cd ISCP_LEARNING_MANAGEMENT_SYSTEM_LARAVEL
```

3. Install dependency:
```bash
   composer install
```

4. Salin file environment:
```bash
   copy .env.example .env
```

5. Konfigurasi file `.env` (koneksi database).

6. Generate application key:
```bash
   php artisan key:generate
```

7. Buat database baru sesuai nama pada `.env`.

8. Jalankan migrasi:
```bash
   php artisan migrate
```

9. (Opsional) Jalankan seeder untuk data awal:
```bash
   php artisan db:seed
```

10. Jalankan server lokal:
```bash
    php artisan serve
```

## Status Project

Project ini dikembangkan sebagai bagian dari Information System Capstone Project (ISCP).

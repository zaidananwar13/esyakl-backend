# Sub-Plan 01: Quick Wins — Auto-Verify Register, Image Lazy-Loading, & Nginx Gzip

- **ID Plan**: SUB-01
- **Fokus**: Operasional Pendaftaran & Penghematan Bandwidth Awal
- **Estimasi Waktu**: ~45 Menit
- **Tingkat Risiko**: Sangat Rendah (0% downtime)

---

## 🎯 Tujuan
1. Mengatasi masalah email verifikasi yang tidak pernah sampai ke inbox user (karena server belum memiliki akun SMTP resmi).
2. Mempercepat pemuatan awal (*First Contentful Paint*) dengan menerapkan `loading="lazy"` pada gambar katalog kelas dan blog.
3. Menyediakan konfigurasi kompresi Gzip dan browser cache di Nginx server agar file JS/CSS terkompres hingga 60-70%.

---

## 📋 Langkah Kerja Detail

### 1. Backend: Auto-Verify Saat Registrasi Akun
- **File Target**: `app/Services/AuthService.php`
- **Tindakan**:
  - Pada method `register(array $data)`:
    ```php
    // Ganti nilai awal email_verified_at dari null menjadi timestamp saat ini
    $user->email_verified_at = $now;
    ```
  - Ubah pesan respon sukses menjadi:
    ```php
    'message' => 'Pendaftaran berhasil! Silakan masuk dengan akun baru Anda.',
    ```
  - Pembungkusan `sendVerificationEmail` tetap dipertahankan dengan `try/catch` agar jika nanti SMTP diisi, email tetap dicoba dikirim tanpa menghalangi login user.

### 2. Frontend: Penyesuaian Respon Sukses Pendaftaran
- **File Target**: `src/components/Sign-up-content.svelte`
- **Tindakan**:
  - Tangkap respon sukses pendaftaran (kode 200).
  - Tampilkan SweetAlert sukses: *"Pendaftaran Berhasil! Silakan masuk."* ➔ Redirect langsung ke `/sign-in`.

### 3. Frontend: Penerapan `loading="lazy"` pada Komponen Gambar
- **File Target**:
  - `src/components/Class-info.svelte`
  - `src/components/Blog-list.svelte`
  - `src/components/Event-list.svelte`
  - `src/components/Classroom-content.svelte`
- **Tindakan**:
  - Tambahkan atribut `loading="lazy"` dan `decoding="async"` pada seluruh elemen `<img>`.
  - Browser mobile tidak akan mengunduh gambar sebelum pengguna menggulir layar mendekati gambar tersebut.

### 4. Server: Template Konfigurasi Nginx Gzip & Cache Header
- **Target di Server**: `/etc/nginx/sites-available/arabic-go.com`
- **Snippet yang Ditambahkan**:
  ```nginx
  # Gzip Compression
  gzip on;
  gzip_vary on;
  gzip_min_length 1024;
  gzip_proxied expired no-cache no-store private auth;
  gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml application/javascript application/json;
  gzip_disable "MSIE [1-6]\.";

  # Static Assets Browser Cache
  location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
      expires 30d;
      add_header Cache-Control "public, no-transform";
  }
  ```

---

## ✅ Kriteria Keberhasilan (Acceptance Criteria)
1. Pendaftaran user baru langsung menghasilkan `email_verified_at` terisi (tidak null).
2. User baru bisa langsung login di web tanpa muncul error *"Akun belum diverifikasi"*.
3. Build Sapper sukses tanpa error.
4. Ukuran transfer file JS saat dicek via Network tab berkurang drastis berkat Gzip.

---

## 📌 Perintah Update untuk 86 Pengguna Lama (Disimpan untuk Nanti)
Status saat ini 86 pengguna lama dibiarkan seperti sedia kala sesuai permintaan pengguna. Jika di kemudian hari ingin mengaktifkan seluruh 86 akun lama tersebut sekaligus, jalankan salah satu perintah berikut:

### Opsi 1: Melalui SQL Direct (PostgreSQL / DBeaver / psql)
```sql
UPDATE "user" 
SET email_verified_at = created_at 
WHERE email_verified_at IS NULL;
```

### Opsi 2: Melalui Terminal Laravel Artisan Tinker
```bash
php artisan tinker --execute="DB::table('user')->whereNull('email_verified_at')->update(['email_verified_at' => DB::raw('created_at')]);"
```

---

## 🏁 Status Implementasi
- **Status**: 🟢 **Selesai Diimplementasikan & Diverifikasi di Lokal**
- **Perubahan Backend**:
  - `app/Services/AuthService.php`: Auto-verify pendaftaran baru (`$user->email_verified_at = $now`) & try-catch `sendVerificationEmail`.
  - `app/Services/UserService.php`: Auto-verify pendaftaran baru (`$user->email_verified_at = time()`).
- **Perubahan Frontend**:
  - `src/components/Sign-up-content.svelte`: Notifikasi sukses pendaftaran disesuaikan mengarahkan user langsung login.
  - `src/components/Class-info.svelte`: `loading="lazy"` & `decoding="async"` pada thumbnail dan avatar review.
  - `src/components/Blog-list.svelte`: `loading="lazy"` & `decoding="async"` pada gambar blog.
  - `src/components/Event-list.svelte`: `loading="lazy"` & `decoding="async"` pada poster event.
  - `src/components/Classroom-content.svelte`: `loading="lazy"` & `decoding="async"` pada foto instruktur/guru.
  - `src/components/Classes-list.svelte`: `loading="lazy"` & `decoding="async"` pada katalog grid kelas.
- **Hasil Pengujian**:
  - Test suite: 757 passed (1571 assertions).
  - Pendaftaran user baru: Berhasil langsung login dengan kode 200 tanpa verifikasi email manual.

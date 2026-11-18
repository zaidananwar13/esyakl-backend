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

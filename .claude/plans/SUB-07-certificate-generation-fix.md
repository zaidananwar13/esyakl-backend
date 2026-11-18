# Sub-Plan 07: Perbaikan Generator & Unduhan Sertifikat (Certificate System)

- **ID Plan**: SUB-07
- **Fokus**: Perbaikan Fatal Method Static & Sistem Unduh Sertifikat
- **Estimasi Waktu**: ~45 Menit
- **Tingkat Risiko**: Rendah (0% downtime)

---

## 🎯 Masalah (Error No. 4)
Terdapat beberapa masalah pada alur penerbitan dan pengunduhan sertifikat:
1. **Fatal Runtime Error di Controller**: Pada `app/Http/Controllers/API/CertificateController.php` method `create(Request $request)` dideklarasikan sebagai `public static function create(...)`, tetapi di dalamnya memanggil `$this->successResponse(...)` dan `$this->errorResponse(...)`. Di PHP 8.x+, pemanggilan `$this` dalam konteks static menyebabkan fatal crash:
   ```
   Fatal Error: Using $this when not in object context
   ```
2. **Variabel View Kurang di DomPDF**: Template `resources/views/pdf/cert.blade.php` membutuhkan variabel `$template` dan `$img` (QR Code base64). Pada `CertificateController@create`, kedua variabel ini tidak dikirimkan sehingga memicu error `Undefined variable`.
3. **Typo Filter di CertificateService**: Pada `CertificateService@createCertificate`, pengecekan duplikasi menggunakan `$classStudent->id_user` alih-alih `$classStudent->id_class_student`.
4. **Symlink Storage Lokal**: Direktori `public/storage` belum terhubung ke `storage/app/public` sehingga file PDF sertifikat tidak dapat diunduh (404 Not Found) di lingkungan pengujian lokal.

---

## 📋 Langkah Kerja Detail

### 1. Perbaikan Method `CertificateController@create`
- **File Target**: `app/Http/Controllers/API/CertificateController.php`
- **Tindakan**:
  - Hapus keyword `static`: ubah menjadi `public function create(Request $request)`.
  - Tambahkan validasi payload input (`api_token` dan `class`). Jika user atau pendaftaran kelas (`ClassStudent`) tidak ditemukan, kembalikan respons error 404/400 yang rapi.
  - Lengkapi passing parameter ke DomPDF (`$template` dan `$img` QR Code) atau gunakan delegasi ke `CertificateService`.
  - Simpan format URL file sertifikat yang konsisten dengan `CertificateService` (`config('app.url') . '/storage/files/user/' . $file`).

### 2. Perbaikan Koreksi Kunci di `CertificateService`
- **File Target**: `app/Services/CertificateService.php`
- **Tindakan**:
  - Pada method `createCertificate($classStudent)`:
    ```php
    // Ganti:
    $existing = ClassCertificate::where('id_class_student', $classStudent->id_user)->first();
    // Menjadi:
    $existing = ClassCertificate::where('id_class_student', $classStudent->id_class_student)->first();
    ```

### 3. Pembuatan Junction / Storage Link di Lokal
- Buat junction antara `public/storage` dan `storage/app/public` agar web server lokal dapat menyajikan file PDF statis.

### 4. Seeding Data Sertifikat untuk Pengujian
- Hubungkan akun uji coba (`test@mail.test` / ID 13) dengan sertifikat aktif untuk salah satu kelas yang telah diikutinya (misal Class ID 3 - *Nahwu Class - Beginner*).
- Generate file PDF nyata di `storage/app/public/files/user/` dan token sertifikat unik.

---

## ✅ Kriteria Keberhasilan (Acceptance Criteria)
1. Endpoint `POST /api/certificate/create` berjalan tanpa fatal error static context.
2. Endpoint `POST /api/certificate` mengembalikan daftar sertifikat aktif beserta URL file unduhan yang valid.
3. Halaman frontend `http://localhost:3000/student/my-certificate` menampilkan kartu sertifikat, pratinjau iframe, dan tombol unduh berfungsi normal.
4. Halaman verifikasi publik `http://localhost:3000/certificate/<token>` menampilkan rincian sertifikat siswa secara lengkap.

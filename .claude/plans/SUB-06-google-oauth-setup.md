# Sub-Plan 06: Konfigurasi & Penanganan Google OAuth (Single Sign-On)

- **ID Plan**: SUB-06
- **Fokus**: Integrasi Google OAuth & Penanganan Graceful Error
- **Estimasi Waktu**: ~30 Menit
- **Tingkat Risiko**: Sangat Rendah (0% downtime)

---

## 🎯 Masalah (Error No. 2)
Ketika pengguna mengklik tombol **"Masuk dengan Google"** di halaman login (`/sign-in`), sistem mengarahkan ke endpoint `/api/auth/google`. Namun karena kredensial Google OAuth (`GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET`) di file `.env` belum diisi, Laravel Socialite melempar exception `InvalidStateException` atau `InvalidArgumentException` yang menyebabkan error 500.

---

## 📋 Langkah Kerja Detail

### 1. Registrasi Kredensial di Google Cloud Console
1. Buka [Google Cloud Console](https://console.cloud.google.com/).
2. Buat proyek baru atau pilih proyek yang sudah ada (misal: `Arabic Go Production`).
3. Masuk ke menu **APIs & Services** > **OAuth consent screen**:
   - Pilih User Type: **External**.
   - Isi Nama Aplikasi: **Arabic Go**, Email Dukungan, dan Developer Contact.
   - Simpan (*Save and Continue*).
4. Masuk ke menu **APIs & Services** > **Credentials**:
   - Klik **Create Credentials** > **OAuth Client ID**.
   - Application type: **Web application**.
   - Name: `Arabic Go Web Client`.
   - **Authorized JavaScript origins**:
     - `https://arabic-go.com`
     - `https://api.arabic-go.com`
     - `http://localhost:3000` (untuk pengujian lokal)
   - **Authorized redirect URIs**:
     - `https://api.arabic-go.com/api/auth/google/callback` (Production)
     - `http://127.0.0.1:8000/api/auth/google/callback` (Local Development)
5. Salin **Client ID** dan **Client Secret**.

### 2. Konfigurasi Environment (`.env`)
Tambahkan kredensial pada file `.env` (baik di local maupun di VPS):
```env
GOOGLE_CLIENT_ID="<YOUR_CLIENT_ID>.apps.googleusercontent.com"
GOOGLE_CLIENT_SECRET="<YOUR_CLIENT_SECRET>"
GOOGLE_REDIRECT_URI="${APP_URL}/api/auth/google/callback"
```

### 3. Graceful Exception Handling di Backend
- **File Target**: `app/Http/Controllers/API/AuthController.php` / `app/Services/AuthService.php`
- **Tindakan**:
  - Bungkus pemanggilan `Socialite::driver('google')` dalam blok `try/catch`.
  - Jika kredensial belum diisi atau terjadi kegagalan dari sisi Google, jangan lemparkan exception raw 500.
  - Alihkan pengguna kembali ke halaman login frontend dengan pesan yang ramah:
    ```php
    if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
        return redirect(config('app-settings.frontend_url') . '/sign-in?oauth_error=' . urlencode('Fitur login Google sedang dalam pemeliharaan. Silakan gunakan email dan password.'));
    }
    ```

### 4. Notifikasi di Frontend
- **File Target**: `src/components/Sign-in-content.svelte`
- **Tindakan**:
  - Tangkap parameter URL `oauth_error`.
  - Jika parameter tersebut ada, tampilkan SweetAlert modal / alert merah yang ramah kepada pengguna.

---

## ✅ Kriteria Keberhasilan (Acceptance Criteria)
1. Pengguna yang mengklik "Masuk dengan Google" berhasil diarahkan ke layar pemilihan akun Google resmi.
2. Setelah memilih akun Google, akun otomatis terdaftar/terhubung dan langsung login ke dashboard kelas.
3. Jika konfigurasi OAuth di server belum diset, sistem tidak crash (menampilkan pesan ramah alih-alih error 500).

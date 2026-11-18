# Master Plan: Optimasi Performa & Stabilitas Sistem E-Syakl
**Target Lingkungan**: Production Server & Local Dev  
**Scope**: Backend (`esyakl-backend`) & Frontend (`arabic-go.com`)  
**Format Perintah**: Pengguna cukup memberikan perintah:  
👉 *"Kerjakan Sub 1"*, *"Kerjakan Sub 2"*, dst.

---

## 📌 Daftar Sub-Plan

| Kode Sub-Plan | Judul Rencana | Estimasi Waktu | Dampak / Manfaat Utama | Status |
|---|---|---|---|---|
| [**SUB-01**](./SUB-01-quick-wins-auto-verify-lazyload.md) | **Quick Wins: Auto-Verify Registrasi, Lazy Loading Gambar, & Nginx Gzip** | ~45 menit | Pendaftaran langsung aktif tanpa tunggu SMTP, bandwidth hemat 60-70%. | 🟢 Selesai di Lokal (757 Tests Pass) |
| [**SUB-02**](./SUB-02-backend-query-n-plus-1-optimization.md) | **Backend: Eliminasi N+1 Query pada Classroom & MyClass Service** | ~1-2 jam | Respon API kelas turun dari ~1,5 detik menjadi <200ms (Pangkas 40 query ➔ 2 query). | ⚪ Menunggu |
| [**SUB-03**](./SUB-03-postgresql-database-indexing.md) | **Database: Indeks Komposit PostgreSQL (Zero-Downtime)** | ~30 menit | Pencarian data user & enrollment instan, mencegah CPU spike saat traffic tinggi. | ⚪ Menunggu |
| [**SUB-04**](./SUB-04-frontend-jquery-elimination.md) | **Frontend: Eliminasi Manipulasi jQuery pada Komponen Kritis** | ~1-2 jam | Menghilangkan lag & patah-patah (*layout thrashing*) di smartphone/mobile browser. | ⚪ Menunggu |
| [**SUB-05**](./SUB-05-client-side-caching.md) | **Network: Client-Side Caching untuk Katalog Kelas & Profil** | ~1 jam | Berpindah tab/halaman instan tanpa spinner loading berputar ulang. | ⚪ Menunggu |
| [**SUB-06**](./SUB-06-google-oauth-setup.md) | **Auth: Konfigurasi & Penanganan Error Google OAuth (SSO)** | ~30 menit | Integrasi Google Sign-In & mencegah crash 500 saat kredensial OAuth kosong (Error No. 2). | ⚪ Menunggu |
| [**SUB-07**](./SUB-07-certificate-generation-fix.md) | **Feature: Perbaikan Fatal Error Generator & Unduhan Sertifikat** | ~45 menit | Memperbaiki bug static controller, template PDF, dan menyediakan unduhan sertifikat (Error No. 4). | 🟢 Selesai & Dipush (`51e833b`) |

---

## 🛠️ Panduan Eksekusi

Setiap file di dalam folder ini berisi panduan implementasi mandiri (*self-contained*), file target, solusi teknis teruji, dan kriteria verifikasi.

Untuk menjalankan, Anda cukup memanggil:
- **"Kerjakan Sub 1"** ➔ Agen akan mengeksekusi seluruh checklist di `SUB-01`.
- **"Kerjakan Sub 2"** ➔ Agen akan mengeksekusi seluruh checklist di `SUB-02`.
- **"Kerjakan Sub 7"** ➔ Agen akan mengeksekusi perbaikan dan pengujian sertifikat di `SUB-07`, dan seterusnya.

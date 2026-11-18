# Laporan Audit Error Sistem E-Syakl (Backend & Frontend)
**Tanggal Audit**: 18 November 2026  
**Lingkungan**: Local Laragon (PHP 8.4, PostgreSQL 14+, Sapper / Svelte 3)  
**Branch Backend**: `upgrade/laravel-13`  
**Branch Frontend**: `dev/update-system`  

---

## 1. Ringkasan Eksekutif
Audit menyeluruh dilakukan terhadap seluruh komponen sistem (Backend Laravel 13 dan Frontend Sapper/Svelte) menyusul pembaruan modul autentikasi (login dengan username/email dan register).

### Ringkasan Status Sistem:
- **Modul Login & Register**: ✅ Berfungsi normal (mendukung username & email, email verification, hashing Bcrypt aman).
- **Backend API & Routing**: ✅ 151 rute API terdaftar dan dapat di-load tanpa error syntax.
- **Frontend Build**: ✅ `npm run build` sukses tanpa error kompilasi fatal.
- **PHPUnit Test Suite**: ⚠️ 9 Failures, 2 Risky dari 315 tests (disebabkan perubahan security hardening sebelumnya di mana kode service/controller sudah diperketat namun assertion test belum disesuaikan).
- **Struktur Model vs Basis Data**: ⚠️ 6 Eloquent Model warisan lama (legacy) tidak memiliki tabel fisik di PostgreSQL karena telah digantikan oleh skema baru.
- **Aksesibilitas Frontend**: ℹ️ 193 Svelte A11y compiler warnings.

---

## 2. Rincian Masalah

### Kategori A: Regresi Test PHPUnit (9 Failures & 2 Risky) - Ticket `ERR-009`

| ID Test | File Pengujian | Kode Status Aktual vs Ekspektasi | Akar Masalah | Solusi Perbaikan |
|---|---|---|---|---|
| **TEST-01** | `tests/Feature/ExampleTest.php` | `404` vs `200` | Rute `routes/web.php` diprefiks dengan `/web` di `RouteServiceProvider`. Endpoint `GET /` tidak terdaftar, rute aktif adalah `GET /web`. | Ubah asersi di `ExampleTest` menjadi `$this->get('/web')`. |
| **TEST-02** | `tests/Unit/Controllers/PaymentControllerTest.php` | `422` vs `401` | Method `createSnapPayment` memvalidasi kelengkapan payload input (`class => required`) sebelum memeriksa autentikasi `api_token`. Test memanggil tanpa parameter `class`. | Berikan payload `class` pada request test atau periksa auth sebelum validasi payload. |
| **TEST-03** | `tests/Unit/Services/PromoServiceTest.php` (`test_validate_promo_code_returns_error_when_not_found`) | `404` vs `401` | `PromoService` mengembalikan standar REST 404 (Not Found) untuk kupon tidak terdaftar. Asersi test lama masih memeriksa 401 (Unauthorized). | Sesuaikan ekspektasi test menjadi `$this->assertEquals(404, $result['code'])`. |
| **TEST-04** | `tests/Unit/Services/PromoServiceTest.php` (`test_get_promo_details_returns_error_when_code_is_null`) | `400` vs `401` | `PromoService` mengembalikan 400 (Bad Request) untuk kode null/kosong. Test lama memeriksa 401. | Sesuaikan ekspektasi test menjadi `$this->assertEquals(400, $result['code'])`. |
| **TEST-05** | `tests/Unit/Services/PromoServiceTest.php` (`test_get_promo_details_returns_error_when_not_found`) | `404` vs `401` | `PromoService` mengembalikan 404 saat kode promo tidak ada. Test lama memeriksa 401. | Sesuaikan ekspektasi test menjadi `$this->assertEquals(404, $result['code'])`. |
| **TEST-06** | `tests/Unit/Controllers/QuizControllerTest.php` (`test_freekey_returns_success`) | `403` vs `200` | Endpoint `freekey` (pembocoran kunci jawaban) telah dikunci dengan proteksi role `admin`. Test memanggil tanpa privilege admin. | Buat mock user dengan `type => 'admin'` pada context test. |
| **TEST-07** | `tests/Unit/Controllers/QuizControllerTest.php` (`test_freekey_with_null_syllabus_from_request`) | `403` vs `200` | Sama dengan TEST-06 (membutuhkan admin authentication). | Tambahkan context user admin pada test. |
| **TEST-08** | `tests/Unit/Controllers/QuizControllerTest.php` (2 Risky tests) | Error / Exception Handler Warning | PHPUnit mendeteksi error handler Laravel tidak di-*restore* dengan bersih saat test selesai. | Tambahkan `$this->restoreExceptionHandler()` di teardown. |
| **TEST-09** | `tests/Unit/Services/UserServiceTest.php` (`update_password_returns_error_when_password_too_short`) | String error assertion mismatch | Pesan error diubah menjadi `'Password must be at least 8 characters long'`, test mencari substring `'password length'`. | Sesuaikan substring assertion dengan pesan baru. |
| **TEST-10** | `tests/Unit/Services/UserServiceTest.php` (`update_password_validates_minimum_length_boundary`) | `422` vs Non-422 | Kebijakan keamanan password dinaikkan dari min 6 karakter menjadi min 8 karakter. Password 6 digit kini mengembalikan 422 (sebelumnya lolos). | Perbarui boundary pengujian ke 8 karakter. |

---

### Kategori B: Model Warisan (Legacy) Tanpa Tabel Database - Ticket `ERR-010`

Berdasarkan pengecekan terhadap seluruh 51 model Eloquent dan 52 tabel PostgreSQL:

| Model | Tabel yang Ditarget | Status Database | Penjelasan & Rute Pemanggil |
|---|---|---|---|
| `Discussion` | `discussions` | ❌ Tidak ada | Model orphan peninggalan skema awal. Sistem diskusi aktif menggunakan `ClassDiscussion` (tabel `class_discussion`). |
| `Instansi` | `instansi` | ❌ Tidak ada | Model orphan, tabel dihapus/tidak pernah dibuat di skema database produksi. |
| `Kelas_User` | `kelas_user` | ❌ Tidak ada | Model orphan skema bahasa Indonesia. Sistem peserta kelas aktif menggunakan `ClassStudent` (tabel `class_student`). |
| `KelasHistory` | `kelas_history` | ❌ Tidak ada | Tabel tidak ada. Dipanggil di `updateClassHistory()` di `ClassService.php` dan `SyllabusService.php`. Kedua method ini tidak pernah dipanggil oleh controller/frontend aktif. |
| `SilabusChecker` | `silabus_checker` | ❌ Tidak ada | Tabel tidak ada. Dipanggil di helper function `Help::checkSilabusAccessUser()` yang hanya dipakai oleh `SyllabusService.php`. |
| `Sub_Kategori_Silabus` | `sub_kategori_silabus` | ❌ Tidak ada | Model silabus versi lama. Sistem silabus aktif menggunakan model `Syllabus` (tabel `syllabus`) dan `SyllabusStudent` (tabel `syllabus_student`). |

> **Catatan Dampak Operasional**:
> Frontend aktif (`arabic-go.com`) mengakses silabus melalui endpoint:
> - `POST /my-class/classroom` (`ClassroomController@classroom`)
> - `POST /auth/syllabus` (`FEAuthController@index`)
>
> Kedua endpoint tersebut menggunakan model modern (`Syllabus`, `SyllabusStudent`, `ClassStudent`) yang tabelnya **lengkap dan normal**, sehingga pengguna di antarmuka web tidak terganggu oleh ketiadaan tabel-tabel legacy di atas.

---

### Kategori C: Frontend Accessibility & Sapper Warnings

- Total 193 peringatan A11y terdeteksi saat `npm run build`:
  - Tag `<a>` tanpa atribut `href` yang valid (menggunakan `on:click`).
  - Elemen form (`<input>`, `<textarea>`) tanpa tag `<label>` atau atribut `aria-label`.
  - Tombol tanpa teks deskriptif yang terbaca oleh screen reader.
- Masalah ini berkategori **Low Priority** dan tidak menghentikan runtime aplikasi Sapper.

---

## 3. Rencana Perbaikan (Action Plan)

1. **Sprint 1 (Test Suite Remediation - Ticket ERR-009)**:
   - Perbarui assertion pada `ExampleTest.php`, `PaymentControllerTest.php`, `PromoServiceTest.php`, `QuizControllerTest.php`, dan `UserServiceTest.php`.
   - Pastikan eksekusi `vendor/bin/phpunit` menghasilkan status **315 passed, 0 failures, 0 risky**.
2. **Sprint 2 (Deprecation & Clean-up - Ticket ERR-010)**:
   - Beri anotasi `@deprecated` pada model orphan (`Discussion`, `Instansi`, `Kelas_User`, `KelasHistory`, `SilabusChecker`, `Sub_Kategori_Silabus`).
   - Hapus atau amankan method uncalled di `ClassService.php` dan `helper.php` agar tidak menimbulkan fatal error jika terpanggil secara tidak sengaja.
3. **Sprint 3 (Frontend A11y & Form Hygiene)**:
   - Tambahkan atribut `href="javascript:void(0)"` atau ganti ke elemen `<button type="button">` untuk aksi interaktif non-link.
   - Tambahkan `aria-label` pada elemen input.

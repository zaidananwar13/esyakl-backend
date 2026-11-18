# Sub-Plan 03: PostgreSQL Database Indexing & Query Acceleration

- **ID Plan**: SUB-03
- **Fokus**: Optimasi Kecepatan Eksekusi Query Database PostgreSQL (Zero-Downtime)
- **Estimasi Waktu**: ~30 Menit
- **Tingkat Risiko**: Sangat Rendah (Menggunakan `CONCURRENTLY`, tidak ada tabel yang di-lock)

---

## 🎯 Tujuan
Membuat indeks komposit (*composite indexes*) pada kolom-kolom relasi yang paling sering difilter di PostgreSQL, mencegah operasi *Sequential Scan* (membaca jutaan baris dari disk), dan mempercepat proses login, pencarian kelas, serta pengecekan progress silabus secara instan.

---

## 📋 Daftar Index yang Perlu Dibuat

| Nama Index | Tabel Target | Kolom Target | Alasan / Rute yang Menggunakan |
|---|---|---|---|
| `idx_user_email_lower` | `user` | `(LOWER(email))` | Pencarian login akun via email (case-insensitive) |
| `idx_user_username_lower` | `user` | `(LOWER(username))` | Pencarian login akun via username (case-insensitive) |
| `idx_class_student_user_class` | `class_student` | `(id_user, id_class)` | Pengecekan akses kelas pengguna di `/my-class/classroom` |
| `idx_syllabus_student_progress` | `syllabus_student` | `(id_class_student, id_syllabus)` | Pengecekan status kelulusan materi silabus |
| `idx_quiz_student_user_quiz` | `quiz_student` | `(id_user, id_quiz)` | Riwayat skor kuis mahasiswa |
| `idx_class_lecture_syllabus` | `class_lecture` | `(id_syllabus)` | Pengambilan daftar video materi per silabus |

---

## 📋 Langkah Kerja Detail

### 1. Buat Script SQL Aman (Zero-Downtime)
Buat file migrasi/script SQL `.claude/scripts/add_performance_indexes.sql`:

```sql
-- Indeks pencarian login (case-insensitive)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_user_email_lower ON "user" (LOWER(email));
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_user_username_lower ON "user" (LOWER(username));

-- Indeks relasi kelas & silabus
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_class_student_user_class ON "class_student" (id_user, id_class);
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_syllabus_student_progress ON "syllabus_student" (id_class_student, id_syllabus);
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_class_lecture_syllabus ON "class_lecture" (id_syllabus);

-- Indeks kuis & nilai
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_quiz_student_user_quiz ON "quiz_student" (id_user, id_quiz);
```

### 2. Cara Eksekusi di Server (Bisa via Tinker atau psql)
Di terminal server:
```bash
sudo -u postgres psql -d arabic_go -f /var/www/api.arabic-go.com-l13/.claude/scripts/add_performance_indexes.sql
```
*Atau dieksekusi satu baris via Artisan Tinker tanpa perlu keluar dari terminal aplikasi.*

---

## ✅ Kriteria Keberhasilan (Acceptance Criteria)
1. Perintah SQL selesai tanpa error.
2. Query `EXPLAIN ANALYZE SELECT * FROM "user" WHERE LOWER(email) = 'test@mail.test'` berubah dari `Seq Scan` menjadi `Bitmap Index Scan` (waktu eksekusi <0.1 ms).
3. Tidak ada tabel yang terkunci saat proses index dibuat.

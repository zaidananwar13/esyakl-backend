# Sub-Plan 02: Backend Query Optimization & Eliminasi N+1 Queries

- **ID Plan**: SUB-02
- **Fokus**: Pangkas Waktu Respon API Halaman Kelas & Classroom (Kecepatan Naik 3x - 5x)
- **Estimasi Waktu**: ~1-2 Jam
- **Tingkat Risiko**: Rendah (Dilindungi oleh 757 unit test)

---

## 🎯 Tujuan
Mengurangi beban CPU database PostgreSQL dan memangkas waktu tunggu pengguna saat membuka halaman kelas/classroom dengan mengganti query berulang di dalam perulangan (`foreach`) menjadi satu kali query batch (`whereIn` & eager loading).

---

## 📋 Analisis Titik Kritis N+1 Query

### 1. `app/Services/ClassroomService.php` (`getClassroomData`)
- **Kondisi Saat Ini**:
  Saat mengambil data silabus kelas:
  ```php
  foreach ($syllabuses as $syll) {
      // 1. Query mencari materi video untuk silabus ini
      $lectures = ClassLecture::where('id_syllabus', $syll->id_syllabus)->get();
      // 2. Query mencari status kelulusan student untuk silabus ini
      $progress = SyllabusStudent::where('id_syllabus', $syll->id_syllabus)->first();
      // 3. Query rating/review
      ...
  }
  ```
  Jika ada 20 materi dalam 1 kelas, backend menembak **60 query terpisah** ke PostgreSQL!
- **Solusi Batching**:
  Ambil seluruh ID silabus terlebih dahulu:
  ```php
  $syllabusIds = $syllabuses->pluck('id_syllabus');
  // Ambil semua lecture sekaligus dalam 1 query:
  $allLectures = ClassLecture::whereIn('id_syllabus', $syllabusIds)->get()->groupBy('id_syllabus');
  // Ambil semua status progress student sekaligus dalam 1 query:
  $allProgress = SyllabusStudent::where('id_class_student', $classStudent->id_class_student)
      ->whereIn('id_syllabus', $syllabusIds)
      ->get()
      ->keyBy('id_syllabus');
  ```
  ➔ **Total query berkurang dari 60 query menjadi hanya 2 query!**

### 2. `app/Services/MyClassService.php` (`index` / `progress`)
- **Kondisi Saat Ini**:
  Pada daftar "Kelas Saya", perulangan tiap kelas memanggil kalkulasi progress, sertifikat, dan review secara berurutan.
- **Solusi Batching**:
  Pre-load data progress dan sertifikat siswa menggunakan `whereIn('id_class_student', $classStudentIds)`.

---

## 📋 Langkah Kerja Detail
1. Refaktor `ClassroomService::getClassroomData` menggunakan teknik batch collection grouping.
2. Refaktor query review dan rating di `ClassService.php` agar menggunakan agregasi SQL tunggal (`withCount`, `withAvg`).
3. Jalankan test suite PHPUnit: `vendor/bin/phpunit` untuk memastikan seluruh kontrak respons JSON tetap identik dan tidak ada regresi.

---

## ✅ Kriteria Keberhasilan (Acceptance Criteria)
1. Eksekusi `vendor/bin/phpunit` tetap menghasilkan **757 passed (100% Green)**.
2. Jumlah query database pada endpoint `POST /my-class/classroom` turun dari >30 query menjadi **<5 query**.
3. Response time endpoint berkurang drastis di bawah 250ms.

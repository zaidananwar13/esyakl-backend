# Sub-Plan 05: Client-Side Caching & Instant Page Transitions

- **ID Plan**: SUB-05
- **Fokus**: Menghilangkan Loading Spinner Berulang & Membuat Navigasi Halaman Instan
- **Estimasi Waktu**: ~1 Jam
- **Tingkat Risiko**: Sangat Rendah (Non-Destructive)

---

## 🎯 Tujuan
Mencegah aplikasi menembak ulang API yang sama dari nol setiap kali pengguna berpindah tab atau halaman. Data yang jarang berubah (seperti daftar kelas, katalog silabus, FAQ, dan profil pengguna) disimpan sementara di memori browser/`sessionStorage` selama 3–5 menit, sehingga saat berpindah halaman, konten langsung tampil seketika (*0 millisecond delay*).

---

## 📋 Masalah Saat Ini
Saat pengguna berada di halaman **"Daftar Kelas"**, lalu berpindah ke halaman **"Blog"**, lalu kembali lagi ke **"Daftar Kelas"**:
1. Frontend menghapus seluruh daftar kelas dari layar.
2. Memunculkan spinner loading berputar.
3. Mengirim request HTTP baru ke server.
4. Menunggu respon selama 1-2 detik baru menampilkan daftar kelas kembali.

Hal ini membuat aplikasi terasa seperti website jadul yang selalu me-refresh seluruh data.

---

## 📋 Langkah Kerja Detail

### 1. Buat Layer Cache Ringan di `src/api/ApiController.js`
- Simpan cache request method `GET` berdasarkan key URL endpoint dan query parameters:
  ```javascript
  const apiCache = new Map();
  const CACHE_TTL_MS = 5 * 60 * 1000; // 5 Menit

  export const CachedApiController = async ({ method, endpoint, datas, token, lang }) => {
    // Hanya cache request GET
    if (method.toUpperCase() === "GET") {
      const cacheKey = `${endpoint}_${lang || "id"}`;
      const cached = apiCache.get(cacheKey);
      
      if (cached && (Date.now() - cached.timestamp < CACHE_TTL_MS)) {
        return cached.data; // Kembalikan instan tanpa kirim request ke server!
      }

      const response = await ApiController({ method, endpoint, datas, token, lang });
      apiCache.set(cacheKey, { data: response, timestamp: Date.now() });
      return response;
    }

    return ApiController({ method, endpoint, datas, token, lang });
  };
  ```

### 2. Terapkan pada Halaman Katalog & Data Statis
- Gunakan `CachedApiController` pada halaman:
  - `src/routes/classes.svelte` (Katalog kelas)
  - `src/routes/faq.svelte` (FAQ)
  - `src/routes/blogs.svelte` (Daftar artikel)

### 3. Mekanisme Invalidation (Hapus Cache saat Update)
- Saat pengguna melakukan checkout kelas baru atau update profil, panggil fungsi `clearApiCache()` agar data langsung segar kembali.

---

## ✅ Kriteria Keberhasilan (Acceptance Criteria)
1. Berpindah antar tab (Kelas ➔ Blog ➔ Kelas) terjadi seketika tanpa ada loading spinner yang berputar ulang.
2. Jumlah request ke server berkurang lebih dari 50% saat pengguna menjelajahi web.
3. `npm run build` tetap selesai tanpa error.

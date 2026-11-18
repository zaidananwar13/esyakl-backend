# Sub-Plan 04: Frontend Responsiveness & Eliminasi jQuery pada Komponen Kritis

- **ID Plan**: SUB-04
- **Fokus**: Menghilangkan Delay Render (*Layout Thrashing*) & Memperhalus UI Mobile
- **Estimasi Waktu**: ~1-2 Jam
- **Tingkat Risiko**: Rendah (Terisolasi pada komponen terkait)

---

## 🎯 Tujuan
Membersihkan pemanggilan langsung jQuery (`window.$("#...").text()`, `.show()`, `.hide()`) di dalam komponen Svelte utama, dan mengembalikannya ke sistem reaktivitas bawaan Svelte (`bind:value`, variabel reaktif, blok `{#if}`). 

Hasilnya: Browser smartphone tidak perlu menghitung ulang layout berkali-kali saat user mengetik atau menekan tombol (*Zero Layout Thrashing*).

---

## 📋 Komponen Target & Masalah

### 1. `src/components/Sign-in-content.svelte` & `Sign-up-content.svelte`
- **Kondisi Saat Ini**:
  Menggunakan `window.$("#login-button").text("Masuk")`, `window.$("#message-password").text(msg)`.
  Pengecekan input masih mengambil nilai mentah `window.$("#email").val()`.
- **Perbaikan**:
  Gunakan reaktivitas Svelte:
  ```svelte
  <script>
    let identifier = "";
    let password = "";
    let isLoading = false;
    let errorMessage = "";
  </script>

  <input bind:value={identifier} placeholder="Email or Username" />
  <input type="password" bind:value={password} placeholder="Password" />

  {#if errorMessage}
    <p class="error-text">{errorMessage}</p>
  {/if}

  <button class="primary-button" disabled={isLoading}>
    {isLoading ? "Memproses..." : "Masuk"}
  </button>
  ```

### 2. `src/components/Setting-content.svelte` (Profil Mahasiswa)
- **Kondisi Saat Ini**:
  Terdapat puluhan baris `window.$("#full-name-field-error").addClass("text-danger")` yang membuat form profil terasa lambat saat diketik.
- **Perbaikan**:
  Ganti validasi form menjadi reaktif variabel lokal.

---

## 📋 Langkah Kerja Detail
1. Refaktor `Sign-in-content.svelte` dan `Sign-up-content.svelte` agar 100% menggunakan native Svelte state bindings.
2. Hapus dependensi eksternal terhadap pemanggilan jQuery modal body jika memungkinkan.
3. Jalankan `npm run build` dan verifikasi bahwa waktu rendering di DevTools Performance tab tidak lagi memicu *Forced Synchronous Layout*.

---

## ✅ Kriteria Keberhasilan (Acceptance Criteria)
1. `npm run build` selesai tanpa error.
2. Form login dan register merespon pengetikan dan klik tombol secara instan (0 delay).
3. Transisi error message dan loading state berjalan mulus tanpa kedip (*flicker*).

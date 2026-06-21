# prfx-equipments — Documentation

## Struktur

```
docs/
├── README.md          ← Indeks + rules penulisan (file ini)
├── user/              ← Dokumentasi untuk pengguna akhir
│   └── getting-started.md
└── developer/         ← Dokumentasi untuk developer / kontributor
    └── architecture.md
```

## Aturan Penulisan

### Bahasa

- **User docs** — Bahasa Indonesia, gaya santai tapi jelas. Bayangkan menjelaskan ke rekan kerja yang baru pertama kali pakai.
- **Developer docs** — Bahasa Inggris atau Indonesia sesuai kebutuhan teknis.

### Gaya

- Kalimat pendek. Satu paragraf = satu ide.
- Pakai **contoh** kapan bisa, jangan cuma teori.
- Screenshot / diagram sangat dianjurkan (simpan di `docs/assets/`).
- Hindari jargon teknis di user docs. Kalau tidak bisa hindari, jelaskan artinya.

### Format

- Heading pakai `#` `##` `##` — jangan loncat level.
- List pakai `-` untuk bullet, `1.` `2.` `3.` untuk langkah berurutan.
- Code block selalu cantumkan language: ```php, ```bash, ```sql.
- File baru selalu mulai dengan `# Judul` di baris pertama.

### Konsistensi

- Nama fitur selalu sama. Kalau di UI tertulis "Equipment Group", jangan tulis "Grup Peralatan" di docs.
- Link antar docs pakai relative path: `[Getting Started](user/getting-started.md)`.
- Setiap file punya "Last updated" di bagian bawah.
- **Permission/CRUD** — tiap fitur baru wajib didokumentasikan hak aksesnya di user docs (langkah setting) dan developer docs (pattern implementasi).

### Review

- Sebelum merge, baca ulang seolah kamu user baru.
- Cek semua link masih valid.
- Pastikan contoh kode masih jalan di versi terakhir.

---

## Kontribusi Docs

1. Fork / branch dari `main`.
2. Edit atau tambah file di `docs/user/` atau `docs/developer/`.
3. Update indeks di file ini kalau menambah file baru.
4. PR dengan deskripsi perubahan.

---

**Last updated:** 2026-06-21
# Register My Unit — Equipment Unit

## Apa itu "My Unit"?

**My Unit** (Equipment Unit) adalah data peralatan milik customer yang didaftarkan oleh customer sebagai pemilik alat. Di sini customer mendaftarkan alat fisik yang dimiliki, dengan memilih item dari katalog yang sudah disediakan system.

Satu unit peralatan punya detail lengkap seperti kode unit, nomor seri, lokasi, sertifikat, dan status.

**Contoh:** PT. ABC punya 3 unit Crane di lokasi Jakarta. Saat mendaftarkan, mereka memilih item "Overhead Crane" dari katalog (yang sudah dibuat oleh admin), lalu mengisi detail masing-masing unit.

## Siapa yang Bisa Akses?

### Admin / Staff

Staff internal aplikasi bisa melihat, menambah, mengedit, dan menghapus semua unit. Ini wajar karena mereka pengelola system.

### Customer

Customer hanya bisa melihat unit milik sendiri. Customer tidak bisa melihat unit milik customer lain.

### Surveyor / Pihak Ketiga

**Surveyor tidak bisa melihat data customer sama sekali** — kecuali customer itu sendiri yang mengundang dan menyetujui koneksi lewat **Connections** module.

Mekanismenya:

1. Customer mengirim **undangan** (invite) ke surveyor lewat menu Connections.
2. Surveyor menerima undangan → koneksi **active**.
3. Surveyor baru bisa melihat unit customer tersebut — dan hanya customer itu saja, tidak semua.

> 🔒 Jadi data customer benar-benar terproteksi. Tidak ada pihak luar yang bisa mengakses data customer tanpa izin eksplisit dari customer yang bersangkutan. Izin ini dikelola sepenuhnya oleh customer lewat menu **Connections**.

### Cara Setting Permission

1. Buka **Setup → Staff → Roles**.
2. Pilih role yang ingin diedit (atau buat baru).
3. Cari bagian **My Equipments**.
4. Centang permission yang sesuai.
5. Klik **Save**.

**Catatan:** Tombol Create, Edit, Delete di halaman akan otomatis muncul/sembunyi sesuai hak akses kamu.

## Cara Daftarkan Unit Baru

### Langkah 1: Buka Halaman My Equipments

1. Masuk ke menu **Equipments → My Equipments**.
2. Klik tombol **+ Register My Unit** (pojok kanan atas).

### Langkah 2: Isi Data

Form registrasi punya beberapa bagian:

#### A. Data Utama

| Field | Keterangan | Wajib? |
|---|---|---|
| **Customer** | Pilih customer pemilik unit | ✅ Ya |
| **Item** | Pilih item dari katalog (Equipment) | ✅ Ya |
| **Unit Name** | Nama unit (contoh: `Crane #01`) | ✅ Ya |
| **Unit Code** | Kode unik unit (contoh: `CR-001`) | ✅ Ya |
| **Serial Number** | Nomor seri dari pabrik | Opsional |
| **Location** | Lokasi unit berada | Opsional |
| **Capacity** | Kapasitas (contoh: `50 ton`) | Opsional |
| **Brand** | Merek (contoh: `Toyota`) | Opsional |
| **Model/Type** | Tipe model (contoh: `2024-A`) | Opsional |
| **Procurement Year** | Tahun pengadaan | Opsional |
| **Manufacture Year** | Tahun pembuatan | Opsional |
| **Country of Origin** | Negara asal | Opsional |
| **Manufacturer** | Nama pabrikan | Opsional |
| **Cert Expired Date** | Tanggal expired sertifikat kalibrasi/ujian | Opsional |
| **Status** | Status unit (Draft / Active / Inactive / Expired) | ✅ Ya |
| **Notes** | Catatan tambahan | Opsional |

#### B. Item Details (Tabel)

Di bawah form utama ada tabel **Item Details** untuk menambahkan sub-item:

| Field | Keterangan |
|---|---|
| **Item** | Nama item dari katalog |
| **Unit Code** | Kode unit sub-item |
| **Serial Number** | Nomor seri sub-item |
| **Location** | Lokasi sub-item |
| **Cert Expired** | Tanggal expired sertifikat |

Klik **+ Add** untuk menambah baris. Klik **×** untuk hapus baris.

### Langkah 3: Simpan

Klik **Save** di bawah form. Setelah sukses:

- Muncul notifikasi "Equipment unit added successfully".
- Halaman kembali ke daftar My Equipments.
- Panel kanan otomatis terbuka menampilkan unit yang baru dibuat.

## Melihat Detail Unit

1. Di halaman **My Equipments**, klik nama unit di tabel.
2. Panel kanan terbuka menampilkan detail lengkap.
3. Tab yang tersedia:

| Tab | Isi |
|---|---|
| **Equipment** | Detail unit (kode, seri, lokasi, kapasitas, brand, sertifikat, dll) |
| **Jobs** | Daftar pekerjaan yang terkait unit ini |
| **History** | Riwayat RFQ, quotation, order, inspection terkait |
| **Activity** | Log aktivitas (siapa melakukan apa, kapan) |
| **Reminders** | Pengingat (reminder) untuk unit ini |
| **Notes** | Catatan yang bisa ditambah oleh staff |
| **Emails Tracking** | Riwayat email yang dikirim terkait unit ini |
| **Views** | Siapa saja yang sudah lihat unit ini |

## Edit Unit

1. Buka detail unit (klik nama di tabel).
2. Klik tombol **Edit** (ikon pensil) di panel kanan.
3. Ubah data yang diperlukan.
4. Klik **Save**.

## Hapus Unit

1. Buka detail unit.
2. Klik tombol **More** (dropdown) di panel kanan.
3. Pilih **Delete**.
4. Konfirmasi penghapusan.

> ⚠️ Unit yang sudah terhubung dengan invoice tidak bisa dihapus.

## Export PDF

1. Buka detail unit.
2. Klik tombol **PDF** (dropdown) di panel kanan.
3. Pilih:
   - **View PDF** — buka di tab yang sama
   - **View PDF in New Window** — buka di tab baru
   - **Download** — unduh file PDF
   - **Print** — cetak langsung

## Kirim ke Customer

1. Buka detail unit.
2. Klik tombol **Email** (ikon amplop) di panel kanan.
3. Customer akan dapat link untuk melihat unit ini.

## Status Unit

| Status | Arti |
|---|---|
| **Draft** | Unit baru dibuat, belum aktif |
| **Active** | Unit aktif dan sertifikat masih berlaku |
| **Inactive** | Unit tidak aktif / sertifikat akan expired (< 30 hari) |
| **Expired** | Sertifikat sudah expired |

> Status **Inactive** dan **Expired** otomatis dihitung dari tanggal **Cert Expired Date**.

## Tips

- **Quick Stats** — di halaman My Equipments ada tombol statistik di atas tabel. Klik untuk filter cepat per status.
- **Bulk Create RFQ** — centang beberapa unit, lalu klik **Create RFQ** untuk buat permintaan harga sekaligus. (Hanya tersedia bagi yang memiliki akses ke module RFQ.)
- **Reload halaman** — panel kanan tetap terbuka di unit yang sama setelah reload (F5).

---

**Last updated:** 2026-06-21
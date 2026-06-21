# Getting Started — Equipment Module

## Apa ini?

Module **Equipments** mengatur katalog peralatan di Perfex CRM. Kamu bisa mengelompokkan peralatan berdasarkan **Group → Subgroup → Category**, lalu menambahkan item peralatan di dalamnya.

## Struktur Data

```
Group (Grup)
 └── Subgroup (Sub-grup)
      └── Category (Kategori)
           └── Equipment (Peralatan)
```

**Contoh nyata:**

```
Group:     Lab & Testing
Subgroup:  Microscopes
Category:  Digital Microscope
Equipment: Olympus CX23
```

## Langkah Pertama

### 1. Buat Group

1. Masuk ke menu **Equipments → Groups**.
2. Klik tombol **+ New Group**.
3. Isi kolom:
   - **Code** — kode singkat (contoh: `LAB`)
   - **Name** — nama grup (contoh: `Lab & Testing`)
4. Klik **Save**.

### 2. Buat Subgroup

1. Masuk ke menu **Equipments → Subgroups**.
2. Klik **+ New Subgroup**.
3. Isi kolom:
   - **Group** — pilih grup yang sudah dibuat
   - **Code** — kode singkat (contoh: `MIC`)
   - **Name** — nama subgroup (contoh: `Microscopes`)
4. Klik **Save**.

### 3. Buat Category

1. Masuk ke menu **Equipments → Categories**.
2. Klik **+ New Category**.
3. Isi kolom:
   - **Group** — pilih grup
   - **Subgroup** — pilih subgroup (sesuai grup)
   - **Name** — nama kategori (contoh: `Digital Microscope`)
4. Klik **Save**.

### 4. Tambah Equipment

1. Masuk ke menu **Equipments** (halaman utama).
2. Klik **+ New Equipment**.
3. Isi kolom:
   - **Category** — pilih kategori
   - **Description** — nama peralatan (contoh: `Olympus CX23`)
4. Klik **Save**.

## Tips

- **Quick Stats** — di halaman Subgroups dan Categories ada tombol statistik di atas tabel. Klik untuk filter cepat per grup.
- **Panel kanan** — klik baris di tabel untuk melihat detail di panel kanan tanpa pindah halaman.
- **Edit cepat** — klik link **Edit** di bawah nama item untuk langsung edit.

---

**Last updated:** 2026-06-21
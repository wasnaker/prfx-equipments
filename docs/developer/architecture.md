# Architecture — Equipment Module

## Overview

Module ini mengikuti pattern Perfex CRM standar:

- **Controllers** — `modules/equipments/controllers/`
- **Models** — `modules/equipments/models/`
- **Views** — `modules/equipments/views/admin/`
- **Assets (JS)** — `modules/equipments/assets/js/`

## Database

| Table | Purpose |
|---|---|
| `tblitems_groups` | Grup peralatan |
| `tblitems_subgroups` | Sub-grup (child of group) |
| `tblitems_categories` | Kategori (child of subgroup) |
| `tblitems` | Master item / peralatan (category_id, subgroup_id, group_id) |

## Key Patterns

### MVC Rules

Lihat `CLAUDE.md` di root module untuk 8 aturan MVC yang wajib diikuti.

### DataTable

Setiap entity punya 2 file:

1. `views/admin/tables/{entity}.php` — query + render data
2. `views/admin/{entity}/table_html.php` — heading kolom

Keduanya harus selalu sinkron. Kalau hapus kolom di `tables/{entity}.php`, hapus juga headernya di `{entity}/table_html.php`.

### Small Table (Panel Kanan)

Halaman manage pakai pola "small table" — klik baris di tabel kiri, detail muncul di panel kanan via hash URL (`#id`).

Aturan:
- `init_*()` dipanggil **unconditional** (tanpa guard `if (!id)`)
- `onclick` pakai `do_hash_helper(id); return false;`
- `href` pakai full URL segment untuk right-click open-in-new-tab

Lihat `LOAD_SMALL_TABLE_ITEM.md` di audit module untuk detail.

### Permissions (CRUD)

Setiap controller memanggil `staff_cant()` atau `staff_can()` sebelum action. Semua permission pakai module key `'equipments'`:

| Aksi | Permission | Di mana dipanggil |
|---|---|---|
| Lihat data | `view` atau `view_own` | Constructor, `list_*()`, `get_*_data_ajax()` |
| Buat data baru | `create` | `save()` — cek sebelum insert |
| Ubah data | `edit` | `save()` — cek sebelum update |
| Hapus data | `delete` | `delete()` — cek sebelum query delete |

**Aturan penting:**

- `view_own` diperbolehkan jika staff hanya boleh lihat data miliknya sendiri.
- Di halaman **Manage**, tombol **+ New**, **Edit**, dan **Delete** hanya muncul kalau staff punya hak akses yang sesuai.
- Kalau tidak punya hak akses → tombol tidak tampil (tidak perlu check di view manual, sudah dihandle di controller).

```php
// Contoh pattern di controller
if (staff_cant('create', 'equipments')) {
    access_denied('equipments');
}

if (staff_cant('edit', 'equipments')) {
    access_denied('equipments');
}

if (staff_cant('delete', 'equipments')) {
    access_denied('equipments');
}
```

### DataTable Row Actions

Row action (Edit / Delete) hanya tampilkan kalau staff punya permission:

```php
// Di view
if (staff_can('edit', 'equipments')) {
    echo '<a href="...">Edit</a>';
}
if (staff_can('delete', 'equipments')) {
    echo '<a href="..." class="_delete">Delete</a>';
}
```

---

**Last updated:** 2026-06-21
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

---

**Last updated:** 2026-06-21
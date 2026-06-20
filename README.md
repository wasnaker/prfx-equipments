# Equipments Module

Equipment catalog management — hierarchical master data (categories, subgroups, items) used across all transaction documents (RFQs, quotations, orders, inspections, etc.).

## Category
Master Data

## Key Features

- **Equipment CRUD** — Hierarchical items: Category → Subgroup → Item (code, rate, unit, status)
- **Statuses** — draft, active, inactive, expired
- **Categories & Subgroups** — Separate admin views for managing categories and subgroups
- **My Equipments** — Entity-scoped view (customer/surveyor) with `Myequipments` controller
- **Equipment as Document** — Send equipment list to customer (email + PDF) like a quotation
- **Activity Log** — `equipment_activity` tracks all CRUD operations
- **Job Integration** — Injects equipment data into job views via `equipments_inject_job_equipment_data` hook
- **Members Integration** — `equipment_members` table
- **Global Search** — Searchable via `equipments_global_search_helper`
- **Dashboard Widget** — `equipments_add_dashboard_widget` hook
- **Memberships** — Pro-plan feature gating via `equipments_memberships_helper`
- **DataTable** — Actor-aware column visibility (`datatable_helper`)
- **Relation Support** — Registered as relation type for cross-module linking
- **Mail Templates** — Equipment_send_to_customer, accepted/declined to staff, expiration reminder

## Controllers

| Controller | Route | Description |
|---|---|---|
| Equipments | `/admin/equipments/` | Admin equipment catalog CRUD |
| Myequipments | `/admin/myequipments/` | Entity-scoped equipment view |
| Myequipments_api | REST API | Equipment API for mobile |
| Categories | `/admin/categories/` | Category management |
| Groups | `/admin/groups/` | Group management |
| Subgroups | `/admin/subgroups/` | Subgroup management |
| Email_equipment_equipments | Cron | Email notifications |

## Helpers (8)

- `equipments_helper` — Module utilities (PDF, formatting)
- `equipments_menu_helper` — Sidebar menu
- `equipments_capability_helpers` — Permission checks
- `equipments_email_templates_helper` — Email registration
- `equipments_global_search_helper` — Global search
- `equipments_memberships_helper` — Pro-plan gating
- `equipment_relation_helpers` — Relation hooks
- `datatable_helper` — DataTable column logic

## Models

- `Equipments_model` — Equipment CRUD via `invoice_items_model` (reuses Perfex items)

## Database Tables

- `tblitems` (core Perfex, extended) — Equipment items: code, subgroup_id, status, category_id
- `tblitems_categories` — Category master data
- `tblitems_subgroups` — Subgroup master data
- `tblitems_groups` — Group master data
- `tblitems_group_values` — Group value options
- `tblitems_data` — Item data
- `tblitem_custom_fields` — Custom fields per item
- `tblitem_custom_fields_values` — Custom field values
- `tblitem_equipment` — Equipment-to-document linking
- `tblequipment_activity` — Activity log
- `tblequipment_jobs` — Equipment-job linking
- `tblequipment_members` — Equipment-member linking

## Key Design Notes

- **Reuses Perfex items** — `Equipments_model` delegates to `invoice_items_model` for CRUD, adding equipment-specific metadata
- **Shared across transactions** — Equipment catalog used by platforms, rfqs, quotations, orders, inspections, jobreports, billings
- **Hierarchical** — Categories → Subgroups → Items, with groups and group_values for flexible attributes
- **Document-style send** — Equipment lists can be sent to customers like proposals (with email + PDF)
- **Seeder support** — CategoriesSeeder, EquipmentsSeeder, SubgroupsSeeder for demo data

## Dependencies

- Core: `invoice_items_model`, `clients_model`
- `apps` module (App_table, Api_base)
- `memberships` module (Pro-plan gating)

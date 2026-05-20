.
├── assets
│   ├── css
│   │   ├── equipments.css
│   │   └── index.html
│   ├── index.html
│   └── js
│       ├── equipments.js
│       └── index.html
├── config
│   ├── index.html
│   └── routes.php
├── controllers
│   ├── Categories.php
│   ├── Email_equipment_equipments.php
│   ├── Equipments.php
│   ├── Groups.php
│   ├── index.html
│   ├── Myequipments.php
│   └── Subgroups.php
├── database
│   └── seeds
│       ├── BaseSeeder.php
│       ├── CategoriesSeeder.php
│       ├── EquipmentsSeeder.php
│       ├── index.html
│       └── SubgroupsSeeder.php
├── equipments.md
├── equipments.php
├── equipmwnts.md
├── helpers
│   ├── equipments_helper.php
│   └── index.html
├── index.html
├── install
│   ├── equipment_activity.php
│   ├── equipment_equipment.php
│   ├── equipment_items.php
│   ├── equipment_jobs.php
│   ├── equipment_members.php
│   ├── equipments.php
│   ├── index.html
│   └── items_categories.php
├── install.php
├── language
│   ├── english
│   │   ├── equipments_lang.php
│   │   └── index.html
│   ├── index.html
│   └── indonesia
│       ├── equipments_lang.php
│       └── index.html
├── libraries
│   ├── gdpr
│   │   ├── Gdpr_equipments.php
│   │   └── index.html
│   ├── index.html
│   ├── mails
│   │   ├── Equipment_accepted_to_customer.php
│   │   ├── Equipment_accepted_to_staff.php
│   │   ├── Equipment_declined_to_staff.php
│   │   ├── Equipment_expiration_reminder.php
│   │   ├── Equipment_send_to_customer_already_sent.php
│   │   ├── Equipment_send_to_customer.php
│   │   └── index.html
│   ├── merge_fields
│   │   ├── Equipment_client_merge_fields.php
│   │   ├── Equipment_merge_fields.php
│   │   └── index.html
│   └── pdf
│       ├── Equipment_pdf.php
│       └── index.html
├── models
│   ├── Categories_model.php
│   ├── Equipments_model.php
│   ├── Groups_model.php
│   ├── index.html
│   ├── Myequipments_model.php
│   └── Subgroups_model.php
├── services
│   ├── equipments
│   │   ├── EquipmentsPipeline.php
│   │   └── index.html
│   └── index.html
└── views
    ├── admin
    │   ├── categories
    │   │   ├── categories_top_stats.php
    │   │   ├── category.php
    │   │   ├── category_preview_template.php
    │   │   ├── category_template.php
    │   │   ├── index.html
    │   │   ├── list_template.php
    │   │   ├── manage.php
    │   │   ├── quick_stats.php
    │   │   └── table_html.php
    │   ├── clients
    │   │   ├── groups
    │   │   │   ├── equipments.php
    │   │   │   └── index.html
    │   │   ├── index.html
    │   │   └── modals
    │   │       ├── index.html
    │   │       └── zip_equipments.php
    │   ├── emails
    │   │   └── equipment_email_templates.php
    │   ├── equipments
    │   │   ├── _add_edit_equipment.php
    │   │   ├── _add_edit_items.php
    │   │   ├── _add_edit_jobs.php
    │   │   ├── billing_and_shipping_template.php
    │   │   ├── equipment.php
    │   │   ├── equipment_preview_template.php
    │   │   ├── equipments_report.php
    │   │   ├── equipments_top_stats.php
    │   │   ├── equipments_total_template.php
    │   │   ├── equipment_template.php
    │   │   ├── index.html
    │   │   ├── list_template.php
    │   │   ├── manage.php
    │   │   ├── pipeline
    │   │   │   ├── equipment.php
    │   │   │   ├── index.html
    │   │   │   ├── _kanban_card.php
    │   │   │   ├── manage.php
    │   │   │   └── pipeline.php
    │   │   ├── proposal_convert_template.php
    │   │   ├── quick_stats.php
    │   │   └── table_html.php
    │   ├── groups
    │   │   ├── group.php
    │   │   ├── group_preview_template.php
    │   │   ├── groups_top_stats.php
    │   │   ├── group_template.php
    │   │   ├── index.html
    │   │   ├── list_template.php
    │   │   ├── manage.php
    │   │   ├── quick_stats.php
    │   │   └── table_html.php
    │   ├── index.html
    │   ├── myequipments
    │   │   ├── _add_edit_myequipment.php
    │   │   ├── index.html
    │   │   ├── list_template.php
    │   │   ├── manage.php
    │   │   ├── myequipment.php
    │   │   ├── myequipment_preview_template.php
    │   │   ├── myequipments_top_stats.php
    │   │   ├── myequipment_template.php
    │   │   ├── quick_stats.php
    │   │   └── table_html.php
    │   ├── projects
    │   │   ├── index.html
    │   │   └── project_equipments.php
    │   ├── reports
    │   │   ├── includes
    │   │   │   ├── index.html
    │   │   │   └── sales_equipments.php
    │   │   └── index.html
    │   ├── settings
    │   │   ├── includes
    │   │   │   ├── equipments.php
    │   │   │   ├── equipments--schedule.php
    │   │   │   └── index.html
    │   │   └── index.html
    │   ├── subgroups
    │   │   ├── index.html
    │   │   ├── list_template.php
    │   │   ├── manage.php
    │   │   ├── quick_stats.php
    │   │   ├── subgroup.php
    │   │   ├── subgroup_preview_template.php
    │   │   ├── subgroups_top_stats.php
    │   │   ├── subgroup_template.php
    │   │   └── table_html.php
    │   └── tables
    │       ├── categories.php
    │       ├── equipments.php
    │       ├── equipments_widget.php
    │       ├── groups.php
    │       ├── index.html
    │       ├── myequipments.php
    │       └── subgroups.php
    ├── forms
    │   └── index.html
    ├── index.html
    ├── themes
    │   ├── index.html
    │   └── perfex
    │       ├── index.html
    │       ├── template_parts
    │       │   ├── equipments_stats.php
    │       │   ├── index.html
    │       │   └── projects
    │       │       └── index.html
    │       └── views
    │           ├── equipmenthtml.php
    │           ├── equipmentpdf.php
    │           └── index.html
    └── widget.php

44 directories, 155 files

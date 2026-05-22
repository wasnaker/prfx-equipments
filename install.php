<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/install/equipments.php');
require_once(__DIR__ . '/install/items_categories.php');

// Seed K3 master data — runs on every install, idempotent
require_once(__DIR__ . '/database/seeds/BaseSeeder.php');
require_once(__DIR__ . '/database/seeds/SubgroupsSeeder.php');
(new SubgroupsSeeder($CI->db))->run();
require_once(__DIR__ . '/database/seeds/CategoriesSeeder.php');
(new CategoriesSeeder($CI->db))->run();
require_once(__DIR__ . '/database/seeds/EquipmentsSeeder.php');
(new EquipmentsSeeder($CI->db))->run();
require_once(__DIR__ . '/install/equipment_equipment.php');
require_once(__DIR__ . '/install/equipment_members.php');
require_once(__DIR__ . '/install/equipment_jobs.php');
require_once(__DIR__ . '/install/equipment_items.php');
require_once(__DIR__ . '/install/equipment_activity.php');

// ─── Email Templates ──────────────────────────────────────────────────────────

require_once __DIR__ . '/helpers/email_templates_helper.php';
equipments_register_email_templates();

// Add module options
add_option('equipment_prefix', 'SCH-');
add_option('next_equipment_number', 1);
add_option('default_equipment_assigned', 0);
add_option('equipment_number_format', 1);
add_option('equipment_year', date('Y'));
add_option('delete_only_on_last_equipment', 0);
add_option('equipment_number_decrement_on_delete', 0);
add_option('exclude_equipment_from_client_area_with_draft_status', 1);
add_option('equipment_due_after', 7);
add_option('allow_staff_view_equipments_assigned', 1);
add_option('show_assigned_on_equipment', 1);
add_option('require_client_logged_in_to_view_equipment', 0);
add_option('show_project_on_equipment', 1);
add_option('equipments_pipeline_limit', 1);
add_option('default_equipments_pipeline_sort', 1);
add_option('default_equipments_pipeline_sort_type', 'asc');
add_option('equipment_accept_identity_confirmation', 1);
add_option('equipment_qrcode_size', '160');
add_option('equipment_send_telegram_message', 0);
add_option('show_pdf_signature_equipment', 0);

// Add equipment_emails column to contacts table if not exists
if (!$CI->db->field_exists('equipment_emails', db_prefix() . 'contacts')) {
    $CI->db->query('ALTER TABLE ' . db_prefix() . 'contacts ADD COLUMN `equipment_emails` tinyint(1) NOT NULL DEFAULT 1 AFTER `estimate_emails`');
}

// Add assign_to column to equipments table if not exists
if (!$CI->db->field_exists('assign_to', db_prefix() . 'equipments')) {
    $CI->db->query('ALTER TABLE ' . db_prefix() . 'equipments ADD COLUMN `assign_to` int(11) NOT NULL DEFAULT 0 AFTER `addedfrom`');
}


// Add status column to tblitems (catalog equipment status)
if (!$CI->db->field_exists('status', db_prefix() . 'items')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD COLUMN `status` TINYINT(1) NOT NULL DEFAULT 2 AFTER `addedfrom`');
}

// Add manufacture_year + status columns to tblcustomer_equipment
if ($CI->db->table_exists(db_prefix() . 'customer_equipment')) {
    if (!$CI->db->field_exists('manufacture_year', db_prefix() . 'customer_equipment')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'customer_equipment` ADD COLUMN `manufacture_year` YEAR DEFAULT NULL AFTER `procurement_year`');
    }
    if (!$CI->db->field_exists('status', db_prefix() . 'customer_equipment')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'customer_equipment` ADD COLUMN `status` TINYINT(1) NOT NULL DEFAULT ' . EQUIPMENT_STATUS_ACTIVE . ' AFTER `cert_expired_date`');
    }
}

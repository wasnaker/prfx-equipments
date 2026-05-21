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

$CI->db->query("
INSERT IGNORE INTO `" . db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES
('equipments', 'equipment-send-to-client', 'english', 'Send Equipment to Customer', 'Equipment #{equipment_number}', '<p>Dear {contact_firstname} {contact_lastname},</p><p>Please find the attached equipment <strong>#{equipment_number}</strong>.</p><p><strong>Equipment status:</strong> {equipment_status}</p><p>You can view the equipment here: <a href=\"{equipment_link}\">{equipment_number}</a></p><p>We look forward to your response.</p><p>Kind Regards,</p><p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 1),
('equipments', 'equipment-already-send', 'english', 'Equipment Already Sent to Customer', 'Equipment #{equipment_number}', '<p>Dear {contact_firstname} {contact_lastname},</p><p>Thank you for your equipment request.</p><p>You can view the equipment here: <a href=\"{equipment_link}\">{equipment_number}</a></p><p>Please contact us for more information.</p><p>Kind Regards,</p><p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 2),
('equipments', 'equipment-declined-to-staff', 'english', 'Equipment Declined (Sent to Staff)', 'Customer Declined Equipment', '<p>Hi,</p><p>Customer ({client_company}) declined equipment with number <strong>#{equipment_number}</strong>.</p><p>You can view the equipment here: <a href=\"{equipment_link}\">{equipment_number}</a></p><p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 3),
('equipments', 'equipment-accepted-to-staff', 'english', 'Equipment Accepted (Sent to Staff)', 'Customer Accepted Equipment', '<p>Hi,</p><p>Customer ({client_company}) accepted equipment with number <strong>#{equipment_number}</strong>.</p><p>You can view the equipment here: <a href=\"{equipment_link}\">{equipment_number}</a></p><p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 4),
('equipments', 'equipment-thank-you-to-customer', 'english', 'Thank You Email (Sent to Customer After Accept)', 'Thank you for accepting equipment', '<p>Dear {contact_firstname} {contact_lastname},</p><p>Thank you for accepting the equipment.</p><p>We look forward to doing business with you. We will contact you as soon as possible.</p><p>Kind Regards,</p><p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 5),
('equipments', 'equipment-expiry-reminder', 'english', 'Equipment Expiration Reminder', 'Equipment Expiration Reminder', '<p>Hello {contact_firstname} {contact_lastname},</p><p>The equipment <strong>#{equipment_number}</strong> will expire on <strong>{equipment_expirydate}</strong>.</p><p>You can view the equipment here: <a href=\"{equipment_link}\">{equipment_number}</a></p><p>Kind Regards,</p><p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 6),
('equipments', 'equipment-expiry-reminder', 'english', 'Equipment Expiration Reminder', 'Equipment Expiration Reminder', '<p>Hello {contact_firstname} {contact_lastname},</p><p>The equipment <strong>#{equipment_number}</strong> will expire on <strong>{equipment_expirydate}</strong>.</p><p>You can view the equipment here: <a href=\"{equipment_link}\">{equipment_number}</a></p><p>Kind Regards,</p><p>{email_signature}</p>', '{companyname} | CRM', '', 0, 1, 6)
");

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

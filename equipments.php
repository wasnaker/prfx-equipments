<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Equipments
Description: Full-featured Equipments module for Perfex CRM — independent of core Estimates
Version: 1.0.0
Requires at least: 2.3.*
*/

define('EQUIPMENTS_MODULE_NAME', 'equipments');
define('EQUIPMENTS_ATTACHMENTS_FOLDER', FCPATH . 'uploads/equipments/');

// Pre-load equipment mail template classes from module path.
// App_mail_template::createReflectionMailClass() resolves paths without a module hint,
// falling back to APPPATH/libraries/mails/ and emitting a warning for missing files.
// Pre-loading ensures the class is already defined before that include_once runs,
// so ReflectionClass succeeds and no exception is thrown.
// The spl_autoload_register is a secondary safety net for any late-bound calls.
$_equipment_mail_classes = [
    'Equipment_send_to_customer',
    'Equipment_send_to_customer_already_sent',
    'Equipment_accepted_to_customer',
    'Equipment_accepted_to_staff',
    'Equipment_declined_to_staff',
    'Equipment_expiration_reminder',
];
foreach ($_equipment_mail_classes as $_qmc) {
    $_qmc_path = FCPATH . 'modules/' . EQUIPMENTS_MODULE_NAME . '/libraries/mails/' . $_qmc . '.php';
    if (file_exists($_qmc_path) && !class_exists(strtolower($_qmc), false)) {
        include_once($_qmc_path);
    }
}
unset($_equipment_mail_classes, $_qmc, $_qmc_path);

spl_autoload_register(function ($class) {
    $mail_path = FCPATH . 'modules/' . EQUIPMENTS_MODULE_NAME . '/libraries/mails/' . ucfirst($class) . '.php';
    if (file_exists($mail_path)) {
        include_once($mail_path);
    }
});

// SMS trigger constants
define('SMS_TRIGGER_EQUIPMENT_EXP_REMINDER', 'equipment_expiration_reminder');

// Status constants
define('EQUIPMENT_STATUS_DRAFT',    1);
define('EQUIPMENT_STATUS_ACTIVE',   2);
define('EQUIPMENT_STATUS_INACTIVE', 3);
define('EQUIPMENT_STATUS_EXPIRED',  5);

// ─── Hooks ───────────────────────────────────────────────────────────────────

hooks()->add_action('admin_init',          'equipments_settings_tab');
hooks()->add_action('admin_init',          'equipments_register_app_table');
hooks()->add_action('after_cron_run',        'equipments_notification');
hooks()->add_action('staff_member_deleted',  'equipments_staff_member_deleted');
hooks()->add_action('after_equipment_added',  'equipments_sync_project_on_save');
hooks()->add_action('after_equipment_updated','equipments_sync_project_on_save');
hooks()->add_action('after_equipment_deleted','equipments_clear_project_on_delete');

hooks()->add_filter('get_task', 'equipments_inject_task_equipment_data');

hooks()->add_filter('migration_tables_to_replace_old_links',    'equipments_migration_tables_to_replace_old_links');
hooks()->add_filter('other_merge_fields_available_for',          'equipments_other_merge_fields_available_for');
hooks()->add_filter('get_dashboard_widgets',        'equipments_add_dashboard_widget');
hooks()->add_filter('module_equipments_action_links', 'module_equipments_action_links');
require_once __DIR__ . '/helpers/datatable_helper.php';
// ─── Menu Helpers ──────────────────────────────────────────────────────────────

require_once(__DIR__ . '/helpers/equipments_menu_helper.php');

// ─── Activation / Deactivation ───────────────────────────────────────────────

register_activation_hook(EQUIPMENTS_MODULE_NAME, 'equipments_module_activation_hook');

function equipments_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
    // Create uploads directory
    if (!file_exists(EQUIPMENTS_ATTACHMENTS_FOLDER)) {
        mkdir(EQUIPMENTS_ATTACHMENTS_FOLDER, 0755, true);
    }
    log_activity('Equipments module activated');
}

register_deactivation_hook(EQUIPMENTS_MODULE_NAME, 'equipments_module_deactivation_hook');

function equipments_module_deactivation_hook()
{
    $CI = &get_instance();
    $CI->db->query('DROP TABLE IF EXISTS ' . db_prefix() . 'equipment_activity');
    $CI->db->query('DROP TABLE IF EXISTS ' . db_prefix() . 'equipment_items');
    $CI->db->query('DROP TABLE IF EXISTS ' . db_prefix() . 'equipment_tasks');
    $CI->db->query('DROP TABLE IF EXISTS ' . db_prefix() . 'equipment_members');
    $CI->db->query('DROP TABLE IF EXISTS ' . db_prefix() . 'equipment_equipment');
    $CI->db->query('DROP TABLE IF EXISTS ' . db_prefix() . 'equipments');

    // Remove options
    $CI->db->where('name LIKE', 'equipment%')->delete(db_prefix() . 'options');
    $CI->db->where('type', 'equipments')->delete(db_prefix() . 'emailtemplates');

    log_activity('Equipments module deactivated - tables dropped');
}

// ─── Language ─────────────────────────────────────────────────────────────────

register_language_files(EQUIPMENTS_MODULE_NAME, [EQUIPMENTS_MODULE_NAME]);

// ─── Email Templates Helper ─────────────────────────────────────────────────

require_once(__DIR__ . '/helpers/equipments_email_templates_helper.php');

// ─── Relation Helpers ──────────────────────────────────────────────────────────

require_once(__DIR__ . '/helpers/equipment_relation_helpers.php');

// ─── Capability Helpers ────────────────────────────────────────────────────────

require_once(__DIR__ . '/helpers/equipments_capability_helpers.php');

// ─── Global Search Helper ─────────────────────────────────────────────────────

require_once(__DIR__ . '/helpers/equipments_global_search_helper.php');

require_once(__DIR__ . '/helpers/equipments_memberships_helper.php');

// ─── Menu ─────────────────────────────────────────────────────────────────────

// ─── Permissions ──────────────────────────────────────────────────────────────


/**
 * Ensure role-based equipment permissions are seeded in tbl_options.
 * Surveyor role gets view/create/edit by default (read from DB on first setup).
 */

// ─── Settings Tab ─────────────────────────────────────────────────────────────

function equipments_settings_tab()
{
    $CI = &get_instance();
    $CI->app->add_settings_section_child('finance', 'equipments', [
        'name'     => _l('equipments'),
        'view'     => 'equipments/admin/settings/includes/equipments',
        'position' => 52,
        'icon'     => 'fa-solid fa-file-invoice',
    ]);
}

// ─── App_table Registration ───────────────────────────────────────────────────

function equipments_register_app_table()
{
    $tablePath = FCPATH . 'modules/' . EQUIPMENTS_MODULE_NAME . '/views/admin/tables/equipments';

    $equipmentsTable = App_table::new('equipments', $tablePath)->customfieldable('equipment');
    App_table::register($equipmentsTable);

    App_table::register(
        App_table::new('project_equipments', $tablePath)
            ->relatedTo($equipmentsTable->id())
            ->setRules($equipmentsTable->rules())
    );

    App_table::register(
        App_table::new('equipments_widget', FCPATH . 'modules/' . EQUIPMENTS_MODULE_NAME . '/views/admin/tables/equipments_widget')
    );

    App_table::register(
        App_table::new('groups', FCPATH . 'modules/' . EQUIPMENTS_MODULE_NAME . '/views/admin/tables/groups')
    );

    App_table::register(
        App_table::new('subgroups', FCPATH . 'modules/' . EQUIPMENTS_MODULE_NAME . '/views/admin/tables/subgroups')
    );

    App_table::register(
        App_table::new('categories', FCPATH . 'modules/' . EQUIPMENTS_MODULE_NAME . '/views/admin/tables/categories')
    );

    App_table::register(
        App_table::new('myequipments', FCPATH . 'modules/' . EQUIPMENTS_MODULE_NAME . '/views/admin/tables/myequipments')
    );
}

// ─── Dashboard Widget ─────────────────────────────────────────────────────────

function equipments_add_dashboard_widget($widgets)
{
    $widgets[] = [
        'path'      => 'equipments/widget',
        'container' => 'left-8',
    ];
    return $widgets;
}

// ─── Staff Member Deleted ─────────────────────────────────────────────────────

function equipments_staff_member_deleted($data)
{
    $CI = &get_instance();
    $CI->db->where('sale_agent', $data['id']);
    $CI->db->update(db_prefix() . 'equipments', ['sale_agent' => $data['transfer_data_to']]);
}

// ─── Project Sync ─────────────────────────────────────────────────────────────

function equipments_sync_project_on_save($equipment_id)
{
    $CI = &get_instance();
    $equipment = $CI->db->select('project_id, date')
        ->get_where(db_prefix() . 'equipments', ['id' => (int) $equipment_id])
        ->row();

    if (!$equipment || !$equipment->project_id) {
        return;
    }

    $CI->db->where('id', (int) $equipment->project_id);
    $CI->db->update(db_prefix() . 'projects', [
        'equipmentid'     => (int) $equipment_id,
        'equipmentd_date' => $equipment->date,
    ]);
}

function equipments_clear_project_on_delete($equipment_id)
{
    $CI = &get_instance();
    $CI->db->where('equipmentid', (int) $equipment_id);
    $CI->db->update(db_prefix() . 'projects', [
        'equipmentid'     => null,
        'equipmentd_date' => null,
    ]);
}

// ─── Migration ────────────────────────────────────────────────────────────────

function equipments_migration_tables_to_replace_old_links($tables)
{
    $tables[] = [
        'table' => db_prefix() . 'equipments',
        'field' => 'clientnote',
    ];
    $tables[] = [
        'table' => db_prefix() . 'equipments',
        'field' => 'adminnote',
    ];
    return $tables;
}

// ─── Action Links ─────────────────────────────────────────────────────────────

function module_equipments_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('settings?group=equipments') . '">' . _l('settings') . '</a>';
    return $actions;
}

// ─── Merge Fields ─────────────────────────────────────────────────────────────

function equipments_other_merge_fields_available_for($available_for)
{
    $available_for[] = 'equipments';
    return $available_for;
}

// ─── Email Templates Section ──────────────────────────────────────────────────

// ─── Cron Notification ────────────────────────────────────────────────────────

function equipments_notification()
{
    $CI = &get_instance();
    $CI->load->model('equipments/Equipments_model', 'equipments_model');

    // Send expiry reminders
    if (method_exists($CI->equipments_model, 'send_expiry_reminder')) {
        $CI->equipments_model->send_expiry_reminder();
    }

    // Auto-expire overdue equipments
    $CI->db->where('expirydate <', date('Y-m-d'));
    $CI->db->where('status', EQUIPMENT_STATUS_ACTIVE);
    $CI->db->update(db_prefix() . 'equipments', ['status' => EQUIPMENT_STATUS_EXPIRED]);
}

// ─── Load Helper & Assets ─────────────────────────────────────────────────────

$CI = &get_instance();
$CI->load->helper(EQUIPMENTS_MODULE_NAME . '/equipments');

// Register merge fields directly (filter approach is too late — App_merge_fields
// is constructed before modules load; ->register() adds to the existing instance).
register_merge_fields([
    'equipments/merge_fields/equipment_merge_fields',
    'equipments/merge_fields/equipment_client_merge_fields',
]);

$current_url = $CI->uri->segment(1) . '/' . $CI->uri->segment(2);
if (
    ($CI->uri->segment(1) == 'admin' && $CI->uri->segment(2) == 'equipments') ||
    $CI->uri->segment(1) == 'equipments'
) {
    $CI->app_css->add(
        EQUIPMENTS_MODULE_NAME . '-css',
        base_url('modules/' . EQUIPMENTS_MODULE_NAME . '/assets/css/equipments.css')
    );
    $CI->app_scripts->add(
        EQUIPMENTS_MODULE_NAME . '-js',
        base_url('modules/' . EQUIPMENTS_MODULE_NAME . '/assets/js/equipments.js')
    );
}

function equipments_inject_task_equipment_data($task)
{
    if (!$task) { return $task; }
    $CI  = &get_instance();
    $pfx = db_prefix();

    $equipment = $CI->db
        ->select('s.id, s.formatted_number, s.date, s.expirydate, s.status,
                  s.assign_to, s.reference_no,
                  CONCAT(st.firstname," ",st.lastname) as assign_to_name')
        ->from($pfx . 'equipment_items si')
        ->join($pfx . 'equipments s', 's.id = si.equipment_id')
        ->join($pfx . 'staff st', 'st.staffid = s.assign_to', 'left')
        ->where('si.task_id', (int)$task->id)
        ->order_by('s.date', 'DESC')
        ->limit(1)
        ->get()->row();

    if ($equipment) {
        $equipment->members = $CI->db
            ->select('sm.staff_id, CONCAT(st.firstname," ",st.lastname) as full_name')
            ->from($pfx . 'equipment_members sm')
            ->join($pfx . 'staff st', 'st.staffid = sm.staff_id', 'left')
            ->where('sm.equipment_id', (int)$equipment->id)
            ->get()->result_array();
    }

    $task->equipment = $equipment;

    return $task;
}

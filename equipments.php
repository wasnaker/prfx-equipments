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

hooks()->add_action('admin_init',          'equipments_module_init_menu_items');
hooks()->add_action('after_email_templates', 'equipments_email_templates_section');
hooks()->add_action('admin_init',          'equipments_settings_tab');
hooks()->add_action('admin_init',          'equipments_register_app_table');
hooks()->add_action('after_cron_run',        'equipments_notification');
hooks()->add_action('staff_member_deleted',  'equipments_staff_member_deleted');
hooks()->add_action('after_equipment_added',  'equipments_sync_program_on_save');
hooks()->add_action('after_equipment_updated','equipments_sync_program_on_save');
hooks()->add_action('after_equipment_deleted','equipments_clear_program_on_delete');

hooks()->add_filter('get_job', 'equipments_inject_job_equipment_data');

hooks()->add_filter('migration_tables_to_replace_old_links',    'equipments_migration_tables_to_replace_old_links');
hooks()->add_filter('other_merge_fields_available_for',          'equipments_other_merge_fields_available_for');
hooks()->add_filter('global_search_result_query',  'equipments_global_search_result_query', 10, 3);
hooks()->add_filter('global_search_result_output', 'equipments_global_search_result_output', 10, 2);
hooks()->add_filter('get_dashboard_widgets',        'equipments_add_dashboard_widget');
hooks()->add_filter('module_equipments_action_links', 'module_equipments_action_links');
hooks()->add_filter('membership_plan_protections', 'equipments_membership_plan_protections');
hooks()->add_filter('membership_plan_features',    'equipments_membership_plan_features', 20, 2);
require_once __DIR__ . '/helpers/datatable_helper.php';

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
    $CI->db->query('DROP TABLE IF EXISTS ' . db_prefix() . 'equipment_jobs');
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

// ─── Relation Helpers ──────────────────────────────────────────────────────────

require_once(__DIR__ . '/helpers/equipment_relation_helpers.php');

// ─── Capability Helpers ────────────────────────────────────────────────────────

require_once(__DIR__ . '/helpers/equipments_capability_helpers.php');

// ─── Menu ─────────────────────────────────────────────────────────────────────

function equipments_module_init_menu_items()
{
    $CI = &get_instance();

    $CI->app->add_quick_actions_link([
        'name'       => _l('equipments'),
        'url'        => 'equipments',
        'permission' => 'equipments',
        'icon'       => 'fa-solid fa-file-invoice',
        'position'   => 11,
    ]);

    if (has_permission('equipments', '', 'view') || staff_can('view_own', 'equipments')) {
        $CI->app_menu->add_sidebar_children_item('wasnaker-masterdata', [
            'slug'     => 'equipments',
            'name'     => _l('equipments'),
            'href'     => admin_url('equipments'),
            'position' => 1,
        ]);

        $CI->app_menu->add_sidebar_children_item('reports', [
            'slug'     => 'equipments-report',
            'name'     => _l('equipments_report'),
            'href'     => admin_url('equipments/equipments_report'),
            'position' => 35,
        ]);
    }

    $me  = get_staff(get_staff_user_id());
    $ct  = $me ? ($me->client_type ?? '') : '';

    if (has_permission('equipments', '', 'view') || in_array($ct, ['customer', 'surveyor'])) {
        $CI->app_menu->add_sidebar_children_item('wasnaker-masterdata', [
            'slug'     => 'myequipments',
            'name'     => _l('my_equipment_units'),
            'href'     => admin_url('equipments/myequipments'),
            'position' => 2,
        ]);
    }
}

// ─── Permissions ──────────────────────────────────────────────────────────────


function equipments_membership_plan_protections(array $protections): array
{
    $protections['bulk_create_rfq'] = [
        'min_plan' => 'enterprise',
        'label'    => _l('bulk_create_rfq'),
    ];
    $protections['register_my_unit'] = [
        'min_plan' => 'pro',
        'label'    => _l('register_my_unit'),
    ];
    return $protections;
}

function equipments_membership_plan_features(array $features, string $type = 'customer'): array
{
    if ($type !== 'customer') { return $features; }

    $_plan_level = function (string $name): int {
        $h = defined('MEMBERSHIP_PLAN_HIERARCHY')
            ? MEMBERSHIP_PLAN_HIERARCHY
            : ['free' => 0, 'basic' => 1, 'pro' => 2, 'enterprise' => 3];
        return $h[strtolower($name)] ?? 0;
    };

    $features[] = [
        'key'        => 'register_my_unit',
        'label'      => _l('register_my_unit'),
        'group'      => _l('membership_group_transactions'),
        'module'     => 'equipments',
        'value_type' => 'boolean',
        'featured'   => false,
        'sort_order' => 43,
        'value_callback' => function (array $plan) use ($_plan_level) {
            return $_plan_level($plan['name'] ?? '') >= 2 ? 1 : 0; // >= pro
        },
    ];
    $features[] = [
        'key'        => 'bulk_create_rfq',
        'label'      => _l('bulk_create_rfq'),
        'group'      => _l('membership_group_transactions'),
        'module'     => 'equipments',
        'value_type' => 'boolean',
        'featured'   => false,
        'sort_order' => 44,
        'value_callback' => function (array $plan) use ($_plan_level) {
            return $_plan_level($plan['name'] ?? '') >= 3 ? 1 : 0; // >= enterprise
        },
    ];
    return $features;
}


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

// ─── Program Sync ─────────────────────────────────────────────────────────────

function equipments_sync_program_on_save($equipment_id)
{
    $CI = &get_instance();
    $equipment = $CI->db->select('program_id, date')
        ->get_where(db_prefix() . 'equipments', ['id' => (int) $equipment_id])
        ->row();

    if (!$equipment || !$equipment->program_id) {
        return;
    }

    $CI->db->where('id', (int) $equipment->program_id);
    $CI->db->update(db_prefix() . 'programs', [
        'equipmentid'     => (int) $equipment_id,
        'equipmentd_date' => $equipment->date,
    ]);
}

function equipments_clear_program_on_delete($equipment_id)
{
    $CI = &get_instance();
    $CI->db->where('equipmentid', (int) $equipment_id);
    $CI->db->update(db_prefix() . 'programs', [
        'equipmentid'     => null,
        'equipmentd_date' => null,
    ]);
}

// ─── Global Search ────────────────────────────────────────────────────────────

function equipments_global_search_result_output($output, $data)
{
    if ($data['type'] == 'equipments') {
        $output = '<a href="' . admin_url('equipments/list_equipments/' . $data['result']['id']) . '">'
            . format_equipment_number($data['result']['id']) . '</a>';
    }
    return $output;
}

function equipments_global_search_result_query($result, $q, $limit)
{
    $CI = &get_instance();
    if (has_permission('equipments', '', 'view') || staff_can('view_own', 'equipments')) {
        $CI->db->select()
            ->from(db_prefix() . 'equipments')
            ->like('formatted_number', $q)
            ->limit($limit);

        $result[] = [
            'result'         => $CI->db->get()->result_array(),
            'type'           => 'equipments',
            'search_heading' => _l('equipments'),
        ];

        if (isset($result[0]['result'][0]['id'])) {
            return $result;
        }

        $CI->db->select()
            ->from(db_prefix() . 'equipments')
            ->join(db_prefix() . 'clients', db_prefix() . 'equipments.client_id=' . db_prefix() . 'clients.userid', 'left')
            ->like(db_prefix() . 'clients.company', $q)
            ->or_like(db_prefix() . 'equipments.formatted_number', $q)
            ->order_by(db_prefix() . 'clients.company', 'ASC')
            ->limit($limit);

        $result[] = [
            'result'         => $CI->db->get()->result_array(),
            'type'           => 'equipments',
            'search_heading' => _l('equipments'),
        ];
    }
    return $result;
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

function equipments_email_templates_section()
{
    $CI = &get_instance();
    $CI->load->model('emails_model');
    $data['equipment_email_templates'] = $CI->emails_model->get([
        'type'     => 'equipments',
        'language' => 'english',
    ]);
    $data['hasPermissionEdit'] = staff_can('edit', 'email_templates');
    $CI->load->view('equipments/admin/emails/equipment_email_templates', $data);
}

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

function equipments_inject_job_equipment_data($job)
{
    if (!$job) { return $job; }
    $CI  = &get_instance();
    $pfx = db_prefix();

    $equipment = $CI->db
        ->select('s.id, s.formatted_number, s.date, s.expirydate, s.status,
                  s.assign_to, s.reference_no,
                  CONCAT(st.firstname," ",st.lastname) as assign_to_name')
        ->from($pfx . 'equipment_items si')
        ->join($pfx . 'equipments s', 's.id = si.equipment_id')
        ->join($pfx . 'staff st', 'st.staffid = s.assign_to', 'left')
        ->where('si.job_id', (int)$job->id)
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

    $job->equipment = $equipment;

    return $job;
}

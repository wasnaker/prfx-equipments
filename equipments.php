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
hooks()->add_action('admin_init',          'equipments_permissions');
hooks()->add_action('admin_init',          'equipments_settings_tab');
hooks()->add_action('admin_init',          'equipments_register_app_table');
hooks()->add_action('admin_init',          'equipments_ensure_role_permissions');
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
hooks()->add_filter('get_contact_permissions',        'equipments_add_contact_permission');
hooks()->add_filter('staff_can', 'equipments_staff_can_filter', 10, 4);
hooks()->add_filter('membership_plan_protections', 'equipments_membership_plan_protections');
hooks()->add_filter('membership_plan_features',    'equipments_membership_plan_features', 20, 2);
hooks()->add_filter('get_relation_data',    'equipments_get_relation_data');
hooks()->add_filter('relation_values',  'equipments_get_relation_values');
hooks()->add_filter('init_relation_options', 'equipments_init_relation_options');
hooks()->add_filter('myequipments_table_sql_columns', 'myequipments_scope_sql_columns');
hooks()->add_filter('myequipments_table_columns',     'myequipments_scope_header_columns');

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

function equipments_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'     => _l('permission_view') . ' (' . _l('permission_global') . ')',
        'view_own' => _l('permission_view_own'),
        'edit_own' => _l('permission_edit_own'),
        'create'   => _l('permission_create'),
        'edit'     => _l('permission_edit'),
        'delete'   => _l('permission_delete'),
    ];
    register_staff_capabilities('equipments', $capabilities, _l('equipments'));

    // Customer-facing equipment registry (myequipments = tblcustomer_equipment)
    $my_capabilities = [];
    $my_capabilities['capabilities'] = [
        'view'     => _l('permission_view') . ' (' . _l('permission_global') . ')',
        'view_own' => _l('permission_view_own'),
        'create'   => _l('permission_create'),
        'edit'     => _l('permission_edit'),
        'edit_own' => _l('permission_edit_own'),
        'mark_as'  => _l('permission_mark_as'),
        'delete'   => _l('permission_delete'),
    ];
    register_staff_capabilities('myequipments', $my_capabilities, _l('my_equipment_units'));
}

/**
 * Hide client SQL column for customer (they only see their own equipment).
 * For surveyor: client column stays (scoped by order/connection in WHERE).
 */
function myequipments_scope_sql_columns(array $columns): array
{
    $me = get_staff(get_staff_user_id());
    if (($me->client_type ?? '') === 'customer') {
        $columns = array_values(array_filter($columns, function ($col) {
            return strpos($col, 'clients.company') === false;
        }));
    }
    return $columns;
}

/**
 * Hide client header column for customer — same actor, same reason.
 */
function myequipments_scope_header_columns(array $columns): array
{
    $me = get_staff(get_staff_user_id());
    if (($me->client_type ?? '') === 'customer') {
        // Remove index 4 (client column) — 0=checkbox,1=unit,2=name,3=type,4=client
        unset($columns[4]);
        $columns = array_values($columns);
    }
    return $columns;
}

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

function equipments_staff_can_filter($result, $capability, $feature, $staff_id)
{
    if (!in_array($feature, ['equipments', 'myequipments'])) { return $result; }
    if ($result === true) { return true; }

    $CI   = &get_instance();
    $role = $CI->db->select('role')
        ->get_where(db_prefix() . 'staff', ['staffid' => $staff_id])
        ->row();

    if (!$role || empty($role->role)) { return $result; }

    $prefix = $feature === 'myequipments' ? 'myequipment' : 'equipment';
    $opt    = get_option($prefix . '_' . $capability . '_role_' . (int) $role->role);
    if ($opt !== '') {
        return $opt == '1';
    }

    return $result;
}

/**
 * Ensure role-based equipment permissions are seeded in tbl_options.
 * Surveyor role gets view/create/edit by default (read from DB on first setup).
 */
function equipments_ensure_role_permissions()
{
    $CI = &get_instance();

    $role_caps = [
        'Customer'              => ['view_own'],
        'Customer Admin'        => ['view'],
        'Customer Branch Admin' => ['view_own'],
        'Surveyor'              => ['view_own'],
        'Surveyor Admin'        => ['view', 'create', 'edit', 'mark_as', 'convert_to_report'],
        'Surveyor Branch Admin' => ['view_own', 'create', 'edit_own', 'mark_as', 'convert_to_report'],
        // Institution (pengawasan K3)
        'Institution'              => ['view'],
        'Institution Admin'        => ['view', 'mark_as'],
        'Institution Unit Admin'   => ['view', 'mark_as'],
        // Platform roles
        'Customer Service'      => ['view'],
        'IT Support'            => ['view'],
    ];

    $denied = [
        'Customer Admin'   => ['view_own', 'edit_own'],
        'Surveyor Admin'   => ['view_own', 'edit_own'],
    ];

    foreach ($role_caps as $role_name => $caps) {
        $role = $CI->db->get_where(db_prefix() . 'roles', ['name' => $role_name])->row();
        if (!$role) { continue; }
        $rid = (int) $role->roleid;
        foreach ($caps as $cap) {
            $key = 'equipment_' . $cap . '_role_' . $rid;
            if (get_option($key) === '') { add_option($key, '1'); }
        }
    }

    foreach ($denied as $role_name => $caps) {
        $role = $CI->db->get_where(db_prefix() . 'roles', ['name' => $role_name])->row();
        if (!$role) { continue; }
        $rid = (int) $role->roleid;
        foreach ($caps as $cap) {
            $key = 'equipment_' . $cap . '_role_' . $rid;
            if (get_option($key) === '') { add_option($key, '0'); }
        }
    }

    // ── myequipments (tblcustomer_equipment) defaults ─────────────────────────
    $my_role_caps = [
        'Customer'              => ['view', 'view_own'],
        'Customer Admin'        => ['view', 'create', 'edit', 'mark_as'],
        'Customer Branch Admin' => ['view', 'view_own', 'create', 'edit_own'],
        'Customer Service'      => ['view'],
        'IT Support'            => ['view'],
        // Surveyor: view connected customer equipment (filtered by tblclient_connections)
        'Surveyor'              => ['view', 'view_own'],
        'Surveyor Admin'        => ['view'],
        'Surveyor Branch Admin' => ['view', 'view_own'],
    ];

    $my_denied = [
        'Customer'              => ['create', 'edit', 'edit_own', 'mark_as', 'delete'],
        'Customer Admin'        => ['view_own', 'edit_own', 'delete'],
        'Customer Branch Admin' => ['edit', 'mark_as', 'delete'],
        'Customer Service'      => ['view_own', 'create', 'edit', 'edit_own', 'mark_as', 'delete'],
        'IT Support'            => ['view_own', 'create', 'edit', 'edit_own', 'mark_as', 'delete'],
        'Surveyor'              => ['create', 'edit', 'edit_own', 'mark_as', 'delete'],
        'Surveyor Admin'        => ['view_own', 'create', 'edit', 'edit_own', 'mark_as', 'delete'],
        'Surveyor Branch Admin' => ['create', 'edit', 'edit_own', 'mark_as', 'delete'],
    ];

    foreach ($my_role_caps as $role_name => $caps) {
        $role = $CI->db->get_where(db_prefix() . 'roles', ['name' => $role_name])->row();
        if (!$role) { continue; }
        $rid = (int) $role->roleid;
        foreach ($caps as $cap) {
            $key = 'myequipment_' . $cap . '_role_' . $rid;
            if (get_option($key) === '') { add_option($key, '1'); }
        }
    }

    foreach ($my_denied as $role_name => $caps) {
        $role = $CI->db->get_where(db_prefix() . 'roles', ['name' => $role_name])->row();
        if (!$role) { continue; }
        $rid = (int) $role->roleid;
        foreach ($caps as $cap) {
            $key = 'myequipment_' . $cap . '_role_' . $rid;
            if (get_option($key) === '') { add_option($key, '0'); }
        }
    }
}

function equipments_add_contact_permission($permissions)
{
    $permissions[] = [
        'id'         => 7,
        'name'       => _l('customer_permission_equipment'),
        'short_name' => 'equipments',
    ];
    return $permissions;
}

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

// ─── Relation data hooks (program search in equipment form) ───────────────────

function equipments_get_relation_data($data)
{
    $CI   = &get_instance();
    $type = $CI->input->post('type');
    $q    = trim((string) $CI->input->post('q'));

    if ($type === 'equipment_program') {
        $customer_id = $CI->input->post('customer_id');

        $CI->db->select('p.id, p.formatted_number as name')
               ->from(db_prefix() . 'programs p')
               ->where('p.status !=', 99);

        if ($customer_id) {
            $CI->db->where('p.client_id', (int) $customer_id);
        }
        if ($q) {
            $CI->db->like('p.formatted_number', $q);
        }

        return $CI->db->get()->result_array();
    }

    if ($type === 'equipment_equipment') {
        $customer_id = (int) $CI->input->post('customer_id');

        $CI->db->select("ce.id, CONCAT(i.description, ' (', ce.unit_code, ')') AS name")
               ->from(db_prefix() . 'customer_equipment ce')
               ->join(db_prefix() . 'items i', 'i.id = ce.item_id', 'left');

        if ($customer_id) {
            $CI->db->where('ce.client_id', $customer_id);
        }
        if ($q) {
            $CI->db->like('i.description', $q);
        }

        return $CI->db->get()->result_array();
    }

    if ($type === 'equipment_job') {
        $program_id = (int) $CI->input->post('program_id');

        // Require a program to be selected — empty without it
        if (!$program_id) { return []; }

        $CI->db->select('j.id, j.name')
               ->from(db_prefix() . 'jobs j')
               ->where('j.rel_type', 'program')
               ->where('j.rel_id', $program_id);

        if ($q) {
            $CI->db->like('j.name', $q);
        }

        return $CI->db->get()->result_array();
    }

    if ($type === 'equipment_member') {
        $me        = $CI->db->get_where(db_prefix() . 'staff', ['staffid' => get_staff_user_id()])->row();
        $client_id = ($me && !empty($me->client_id)) ? (int) $me->client_id : 0;

        $CI->db->select("staffid as id, CONCAT(firstname, ' ', lastname) as name")
               ->from(db_prefix() . 'staff')
               ->where('active', 1);

        if ($client_id) {
            $CI->db->where('client_id', $client_id);
        }

        if ($q) {
            $CI->db->group_start()
                   ->like('firstname', $q)
                   ->or_like('lastname', $q)
                   ->group_end();
        }

        return $CI->db->get()->result_array();
    }

    // Catalog items filtered by subgroup/category
    if ($type === 'equipment_item') {
        $subgroup_id = (int) $CI->input->post('subgroup_id');
        $category_id = (int) $CI->input->post('category_id');

        $CI->db->select("i.id, CONCAT(i.description, IF(sg.name IS NOT NULL, CONCAT(' — ', sg.name), '')) as name")
               ->from(db_prefix() . 'items i')
               ->join(db_prefix() . 'items_subgroups sg', 'sg.id = i.subgroup_id', 'left');

        if ($subgroup_id) { $CI->db->where('i.subgroup_id', $subgroup_id); }
        if ($category_id) { $CI->db->where('i.category_id', $category_id); }
        if ($q)           { $CI->db->like('i.description', $q); }

        return $CI->db->order_by('i.description', 'ASC')->get()->result_array();
    }

    // Subgroups filtered by group_id
    if ($type === 'equipment_subgroup') {
        $group_id = (int) $CI->input->post('group_id');

        $CI->db->select('id, name')
               ->from(db_prefix() . 'items_subgroups');

        if ($group_id) {
            $CI->db->where('group_id', $group_id);
        }
        if ($q) {
            $CI->db->like('name', $q);
        }

        return $CI->db->order_by('name', 'ASC')->get()->result_array();
    }

    // Categories filtered by subgroup_id
    if ($type === 'equipment_category') {
        $subgroup_id = (int) $CI->input->post('subgroup_id');

        $CI->db->select('id, name')
               ->from(db_prefix() . 'items_categories');

        if ($subgroup_id) {
            $CI->db->where('subgroup_id', $subgroup_id);
        }
        if ($q) {
            $CI->db->like('name', $q);
        }

        return $CI->db->order_by('sort_order', 'ASC')->order_by('name', 'ASC')->get()->result_array();
    }

    return $data;
}

function equipments_get_relation_values($values)
{
    $type = $values['type'] ?? '';

    if ($type === 'equipment_program') {
        $relation       = $values['relation'];
        $values['id']   = is_array($relation) ? ($relation['id']   ?? '') : ($relation->id   ?? '');
        $values['name'] = is_array($relation) ? ($relation['name'] ?? '') : ($relation->name ?? '');
        $values['link'] = admin_url('programs/view/' . $values['id']);
        return $values;
    }

    if (in_array($type, ['equipment_equipment', 'equipment_job', 'equipment_member', 'equipment_subgroup', 'equipment_category', 'equipment_item'])) {
        $relation       = $values['relation'];
        $values['id']   = is_array($relation) ? ($relation['id']   ?? '') : ($relation->id   ?? '');
        $values['name'] = is_array($relation) ? ($relation['name'] ?? '') : ($relation->name ?? '');
        $values['link'] = '';
        return $values;
    }

    return $values;
}

function equipments_init_relation_options($data)
{
    $CI   = &get_instance();
    $type = $CI->input->post('type');
    if ($type === 'equipment_program' || $type === 'equipment_equipment' || $type === 'equipment_job' || $type === 'equipment_member') {
        if (!has_permission('equipments', '', 'view')) { return []; }
        return $data;
    }
    return $data;
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

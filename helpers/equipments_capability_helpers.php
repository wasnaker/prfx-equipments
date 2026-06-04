<?php

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('admin_init', 'equipments_permissions');
hooks()->add_action('admin_init', 'equipments_ensure_role_permissions');
hooks()->add_filter('staff_can', 'equipments_staff_can_filter', 10, 4);
hooks()->add_filter('get_contact_permissions', 'equipments_add_contact_permission');
hooks()->add_filter('role_capabilities_features', 'equipments_role_capabilities_features');

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

function equipments_ensure_role_permissions()
{
    $CI = &get_instance();

    $role_caps = [
        'Customer'              => ['view_own'],
        'Customer Admin'        => ['view'],
        'Customer Branch Admin' => ['view_own'],
        'Surveyor'              => ['view_own'],
        'Surveyor Admin'        => ['view', 'mark_as', 'convert_to_report'],
        'Surveyor Branch Admin' => ['view_own', 'mark_as', 'convert_to_report'],
        'Assessor'              => ['view'],
        'Surveyor Sales'        => ['view'],
        'Surveyor Finance'      => ['view'],
        'Surveyor Operation'    => ['view'],
        'Finance'               => ['view'],
        'Association'           => ['view'],
        'Association Admin'     => ['view'],
        'Institution'              => ['view'],
        'Institution Admin'        => ['view', 'mark_as'],
        'Institution Unit Admin'   => ['view', 'mark_as'],
        'Customer Service'      => ['view'],
        'IT Support'            => ['view', 'create', 'edit'],
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

    $my_role_caps = [
        'Customer'              => ['view', 'view_own'],
        'Customer Admin'        => ['view', 'create', 'edit', 'mark_as'],
        'Customer Branch Admin' => ['view', 'view_own', 'create', 'edit_own'],
        'Customer Service'      => ['view'],
        'IT Support'            => ['view'],
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

function equipments_role_capabilities_features($features)
{
    $features['equipments'] = [
        'module'        => 'equipments',
        'label'         => _l('equipments'),
        'prefix'        => 'equipment',
        'capabilities'  => ['view', 'view_own', 'edit_own', 'create', 'edit', 'delete'],
        'resource_type' => 'equipment',
    ];

    $features['myequipments'] = [
        'module'        => 'equipments',
        'label'         => _l('myequipments'),
        'prefix'        => 'myequipment',
        'capabilities'  => ['view', 'view_own', 'create', 'edit', 'edit_own', 'mark_as', 'delete'],
        'resource_type' => 'myequipment',
    ];

    return $features;
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

<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ─── Hook Registrations ────────────────────────────────────────────────────────

hooks()->add_action('admin_init', 'equipments_init_menu_items');

// ─── Menu Item Registration ───────────────────────────────────────────────────

function equipments_init_menu_items()
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

    $me = get_staff(get_staff_user_id());
    $ct = $me ? ($me->client_type ?? '') : '';

    if (has_permission('equipments', '', 'view') || in_array($ct, ['customer', 'surveyor'])) {
        $CI->app_menu->add_sidebar_children_item('wasnaker-masterdata', [
            'slug'     => 'myequipments',
            'name'     => _l('my_equipment_units'),
            'href'     => admin_url('equipments/myequipments'),
            'position' => 2,
        ]);
    }
}

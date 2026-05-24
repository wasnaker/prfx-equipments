<?php

defined('BASEPATH') or exit('No direct script access allowed');

// ─── Hook Registrations ───────────────────────────────────────────────────────

hooks()->add_filter('membership_plan_protections', 'equipments_membership_plan_protections');
hooks()->add_filter('membership_plan_features',    'equipments_membership_plan_features', 20, 2);

// ─── Membership Plan Protections & Features ────────────────────────────────────

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

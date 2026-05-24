<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ─── Hook Registrations ────────────────────────────────────────────────────────

hooks()->add_filter('global_search_result_query',  'equipments_global_search_result_query', 10, 3);
hooks()->add_filter('global_search_result_output', 'equipments_global_search_result_output', 10, 2);

// ─── Global Search: Query ──────────────────────────────────────────────────────

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

// ─── Global Search: Output ─────────────────────────────────────────────────────

function equipments_global_search_result_output($output, $data)
{
    if ($data['type'] == 'equipments') {
        $output = '<a href="' . admin_url('equipments/list_equipments/' . $data['result']['id']) . '">'
            . format_equipment_number($data['result']['id']) . '</a>';
    }
    return $output;
}

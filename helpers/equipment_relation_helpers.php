<?php

defined('BASEPATH') or exit('No direct script access allowed');

// ─── Relation Data Hooks ─────────────────────────────────────────────────────
// Custom relation types: equipment_project, equipment_equipment, equipment_task, equipment_member, equipment_item, equipment_subgroup, equipment_category

hooks()->add_filter('get_relation_data',    'equipments_get_relation_data');
hooks()->add_filter('relation_values',      'equipments_get_relation_values');
hooks()->add_filter('init_relation_options', 'equipments_init_relation_options');

/**
 * Hook filter: get_relation_data
 * Extends relation system to support custom relation types for equipment
 *
 * @param array $data Existing relation data (pass-through)
 * @return array Filtered relation data with custom types populated
 */
function equipments_get_relation_data($data)
{
    $CI   = &get_instance();
    $type = $CI->input->post('type');
    $q    = trim((string) $CI->input->post('q'));

    if ($type === 'equipment_project') {
        $customer_id = $CI->input->post('customer_id');

        $CI->db->select('p.id, p.formatted_number as name')
               ->from(db_prefix() . 'projects p')
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

    if ($type === 'equipment_task') {
        $project_id = (int) $CI->input->post('project_id');

        if (!$project_id) { return []; }

        $CI->db->select('j.id, j.name')
               ->from(db_prefix() . 'tasks j')
               ->where('j.rel_type', 'project')
               ->where('j.rel_id', $project_id);

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

/**
 * Hook filter: relation_values
 * Format custom relation types for display in UI
 *
 * @param array $values Relation values array with 'type', 'relation', etc.
 * @return array Filtered values with name, id, link formatted for UI
 */
function equipments_get_relation_values($values)
{
    $type = $values['type'] ?? '';

    if ($type === 'equipment_project') {
        $relation       = $values['relation'];
        $values['id']   = is_array($relation) ? ($relation['id']   ?? '') : ($relation->id   ?? '');
        $values['name'] = is_array($relation) ? ($relation['name'] ?? '') : ($relation->name ?? '');
        $values['link'] = admin_url('projects/view/' . $values['id']);
        return $values;
    }

    if (in_array($type, ['equipment_equipment', 'equipment_task', 'equipment_member', 'equipment_subgroup', 'equipment_category', 'equipment_item'])) {
        $relation       = $values['relation'];
        $values['id']   = is_array($relation) ? ($relation['id']   ?? '') : ($relation->id   ?? '');
        $values['name'] = is_array($relation) ? ($relation['name'] ?? '') : ($relation->name ?? '');
        $values['link'] = '';
        return $values;
    }

    return $values;
}

/**
 * Hook filter: init_relation_options
 * Apply permission checks and filtering for custom relation types
 * Only staff with 'equipments' view permission can access relation options
 *
 * @param array $data Relation options to render (pass-through)
 * @return array Filtered options, or empty array if permission denied
 */
function equipments_init_relation_options($data)
{
    $CI   = &get_instance();
    $type = $CI->input->post('type');
    if ($type === 'equipment_project' || $type === 'equipment_equipment' || $type === 'equipment_task' || $type === 'equipment_member') {
        if (!staff_can('view', 'equipments') && !staff_can('view_own', 'equipments')) { return []; }
        return $data;
    }
    return $data;
}

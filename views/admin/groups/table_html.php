<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [
    _l('group_name'),
    _l('group_code'),
    _l('group_statuses'),
    _l('equipment_subgroup'),
    _l('equipments'),
];

$table_data = hooks()->apply_filters('groups_table_columns', $table_data);

render_datatable($table_data, isset($class) ? $class : 'groups', [], ['id' => $table_id ?? 'groups']);

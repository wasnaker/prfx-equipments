<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [
    _l('subgroup_name'),
    _l('group_code'),
    _l('equipment_group'),
    _l('color'),
    _l('sort_order'),
    _l('equipments'),
];

$table_data = hooks()->apply_filters('subgroups_table_columns', $table_data);
render_datatable($table_data, isset($class) ? $class : 'subgroups', [], ['id' => $table_id ?? 'subgroups']);

<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [
    _l('subgroup_name'),
    _l('equipment_group'),
    _l('equipments'),
];

$table_data = hooks()->apply_filters('subgroups_table_columns', $table_data);
render_datatable($table_data, isset($class) ? $class : 'subgroups', [], ['id' => $table_id ?? 'subgroups']);

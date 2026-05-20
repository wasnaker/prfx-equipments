<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [
    _l('equipment_code'),
    _l('equipment_type'),
    _l('equipment_category'),
    _l('equipment_subgroup'),
    _l('equipment_group'),
    _l('equipment_status'),
];

$table_data = hooks()->apply_filters('equipments_table_columns', $table_data);

render_datatable($table_data, isset($class) ? $class : 'equipments', [], ['id' => $table_id ?? 'equipments']);

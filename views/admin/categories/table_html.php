<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [
    _l('category_name'),
    _l('equipment_group'),
    _l('equipment_subgroup'),
    _l('sort_order'),
    _l('equipments'),
];

$table_data = hooks()->apply_filters('categories_table_columns', $table_data);
render_datatable($table_data, isset($class) ? $class : 'categories', [], ['id' => $table_id ?? 'categories']);

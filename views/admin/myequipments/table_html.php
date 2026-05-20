<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [
    ['name' => '<div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="myequipments"><label></label></div>', 'th_attrs' => ['class' => 'not_visible_by_default dt-bulk-checkbox']],
    _l('equipment_unit_code'),
    _l('customer_equipment_name'),
    _l('equipment_type'),
    _l('equipment_dt_table_heading_client'),
    _l('equipment_equipment_serial_number'),
    _l('location'),
    _l('procurement_year'),
    _l('manufacture_year'),
    _l('cert_expired_date'),
    _l('equipment_status'),
];

$table_data = hooks()->apply_filters('myequipments_table_columns', $table_data);
render_datatable($table_data, isset($class) ? $class : 'myequipments', [], ['id' => $table_id ?? 'myequipments']);

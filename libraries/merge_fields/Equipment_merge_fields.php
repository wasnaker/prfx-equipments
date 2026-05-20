<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Equipment_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
                [
                    'name'      => 'Equipment Link',
                    'key'       => '{equipment_link}',
                    'available' => [
                        'equipments',
                    ],
                ],
                [
                    'name'      => 'Equipment Number',
                    'key'       => '{equipment_number}',
                    'available' => [
                        'equipments',
                    ],
                ],
                [
                    'name'      => 'Reference no.',
                    'key'       => '{equipment_reference_no}',
                    'available' => [
                        'equipments',
                    ],
                ],
                [
                    'name'      => 'Equipment Expiry Date',
                    'key'       => '{equipment_expirydate}',
                    'available' => [
                        'equipments',
                    ],
                ],
                [
                    'name'      => 'Equipment Date',
                    'key'       => '{equipment_date}',
                    'available' => [
                        'equipments',
                    ],
                ],
                [
                    'name'      => 'Equipment Status',
                    'key'       => '{equipment_status}',
                    'available' => [
                        'equipments',
                    ],
                ],
                [
                    'name'      => 'Equipment Sale Agent',
                    'key'       => '{equipment_sale_agent}',
                    'available' => [
                        'equipments',
                    ],
                ],
                [
                    'name'      => 'Equipment Total',
                    'key'       => '{equipment_total}',
                    'available' => [
                        'equipments',
                    ],
                ],
                [
                    'name'      => 'Equipment Subtotal',
                    'key'       => '{equipment_subtotal}',
                    'available' => [
                        'equipments',
                    ],
                ],
                [
                    'name'      => 'Project name',
                    'key'       => '{project_name}',
                    'available' => [
                        'equipments',
                    ],
                ],
            ];
    }

    /**
     * Merge fields for equipments
     * @param  mixed $equipment_id equipment id
     * @return array
     */
    public function format($equipment_id)
    {
        $fields = [];
        $this->ci->db->where('id', $equipment_id);
        $equipment = $this->ci->db->get(db_prefix().'equipments')->row();

        if (!$equipment) {
            return $fields;
        }

        $currency = get_currency($equipment->currency);

        $fields['{equipment_sale_agent}']   = e(get_staff_full_name($equipment->sale_agent));
        $fields['{equipment_total}']        = e(app_format_money($equipment->total, $currency));
        $fields['{equipment_subtotal}']     = e(app_format_money($equipment->subtotal, $currency));
        $fields['{equipment_link}']         = site_url('equipment/' . $equipment_id . '/' . $equipment->hash);
        $fields['{equipment_number}']       = e(format_equipment_number($equipment_id));
        $fields['{equipment_reference_no}'] = e($equipment->reference_no);
        $fields['{equipment_expirydate}']   = e(_d($equipment->expirydate));
        $fields['{equipment_date}']         = e(_d($equipment->date));
        $fields['{equipment_status}']       = e(format_equipment_status($equipment->status, '', false));
        $fields['{project_name}']          = e(get_project_name_by_id($equipment->project_id));
        $fields['{equipment_short_url}']    = get_equipment_shortlink($equipment);

        $custom_fields = get_custom_fields('equipment');
        foreach ($custom_fields as $field) {
            $fields['{' . $field['slug'] . '}'] = get_custom_field_value($equipment_id, $field['id'], 'equipment');
        }

        return hooks()->apply_filters('equipment_merge_fields', $fields, [
        'id'       => $equipment_id,
        'equipment' => $equipment,
     ]);
    }
}

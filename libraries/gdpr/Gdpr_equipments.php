<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Gdpr_equipments
{
    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    public function export($customer_id)
    {
        $valAllowed = get_option('gdpr_contact_data_portability_allowed');
        if (empty($valAllowed)) {
            $valAllowed = [];
        } else {
            $valAllowed = unserialize($valAllowed);
        }

        $this->ci->db->where('client_id', $customer_id);
        $equipments = $this->ci->db->get(db_prefix().'equipments')->result_array();

        $this->ci->db->where('show_on_client_portal', 1);
        $this->ci->db->where('fieldto', 'equipment');
        $this->ci->db->order_by('field_order', 'asc');
        $custom_fields = $this->ci->db->get(db_prefix().'customfields')->result_array();

        $this->ci->load->model('currencies_model');
        foreach ($equipments as $equipmentsKey => $equipment) {
            unset($equipments[$equipmentsKey]['adminnote']);
            $equipments[$equipmentsKey]['shipping_country'] = get_country($equipment['shipping_country']);
            $equipments[$equipmentsKey]['billing_country']  = get_country($equipment['billing_country']);

            $equipments[$equipmentsKey]['currency'] = $this->ci->currencies_model->get($equipment['currency']);

            $equipments[$equipmentsKey]['items'] = _prepare_items_array_for_export(get_items_by_type('equipment', $equipment['id']), 'equipment');

            if (in_array('equipments_notes', $valAllowed)) {
                // Notes
                $this->ci->db->where('rel_id', $equipment['id']);
                $this->ci->db->where('rel_type', 'equipment');

                $equipments[$equipmentsKey]['notes'] = $this->ci->db->get(db_prefix().'notes')->result_array();
            }
            if (in_array('equipments_activity_log', $valAllowed)) {
                // Activity
                $this->ci->db->where('rel_id', $equipment['id']);
                $this->ci->db->where('rel_type', 'equipment');

                $equipments[$equipmentsKey]['activity'] = $this->ci->db->get(db_prefix().'sales_activity')->result_array();
            }
            $equipments[$equipmentsKey]['views'] = get_views_tracking('equipment', $equipment['id']);

            $equipments[$equipmentsKey]['tracked_emails'] = get_tracked_emails($equipment['id'], 'equipment');

            $equipments[$equipmentsKey]['additional_fields'] = [];

            foreach ($custom_fields as $cf) {
                $equipments[$equipmentsKey]['additional_fields'][] = [
                    'name'  => $cf['name'],
                    'value' => get_custom_field_value($equipment['id'], $cf['id'], 'equipment'),
                ];
            }
        }

        return $equipments;
    }
}

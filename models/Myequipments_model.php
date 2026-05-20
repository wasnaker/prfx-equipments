<?php defined('BASEPATH') or exit('No direct script access allowed');

class Myequipments_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = [])
    {
        $this->db->select(
            db_prefix() . 'customer_equipment.*,' .
            db_prefix() . 'items.description as item_name,' .
            db_prefix() . 'items.code as item_code,' .
            '(SELECT name FROM ' . db_prefix() . 'items_subgroups WHERE id=' . db_prefix() . 'items.subgroup_id) as subgroup_name,' .
            '(SELECT name FROM ' . db_prefix() . 'items_groups WHERE id=' . db_prefix() . 'items.group_id) as group_name,' .
            db_prefix() . 'clients.company as client_company'
        );
        $this->db->from(db_prefix() . 'customer_equipment');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.id = ' . db_prefix() . 'customer_equipment.item_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'customer_equipment.client_id', 'left');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'customer_equipment.id', $id);
            return $this->db->get()->row();
        }

        $this->db->order_by(db_prefix() . 'customer_equipment.datecreated', 'DESC');
        return $this->db->get()->result_array();
    }

    public function add($data)
    {
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom']   = get_staff_user_id();
        $this->db->insert(db_prefix() . 'customer_equipment', $data);
        $id = $this->db->insert_id();
        if ($id) {
            $this->log_activity($id, 'myequipment_activity_created');
        }
        return $id;
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id)->update(db_prefix() . 'customer_equipment', $data);
        if ($this->db->affected_rows() >= 0) {
            $this->log_activity($id, 'myequipment_activity_updated');
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $this->db->where('id', $id)->delete(db_prefix() . 'customer_equipment');
        return $this->db->affected_rows() > 0;
    }

    public function get_activity($id)
    {
        return $this->db->where('equipmentid', (int) $id)
            ->order_by('dateadded', 'DESC')
            ->get(db_prefix() . 'equipment_activity')->result_array();
    }

    private function log_activity($id, $description)
    {
        $this->db->insert(db_prefix() . 'equipment_activity', [
            'equipmentid' => $id,
            'description' => $description,
            'staffid'     => get_staff_user_id(),
            'dateadded'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function get_statuses()
    {
        return [
            EQUIPMENT_STATUS_DRAFT,
            EQUIPMENT_STATUS_ACTIVE,
            EQUIPMENT_STATUS_INACTIVE,
            EQUIPMENT_STATUS_EXPIRED,
        ];
    }

    public function get_cert_status($cert_expired_date)
    {
        if (empty($cert_expired_date)) {
            return EQUIPMENT_STATUS_DRAFT;
        }
        $today    = date('Y-m-d');
        $soon     = date('Y-m-d', strtotime('+30 days'));
        if ($cert_expired_date < $today) {
            return EQUIPMENT_STATUS_EXPIRED;
        }
        if ($cert_expired_date <= $soon) {
            return EQUIPMENT_STATUS_INACTIVE;
        }
        return EQUIPMENT_STATUS_ACTIVE;
    }
}

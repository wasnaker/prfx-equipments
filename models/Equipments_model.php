<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Equipments_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_items_model');
    }

    public function get($id = '')
    {
        $item = $this->invoice_items_model->get($id);

        if ($item && is_object($item) && !empty($item->itemid)) {
            $row = $this->db
                ->select('i.code, i.subgroup_id, i.status, i.addedfrom, i.category_id,
                          sg.name AS subgroup_name,
                          cat.name AS category_name')
                ->from(db_prefix() . 'items i')
                ->join(db_prefix() . 'items_subgroups sg', 'sg.id = i.subgroup_id', 'left')
                ->join(db_prefix() . 'items_categories cat', 'cat.id = i.category_id', 'left')
                ->where('i.id', (int) $item->itemid)
                ->get()->row();
            if ($row) {
                $item->code          = $row->code;
                $item->subgroup_id   = $row->subgroup_id;
                $item->subgroup_name = $row->subgroup_name;
                $item->category_id   = $row->category_id;
                $item->category_name = $row->category_name;
                $item->status        = $row->status;
                $item->addedfrom     = $row->addedfrom;
            }
        }

        return $item;
    }

    public function add($data)
    {
        if (empty($data['addedfrom'])) {
            $data['addedfrom'] = get_staff_user_id();
        }
        return $this->invoice_items_model->add($data);
    }

    public function update($data, $id)
    {
        $allowed = ['description', 'long_description', 'rate', 'unit', 'group_id', 'subgroup_id', 'category_id', 'code', 'status', 'addedfrom'];
        $filtered = array_intersect_key($data, array_flip($allowed));
        $filtered['itemid'] = $id;
        return $this->invoice_items_model->edit($filtered);
    }

    public function delete($id)
    {
        return $this->invoice_items_model->delete($id);
    }

    public function get_groups()
    {
        return $this->invoice_items_model->get_groups();
    }

    public function add_group($data)
    {
        return $this->invoice_items_model->add_group($data);
    }

    public function edit_group($data, $id)
    {
        return $this->invoice_items_model->edit_group($data, $id);
    }

    public function delete_group($id)
    {
        return $this->invoice_items_model->delete_group($id);
    }

    // ── Subgroups ─────────────────────────────────────────────────────────────

    public function get_subgroups()
    {
        return $this->db
            ->select('s.*, g.name AS group_name')
            ->from(db_prefix() . 'items_subgroups s')
            ->join(db_prefix() . 'items_groups g', 'g.id = s.group_id', 'left')
            ->order_by('g.name', 'ASC')
            ->order_by('s.sort_order', 'ASC')
            ->order_by('s.name', 'ASC')
            ->get()->result_array();
    }

    public function get_subgroup($id)
    {
        return $this->db->where('id', $id)
            ->get(db_prefix() . 'items_subgroups')->row();
    }

    public function add_subgroup(array $data)
    {
        $this->db->insert(db_prefix() . 'items_subgroups', $data);
        return $this->db->insert_id();
    }

    public function edit_subgroup(array $data, $id)
    {
        $this->db->where('id', $id)->update(db_prefix() . 'items_subgroups', $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete_subgroup($id)
    {
        $this->db->where('id', $id)->delete(db_prefix() . 'items_subgroups');
        return $this->db->affected_rows() > 0;
    }

    public function get_equipment_activity($id)
    {
        return $this->db->where('equipmentid', (int) $id)
            ->order_by('dateadded', 'DESC')
            ->get(db_prefix() . 'equipment_activity')->result_array();
    }

    // ── Document statuses (used by quick_stats) ───────────────────────────────

    public function get_statuses()
    {
        return [
            EQUIPMENT_STATUS_DRAFT,
            EQUIPMENT_STATUS_ACTIVE,
            EQUIPMENT_STATUS_INACTIVE,
            EQUIPMENT_STATUS_EXPIRED,
        ];
    }



    /**
     * Get equipment unique year for filtering
     *
     * @return array
     */
    public function get_equipments_years()
    {
        return [];
    }
}

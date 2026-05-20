<?php defined('BASEPATH') or exit('No direct script access allowed');

class Subgroups_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            return $this->db
                ->select('s.*, g.name AS group_name')
                ->from(db_prefix() . 'items_subgroups s')
                ->join(db_prefix() . 'items_groups g', 'g.id = s.group_id', 'left')
                ->where('s.id', $id)
                ->get()->row();
        }
        return $this->db
            ->select('s.*, g.name AS group_name')
            ->from(db_prefix() . 'items_subgroups s')
            ->join(db_prefix() . 'items_groups g', 'g.id = s.group_id', 'left')
            ->order_by('g.name', 'ASC')
            ->order_by('s.sort_order', 'ASC')
            ->order_by('s.name', 'ASC')
            ->get()->result_array();
    }

    public function add($data)
    {
        $this->db->insert(db_prefix() . 'items_subgroups', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id)->update(db_prefix() . 'items_subgroups', $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete($id)
    {
        $this->db->where('id', $id)->delete(db_prefix() . 'items_subgroups');
        return $this->db->affected_rows() > 0;
    }
}

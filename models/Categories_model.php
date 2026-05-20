<?php defined('BASEPATH') or exit('No direct script access allowed');

class Categories_model extends App_Model
{
    public function get($id = '')
    {
        if (is_numeric($id)) {
            return $this->db
                ->select('c.*, s.name AS subgroup_name, g.name AS group_name')
                ->from(db_prefix() . 'items_categories c')
                ->join(db_prefix() . 'items_subgroups s', 's.id = c.subgroup_id', 'left')
                ->join(db_prefix() . 'items_groups g', 'g.id = s.group_id', 'left')
                ->where('c.id', $id)
                ->get()->row();
        }
        return $this->db
            ->select('c.*, s.name AS subgroup_name, g.name AS group_name')
            ->from(db_prefix() . 'items_categories c')
            ->join(db_prefix() . 'items_subgroups s', 's.id = c.subgroup_id', 'left')
            ->join(db_prefix() . 'items_groups g', 'g.id = s.group_id', 'left')
            ->order_by('s.name', 'ASC')
            ->order_by('c.sort_order', 'ASC')
            ->order_by('c.name', 'ASC')
            ->get()->result_array();
    }

    public function add($data)
    {
        $this->db->insert(db_prefix() . 'items_categories', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id)->update(db_prefix() . 'items_categories', $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete($id)
    {
        $this->db->where('category_id', $id)->update(db_prefix() . 'items', ['category_id' => null]);
        $this->db->where('id', $id)->delete(db_prefix() . 'items_categories');
        return $this->db->affected_rows() > 0;
    }

    public function get_by_subgroup($subgroup_id)
    {
        return $this->db
            ->where('subgroup_id', $subgroup_id)
            ->order_by('sort_order', 'ASC')
            ->order_by('name', 'ASC')
            ->get(db_prefix() . 'items_categories')->result_array();
    }
}

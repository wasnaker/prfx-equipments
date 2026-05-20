<?php defined('BASEPATH') or exit('No direct script access allowed');

class Groups_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            return $this->db->get_where(db_prefix() . 'items_groups', ['id' => $id])->row();
        }
        return $this->db->order_by('name', 'ASC')->get(db_prefix() . 'items_groups')->result_array();
    }

    public function add($data)
    {
        $this->db->insert(db_prefix() . 'items_groups', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id)->update(db_prefix() . 'items_groups', $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete($id)
    {
        $this->db->where('id', $id)->delete(db_prefix() . 'items_groups');
        return $this->db->affected_rows() > 0;
    }
}

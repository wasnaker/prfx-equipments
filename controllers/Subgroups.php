<?php defined('BASEPATH') or exit('No direct script access allowed');

class Subgroups extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('equipments/Subgroups_model', 'subgroups_model');
    }

    public function index($id = '')
    {
        $this->list_subgroups($id);
    }

    public function list_subgroups($id = '')
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) {
            access_denied('equipments');
        }

        $data['subgroups_table']   = App_table::find('subgroups');
        $data['subgroupid']        = $id;
        $data['title']             = _l('equipment_subgroups');
        $data['groups']            = $this->db->order_by('name', 'ASC')->get(db_prefix() . 'items_groups')->result_array();
        $data['subgroup_statuses'] = [1, 0];

        $this->load->view('admin/subgroups/manage', $data);
    }

    public function table()
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) {
            ajax_access_denied();
        }
        App_table::find('subgroups')->output([]);
    }

    public function subgroup($id = '')
    {
        if ($id) {
            if (staff_cant('edit', 'equipments')) {
                access_denied('equipments');
            }
            $subgroup = $this->subgroups_model->get($id);
            if (!$subgroup) { show_404(); }
            $data['subgroup'] = $subgroup;
            $data['title']    = _l('edit_subgroup');
        } else {
            if (staff_cant('create', 'equipments')) {
                access_denied('equipments');
            }
            $data['title'] = _l('new_subgroup');
        }

        $data['groups'] = $this->db->order_by('name', 'ASC')->get(db_prefix() . 'items_groups')->result_array();
        $this->load->view('admin/subgroups/subgroup', $data);
    }

    public function add_edit_subgroup()
    {
        if (!$this->input->post()) {
            redirect(admin_url('equipments/subgroups'));
        }

        if (staff_cant('edit', 'equipments')) {
            access_denied('equipments');
        }

        $id   = $this->input->post('id');
        $data = [
            'name'       => $this->input->post('name'),
            'code'       => $this->input->post('code'),
            'color'      => $this->input->post('color') ?: '#3b82f6',
            'sort_order' => (int) $this->input->post('sort_order'),
            'group_id'   => (int) $this->input->post('group_id'),
        ];

        if ($id) {
            $success = $this->subgroups_model->update($id, $data);
        } else {
            $id      = $this->subgroups_model->add($data);
            $success = (bool) $id;
        }

        $message = $success ? _l('subgroup_saved') : _l('subgroup_save_failed');

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success, 'message' => $message, 'id' => $id]);
            return;
        }

        set_alert($success ? 'success' : 'danger', $message);
        redirect(admin_url('equipments/subgroups/list_subgroups' . ($success ? '/' . $id : '')));
    }

    public function delete_subgroup($id)
    {
        if (staff_cant('delete', 'equipments')) {
            access_denied('equipments');
        }

        $success = $this->subgroups_model->delete($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success]);
            return;
        }

        if ($success) { set_alert('success', _l('subgroup_deleted')); }
        redirect(admin_url('equipments/subgroups'));
    }

    public function get_subgroup($id)
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) { ajax_access_denied(); }
        echo json_encode($this->subgroups_model->get($id));
    }

    public function get_subgroup_data_ajax($id, $to_return = false)
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) { echo _l('access_denied'); die; }

        $subgroup = $this->subgroups_model->get($id);
        if (!$subgroup) { echo _l('subgroup_not_found'); die; }

        $data['subgroup']       = $subgroup;
        $data['totalItems']     = total_rows(db_prefix() . 'items', ['subgroup_id' => $id]);
        $data['subgroup_fields'] = [];
        $data['checklist_tabs']  = [];

        if ($to_return == false) {
            $this->load->view('admin/subgroups/subgroup_preview_template', $data);
        } else {
            return $this->load->view('admin/subgroups/subgroup_preview_template', $data, true);
        }
    }
}

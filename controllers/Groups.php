<?php defined('BASEPATH') or exit('No direct script access allowed');

class Groups extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('equipments/Groups_model', 'groups_model');
    }

    public function index($id = '')
    {
        $this->list_groups($id);
    }

    public function list_groups($id = '')
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) {
            access_denied('equipments');
        }

        $data['title']          = _l('equipment_groups');
        $data['groups']         = $this->groups_model->get();
        $data['groups_table']   = App_table::find('groups');
        $data['group_statuses'] = [1, 0];
        $data['groupid']        = $id;

        $this->load->view('admin/groups/manage', $data);
    }

    public function table()
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) {
            ajax_access_denied();
        }
        App_table::find('groups')->output([]);
    }

    public function group($id = '')
    {
        if ($id) {
            if (staff_cant('edit', 'equipments')) {
                access_denied('equipments');
            }
            $group = $this->groups_model->get($id);
            if (!$group) {
                show_404();
            }
            $data['group'] = $group;
            $data['title'] = _l('edit_group');
        } else {
            if (staff_cant('create', 'equipments')) {
                access_denied('equipments');
            }
            $data['title'] = _l('new_group');
        }

        $this->load->view('admin/groups/group', $data);
    }

    public function add_edit_group()
    {
        if (!$this->input->post()) {
            redirect(admin_url('equipments/groups'));
        }

        if (staff_cant('edit', 'equipments')) {
            access_denied('equipments');
        }

        $id   = $this->input->post('id');
        $data = [
            'name' => $this->input->post('name'),
            'code' => $this->input->post('code'),
        ];

        if ($id) {
            $success = $this->groups_model->update($id, $data);
            $message = $success ? _l('group_saved') : _l('group_save_failed');
        } else {
            $id      = $this->groups_model->add($data);
            $success = (bool) $id;
            $message = $success ? _l('group_saved') : _l('group_save_failed');
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success, 'message' => $message, 'id' => $id]);
            return;
        }

        if ($success) {
            set_alert('success', $message);
        } else {
            set_alert('danger', $message);
        }
        redirect(admin_url('equipments/groups'));
    }

    public function delete_group($id)
    {
        if (staff_cant('delete', 'equipments')) {
            access_denied('equipments');
        }

        $success = $this->groups_model->delete($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success]);
            return;
        }

        if ($success) {
            set_alert('success', _l('group_deleted'));
        }
        redirect(admin_url('equipments/groups'));
    }

    public function get_group($id)
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) {
            ajax_access_denied();
        }
        echo json_encode($this->groups_model->get($id));
    }

    public function get_group_data_ajax($id, $to_return = false)
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) {
            echo _l('access_denied');
            die;
        }

        $group = $this->groups_model->get($id);

        if (!$group) {
            echo _l('group_not_found');
            die;
        }

        $data['group']      = $group;
        $data['totalItems'] = total_rows(db_prefix() . 'items', ['group_id' => $id]);
        $data['totalSubs']  = total_rows(db_prefix() . 'items_subgroups', ['group_id' => $id]);

        if ($to_return == false) {
            $this->load->view('admin/groups/group_preview_template', $data);
        } else {
            return $this->load->view('admin/groups/group_preview_template', $data, true);
        }
    }
}

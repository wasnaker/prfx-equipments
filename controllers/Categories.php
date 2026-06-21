<?php defined('BASEPATH') or exit('No direct script access allowed');

class Categories extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('equipments/Categories_model', 'categories_model');
    }

    public function index($id = '')
    {
        $this->list_categories($id);
    }

    public function list_categories($id = '')
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) {
            access_denied('equipments');
        }

        $data['categories_table'] = App_table::find('categories');
        $data['categoryid']       = $id;
        $data['title']            = _l('equipment_categories');
        $data['subgroups']        = $this->db->order_by('name', 'ASC')->get(db_prefix() . 'items_subgroups')->result_array();

        $this->load->view('admin/categories/manage', $data);
    }

    public function table()
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) {
            ajax_access_denied();
        }
        App_table::find('categories')->output([]);
    }

    public function category($id = '')
    {
        if ($id) {
            if (staff_cant('edit', 'equipments')) {
                access_denied('equipments');
            }
            $category = $this->categories_model->get($id);
            if (!$category) { show_404(); }
            $data['category'] = $category;
            $data['title']    = _l('edit_category');
        } else {
            if (staff_cant('create', 'equipments')) {
                access_denied('equipments');
            }
            $data['title'] = _l('new_category');
        }

        $data['groups']    = $this->db->order_by('name', 'ASC')->get(db_prefix() . 'items_groups')->result_array();
        $data['subgroups'] = $this->db->select('s.*, g.name as group_name')
            ->from(db_prefix() . 'items_subgroups s')
            ->join(db_prefix() . 'items_groups g', 'g.id = s.group_id', 'left')
            ->order_by('g.name', 'ASC')->order_by('s.name', 'ASC')
            ->get()->result_array();
        $this->load->view('admin/categories/category', $data);
    }

    public function add_edit_category()
    {
        if (!$this->input->post()) {
            redirect(admin_url('equipments/categories'));
        }

        if (staff_cant('edit', 'equipments')) {
            access_denied('equipments');
        }

        $id   = $this->input->post('id');
        $data = [
            'name'        => $this->input->post('name'),
            'code'        => $this->input->post('code'),
            'sort_order'  => (int) $this->input->post('sort_order'),
            'subgroup_id' => (int) $this->input->post('subgroup_id'),
        ];

        if ($id) {
            $success = $this->categories_model->update($id, $data);
        } else {
            $data['datecreated'] = date('Y-m-d H:i:s');
            $id      = $this->categories_model->add($data);
            $success = (bool) $id;
        }

        $message = $success ? _l('category_saved') : _l('category_save_failed');

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success, 'message' => $message, 'id' => $id]);
            return;
        }

        set_alert($success ? 'success' : 'danger', $message);
        redirect(admin_url('equipments/categories/list_categories' . ($success ? '/' . $id : '')));
    }

    public function delete_category($id)
    {
        if (staff_cant('delete', 'equipments')) {
            access_denied('equipments');
        }

        $success = $this->categories_model->delete($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $success]);
            return;
        }

        if ($success) { set_alert('success', _l('category_deleted')); }
        redirect(admin_url('equipments/categories'));
    }

    public function get_category($id)
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) { ajax_access_denied(); }
        echo json_encode($this->categories_model->get($id));
    }

    public function get_category_data_ajax($id)
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) { echo _l('access_denied'); die; }

        $category = $this->categories_model->get($id);
        if (!$category) { echo _l('category_not_found'); die; }

        $checklist_tabs = [];

        if (is_module_active('inspections')) {
            $rows = $this->db
                ->where('category_id', $id)
                ->order_by('section_order', 'ASC')
                ->order_by('item_order', 'ASC')
                ->get(db_prefix() . 'inspection_checklist_items')->result_array();

            foreach ($rows as $row) {
                $checklist_tabs[$row['tab_key']][] = $row;
            }
        }

        $data['category']       = $category;
        $data['totalItems']     = total_rows(db_prefix() . 'items', ['category_id' => $id]);
        $data['checklist_tabs'] = $checklist_tabs;

        $this->load->view('admin/categories/category_preview_template', $data);
    }
}

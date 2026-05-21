<?php defined('BASEPATH') or exit('No direct script access allowed');

class Myequipments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('equipments/Myequipments_model', 'myequipments_model');
    }

    public function index($id = '')
    {
        $this->list_myequipments($id);
    }

    public function list_myequipments($id = '')
    {
        if (staff_cant('view', 'myequipments') && !get_client_user_id()) {
            access_denied('equipments');
        }

        $data['title']             = _l('my_equipment_units');
        $data['bodyclass']         = 'myequipments-page';
        $data['myequipments_table'] = App_table::find('myequipments');
        $data['equipment_statuses'] = $this->myequipments_model->get_statuses();

        if (is_numeric($id) && $id > 0) {
            $myequipment = $this->myequipments_model->get($id);
            if ($myequipment) {
                $data['myequipment'] = $myequipment;
            }
        }

        $this->load->view('admin/myequipments/manage', $data);
    }

    public function table()
    {
        App_table::find('myequipments')->output([]);
    }

    public function myequipment($id = '')
    {
        if ($id) {
            $myequipment = $this->myequipments_model->get($id);
            if (!$myequipment) { show_404(); }
            if (!can_do_on_entity('edit', 'myequipments', (int)$myequipment->client_id, 'customer')) {
                access_denied('equipments');
            }
            $data['myequipment'] = $myequipment;
            $data['title']       = _l('edit_myequipment');
        } else {
            if (staff_cant('create', 'myequipments')) {
                access_denied('equipments');
            }
            $data['title']           = _l('register_my_unit');
            $data['prefill_item_id'] = (int) ($this->input->get('item_id') ?: 0);
        }

        $data['items']   = $this->db->select('id, description')->order_by('description', 'ASC')->get(db_prefix() . 'items')->result_array();
        $data['clients'] = $this->clients_model->get('', [db_prefix() . 'clients.active' => 1]);
        $this->load->view('admin/myequipments/myequipment', $data);
    }

    public function add_edit_myequipment()
    {
        if (!$this->input->post()) {
            redirect(admin_url('equipments/myequipments'));
        }

        $id       = $this->input->post('id');
        $postData = $this->input->post();

        $allowed = [
            'client_id', 'item_id', 'customer_equipment_name',
            'unit_code', 'reference', 'latest_cert_number',
            'serial_number', 'location', 'capacity',
            'brand', 'model_type', 'procurement_year',
            'cert_expired_date', 'notes', 'status',
        ];
        $data = array_intersect_key($postData, array_flip($allowed));

        if ($id) {
            $existing = $this->myequipments_model->get($id);
            if (!$existing || !can_do_on_entity('edit', 'myequipments', (int)$existing->client_id, 'customer')) {
                access_denied('equipments');
            }
            $success = $this->myequipments_model->update($id, $data);
            if ($success) { set_alert('success', _l('updated_successfully', _l('myequipment_lowercase'))); }
            redirect(admin_url('equipments/myequipments/' . $id));
        } else {
            $client_id = (int)($data['client_id'] ?? 0);
            if (!can_do_on_entity('create', 'myequipments', $client_id, 'customer')) {
                access_denied('equipments');
            }
            $newid = $this->myequipments_model->add($data);
            if ($newid) { set_alert('success', _l('added_successfully', _l('myequipment_lowercase'))); }
            redirect(admin_url('equipments/myequipments/' . ($newid ?: '')));
        }
    }

    public function delete_myequipment($id)
    {
        $myequipment = $this->myequipments_model->get($id);
        if (!$myequipment || !can_do_on_entity('delete', 'myequipments', (int)$myequipment->client_id, 'customer')) {
            access_denied('equipments');
        }
        $success = $this->myequipments_model->delete($id);
        if ($success) { set_alert('success', _l('deleted_successfully')); }
        redirect(admin_url('equipments/myequipments'));
    }

    public function get_myequipment_data_ajax($id, $to_return = false)
    {
        $myequipment = $this->myequipments_model->get($id);
        if (!$myequipment) { echo _l('myequipment_not_found'); die; }
        if (!$this->_can_view_myequipment($myequipment)) {
            echo _l('access_denied'); die;
        }

        $data['myequipment']          = $myequipment;
        $data['myequipment_statuses'] = $this->myequipments_model->get_statuses();
        $data['activity']             = $this->myequipments_model->get_activity($id);
        $data['totalNotes']           = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'myequipment']);
        $data['members']              = $this->staff_model->get('', ['active' => 1]);

        if ($to_return == false) {
            $this->load->view('admin/myequipments/myequipment_preview_template', $data);
        } else {
            return $this->load->view('admin/myequipments/myequipment_preview_template', $data, true);
        }
    }

    public function get_jobs_table($myequipment_id)
    {
        if (!$this->input->is_ajax_request()) { ajax_access_denied(); }

        $pfx = db_prefix();

        $aColumns = [
            '1',
            db_prefix() . 'jobs.id as job_id',
            db_prefix() . 'jobs.name as job_name',
            'i.description as item_name',
            'ce.unit_code',
            'ce.serial_number',
            'ce.location',
            'ce.cert_expired_date',
        ];

        $sTable       = $pfx . 'jobs';
        $sIndexColumn = 'id';

        $join = [
            'LEFT JOIN ' . $pfx . 'customer_equipment ce ON ce.id = ' . $pfx . 'jobs.customer_equipment_id',
            'LEFT JOIN ' . $pfx . 'items i ON i.id = ce.item_id',
        ];

        $where = ['AND ce.id = ' . (int)$myequipment_id];

        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row   = [];
            $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['job_id'] . '"><label></label></div>';
            $row[] = '<a href="' . admin_url('jobs/list_jobs/' . $aRow['job_id']) . '" onclick="init_job(' . $aRow['job_id'] . '); return false;">' . $aRow['job_id'] . '</a>';
            $row[] = e($aRow['job_name'] ?? '');
            $row[] = e($aRow['item_name'] ?? '');
            $row[] = e($aRow['unit_code'] ?? '');
            $row[] = e($aRow['serial_number'] ?? '');
            $row[] = e($aRow['location'] ?? '');
            $row[] = e($aRow['cert_expired_date'] ?? '');
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function get_history_table($myequipment_id)
    {
        if (!$this->input->is_ajax_request()) { ajax_access_denied(); }

        $pfx = db_prefix();

        $aColumns = [
            '1',
            'r.formatted_number as rfq_no',
            'sv.company as surveyor_name',
            'q.formatted_number as quo_no',
            'o.formatted_number as ord_no',
            'p.formatted_number as prg_no',
            'ins.formatted_number as ins_no',
            'ins.date as ins_date',
        ];

        $sTable       = $pfx . 'jobs j';
        $sIndexColumn = 'j.id';

        $join = [
            'LEFT JOIN ' . $pfx . 'programs p ON p.id = j.rel_id AND j.rel_type = \'program\'',
            'LEFT JOIN ' . $pfx . 'orders o ON o.id = p.order_id',
            'LEFT JOIN ' . $pfx . 'quotations q ON q.id = o.quotation_id',
            'LEFT JOIN ' . $pfx . 'rfqs r ON r.id = q.rfq_id',
            'LEFT JOIN ' . $pfx . 'clients sv ON sv.userid = r.surveyor_id',
            'LEFT JOIN ' . $pfx . 'inspection_jobs ij ON ij.job_id = j.id',
            'LEFT JOIN ' . $pfx . 'inspections ins ON ins.id = ij.inspection_id',
        ];

        $where = ['AND j.customer_equipment_id = ' . (int)$myequipment_id];

        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['r.id as rfq_id', 'q.id as quo_id', 'o.id as ord_id', 'p.id as prg_id', 'ins.id as ins_id']);
        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row   = [];
            $row[] = '<div class="checkbox"><input type="checkbox"><label></label></div>';
            $row[] = $aRow['rfq_id'] ? '<a href="' . admin_url('rfqs/list_rfqs/' . $aRow['rfq_id']) . '">' . e($aRow['rfq_no']) . '</a>' : '—';
            $row[] = e($aRow['surveyor_name'] ?? '—');
            $row[] = $aRow['quo_id'] ? '<a href="' . admin_url('quotations/list_quotations/' . $aRow['quo_id']) . '">' . e($aRow['quo_no']) . '</a>' : '—';
            $row[] = $aRow['ord_id'] ? '<a href="' . admin_url('orders/list_orders/' . $aRow['ord_id']) . '">' . e($aRow['ord_no']) . '</a>' : '—';
            $row[] = $aRow['prg_id'] ? '<a href="' . admin_url('programs/list_programs/' . $aRow['prg_id']) . '">' . e($aRow['prg_no']) . '</a>' : '—';
            $row[] = $aRow['ins_id'] ? '<a href="' . admin_url('inspections/list_inspections/' . $aRow['ins_id']) . '">' . e($aRow['ins_no']) . '</a>' : '—';
            $row[] = $aRow['ins_date'] ? e(_d($aRow['ins_date'])) : '—';
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    private function _can_view_myequipment(object $ce): bool
    {
        if (is_admin()) { return true; }
        $me = get_staff(get_staff_user_id());
        if (!$me) { return false; }

        if (($me->client_type ?? '') === 'customer') {
            return can_do_on_entity('view', 'myequipments', (int)$ce->client_id, 'customer');
        }

        if (($me->client_type ?? '') === 'surveyor' && staff_can('view', 'myequipments')) {
            $sv_ids = get_entity_client_ids((int)$me->client_id, 'surveyor');
            $sv_str = implode(',', array_map('intval', $sv_ids));
            // Surveyor: only if this specific equipment is in one of their order items (has received PO)
            $row = $this->db->query(
                "SELECT COUNT(*) as c
                 FROM " . db_prefix() . "order_items oi
                 JOIN " . db_prefix() . "orders o ON o.id = oi.order_id
                 WHERE o.surveyor_id IN ($sv_str)
                 AND oi.customer_equipment_id = " . (int)$ce->id
            )->row();
            return $row && (int)$row->c > 0;
        }

        return staff_can('view', 'myequipments');
    }

    public function add_note($rel_id)
    {
        $myequipment = $this->myequipments_model->get($rel_id);
        if ($this->input->post() && $myequipment && $this->_can_view_myequipment($myequipment)) {
            $this->misc_model->add_note($this->input->post(), 'myequipment', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id = null)
    {
        if (!$id) { return; }
        $myequipment = $this->myequipments_model->get($id);
        if ($myequipment && $this->_can_view_myequipment($myequipment)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'myequipment');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }
}

<?php

use app\services\equipments\EquipmentsPipeline;

defined('BASEPATH') or exit('No direct script access allowed');

class Equipments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('apps/apps_format');
        $this->load->model('equipments_model');
    }

    /* Get all equipments in case user go on index page */
    public function index($id = '')
    {
        $this->list_equipments($id);
    }

    /* List all equipments datatables */
    public function list_equipments($id = '')
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments') && get_option('allow_staff_view_equipments_assigned') == '0') {
            access_denied('equipments');
        }

        $isPipeline = $this->session->userdata('equipment_pipeline') == 'true';

        $data['equipment_statuses'] = $this->equipments_model->get_statuses();
        $data['equipments_table'] = App_table::find('equipments');
        
        if ($isPipeline && !$this->input->get('status') && !$this->input->get('filter')) {
            $data['title']           = _l('equipments_pipeline');
            $data['bodyclass']       = 'equipments-pipeline equipments-total-manual';
            $data['switch_pipeline'] = false;

            if (is_numeric($id)) {
                $data['equipmentid'] = $id;
            } else {
                $data['equipmentid'] = $this->session->flashdata('equipmentid');
            }

            $this->load->view('admin/equipments/pipeline/manage', $data);
        } else {

            // Pipeline was initiated but user click from home page and need to show table only to filter
            if ($this->input->get('status') || $this->input->get('filter') && $isPipeline) {
                $this->pipeline(0, true);
            }

            $data['equipmentid']            = $id;
            $data['switch_pipeline']       = true;
            $data['title']                 = _l('equipments');
            $data['bodyclass']             = 'equipments-total-manual';
            $data['equipments_years']       = $this->equipments_model->get_equipments_years();
        
            $this->load->view('admin/equipments/manage', $data);
        }
    }

    public function table($client_id = '')
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments') && get_option('allow_staff_view_equipments_assigned') == '0') {
            ajax_access_denied();
        }

        App_table::find('equipments')->output([
            'client_id' => $client_id,
        ]);
    }

    public function widget_table()
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments') && get_option('allow_staff_view_equipments_assigned') == '0') {
            ajax_access_denied();
        }

        App_table::find('equipments_widget')->output([]);
    }

    /* Add new equipment or update existing */
    public function equipment($id = '')
    {
        if ($this->input->post()) {
            $equipment_data = $this->input->post();

            $save_and_send_later = false;
            if (isset($equipment_data['save_and_send_later'])) {
                unset($equipment_data['save_and_send_later']);
                $save_and_send_later = true;
            }

            if ($id == '') {
                if (staff_cant('create', 'equipments')) {
                    access_denied('equipments');
                }
                $id = $this->equipments_model->add($equipment_data);

                if ($id) {
                    set_alert('success', _l('added_successfully', _l('equipment')));

                    $redUrl = admin_url('equipments/list_equipments/' . $id);

                    if ($save_and_send_later) {
                        $this->session->set_userdata('send_later', true);
                        // die(redirect($redUrl));
                    }

                    redirect(
                        !$this->set_equipment_pipeline_autoload($id) ? $redUrl : admin_url('equipments/list_equipments/')
                    );
                }
            } else {
                if (staff_cant('edit', 'equipments')) {
                    access_denied('equipments');
                }
                $_me_sc_edit = get_staff(get_staff_user_id());
                if (($_me_sc_edit->client_type ?? '') === 'surveyor') {
                    $_sc_chk = $this->equipments_model->get((int)$id);
                    if (!$_sc_chk || (int)$_sc_chk->status !== EQUIPMENT_STATUS_DRAFT) {
                        access_denied('equipments');
                    }
                }
                $success = $this->equipments_model->update($equipment_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('equipment')));
                }
                if ($this->set_equipment_pipeline_autoload($id)) {
                    redirect(admin_url('equipments/list_equipments/'));
                } else {
                    redirect(admin_url('equipments/list_equipments/' . $id));
                }
            }
        }
        if ($id == '') {
            $title = _l('create_new_equipment');
        } else {
            $equipment = $this->equipments_model->get($id);

            if (!$equipment || !user_can_view_equipment($id)) {
                blank_page(_l('equipment_not_found'));
            }

            $_me_sc_view = get_staff(get_staff_user_id());
            if (($_me_sc_view->client_type ?? '') === 'surveyor' && (int)$equipment->status !== EQUIPMENT_STATUS_DRAFT) {
                access_denied('equipments');
            }
            $equipment->terms      = $equipment->terms      ?? '';
            $equipment->clientnote = $equipment->clientnote ?? '';
            $equipment->adminnote  = $equipment->adminnote  ?? '';
            $data['equipment'] = $equipment;
            $data['edit']     = true;
            $title            = _l('edit', _l('equipment'));
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }
        if ($this->input->get('program_id')) {
            $data['prefill_program_id'] = (int)$this->input->get('program_id');
            $data['preset_jobs']        = $this->equipments_model->get_program_jobs_for_preset(
                $data['prefill_program_id']
            );
        }

        $this->load->model('invoice_items_model');

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['staff']              = $this->staff_model->get('', ['active' => 1]);
        $data['equipment_statuses'] = $this->equipments_model->get_statuses();
        $data['groups']             = $this->equipments_model->get_groups();
        $data['subgroups']          = $this->equipments_model->get_subgroups();
        $this->load->model('equipments/Categories_model', 'categories_model');
        $data['categories']         = $this->categories_model->get();
        $data['taxes']              = $this->db->get(db_prefix() . 'taxes')->result_array();
        $data['title']              = $title;
        $this->load->view('admin/equipments/equipment', $data);
    }

    public function clear_signature($id)
    {
        if (staff_can('delete', 'equipments') && user_can_view_equipment($id)) {
            $this->equipments_model->clear_signature($id);
        }

        redirect(admin_url('equipments/list_equipments/' . $id));
    }

    public function update_number_settings($id)
    {
        $response = [
            'success' => false,
            'message' => '',
        ];

        if (staff_can('edit', 'equipments') && user_can_view_equipment($id)) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'equipments', [
                'prefix' => $this->input->post('prefix'),
            ]);

            if ($this->db->affected_rows() > 0) {
                $this->equipments_model->save_formatted_number($id);

                $response['success'] = true;
                $response['message'] = _l('updated_successfully', _l('equipment'));
            }
        }

        echo json_encode($response);
        die;
    }

    public function validate_equipment_number()
    {
        $isedit          = $this->input->post('isedit');
        $number          = $this->input->post('number');
        $date            = $this->input->post('date');
        $original_number = $this->input->post('original_number');
        $number          = trim($number);
        $number          = ltrim($number, '0');

        if ($isedit == 'true') {
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }

        if (total_rows(db_prefix() . 'equipments', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->equipments_model->delete_attachment($id);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Get all equipment data used when user click on equipment number in a datatable left side*/
    public function get_equipment_data_ajax($id, $to_return = false)
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments') && get_option('allow_staff_view_equipments_assigned') == '0') {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die('No equipment found');
        }

        $equipment = $this->equipments_model->get($id);

        if (!$equipment || !user_can_view_equipment($id)) {
            echo _l('equipment_not_found');
            die;
        }

        // Set catalog item id alias and defaults for schedule-based template
        $equipment->id             = $equipment->id             ?? $equipment->itemid;
        $equipment->status         = $equipment->status         ?? EQUIPMENT_STATUS_ACTIVE;
        $equipment->sent           = $equipment->sent           ?? 0;
        $equipment->client_id       = $equipment->client_id       ?? 0;
        $equipment->project_id     = $equipment->project_id     ?? 0;
        $equipment->sale_agent     = $equipment->sale_agent     ?? 0;
        $equipment->signature      = $equipment->signature      ?? null;
        $equipment->hash           = $equipment->hash           ?? '';
        $equipment->expirydate     = $equipment->expirydate     ?? null;
        $equipment->datesend       = $equipment->datesend       ?? null;
        $equipment->include_shipping = $equipment->include_shipping ?? 0;
        $equipment->formatted_number = $equipment->formatted_number ?? $equipment->code ?? '';
        $equipment->date           = $equipment->date           ?? null;
        $equipment->attachments    = $equipment->attachments    ?? [];
        $equipment->terms          = $equipment->terms          ?? '';
        $equipment->clientnote     = $equipment->clientnote     ?? '';
        $equipment->adminnote      = $equipment->adminnote      ?? '';

        $equipment->date       = _d($equipment->date);
        $equipment->expirydate = _d($equipment->expirydate);
        if ($equipment->sent == 0) {
            $template_name = 'equipment_send_to_customer';
        } else {
            $template_name = 'equipment_send_to_customer_already_sent';
        }

        // Use prepare() with module hint so get_mail_template_path resolves to module path
        $this->load->model('emails_model');
        $_contact = $this->clients_model->get_contact(get_primary_contact_user_id($equipment->client_id));
        $_email   = $_contact ? $_contact->email : '';
        $data['template'] = $this->app_mail_template->prepare($_email, $template_name, ['equipments']);
        $_slug = $this->app_mail_template->get_default_property_value('slug', $template_name, ['equipments']);
        $_tpl  = $this->emails_model->get(['slug' => $_slug, 'language' => 'english'], 'row');
        $data['template_name']        = $_slug;
        $data['template_system_name'] = $_tpl->name;
        $data['template_id']          = $_tpl->emailtemplateid;
        $data['template_disabled']    = $_tpl->active == 0;

        $data['activity']          = $this->equipments_model->get_equipment_activity($id);
        $data['equipment']          = $equipment;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['equipment_statuses'] = $this->equipments_model->get_statuses();
        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'equipment']);

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }

        if ($to_return == false) {
            $this->load->view('admin/equipments/equipment_preview_template', $data);
        } else {
            return $this->load->view('admin/equipments/equipment_preview_template', $data, true);
        }
    }

    public function get_equipments_total()
    {
        if ($this->input->post()) {
            $data['totals'] = $this->equipments_model->get_equipments_total($this->input->post());

            $this->load->model('currencies_model');

            if (!$this->input->post('customer_id')) {
                $multiple_currencies = call_user_func('is_using_multiple_currencies', db_prefix() . 'equipments');
            } else {
                $multiple_currencies = call_user_func('is_client_using_multiple_currencies', $this->input->post('customer_id'), db_prefix() . 'equipments');
            }

            if ($multiple_currencies) {
                $data['currencies'] = $this->currencies_model->get();
            }

            $data['equipments_years'] = $this->equipments_model->get_equipments_years();

            if (
                count($data['equipments_years']) >= 1
                && !\app\services\utilities\Arr::inMultidimensional($data['equipments_years'], 'year', date('Y'))
            ) {
                array_unshift($data['equipments_years'], ['year' => date('Y')]);
            }

            $data['_currency'] = $data['totals']['currencyid'];
            unset($data['totals']['currencyid']);
            $this->load->view('admin/equipments/equipments_total_template', $data);
        }
    }

    public function get_jobs_table($equipment_id)
    {
        if (!$this->input->is_ajax_request() || !user_can_view_equipment((int)$equipment_id)) {
            ajax_access_denied();
        }

        $pfx = db_prefix();

        $aColumns = [
            '1',
            'j.id as job_id',
            'j.name as job_name',
            'i.description as item_name',
            'ce.unit_code',
            'ce.serial_number',
            'ce.location',
            'ce.cert_expired_date',
        ];

        $sTable       = $pfx . 'equipment_jobs sj';
        $sIndexColumn = 'sj.job_id';

        $join = [
            'LEFT JOIN ' . $pfx . 'jobs j ON j.id = sj.job_id',
            'LEFT JOIN ' . $pfx . 'customer_equipment ce ON ce.id = j.customer_equipment_id',
            'LEFT JOIN ' . $pfx . 'items i ON i.id = ce.item_id',
        ];

        $where = ['AND sj.equipment_id = ' . (int)$equipment_id];

        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['job_id']);
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

    public function add_note($rel_id)
    {
        if ($this->input->post() && user_can_view_equipment($rel_id)) {
            $this->misc_model->add_note($this->input->post(), 'equipment', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if (user_can_view_equipment($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'equipment');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function mark_action_status($status, $id)
    {
        if (!user_can_view_equipment($id)) {
            access_denied('equipments');
        }
        $_me_ma  = get_staff(get_staff_user_id());
        $_ct_ma  = $_me_ma ? ($_me_ma->client_type ?? '') : '';
        $_status = (int) $status;
        if ($_ct_ma === 'surveyor') {
            if (!in_array($_status, [EQUIPMENT_STATUS_DRAFT, EQUIPMENT_STATUS_ACTIVE])) {
                access_denied('equipments');
            }
        } elseif ($_ct_ma === 'customer') {
            if (!in_array($_status, [EQUIPMENT_STATUS_INACTIVE, EQUIPMENT_STATUS_ACTIVE, EQUIPMENT_STATUS_EXPIRED])) {
                access_denied('equipments');
            }
        } elseif (staff_cant('edit', 'equipments')) {
            access_denied('equipments');
        }
        $success = $this->equipments_model->mark_action_status($status, $id, $_ct_ma === 'customer');

        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'success'     => (bool) $success,
                'message'     => $success ? _l('equipment_status_changed_success') : _l('equipment_status_changed_fail'),
                'status_html' => $success ? format_equipment_status($status, 'mtop5 inline-block') : '',
            ]);
            return;
        }

        if ($success) {
            set_alert('success', _l('equipment_status_changed_success'));
        } else {
            set_alert('danger', _l('equipment_status_changed_fail'));
        }
        if ($this->set_equipment_pipeline_autoload($id)) {
            redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('equipments/list_equipments/' . $id));
        }
    }

    public function send_expiry_reminder($id)
    {
        $canView = user_can_view_equipment($id);
        if (!$canView) {
            access_denied('Equipments');
        } else {
            if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments') && $canView == false) {
                access_denied('Equipments');
            }
        }

        $success = $this->equipments_model->send_expiry_reminder($id);
        if ($success) {
            set_alert('success', _l('sent_expiry_reminder_success'));
        } else {
            set_alert('danger', _l('sent_expiry_reminder_fail'));
        }
        if ($this->set_equipment_pipeline_autoload($id)) {
            redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('equipments/list_equipments/' . $id));
        }
    }

    /* Send equipment to email */
    public function send_to_email($id)
    {
        $canView = user_can_view_equipment($id);
        if (!$canView) {
            access_denied('equipments');
        } else {
            if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments') && $canView == false) {
                access_denied('equipments');
            }
        }

        try {
            $success = $this->equipments_model->send_equipment_to_client($id, '', $this->input->post('attach_pdf'), $this->input->post('cc'));
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('equipment_sent_to_client_success'));
        } else {
            set_alert('danger', _l('equipment_sent_to_client_fail'));
        }
        if ($this->set_equipment_pipeline_autoload($id)) {
            redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('equipments/list_equipments/' . $id));
        }
    }


    public function copy($id)
    {
        if (staff_cant('create', 'equipments')) {
            access_denied('equipments');
        }
        if (!$id || !user_can_view_equipment($id)) {
            access_denied('equipments');
        }
        $new_id = $this->equipments_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('equipment_copied_successfully'));
            if ($this->set_equipment_pipeline_autoload($new_id)) {
                redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('equipments/equipment/' . $new_id));
            }
        }
        set_alert('danger', _l('equipment_copied_fail'));
        if ($this->set_equipment_pipeline_autoload($id)) {
            redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('equipments/equipment/' . $id));
        }
    }

    /* Delete equipment */
    public function delete($id)
    {
        if (staff_cant('delete', 'equipments')) {
            access_denied('equipments');
        }
        if (!$id || !user_can_view_equipment($id)) {
            redirect(admin_url('equipments/list_equipments'));
        }
        $success = $this->equipments_model->delete($id);
        if ($success == true) {
            set_alert('success', _l('deleted', _l('equipment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('equipment_lowercase')));
        }
        redirect(admin_url('equipments/list_equipments'));
    }

    public function clear_acceptance_info($id)
    {
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'equipments', get_acceptance_info_array(true));
        }

        redirect(admin_url('equipments/list_equipments/' . $id));
    }

    /* Generates equipment PDF and senting to email  */
    public function pdf($id)
    {
        $canView = user_can_view_equipment($id);
        if (!$canView) {
            access_denied('Equipments');
        } else {
            if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments') && $canView == false) {
                access_denied('Equipments');
            }
        }
        if (!$id) {
            redirect(admin_url('equipments/list_equipments'));
        }
        $equipment        = $this->equipments_model->get($id);
        $equipment_number = format_equipment_number($equipment->id);

        try {
            $pdf = equipment_pdf($equipment);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $fileNameHookData = hooks()->apply_filters('equipment_file_name_admin_area', [
                            'file_name' => mb_strtoupper(slug_it($equipment_number)) . '.pdf',
                            'equipment'  => $equipment,
                        ]);

        $pdf->Output($fileNameHookData['file_name'], $type);
    }

    // Pipeline
    public function get_pipeline()
    {
        if (staff_can('view',  'equipments') || staff_can('view_own',  'equipments') || get_option('allow_staff_view_equipments_assigned') == '1') {
            $data['equipment_statuses'] = $this->equipments_model->get_statuses();
            $this->load->view('admin/equipments/pipeline/pipeline', $data);
        }
    }

    public function pipeline_open($id)
    {
        $canView = user_can_view_equipment($id);
        if (!$canView) {
            access_denied('Equipments');
        } else {
            if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments') && $canView == false) {
                access_denied('Equipments');
            }
        }

        $data['id']       = $id;
        $data['equipment'] = $this->get_equipment_data_ajax($id, true);
        $this->load->view('admin/equipments/pipeline/equipment', $data);
    }

    public function update_pipeline()
    {
        if (staff_can('edit',  'equipments')) {
            $this->equipments_model->update_pipeline($this->input->post());
        }
    }

    public function pipeline($set = 0, $manual = false)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
            'equipment_pipeline' => $set,
        ]);
        if ($manual == false) {
            redirect(admin_url('equipments/list_equipments'));
        }
    }

    public function pipeline_load_more()
    {
        $status = $this->input->get('status');
        $page   = $this->input->get('page');

        $equipments = (new EquipmentsPipeline($status))
            ->search($this->input->get('search'))
            ->sortBy(
                $this->input->get('sort_by'),
                $this->input->get('sort')
            )
            ->page($page)->get();

        foreach ($equipments as $equipment) {
            $this->load->view('admin/equipments/pipeline/_kanban_card', [
                'equipment' => $equipment,
                'status'   => $status,
            ]);
        }
    }

    public function set_equipment_pipeline_autoload($id)
    {
        if ($id == '') {
            return false;
        }

        if ($this->session->has_userdata('equipment_pipeline')
                && $this->session->userdata('equipment_pipeline') == 'true') {
            $this->session->set_flashdata('equipmentid', $id);

            return true;
        }

        return false;
    }

    public function equipments_report()
    {
        if (staff_cant('view', 'equipments') && staff_cant('view_own', 'equipments')) {
            access_denied('equipments');
        }

        if ($this->input->is_ajax_request()) {
            $select = [
                'number',
                get_sql_select_client_company(),
                'YEAR(date) as year',
                'date',
                'expirydate',
                'reference_no',
                'status',
            ];

            $where              = [];
            // Date filter
            $months_report      = $this->input->post('report_months');
            $custom_date_select = '';
            if ($months_report != '') {
                if (is_numeric($months_report)) {
                    if ($months_report == '1') {
                        $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                        $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                    } else {
                        $months_report--;
                        $beginMonth = date('Y-m-01', strtotime("-{$months_report} MONTH"));
                        $endMonth   = date('Y-m-t');
                    }
                    $custom_date_select = 'AND (date BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
                } elseif ($months_report == 'this_month') {
                    $custom_date_select = 'AND (date BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
                } elseif ($months_report == 'this_year') {
                    $custom_date_select = 'AND (date BETWEEN "' . date('Y-01-01') . '" AND "' . date('Y-12-31') . '")';
                } elseif ($months_report == 'last_year') {
                    $y = date('Y', strtotime('last year'));
                    $custom_date_select = 'AND (date BETWEEN "' . $y . '-01-01" AND "' . $y . '-12-31")';
                } elseif ($months_report == 'custom') {
                    $from = to_sql_date($this->input->post('report_from'));
                    $to   = to_sql_date($this->input->post('report_to'));
                    if ($from && $to) {
                        $custom_date_select = 'AND (date BETWEEN "' . $from . '" AND "' . $to . '")';
                    }
                }
                if ($custom_date_select != '') {
                    array_push($where, $custom_date_select);
                }
            }

            if ($this->input->post('equipment_status')) {
                $statuses  = $this->input->post('equipment_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $s) {
                        if ($s != '') {
                            array_push($_statuses, $this->db->escape_str($s));
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('sale_agent_equipments')) {
                $agents  = $this->input->post('sale_agent_equipments');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $this->db->escape_str($agent));
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'equipments';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'equipments.client_id',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'client_id',
                db_prefix() . 'equipments.id',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row    = [];
                $row[]  = format_equipment_number($aRow['id']);
                $row[]  = e($aRow['company'] ?? $aRow['deleted_customer_name'] ?? '');
                $row[]  = $aRow['year'];
                $row[]  = _d($aRow['date']);
                $row[]  = _d($aRow['expirydate']);
                $row[]  = e($aRow['reference_no']);
                $row[]  = format_equipment_status($aRow['status']);
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            return;
        }

        // Non-AJAX: render view
        $data['equipment_statuses']     = $this->equipments_model->get_statuses();
        $data['equipments_sale_agents'] = $this->db->select('sale_agent')->distinct()->from(db_prefix() . 'equipments')->where('sale_agent !=', 0)->get()->result_array();
        $data['title']                 = _l('equipments_report');
        $this->load->view('equipments/admin/equipments/equipments_report', $data);
    }

    public function get_job_data($id = '')
    {
        if (!$id) { show_404(); }
        $job = $this->db->get_where(db_prefix() . 'jobs', ['id' => (int) $id])->row();
        if (!$job) { echo json_encode(null); return; }
        echo json_encode([
            'id'                    => $job->id,
            'description'           => $job->name,
            'customer_equipment_id' => $job->customer_equipment_id ?? null,
        ]);
    }

    public function get_equipment_data($id = '')
    {
        if (!$id) { show_404(); }
        $row = $this->db
            ->select('ce.id, ce.unit_code, ce.serial_number, ce.location, ce.cert_expired_date, i.description as item_name')
            ->from(db_prefix() . 'customer_equipment ce')
            ->join(db_prefix() . 'items i', 'i.id = ce.item_id', 'left')
            ->where('ce.id', (int) $id)
            ->get()->row_array();
        echo json_encode($row ?: []);
    }

    public function get_due_date()
    {
        if ($this->input->post()) {
            $date    = $this->input->post('date');
            $duedate = '';
            if (get_option('equipment_due_after') != 0) {
                $date    = to_sql_date($date);
                $d       = date('Y-m-d', strtotime('+' . get_option('equipment_due_after') . ' DAY', strtotime($date)));
                $duedate = _d($d);
                echo $duedate;
            }
        }
    }
}

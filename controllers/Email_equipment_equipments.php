<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Email_equipment_equipment extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('email_schedule_model');
    }

    public function create($id)
    {
        if (staff_cant('create', 'equipments')) {
            ajax_access_denied();
        }

        $this->load->model('equipments_model');

        if ($this->input->post()) {
            $data = $this->input->post();

            $this->email_schedule_model->create($id, 'equipment', [
                'equipmentd_at' => to_sql_date($data['equipmentd_at'], true),
                'cc'           => $data['cc'],
                'contacts'     => $data['sent_to'],
                'attach_pdf'   => isset($data['attach_pdf']) ? 1 : 0,
                'template'     => 'equipment_send_to_customer',
            ]);

            set_alert('success', _l('email_equipmentd_successfully'));
            redirect(admin_url('equipments/list_equipments/' . $id));
        }

        $data            = $this->equipmentData($id);
        $data['formUrl'] = admin_url('email_equipment_equipment/create/' . $id);
        $data['date']    = get_equipmentd_email_default_date();
        $this->load->view('admin/equipments/equipment', $data);
    }

    public function edit($id)
    {
        $equipment = $this->email_schedule_model->getById($id);
        $this->load->model('equipments_model');
        $data = $this->equipmentData($equipment->rel_id);
        if (staff_can('edit', 'equipments') || $data['equipment']->addedfrom == get_staff_user_id()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                $this->email_schedule_model->update($id, [
                    'equipmentd_at' => to_sql_date($data['equipmentd_at'], true),
                    'cc'           => $data['cc'],
                    'contacts'     => $data['sent_to'],
                    'attach_pdf'   => isset($data['attach_pdf']) ? 1 : 0,
                ]);

                set_alert('success', _l('email_equipmentd_successfully'));
                redirect(admin_url('equipments/list_equipments/' . $equipment->rel_id));
            }

            $data['equipment'] = $equipment;
            $data['formUrl']  = admin_url('email_equipment_equipment/edit/' . $id);
            $data['date']     = $equipment->equipmentd_at;

            $this->load->view('admin/equipments/equipment', $data);
        } else {
            ajax_access_denied();
        }
    }

    protected function equipmentData($id)
    {
        $equipment = $this->equipments_model->get($id);

        $data['equipment'] = $equipment;

        $this->load->model('emails_model');
        $template_name = 'equipment_send_to_customer';
        $slug          = $this->app_mail_template->get_default_property_value('slug', $template_name);
        $template      = $this->emails_model->get(['slug' => $slug, 'language' => 'english'], 'row');

        $data['template_disabled']    = $template->active == 0;
        $data['template_id']          = $template->emailtemplateid;
        $data['template_system_name'] = $template->name;

        return $data;
    }
}

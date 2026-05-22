<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Equipment_send_to_customer_already_sent extends App_mail_template
{
    protected $for = 'customer';

    protected $equipment;

    protected $contact;

    public $slug = 'equipment-already-send';

    public $rel_type = 'equipment';

    public function __construct($equipment, $contact, $cc = '')
    {
        parent::__construct();

        $this->equipment = $equipment;
        $this->contact = $contact;
        $this->cc      = $cc;
    }

    public function build()
    {
        if ($this->ci->input->post('email_attachments')) {
            $_other_attachments = $this->ci->input->post('email_attachments');
            foreach ($_other_attachments as $attachment) {
                $_attachment = $this->ci->equipments_model->get_attachments($this->equipment->id, $attachment);
                $this->add_attachment([
                                'attachment' => get_upload_path_by_type('equipment') . $this->equipment->id . '/' . $_attachment->file_name,
                                'filename'   => $_attachment->file_name,
                                'type'       => $_attachment->filetype,
                                'read'       => true,
                            ]);
            }
        }

        $this->to($this->contact->email)
        ->set_rel_id($this->equipment->id)
        ->set_merge_fields('client_merge_fields', $this->equipment->client_id, $this->contact->id)
        ->set_merge_fields('equipment_merge_fields', $this->equipment->id);
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Equipment_accepted_to_staff extends App_mail_template
{
    protected $for = 'staff';

    protected $equipment;

    protected $staff_email;

    protected $contact_id;

    public $slug = 'equipment-accepted-to-staff';

    public $rel_type = 'equipment';

    public function __construct($equipment, $staff_email, $contact_id)
    {
        parent::__construct();

        $this->equipment    = $equipment;
        $this->staff_email = $staff_email;
        $this->contact_id  = $contact_id;
    }

    public function build()
    {

        $this->to($this->staff_email)
        ->set_rel_id($this->equipment->id)
        ->set_merge_fields('client_merge_fields', $this->equipment->client_id, $this->contact_id)
        ->set_merge_fields('equipment_merge_fields', $this->equipment->id);
    }
}

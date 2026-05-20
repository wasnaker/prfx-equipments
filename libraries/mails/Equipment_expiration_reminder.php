<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Equipment_expiration_reminder extends App_mail_template
{
    protected $for = 'customer';

    protected $equipment;

    protected $contact;

    public $slug = 'equipment-expiry-reminder';

    public $rel_type = 'equipment';

    public function __construct($equipment, $contact)
    {
        parent::__construct();

        $this->equipment = $equipment;
        $this->contact  = $contact;

        // For SMS
        $this->set_merge_fields('client_merge_fields', $this->equipment->clientid, $this->contact['id']);
        $this->set_merge_fields('equipment_merge_fields', $this->equipment->id);
    }

    public function build()
    {
        $this->to($this->contact['email'])
        ->set_rel_id($this->equipment->id);
    }
}

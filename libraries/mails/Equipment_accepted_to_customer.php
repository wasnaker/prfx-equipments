<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Equipment_accepted_to_customer extends App_mail_template
{
    protected $for = 'customer';

    protected $equipment;

    protected $contact;

    public $slug = 'equipment-thank-you-to-customer';

    public $rel_type = 'equipment';

    public function __construct($equipment, $contact)
    {
        parent::__construct();

        $this->equipment = $equipment;
        $this->contact  = $contact;
    }

    public function build()
    {
        $this->to($this->contact['email'])
        ->set_rel_id($this->equipment->id)
        ->set_merge_fields('client_merge_fields', $this->equipment->client_id, $this->contact['id'])
        ->set_merge_fields('equipment_merge_fields', $this->equipment->id);
    }
}

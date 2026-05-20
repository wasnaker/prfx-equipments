<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Client merge fields available for the 'equipments' email template type.
 * Uses $for = 'client' so App_merge_fields::all() groups these under the
 * "Client" column alongside the core client_merge_fields entries.
 */
class Equipment_client_merge_fields extends App_merge_fields
{
    protected $for = 'client';

    public function build()
    {
        $av = ['equipments'];

        return [
            ['name' => 'Contact Firstname',    'key' => '{contact_firstname}',    'available' => $av],
            ['name' => 'Contact Lastname',     'key' => '{contact_lastname}',     'available' => $av],
            ['name' => 'Contact Phone Number', 'key' => '{contact_phonenumber}',  'available' => $av],
            ['name' => 'Contact Title',        'key' => '{contact_title}',        'available' => $av],
            ['name' => 'Contact Email',        'key' => '{contact_email}',        'available' => $av],
            ['name' => 'Client Company',       'key' => '{client_company}',       'available' => $av],
            ['name' => 'Client Phone Number',  'key' => '{client_phonenumber}',   'available' => $av],
            ['name' => 'Client Country',       'key' => '{client_country}',       'available' => $av],
            ['name' => 'Client City',          'key' => '{client_city}',          'available' => $av],
            ['name' => 'Client Zip',           'key' => '{client_zip}',           'available' => $av],
            ['name' => 'Client State',         'key' => '{client_state}',         'available' => $av],
            ['name' => 'Client Address',       'key' => '{client_address}',       'available' => $av],
            ['name' => 'Client Vat Number',    'key' => '{client_vat_number}',    'available' => $av],
            ['name' => 'Client ID',            'key' => '{client_id}',            'available' => $av],
            ['name' => 'Client Website',       'key' => '{client_website}',       'available' => $av],
        ];
    }
}

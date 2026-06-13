<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ─── Hook Registrations ────────────────────────────────────────────────────────

hooks()->add_action('after_email_templates', 'equipments_email_templates_section');

// ─── Register Email Templates on Install ──────────────────────────────────────

function equipments_register_email_templates()
{
    $CI = &get_instance();
    $CI->load->model('emails_model');

    $templates = [
        [
            'slug'     => 'equipment-registered',
            'name'     => 'Equipment Registered',
            'subject'  => 'New Equipment Registration',
            'message'  => '<p>Hi {staff_firstname},</p><p>A new equipment has been registered.</p>',
            'type'     => 'equipments',
            'language' => 'english',
        ],
    ];

    foreach ($templates as $template) {
        $exists = $CI->db->where('slug', $template['slug'])
                         ->where('language', $template['language'])
                         ->get(db_prefix() . 'emailtemplates')
                         ->num_rows();
        if ($exists === 0) {
            $CI->db->insert(db_prefix() . 'emailtemplates', $template);
        }
    }
}

// ─── Email Templates Section ──────────────────────────────────────────────────

function equipments_email_templates_section()
{
    $CI = &get_instance();
    $CI->load->model('emails_model');
    $data['equipment_email_templates'] = $CI->emails_model->get([
        'type'     => 'equipments',
        'language' => 'english',
    ]);
    $data['hasPermissionEdit'] = staff_can('edit', 'email_templates');
    $CI->load->view('equipments/admin/emails/equipment_email_templates', $data);
}

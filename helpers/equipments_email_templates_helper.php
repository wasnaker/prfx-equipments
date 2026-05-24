<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ─── Hook Registrations ────────────────────────────────────────────────────────

hooks()->add_action('after_email_templates', 'equipments_email_templates_section');

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

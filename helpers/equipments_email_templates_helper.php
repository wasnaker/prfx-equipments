<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Email template definitions for the Equipments module.
 *
 * CONVENTION: All module email templates MUST be defined here,
 * not in install.php or equipments.php.
 *
 * install.php calls equipments_register_email_templates() on activation.
 */

if (!function_exists('equipments_email_templates')) {

function equipments_email_templates(): array
{
    return [
        [
            'slug'    => 'equipment-send-to-client',
            'type'    => 'equipments',
            'name'    => 'Send Equipment to Customer',
            'subject' => 'Equipment #{equipment_number}',
            'message' => '<p>Dear {contact_firstname} {contact_lastname},</p><p>Please find the attached equipment <strong>#{equipment_number}</strong>.</p><p><strong>Equipment status:</strong> {equipment_status}</p><p>You can view the equipment here: <a href="{equipment_link}">{equipment_number}</a></p><p>We look forward to your response.</p><p>Kind Regards,</p><p>{email_signature}</p>',
            'active'  => 1,
        ],
        [
            'slug'    => 'equipment-already-send',
            'type'    => 'equipments',
            'name'    => 'Equipment Already Sent to Customer',
            'subject' => 'Equipment #{equipment_number}',
            'message' => '<p>Dear {contact_firstname} {contact_lastname},</p><p>Thank you for your equipment request.</p><p>You can view the equipment here: <a href="{equipment_link}">{equipment_number}</a></p><p>Please contact us for more information.</p><p>Kind Regards,</p><p>{email_signature}</p>',
            'active'  => 1,
        ],
        [
            'slug'    => 'equipment-declined-to-staff',
            'type'    => 'equipments',
            'name'    => 'Equipment Declined (Sent to Staff)',
            'subject' => 'Customer Declined Equipment',
            'message' => '<p>Hi,</p><p>Customer ({client_company}) declined equipment with number <strong>#{equipment_number}</strong>.</p><p>You can view the equipment here: <a href="{equipment_link}">{equipment_number}</a></p><p>{email_signature}</p>',
            'active'  => 1,
        ],
        [
            'slug'    => 'equipment-accepted-to-staff',
            'type'    => 'equipments',
            'name'    => 'Equipment Accepted (Sent to Staff)',
            'subject' => 'Customer Accepted Equipment',
            'message' => '<p>Hi,</p><p>Customer ({client_company}) accepted equipment with number <strong>#{equipment_number}</strong>.</p><p>You can view the equipment here: <a href="{equipment_link}">{equipment_number}</a></p><p>{email_signature}</p>',
            'active'  => 1,
        ],
        [
            'slug'    => 'equipment-thank-you-to-customer',
            'type'    => 'equipments',
            'name'    => 'Thank You Email (Sent to Customer After Accept)',
            'subject' => 'Thank you for accepting equipment',
            'message' => '<p>Dear {contact_firstname} {contact_lastname},</p><p>Thank you for accepting the equipment.</p><p>We look forward to doing business with you. We will contact you as soon as possible.</p><p>Kind Regards,</p><p>{email_signature}</p>',
            'active'  => 1,
        ],
        [
            'slug'    => 'equipment-expiry-reminder',
            'type'    => 'equipments',
            'name'    => 'Equipment Expiration Reminder',
            'subject' => 'Equipment Expiration Reminder',
            'message' => '<p>Hello {contact_firstname} {contact_lastname},</p><p>The equipment <strong>#{equipment_number}</strong> will expire on <strong>{equipment_expirydate}</strong>.</p><p>You can view the equipment here: <a href="{equipment_link}">{equipment_number}</a></p><p>Kind Regards,</p><p>{email_signature}</p>',
            'active'  => 1,
        ],
    ];
}

function equipments_register_email_templates(): void
{
    foreach (equipments_email_templates() as $tpl) {
        create_email_template(
            $tpl['subject'],
            $tpl['message'],
            $tpl['type'],
            $tpl['name'],
            $tpl['slug'],
            $tpl['active'] ?? 1
        );
    }
}

} // end function_exists guard

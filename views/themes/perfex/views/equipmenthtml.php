<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop15 preview-top-wrapper">
    <div class="row">
        <div class="col-md-3">
            <div class="mbot30">
                <div class="equipment-html-logo">
                    <?= get_dark_company_logo(); ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="top" data-sticky data-sticky-class="preview-sticky-header">
        <div class="container preview-sticky-container">
            <div class="sm:tw-flex sm:tw-justify-between -tw-mx-4">
                <div class="sm:tw-self-end">
                    <h3 class="bold tw-my-0 equipment-html-number">
                        <span class="sticky-visible hide tw-mb-2">
                            <?= e(format_equipment_number($equipment->id)); ?>
                        </span>
                    </h3>
                    <span class="equipment-html-status">
                        <?= format_equipment_status($equipment->status, '', true); ?>
                    </span>
                </div>

                <div class="tw-flex tw-items-end tw-space-x-2 tw-mt-3 sm:tw-mt-0">
                    <?= form_open($this->uri->uri_string(), ['class' => 'action-button']); ?>
                    <button type="submit" name="equipmentpdf" class="btn btn-default action-button download"
                        value="equipmentpdf">
                        <i class="fa-regular fa-file-pdf"></i>
                        <?= _l('clients_invoice_html_btn_download'); ?>
                    </button>
                    <?= form_close(); ?>
                    <?php if (is_client_logged_in() && has_contact_permission('equipments')) { ?>
                    <a href="<?= site_url('clients/equipments/'); ?>"
                        class="btn btn-default action-button go-to-portal">
                        <?= _l('client_go_to_dashboard'); ?>
                    </a>
                    <?php } ?>
                    <?php
                    // Is not accepted, declined and expired
                  if ($equipment->status != 4 && $equipment->status != 3 && $equipment->status != 5) {
                      echo form_open($this->uri->uri_string(), ['class' => 'action-button']);
                      echo form_hidden('equipment_action', 3);
                      echo '<button type="submit" data-loading-text="' . _l('wait_text') . '" autocomplete="off" class="btn btn-default action-button decline"><i class="fa fa-remove"></i> ' . _l('clients_decline_equipment') . '</button>';
                      echo form_close();
                  }
// Is not accepted, declined and expired
if ($equipment->status != 4 && $equipment->status != 3 && $equipment->status != 5) {
    $can_be_accepted = true;
    if ($identity_confirmation_enabled == '0') {
        echo form_open($this->uri->uri_string(), ['class' => 'action-button']);
        echo form_hidden('equipment_action', 4);
        echo '<button type="submit" data-loading-text="' . _l('wait_text') . '" autocomplete="off" class="btn btn-success action-button accept"><i class="fa fa-check"></i> ' . _l('clients_accept_equipment') . '</button>';
        echo form_close();
    } else {
        echo '<button type="button" id="accept_action" class="btn btn-success action-button accept"><i class="fa fa-check"></i> ' . _l('clients_accept_equipment') . '</button>';
    }
} elseif ($equipment->status == 3) {
    if (($equipment->expirydate >= date('Y-m-d') || ! $equipment->expirydate) && $equipment->status != 5) {
        $can_be_accepted = true;
        if ($identity_confirmation_enabled == '0') {
            echo form_open($this->uri->uri_string(), ['class' => 'action-button']);
            echo form_hidden('equipment_action', 4);
            echo '<button type="submit" data-loading-text="' . _l('wait_text') . '" autocomplete="off" class="btn btn-success action-button accept"><i class="fa fa-check"></i> ' . _l('clients_accept_equipment') . '</button>';
            echo form_close();
        } else {
            echo '<button type="button" id="accept_action" class="btn btn-success action-button accept"><i class="fa fa-check"></i> ' . _l('clients_accept_equipment') . '</button>';
        }
    }
}
?>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel_s tw-mt-6">
        <div class="panel-body">
            <div class="col-md-10 col-md-offset-1">
                <div class="row mtop20">
                    <?php
                    // Resolve surveyor entity from equipment creator
                    $_html_creator = get_staff($equipment->addedfrom);
                    $_html_surveyor_client = null;
                    if ($_html_creator && ($_html_creator->client_type ?? '') === 'surveyor' && !empty($_html_creator->client_id)) {
                        $CI->load->model('clients_model');
                        $_html_surveyor_client = $CI->clients_model->get($_html_creator->client_id);
                    }
                    ?>
                    <!-- Surveyor (From) -->
                    <div class="col-md-6 col-sm-6 transaction-html-info-col-left">
                        <h4 class="bold equipment-html-number">
                            <?= e(format_equipment_number($equipment->id)); ?>
                        </h4>
                        <p class="tw-font-medium tw-text-neutral-600"><?= _l('equipment_from'); ?></p>
                        <?php if ($_html_surveyor_client) { ?>
                        <address class="equipment-html-company-info tw-text-neutral-500 tw-text-normal">
                            <strong><?= e($_html_surveyor_client->company); ?></strong><br />
                            <?php if (!empty($_html_surveyor_client->vat)) { ?>
                            <?= _l('client_vat'); ?>: <?= e($_html_surveyor_client->vat); ?><br />
                            <?php } ?>
                            <?php if (!empty($_html_surveyor_client->address)) { ?>
                            <?= e($_html_surveyor_client->address); ?><br />
                            <?php } ?>
                            <?php if (!empty($_html_surveyor_client->city)) { ?>
                            <?= e($_html_surveyor_client->city); ?>
                            <?php if (!empty($_html_surveyor_client->zip)) { echo ' ' . e($_html_surveyor_client->zip); } ?><br />
                            <?php } ?>
                            <?php if (!empty($_html_surveyor_client->phonenumber)) { ?>
                            <?= e($_html_surveyor_client->phonenumber); ?>
                            <?php } ?>
                        </address>
                        <?php } else { ?>
                        <address class="equipment-html-company-info tw-text-neutral-500 tw-text-normal">
                            <?= format_organization_info(); ?>
                        </address>
                        <?php } ?>
                    </div>
                    <!-- Customer (To) -->
                    <div class="col-sm-6 text-right transaction-html-info-col-right">
                        <span class="tw-font-medium tw-text-neutral-600 equipment_to">
                            <?= _l('equipment_to'); ?>
                        </span>
                        <address class="equipment-html-customer-billing-info tw-text-neutral-500 tw-text-normal">
                            <?= format_customer_info($equipment, 'equipment', 'billing'); ?>
                        </address>
                        <?php if ($equipment->include_shipping == 1 && $equipment->show_shipping_on_equipment == 1) { ?>
                        <span class="tw-font-medium tw-text-neutral-700 equipment_ship_to">
                            <?= _l('ship_to'); ?>
                        </span>
                        <address class="equipment-html-customer-shipping-info tw-text-neutral-500 tw-text-normal">
                            <?= format_customer_info($equipment, 'equipment', 'shipping'); ?>
                        </address>
                        <?php } ?>
                        <?php if (! empty($equipment->reference_no)) { ?>
                        <p class="equipment-html-reference-no tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?= _l('reference_no'); ?>:</span>
                            <?= e($equipment->reference_no); ?>
                        </p>
                        <?php } ?>
                        <?php if ($equipment->sale_agent && get_option('show_sale_agent_on_equipments') == 1) { ?>
                        <p class="equipment-html-sale-agent tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?= _l('sale_agent_string'); ?>:</span>
                            <?= e(get_staff_full_name($equipment->sale_agent)); ?>
                        </p>
                        <?php } ?>
                        <?php if ($equipment->project_id && get_option('show_project_on_equipment') == 1) { ?>
                        <p class="equipment-html-project tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?= _l('project'); ?>:</span>
                            <?= e(get_project_name_by_id($equipment->project_id)); ?>
                        </p>
                        <?php } ?>
                        <?php $pdf_custom_fields = get_custom_fields('equipment', ['show_on_pdf' => 1, 'show_on_client_portal' => 1]);
foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($equipment->id, $field['id'], 'equipment');
    if ($value == '') { continue; } ?>
                        <p class="tw-mb-0 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?= e($field['name']); ?>:</span>
                            <?= $value; ?>
                        </p>
                        <?php } ?>
                    </div>
                </div>
                <!-- Dates (left) | Personnel (right) -->
                <div class="row mtop15">
                    <div class="col-md-6">
                        <p class="tw-mb-1 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?= _l('equipment_data_date'); ?>:</span>
                            <span class="tw-text-neutral-500"><?= e(_d($equipment->date)); ?></span>
                        </p>
                        <?php if (!empty($equipment->expirydate)) { ?>
                        <p class="tw-mb-1 tw-text-normal">
                            <span class="tw-font-medium tw-text-neutral-700"><?= _l('equipment_data_expiry_date'); ?>:</span>
                            <span class="tw-text-neutral-500"><?= e(_d($equipment->expirydate)); ?></span>
                        </p>
                        <?php } ?>
                    </div>
                    <?php if (!empty($equipment->members)) { ?>
                    <div class="col-md-6">
                        <p class="tw-font-medium tw-text-neutral-700 tw-mb-1"><?= _l('equipment_members'); ?></p>
                        <p class="tw-text-neutral-500 tw-mb-0">
                            <?= e(implode(', ', array_column($equipment->members, 'full_name'))); ?>
                        </p>
                    </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <?php
                            if (!class_exists('App_rows_table')) {
                                require_once(FCPATH . 'modules/apps/libraries/App_rows_table.php');
                            }
                            $job_columns = [
                                ['label' => _l('equipment_job_name'),               'key' => 'job_name'],
                                ['label' => _l('equipment_equipment_item_name'),    'key' => 'item_name'],
                                ['label' => _l('equipment_equipment_unit_code'),    'key' => 'unit_code'],
                                ['label' => _l('equipment_equipment_serial_number'),'key' => 'serial_number'],
                                ['label' => _l('equipment_equipment_location'),     'key' => 'location'],
                                ['label' => _l('equipment_equipment_cert_expired'), 'key' => 'cert_expired_date'],
                            ];
                            echo (new App_rows_table($equipment->jobs ?? [], $job_columns, 'html', true))->table();
                            ?>
                        </div>
                    </div>
                    <?php hooks()->do_action('after_total_summary_equipmenthtml', $equipment); ?>
                    <?php if (count($equipment->attachments) > 0 && $equipment->visible_attachments_to_customer_found == true) { ?>
                    <div class="clearfix"></div>
                    <div class="equipment-html-files">
                        <div class="col-md-12">
                            <hr />
                            <p class="bold mbot15 font-medium">
                                <?= _l('equipment_files'); ?>
                            </p>
                        </div>
                        <?php foreach ($equipment->attachments as $attachment) {
                            // Do not show hidden attachments to customer
                            if ($attachment['visible_to_customer'] == 0) {
                                continue;
                            }
                            $attachment_url = site_url('download/file/sales_attachment/' . $attachment['attachment_key']);
                            if (! empty($attachment['external'])) {
                                $attachment_url = $attachment['external_link'];
                            } ?>
                        <div class="col-md-12 mbot15">
                            <div class="pull-left"><i
                                    class="<?= get_mime_class($attachment['filetype']); ?>"></i>
                            </div>
                            <a
                                href="<?= e($attachment_url); ?>"><?= e($attachment['file_name']); ?></a>
                        </div>
                        <?php
                        } ?>
                    </div>
                    <?php } ?>
                    <?php if (!empty($equipment->assign_to)) {
                        $_html_assign = get_staff($equipment->assign_to); ?>
                    <div class="col-md-6 col-md-offset-6 text-center mtop20 mbot20">
                        <p class="tw-font-medium tw-text-neutral-700"><?= _l('equipment_assign_to'); ?></p>
                        <div style="border-top:1px solid #ccc;padding-top:8px;margin-top:30px;">
                            <span class="tw-text-neutral-600">
                                <?= e($_html_assign ? $_html_assign->firstname . ' ' . $_html_assign->lastname : ''); ?>
                            </span>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if (! empty($equipment->clientnote)) { ?>
                    <div class="col-md-12 equipment-html-note">
                        <p class="tw-mb-2.5 tw-font-medium">
                            <b><?= _l('equipment_note'); ?></b>
                        </p>
                        <div class="tw-text-neutral-500">
                            <?= process_text_content_for_display($equipment->clientnote); ?>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if (! empty($equipment->terms)) { ?>
                    <div class="col-md-12 equipment-html-terms-and-conditions">
                        <hr />
                        <p class="tw-mb-2.5 tw-font-medium">
                            <b><?= _l('terms_and_conditions'); ?></b>
                        </p>
                        <div class="tw-text-neutral-500">
                            <?= process_text_content_for_display($equipment->terms); ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php
   if ($identity_confirmation_enabled == '1' && $can_be_accepted) {
       get_template_part('identity_confirmation_form', ['formData' => form_hidden('equipment_action', 4)]);
   }
?>
    <script>
        $(function() {
            new Sticky('[data-sticky]');
            $('.select-item').on('change', function() {
                const $el = $(this);
                const isChecked = $el.prop('checked') ? 1 : 0;
                const itemId = $el.closest('tr').data('item-id');

                $.post(window.location.href, {
                    item_id: itemId,
                    choosen: isChecked,
                    action: 'equipment_item_selection_changed'
                }).done(function() {
                    window.location.reload();
                });
            });
        })
    </script>
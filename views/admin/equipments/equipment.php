<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            $form_action = (!empty($is_copy)) ? admin_url('equipments/equipment') : $this->uri->uri_string();
            echo form_open($form_action, ['id' => 'equipment-form', 'class' => '_transaction_form equipment-form']);
            if (isset($equipment) && empty($is_copy)) {
                echo form_hidden('isedit');
            }
            ?>
            <div class="col-md-12">
                <h4
                    class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo e( isset($equipment) ? format_equipment_number($equipment) : _l('create_new_equipment')); ?>
                    </span>
                    <?php echo isset($equipment) ? format_equipment_status($equipment->status) : ''; ?>
                </h4>
                <?php $this->load->view('admin/equipments/equipment_template'); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
// Equipment has no financial amounts; disable core currency formatter to prevent errors
init_currency = function() {};

$(function() {
    validate_equipment_form();
    init_billing_and_shipping_details();
    init_ajax_program_search_by_customer_id();
    init_ajax_equipment_job_search_by_program_id();
    init_ajax_search('equipment_member', '#equipment_members_select.ajax-search');
    init_ajax_search('equipment_member', '#equipment_assign_to_select.ajax-search');
});
</script>
</body>

</html>

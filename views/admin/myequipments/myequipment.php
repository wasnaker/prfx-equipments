<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo e(isset($myequipment) ? format_myequipment_number($myequipment) : _l('create_new_myequipment')); ?>
                    </span>
                    <?php echo isset($myequipment) ? format_myequipment_status($myequipment->status) : ''; ?>
                </h4>
                <?php $this->load->view('admin/myequipments/myequipment_template'); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
init_currency = function() {};
$(function() {
    // customer select is handled by render_client_selector() inline script
    init_ajax_search('equipment_item', '#myequipment_item_select.ajax-search', function(val) {
        $('#item_id').val(val);
    });
});
</script>
</body>

</html>

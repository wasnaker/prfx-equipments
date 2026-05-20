<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel-table-full">
                <div id="vueApp">
                    <div class="col-md-12 tw-mb-3">
                        <h4 class="tw-my-0 tw-font-bold tw-text-xl"><?= _l('my_equipment_units'); ?></h4>
                    </div>
                    <div class="col-md-12">
                        <?php $this->load->view('admin/myequipments/quick_stats'); ?>
                    </div>
                    <?php $this->load->view('admin/myequipments/list_template'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var hidden_columns = [6, 7, 8, 9];
</script>
<?php init_tail(); ?>
<script>
$(function() {
    var Myequipments_ServerParams = {};
    $.each($('._hidden_inputs._filters input'), function () {
        Myequipments_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    initDataTable(
        '.table-myequipments',
        admin_url + 'equipments/myequipments/table',
        false,
        false,
        Myequipments_ServerParams,
        [1, 'asc']
    );

    <?php if (isset($myequipment)) { ?>
    init_myequipment(<?= (int)$myequipment->id; ?>);
    <?php } ?>
});

function init_myequipment(id) {
    if (!id) { return; }
    load_small_table_item(id, '#myequipment', 'myequipmentid', 'equipments/myequipments/get_myequipment_data_ajax', '.table-myequipments');
}

// Show/hide Create RFQ button based on checked rows
$(document).on('change', '.table-myequipments tbody input[type="checkbox"]', function() {
    var checked = $('.table-myequipments tbody input[type="checkbox"]:checked').length > 0;
    $('.btn-create-rfq').toggleClass('hide', !checked);
});

function myequipments_create_rfq() {
    var ids = [];
    $('.table-myequipments tbody tr').each(function() {
        var cb = $(this).find('td:eq(0) input[type="checkbox"]');
        if (cb.prop('checked')) {
            ids.push(cb.val());
        }
    });
    if (ids.length === 0) {
        alert_float('warning', '<?= _l('myequipments_select_at_least_one'); ?>');
        return;
    }
    var query = ids.map(function(id) { return 'customer_equipment_ids[]=' + id; }).join('&');
    window.location.href = admin_url + 'rfqs/rfq?' + query;
}
</script>
</body>
</html>

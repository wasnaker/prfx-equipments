<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel-table-full">
                <div id="vueApp">
                    <div class="col-md-12 tw-mb-3">
                        <h4 class="tw-my-0 tw-font-bold tw-text-xl"><?= _l('equipments'); ?></h4>
                        <a href="#" 
							class="equipments-total tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"
							onclick="slideToggle('#stats-top'); init_equipments_total(true); return false;">
								<?= _l('view_financial_stats'); ?>
						</a>
                    </div>                  
                    <div class="col-md-12">
                        <?php $this->load->view('admin/equipments/quick_stats'); ?>
                    </div>
                    <?php $this->load->view('admin/equipments/list_template'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>
var hidden_columns = [];
</script>
<?php init_tail(); ?>
<script>
$(function() {
    var Equipments_ServerParams = {};
    $.each($('._hidden_inputs._filters input'), function () {
        Equipments_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    initDataTable(
        '.table-equipments',
        admin_url + 'equipments/table',
        'undefined',
        'undefined',
        Equipments_ServerParams,
        [[3, 'desc'], [0, 'desc']]
    );
    init_equipment(<?= !empty($equipmentid) ? (int)$equipmentid : ''; ?>);
});
</script>
</body>

</html>
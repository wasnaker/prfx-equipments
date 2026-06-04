<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel-table-full">
                <div id="vueApp">
                    <div class="col-md-12 tw-mb-3">
                        <h4 class="tw-my-0 tw-font-bold tw-text-xl"><?= _l('groups'); ?></h4>
                        <a href="#" 
							class="groups-total tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"
							onclick="slideToggle('#stats-top'); init_groups_total(true); return false;">
								<?= _l('view_financial_stats'); ?>
						</a>
                    </div>                  
                    <div class="col-md-12">
                        <?php $this->load->view('admin/groups/quick_stats'); ?>
                    </div>
                    <?php $this->load->view('admin/groups/list_template'); ?>
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
    var Groups_ServerParams = {};
    $.each($('._hidden_inputs._filters input'), function () {
        Groups_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    initDataTable(
        '.table-groups',
        admin_url + 'equipments/groups/table',
        'undefined',
        'undefined',
        Groups_ServerParams,
        [[3, 'desc'], [0, 'desc']]
    );
    var groupid = $('input[name="groupid"]').val();
    if (groupid) { init_group(groupid); }
});

function init_group(id) {
    if (!id) { return; }
    load_small_table_item(id, '#group', 'groupid', 'equipments/groups/get_group_data_ajax', '.table-groups');
}
</script>
</body>

</html>
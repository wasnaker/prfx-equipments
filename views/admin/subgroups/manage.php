<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel-table-full">
                <div id="vueApp">
                    <div class="col-md-12 tw-mb-3">
                        <h4 class="tw-my-0 tw-font-bold tw-text-xl"><?= _l('equipment_subgroups'); ?></h4>
                    </div>
                    <div class="col-md-12">
                        <?php $this->load->view('admin/subgroups/quick_stats'); ?>
                    </div>
                    <?php $this->load->view('admin/subgroups/list_template'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var hidden_columns = [];
</script>
<?php init_tail(); ?>
<script>
$(function() {
    var Subgroups_ServerParams = {};
    $.each($('._hidden_inputs._filters input'), function () {
        Subgroups_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    initDataTable(
        '.table-subgroups',
        admin_url + 'equipments/subgroups/table',
        'undefined',
        'undefined',
        Subgroups_ServerParams,
        [0, 'asc']
    );
});

function init_subgroup(id) {
    if (!id) { return; }
    load_small_table_item(id, '#subgroup', 'subgroupid', 'equipments/subgroups/get_subgroup_data_ajax', '.table-subgroups');
}
</script>
</body>
</html>

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel-table-full">
                <div id="vueApp">
                    <div class="col-md-12 tw-mb-3">
                        <h4 class="tw-my-0 tw-font-bold tw-text-xl"><?= _l('equipment_categories'); ?></h4>
                    </div>
                    <div class="col-md-12">
                        <?php $this->load->view('admin/categories/quick_stats'); ?>
                    </div>
                    <?php $this->load->view('admin/categories/list_template'); ?>
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
    var Categories_ServerParams = {};
    $.each($('._hidden_inputs._filters input'), function () {
        Categories_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    initDataTable(
        '.table-categories',
        admin_url + 'equipments/categories/table',
        'undefined',
        'undefined',
        Categories_ServerParams,
        [0, 'asc']
    );
    var categoryid = window.location.hash.substr(1) || $('input[name="categoryid"]').val();
    if (categoryid) { init_category(categoryid); }
});

function init_category(id) {
    if (!id) { return; }
    load_small_table_item(id, '#category', 'categoryid', 'equipments/categories/get_category_data_ajax', '.table-categories');
}
</script>
</body>
</html>

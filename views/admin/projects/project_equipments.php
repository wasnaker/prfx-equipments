<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="vueApp">
    <a href="#"
        class="equipments-total tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 tw-block tw-mb-1"
        onclick="slideToggle('#stats-top'); init_equipments_total(true); return false;">
        <?= _l('view_financial_stats'); ?>
    </a>
    <?php include_once module_views_path(EQUIPMENTS_MODULE_NAME) . 'admin/equipments/equipments_top_stats.php'; ?>
    <?php $this->load->view('admin/equipments/quick_stats'); ?>
    <div class="panel_s panel-table-full ">
        <div class="panel-body">
            <div class="project_equipments">
                <?php $this->load->view('admin/equipments/list_template', [
                    'table'    => $equipments_table,
                    'table_id' => $equipments_table->id(),
                ]); ?>
            </div>
        </div>
    </div>
</div>
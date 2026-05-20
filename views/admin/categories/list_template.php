<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">
    <div class="_filters _hidden_inputs hidden"></div>
    <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
        <div class="tw-flex tw-items-center tw-gap-2">
            <?php if (staff_can('create', 'equipments')) { ?>
            <a href="<?= admin_url('equipments/categories/category'); ?>" class="btn btn-primary">
                <i class="fa fa-plus tw-mr-1"></i><?= _l('new_category'); ?>
            </a>
            <?php } ?>
            <a href="<?= admin_url('equipments'); ?>" class="btn btn-default">
                <i class="fa fa-cubes tw-mr-1"></i><?= _l('manage_equipments'); ?>
            </a>
        </div>
        <div class="tw-flex tw-items-center tw-gap-2">
            <a href="#" class="btn btn-default btn-with-tooltip sm:!tw-px-3 toggle-small-view hidden-xs"
                onclick="toggle_small_view('.table-categories','#category'); return false;"
                data-toggle="tooltip" title="<?= _l('toggle_full_view'); ?>">
                <i class="fa fa-angle-double-left"></i>
            </a>
            <app-filters id="<?= $categories_table->id(); ?>"
                view="<?= $categories_table->viewName(); ?>"
                :rules="extra.categoriesRules || []"
                :saved-filters="<?= $categories_table->filtersJs(); ?>"
                :available-rules="<?= $categories_table->rulesJs(); ?>">
            </app-filters>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row tw-mt-2">
        <div class="col-md-12" id="small-table">
            <div class="panel_s"><div class="panel-body">
                <?= form_hidden('categoryid', ''); ?>
                <?php $this->load->view('admin/categories/table_html'); ?>
            </div></div>
        </div>
        <div class="col-md-7 small-table-right-col">
            <div id="category" class="hide"></div>
        </div>
    </div>
</div>

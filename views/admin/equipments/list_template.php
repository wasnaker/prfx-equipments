<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">

    <div class="_filters _hidden_inputs hidden"></div>

    <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
        <div class="tw-flex tw-items-center tw-gap-2">
            <?php if (staff_can('create', 'equipments')) { ?>
            <a href="<?= admin_url('equipments/equipment'); ?>" class="btn btn-primary">
                <i class="fa fa-plus tw-mr-1"></i><?= _l('new_equipment'); ?>
            </a>
            <?php } ?>
            <a href="<?= admin_url('equipments/groups'); ?>" class="btn btn-default">
                <i class="fa fa-tags tw-mr-1"></i><?= _l('manage_groups'); ?>
            </a>
            <a href="<?= admin_url('equipments/subgroups'); ?>" class="btn btn-default">
                <i class="fa fa-layer-group tw-mr-1"></i><?= _l('manage_subgroups'); ?>
            </a>
            <a href="<?= admin_url('equipments/categories'); ?>" class="btn btn-default">
                <i class="fa fa-sitemap tw-mr-1"></i><?= _l('manage_categories'); ?>
            </a>
        </div>
        <div class="tw-flex tw-items-center tw-gap-2">
            <a href="#" class="btn btn-default btn-with-tooltip sm:!tw-px-3 toggle-small-view hidden-xs"
                onclick="toggle_small_view('.table-equipments','#equipment'); return false;"
                data-toggle="tooltip" title="<?= _l('toggle_full_view'); ?>">
                <i class="fa fa-angle-double-left"></i>
            </a>
            <app-filters id="<?= $equipments_table->id(); ?>"
                view="<?= $equipments_table->viewName(); ?>"
                :rules="extra.equipmentsRules || []"
                :saved-filters="<?= $equipments_table->filtersJs(); ?>"
                :available-rules="<?= $equipments_table->rulesJs(); ?>">
            </app-filters>
        </div>
    </div>

    <div class="clearfix"></div>
    <div class="row tw-mt-2">
        <div class="col-md-12" id="small-table">
            <div class="panel_s">
                <div class="panel-body">
                    <?= form_hidden('equipmentid', ''); ?>
                    <?php $this->load->view('admin/equipments/table_html'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-7 small-table-right-col">
            <div id="equipment" class="hide"></div>
        </div>
    </div>
</div>

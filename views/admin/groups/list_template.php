<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">

    <div class="_filters _hidden_inputs hidden"></div>

    <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
        <div class="tw-flex tw-items-center tw-gap-2">
            <?php if (staff_can('create', 'equipments')) { ?>
            <a href="#" class="btn btn-primary" onclick="open_group_modal(); return false;">
                <i class="fa fa-plus tw-mr-1"></i><?= _l('new_group'); ?>
            </a>
            <?php } ?>
            <?php if (staff_can('view', 'equipments')) { ?>
            <a href="<?= admin_url('equipments/subgroups'); ?>" class="btn btn-default">
                <i class="fa fa-layer-group tw-mr-1"></i><?= _l('manage_subgroups'); ?>
            </a>
            <a href="<?= admin_url('equipments/categories'); ?>" class="btn btn-default">
                <i class="fa fa-sitemap tw-mr-1"></i><?= _l('manage_categories'); ?>
            </a>
            <a href="<?= admin_url('equipments'); ?>" class="btn btn-default">
                <i class="fa fa-cubes tw-mr-1"></i><?= _l('manage_equipments'); ?>
            </a>
            <?php } ?>
        </div>
        <div class="tw-flex tw-items-center tw-gap-2">
            <a href="#" class="btn btn-default btn-with-tooltip sm:!tw-px-3 toggle-small-view hidden-xs"
                onclick="toggle_small_view('.table-groups','#group'); return false;"
                data-toggle="tooltip" title="<?= _l('toggle_full_view'); ?>">
                <i class="fa fa-angle-double-left"></i>
            </a>
            <app-filters id="<?= $groups_table->id(); ?>"
                view="<?= $groups_table->viewName(); ?>"
                :rules="extra.groupsRules || []"
                :saved-filters="<?= $groups_table->filtersJs(); ?>"
                :available-rules="<?= $groups_table->rulesJs(); ?>">
            </app-filters>
        </div>
    </div>

    <div class="clearfix"></div>
    <div class="row tw-mt-2">
        <div class="col-md-12" id="small-table">
            <div class="panel_s">
                <div class="panel-body">
                    <?= form_hidden('groupid', ''); ?>
                    <?php $this->load->view('admin/groups/table_html'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-7 small-table-right-col">
            <div id="group" class="hide"></div>
        </div>
    </div>
</div>

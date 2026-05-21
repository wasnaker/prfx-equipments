<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">
    <div class="_filters _hidden_inputs hidden"></div>
    <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
        <div class="tw-flex tw-items-center tw-gap-2">
            <?php if (staff_can('create', 'myequipments')) { ?>
            <a href="<?= admin_url('equipments/myequipments/myequipment'); ?>" class="btn btn-primary">
                <i class="fa fa-plus tw-mr-1"></i><?= _l('register_my_unit'); ?>
            </a>
            <?php } ?>
            <?php if (staff_can('create', 'rfqs')) { ?>
            <a href="#" class="btn btn-success hide btn-create-rfq"
               data-membership-guard="bulk_create_rfq"
               onclick="myequipments_create_rfq(); return false;">
                <i class="fa fa-file-alt tw-mr-1"></i><?= _l('create_rfq'); ?>
            </a>
            <?php } ?>
        </div>
        <div class="tw-flex tw-items-center tw-gap-2">
            <a href="#" class="btn btn-default btn-with-tooltip sm:!tw-px-3 toggle-small-view hidden-xs"
                onclick="toggle_small_view('.table-myequipments','#myequipment'); return false;"
                data-toggle="tooltip" title="<?= _l('toggle_full_view'); ?>">
                <i class="fa fa-angle-double-left"></i>
            </a>
            <app-filters id="<?= $myequipments_table->id(); ?>"
                view="<?= $myequipments_table->viewName(); ?>"
                :rules="extra.myequipmentsRules || []"
                :saved-filters="<?= $myequipments_table->filtersJs(); ?>"
                :available-rules="<?= $myequipments_table->rulesJs(); ?>">
            </app-filters>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row tw-mt-2">
        <div class="col-md-12" id="small-table">
            <div class="panel_s"><div class="panel-body">
                <?= form_hidden('myequipmentid', isset($myequipment) ? $myequipment->id : ''); ?>
                <?php $this->load->view('admin/myequipments/table_html'); ?>
            </div></div>
        </div>
        <div class="col-md-7 small-table-right-col">
            <div id="myequipment" class="hide"></div>
        </div>
    </div>
</div>

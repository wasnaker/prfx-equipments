<?php defined('BASEPATH') or exit('No direct script access allowed');
if ($equipment['status'] == $status) { ?>
<li data-equipment-id="<?= e($equipment['id']); ?>"
    class="">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-text-base pipeline-heading tw-mb-0.5">
                    <a href="<?= admin_url('equipments/list_equipments/' . $equipment['id']); ?>"
                        class="tw-text-neutral-700 hover:tw-text-neutral-900 active:tw-text-neutral-900"
                        onclick="equipment_pipeline_open(<?= e($equipment['id']); ?>); return false;">
                        <?= e(format_equipment_number($equipment['id'])); ?>
                    </a>
                    <?php if (staff_can('edit', 'equipments')) { ?>
                    <a href="<?= admin_url('equipments/equipment/' . $equipment['id']); ?>"
                        target="_blank" class="pull-right tw-font-medium">
                        <small>
                            <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                        </small>
                    </a>
                    <?php } ?>
                </h4>
                <span class="tw-inline-block tw-w-full tw-mb-2">
                    <a href="<?= admin_url('clients/client/' . $equipment['client_id']); ?>"
                        target="_blank">
                        <?= e($equipment['company']); ?>
                    </a>
                </span>
            </div>
            <div class="col-md-12">
                <div class="tw-flex">
                    <div class="tw-grow">
                        <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                            <span class="tw-text-neutral-500">
                                <?= _l('equipment_total'); ?>:
                            </span>
                            <?= e(app_format_money($equipment['total'], $equipment['currency_name'])); ?>
                        </p>
                        <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                            <span class="tw-text-neutral-500">
                                <?= _l('equipment_data_date'); ?>:
                            </span>
                            <?= e(_d($equipment['date'])); ?>
                        </p>
                        <?php if (is_date($equipment['expirydate']) || ! empty($equipment['expirydate'])) { ?>
                        <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                            <span class="tw-text-neutral-500">
                                <?= _l('equipment_data_expiry_date'); ?>:
                            </span>
                            <?= e(_d($equipment['expirydate'])); ?>
                        </p>
                        <?php } ?>
                    </div>
                    <div class="tw-shrink-0 text-right">
                        <small>
                            <i class="fa fa-paperclip"></i>
                            <?= _l('equipment_notes'); ?>:
                            <?= total_rows(db_prefix() . 'notes', [
                                'rel_id'   => $equipment['id'],
                                'rel_type' => 'equipment',
                            ]); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>
<?php } ?>
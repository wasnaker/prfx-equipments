<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s">
    <div class="panel-body">

        <?= form_open(admin_url('equipments/myequipments/add_edit_myequipment'), ['id' => 'myequipment-form', 'autocomplete' => 'off']); ?>
        <?php if (isset($myequipment)) { ?>
            <?= form_hidden('id', $myequipment->id); ?>
        <?php } ?>

        <div class="row">

            <!-- Kolom kiri -->
            <div class="col-md-6">

                <!-- Customer (AJAX search) -->
                <div class="form-group select-placeholder">
                    <label class="control-label"><?= _l('client'); ?> <span class="text-danger">*</span></label>
                    <?php
                    $client_val = '';
                    if (isset($myequipment) && $myequipment->client_id) {
                        $client_data = get_relation_data('customer', $myequipment->client_id);
                        $client_rel  = get_relation_values($client_data, 'customer');
                        $client_val  = $client_rel['name'];
                    }
                    ?>
                    <input type="hidden" name="client_id" id="client_id" value="<?= isset($myequipment) ? $myequipment->client_id : ''; ?>">
                    <select id="myequipment_client_select" class="ajax-search" data-live-search="true" data-width="100%"
                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                        <?php if ($client_val): ?>
                        <option value="<?= isset($myequipment) ? $myequipment->client_id : ''; ?>" selected>
                            <?= e($client_val); ?>
                        </option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Equipment Type (AJAX search) -->
                <div class="form-group select-placeholder">
                    <label class="control-label"><?= _l('equipment_type'); ?> <span class="text-danger">*</span></label>
                    <?php
                    $item_val = '';
                    if (isset($myequipment) && $myequipment->item_id) {
                        $item_row = get_instance()->db->select('description')->get_where(db_prefix() . 'items', ['id' => $myequipment->item_id])->row();
                        $item_val = $item_row ? $item_row->description : '';
                    }
                    ?>
                    <?php
                    $prefill_id = isset($prefill_item_id) && $prefill_item_id ? $prefill_item_id : (isset($myequipment) ? $myequipment->item_id : 0);
                    if (!$item_val && $prefill_id) {
                        $prefill_row = get_instance()->db->select('description')->get_where(db_prefix() . 'items', ['id' => $prefill_id])->row();
                        $item_val    = $prefill_row ? $prefill_row->description : '';
                    }
                    ?>
                    <input type="hidden" name="item_id" id="item_id" value="<?= $prefill_id ?: ''; ?>">
                    <select id="myequipment_item_select" class="ajax-search" data-live-search="true" data-width="100%"
                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                        <?php if ($item_val): ?>
                        <option value="<?= $prefill_id ?: ''; ?>" selected>
                            <?= e($item_val); ?>
                        </option>
                        <?php endif; ?>
                    </select>
                </div>

                <?= render_input('customer_equipment_name', 'equipment_name',
                    isset($myequipment) ? $myequipment->customer_equipment_name : '',
                    'text', ['required' => true]); ?>

                <?= render_input('unit_code', 'unit_code',
                    isset($myequipment) ? $myequipment->unit_code : '',
                    'text', ['required' => true]); ?>

                <?= render_input('serial_number', 'serial_number',
                    isset($myequipment) ? $myequipment->serial_number : ''); ?>

                <?= render_input('brand', 'brand',
                    isset($myequipment) ? $myequipment->brand : ''); ?>

                <?= render_input('model_type', 'model_type',
                    isset($myequipment) ? $myequipment->model_type : ''); ?>

            </div>

            <!-- Kolom kanan -->
            <div class="col-md-6">

                <?= render_input('location', 'location',
                    isset($myequipment) ? $myequipment->location : ''); ?>

                <?= render_input('capacity', 'capacity',
                    isset($myequipment) ? $myequipment->capacity : ''); ?>

                <?= render_input('manufacture_year', 'manufacture_year',
                    isset($myequipment) ? $myequipment->manufacture_year : '',
                    'number', ['min' => 1900, 'max' => date('Y')]); ?>

                <?= render_input('cert_expired_date', 'inspection_equipment_cert_expired',
                    isset($myequipment) ? _d($myequipment->cert_expired_date) : '',
                    'date'); ?>

                <?= render_input('latest_cert_number', 'latest_cert_number',
                    isset($myequipment) ? $myequipment->latest_cert_number : ''); ?>

                <?= render_input('reference', 'reference_no',
                    isset($myequipment) ? $myequipment->reference : ''); ?>

                <?php
                $statuses = $this->myequipments_model->get_statuses();
                $status_options = array_map(fn($s) => ['id' => $s, 'name' => format_equipment_status($s, '', false)], $statuses);
                echo render_select('status', $status_options, ['id', 'name'], 'status',
                    isset($myequipment) ? $myequipment->status : EQUIPMENT_STATUS_ACTIVE);
                ?>

                <?= render_textarea('notes', 'notes',
                    isset($myequipment) ? $myequipment->notes : ''); ?>

            </div>
        </div>

        <div class="text-right mtop15">
            <a href="<?= admin_url('equipments/myequipments'); ?>" class="btn btn-default mright5">
                <?= _l('cancel'); ?>
            </a>
            <button type="submit" class="btn btn-primary">
                <?= _l('submit'); ?>
            </button>
        </div>

        <?= form_close(); ?>

    </div>
</div>


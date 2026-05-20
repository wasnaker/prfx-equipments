<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel-body" id="equipment-equipment-section">
    <div class="row mbot10">
        <div class="col-md-4">
            <div class="form-group select-placeholder">
                <label class="control-label"><?php echo _l('equipment_add_equipment'); ?></label>
                <select id="equipment_equipment_select" data-live-search="true" data-width="100%"
                    class="ajax-search"
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    <option value=""></option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-responsive s_table">
        <table class="table no-mtop" id="equipment-equipment-table">
            <thead>
                <tr>
                    <th width="25%"><?php echo _l('equipment_equipment_item_name'); ?></th>
                    <th width="15%"><?php echo _l('equipment_equipment_unit_code'); ?></th>
                    <th width="15%"><?php echo _l('equipment_equipment_serial_number'); ?></th>
                    <th width="20%"><?php echo _l('equipment_equipment_location'); ?></th>
                    <th width="15%"><?php echo _l('equipment_equipment_cert_expired'); ?></th>
                    <th width="10%" align="center"><i class="fa fa-cog"></i></th>
                </tr>
            </thead>
            <tbody id="equipment-equipment-tbody">
                <?php
                $eq_rows = [];
                if (isset($equipment) && !empty($equipment->equipment)) {
                    $eq_rows = $equipment->equipment;
                }
                foreach ($eq_rows as $i => $eq):
                ?>
                <tr class="equipment-equipment-row" data-id="<?php echo $eq['customer_equipment_id']; ?>">
                    <td><?php echo e($eq['item_name']); ?>
                        <input type="hidden" name="equipment[<?php echo $i; ?>][customer_equipment_id]" value="<?php echo $eq['customer_equipment_id']; ?>">
                    </td>
                    <td><?php echo e($eq['unit_code']); ?></td>
                    <td><?php echo e($eq['serial_number']); ?></td>
                    <td><?php echo e($eq['location']); ?></td>
                    <td><?php echo e($eq['cert_expired_date']); ?></td>
                    <td>
                        <a href="#" class="btn btn-danger btn-sm equipment-equipment-remove" data-id="<?php echo $eq['customer_equipment_id']; ?>">
                            <i class="fa fa-times"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

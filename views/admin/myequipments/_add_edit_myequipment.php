<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel-body" id="myequipment-myequipment-section">
    <div class="row mbot10">
        <div class="col-md-4">
            <div class="form-group select-placeholder">
                <label class="control-label"><?php echo _l('myequipment_add_myequipment'); ?></label>
                <select id="myequipment_myequipment_select" data-live-search="true" data-width="100%"
                    class="ajax-search"
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    <option value=""></option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-responsive s_table">
        <table class="table no-mtop" id="myequipment-myequipment-table">
            <thead>
                <tr>
                    <th width="25%"><?php echo _l('myequipment_myequipment_item_name'); ?></th>
                    <th width="15%"><?php echo _l('myequipment_myequipment_unit_code'); ?></th>
                    <th width="15%"><?php echo _l('myequipment_myequipment_serial_number'); ?></th>
                    <th width="20%"><?php echo _l('myequipment_myequipment_location'); ?></th>
                    <th width="15%"><?php echo _l('myequipment_myequipment_cert_expired'); ?></th>
                    <th width="10%" align="center"><i class="fa fa-cog"></i></th>
                </tr>
            </thead>
            <tbody id="myequipment-myequipment-tbody">
                <?php
                $eq_rows = [];
                if (isset($myequipment) && !empty($myequipment->myequipment)) {
                    $eq_rows = $myequipment->myequipment;
                }
                foreach ($eq_rows as $i => $eq):
                ?>
                <tr class="myequipment-myequipment-row" data-id="<?php echo $eq['customer_myequipment_id']; ?>">
                    <td><?php echo e($eq['item_name']); ?>
                        <input type="hidden" name="myequipment[<?php echo $i; ?>][customer_myequipment_id]" value="<?php echo $eq['customer_myequipment_id']; ?>">
                    </td>
                    <td><?php echo e($eq['unit_code']); ?></td>
                    <td><?php echo e($eq['serial_number']); ?></td>
                    <td><?php echo e($eq['location']); ?></td>
                    <td><?php echo e($eq['cert_expired_date']); ?></td>
                    <td>
                        <a href="#" class="btn btn-danger btn-sm myequipment-myequipment-remove" data-id="<?php echo $eq['customer_myequipment_id']; ?>">
                            <i class="fa fa-times"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

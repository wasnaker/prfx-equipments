<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel-body" id="equipment-tasks-section">
    <div class="row mbot10">
        <div class="col-md-4">
            <div class="form-group select-placeholder">
                <label class="control-label"><?php echo _l('equipment_add_task'); ?></label>
                <select id="equipment_task_select" data-live-search="true" data-width="100%"
                    class="ajax-search"
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    <option value=""></option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-responsive s_table">
        <table class="table no-mtop" id="equipment-tasks-table">
            <thead>
                <tr>
                    <th width="20%"><?php echo _l('equipment_task_name'); ?></th>
                    <th width="20%"><?php echo _l('equipment_equipment_item_name'); ?></th>
                    <th width="13%"><?php echo _l('equipment_equipment_unit_code'); ?></th>
                    <th width="13%"><?php echo _l('equipment_equipment_serial_number'); ?></th>
                    <th width="18%"><?php echo _l('equipment_equipment_location'); ?></th>
                    <th width="12%"><?php echo _l('equipment_equipment_cert_expired'); ?></th>
                    <th width="4%" align="center"><i class="fa fa-cog"></i></th>
                </tr>
            </thead>
            <tbody id="equipment-tasks-tbody">
                <?php
                $task_rows = [];
                if (isset($equipment) && !empty($equipment->tasks)) {
                    $task_rows = $equipment->tasks;
                } elseif (!empty($preset_tasks)) {
                    $task_rows = $preset_tasks;
                }
                foreach ($task_rows as $i => $task):
                ?>
                <tr class="equipment-task-row" data-id="<?php echo $task['task_id']; ?>">
                    <td><?php echo e($task['task_name']); ?>
                        <input type="hidden" name="tasks[<?php echo $i; ?>][task_id]" value="<?php echo $task['task_id']; ?>">
                    </td>
                    <td><?php echo e($task['item_name'] ?? ''); ?></td>
                    <td><?php echo e($task['unit_code'] ?? ''); ?></td>
                    <td><?php echo e($task['serial_number'] ?? ''); ?></td>
                    <td><?php echo e($task['location'] ?? ''); ?></td>
                    <td><?php echo e($task['cert_expired_date'] ?? ''); ?></td>
                    <td>
                        <a href="#" class="btn btn-danger btn-sm equipment-task-remove" data-id="<?php echo $task['task_id']; ?>">
                            <i class="fa fa-times"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

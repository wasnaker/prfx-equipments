<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel-body" id="equipment-jobs-section">
    <div class="row mbot10">
        <div class="col-md-4">
            <div class="form-group select-placeholder">
                <label class="control-label"><?php echo _l('equipment_add_job'); ?></label>
                <select id="equipment_job_select" data-live-search="true" data-width="100%"
                    class="ajax-search"
                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    <option value=""></option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-responsive s_table">
        <table class="table no-mtop" id="equipment-jobs-table">
            <thead>
                <tr>
                    <th width="20%"><?php echo _l('equipment_job_name'); ?></th>
                    <th width="20%"><?php echo _l('equipment_equipment_item_name'); ?></th>
                    <th width="13%"><?php echo _l('equipment_equipment_unit_code'); ?></th>
                    <th width="13%"><?php echo _l('equipment_equipment_serial_number'); ?></th>
                    <th width="18%"><?php echo _l('equipment_equipment_location'); ?></th>
                    <th width="12%"><?php echo _l('equipment_equipment_cert_expired'); ?></th>
                    <th width="4%" align="center"><i class="fa fa-cog"></i></th>
                </tr>
            </thead>
            <tbody id="equipment-jobs-tbody">
                <?php
                $job_rows = [];
                if (isset($equipment) && !empty($equipment->jobs)) {
                    $job_rows = $equipment->jobs;
                } elseif (!empty($preset_jobs)) {
                    $job_rows = $preset_jobs;
                }
                foreach ($job_rows as $i => $job):
                ?>
                <tr class="equipment-job-row" data-id="<?php echo $job['job_id']; ?>">
                    <td><?php echo e($job['job_name']); ?>
                        <input type="hidden" name="jobs[<?php echo $i; ?>][job_id]" value="<?php echo $job['job_id']; ?>">
                    </td>
                    <td><?php echo e($job['item_name'] ?? ''); ?></td>
                    <td><?php echo e($job['unit_code'] ?? ''); ?></td>
                    <td><?php echo e($job['serial_number'] ?? ''); ?></td>
                    <td><?php echo e($job['location'] ?? ''); ?></td>
                    <td><?php echo e($job['cert_expired_date'] ?? ''); ?></td>
                    <td>
                        <a href="#" class="btn btn-danger btn-sm equipment-job-remove" data-id="<?php echo $job['job_id']; ?>">
                            <i class="fa fa-times"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

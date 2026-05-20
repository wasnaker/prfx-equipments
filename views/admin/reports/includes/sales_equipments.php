<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="equipments-report" class="hide">
<div class="row">
   <div class="col-md-4">
      <div class="form-group">
         <label for="equipment_status"><?php echo _l('equipment_status'); ?></label>
         <select name="equipment_status" class="selectpicker" multiple data-width="100%" data-none-selected-text="<?php echo _l('invoice_status_report_all'); ?>">
            <?php foreach($equipment_statuses as $status){ ?>
            <option value="<?php echo e($status); ?>"><?php echo format_equipment_status($status,'',false) ?></option>
            <?php } ?>
         </select>
      </div>
   </div>
   <?php if(count($equipments_sale_agents) > 0 ) { ?>
   <div class="col-md-4">
      <div class="form-group">
         <label for="sale_agent_equipments"><?php echo _l('sale_agent_string'); ?></label>
         <select name="sale_agent_equipments" class="selectpicker" multiple data-width="100%" data-none-selected-text="<?php echo _l('invoice_status_report_all'); ?>">
            <?php foreach($equipments_sale_agents as $agent){ ?>
            <option value="<?php echo e($agent['sale_agent']); ?>"><?php echo e(get_staff_full_name($agent['sale_agent'])); ?></option>
            <?php } ?>
         </select>
      </div>
   </div>
   <?php } ?>
</div>
<div class="clearfix"></div>
   <table class="table table-equipments-report">
      <thead>
       <tr>
         <th><?php echo _l('equipment_dt_table_heading_number'); ?></th>
         <th><?php echo _l('equipment_dt_table_heading_client'); ?></th>
         <th class="not-export"><?php echo _l('report_invoice_number'); ?></th>
         <th class="not-export"><?php echo _l('equipment_year'); ?></th>
         <th><?php echo _l('equipment_dt_table_heading_date'); ?></th>
         <th><?php echo _l('equipment_dt_table_heading_expirydate'); ?></th>
         <th><?php echo _l('equipment_dt_table_heading_amount'); ?></th>
         <th><?php echo _l('report_invoice_amount_with_tax'); ?></th>
         <th><?php echo _l('report_invoice_total_tax'); ?></th>
         <?php foreach($equipment_taxes as $tax){ ?>
         <th><?php echo e($tax['taxname']); ?> <small><?php echo e($tax['taxrate']); ?>%</small></th>
         <?php } ?>
         <th><?php echo _l('equipment_discount'); ?></th>
         <th><?php echo _l('equipment_adjustment'); ?></th>
         <th><?php echo _l('reference_no'); ?></th>
         <th><?php echo _l('equipment_dt_table_heading_status'); ?></th>
      </tr>
   </thead>
   <tbody></tbody>
   <tfoot>
      <tr>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td class="subtotal"></td>
         <td class="total"></td>
         <td class="total_tax"></td>
         <?php foreach($equipment_taxes as $key => $tax){ ?>
         <td class="total_tax_single_<?php echo e($key); ?>"></td>
         <?php } ?>
         <td class="discount_total"></td>
         <td class="adjustment"></td>
         <td></td>
         <td></td>
      </tr>
   </tfoot>
</table>
</div>

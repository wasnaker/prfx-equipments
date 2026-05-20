<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-4 sm:tw-flex sm:tw-items-center sm:tw-justify-between">
                    <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                        <?= _l('equipments_report'); ?>
                    </h4>
                </div>

                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?= _l('period_datepicker'); ?></label>
                                    <select name="report_months" class="selectpicker" data-width="100%"
                                        onchange="init_equipments_report();">
                                        <option value="this_month" selected><?= _l('this_month'); ?></option>
                                        <option value="1"><?= _l('last_month'); ?></option>
                                        <option value="3"><?= _l('report_sales_months_three_months'); ?></option>
                                        <option value="6"><?= _l('report_sales_months_six_months'); ?></option>
                                        <option value="this_year"><?= _l('this_year'); ?></option>
                                        <option value="last_year"><?= _l('last_year'); ?></option>
                                        <option value="custom"><?= _l('period_datepicker'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 custom-report-dates hide">
                                <div class="form-group">
                                    <label><?= _l('report_sales_from_date'); ?></label>
                                    <div class="input-group date">
                                        <input type="text" name="report_from" autocomplete="off"
                                            class="form-control datepicker"
                                            onchange="init_equipments_report();">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 custom-report-dates hide">
                                <div class="form-group">
                                    <label><?= _l('report_sales_to_date'); ?></label>
                                    <div class="input-group date">
                                        <input type="text" name="report_to" autocomplete="off"
                                            class="form-control datepicker"
                                            onchange="init_equipments_report();">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?= _l('equipment_status'); ?></label>
                                    <select name="equipment_status" class="selectpicker" multiple data-width="100%"
                                        data-none-selected-text="<?= _l('invoice_status_report_all'); ?>"
                                        onchange="init_equipments_report();">
                                        <?php foreach ($equipment_statuses as $status) { ?>
                                        <option value="<?= e($status); ?>"><?= format_equipment_status($status, '', false); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php if (count($equipments_sale_agents) > 0) { ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?= _l('sale_agent_string'); ?></label>
                                    <select name="sale_agent_equipments" class="selectpicker" multiple data-width="100%"
                                        data-none-selected-text="<?= _l('invoice_status_report_all'); ?>"
                                        onchange="init_equipments_report();">
                                        <?php foreach ($equipments_sale_agents as $agent) { ?>
                                        <option value="<?= e($agent['sale_agent']); ?>">
                                            <?= e(get_staff_full_name($agent['sale_agent'])); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="clearfix"></div>

                        <div class="row report-table-wrapper">
                            <div class="col-md-12">
                                <div class="tw-flex tw-items-center tw-gap-x-2 tw-mb-3">
                                    <div class="tw-flex tw-items-center tw-gap-x-1.5">
                                        <label class="tw-mb-0 tw-font-medium tw-text-sm tw-text-neutral-600"><?= _l('datatables_show_records'); ?></label>
                                        <select name="dt_length" class="form-control input-sm tw-w-auto">
                                            <option>25</option>
                                            <option>50</option>
                                            <option>100</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-default btn-xs"
                                        onclick="export_report_table('.table-equipments-report', 'Equipments Report');">
                                        <?= _l('export_to_excel'); ?>
                                    </button>
                                    <button type="button" class="btn btn-default btn-xs" onclick="init_equipments_report();">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </div>

                                <table class="table table-equipments-report">
                                    <thead>
                                        <tr>
                                            <th><?= _l('equipment_dt_table_heading_number'); ?></th>
                                            <th><?= _l('client'); ?></th>
                                            <th class="not-export"><?= _l('report_invoice_number'); ?></th>
                                            <th class="not-export"><?= _l('invoice_estimate_year'); ?></th>
                                            <th><?= _l('estimate_dt_table_heading_date'); ?></th>
                                            <th><?= _l('estimate_dt_table_heading_expirydate'); ?></th>
                                            <th><?= _l('estimate_dt_table_heading_amount'); ?></th>
                                            <th><?= _l('report_invoice_amount_with_tax'); ?></th>
                                            <th><?= _l('report_invoice_total_tax'); ?></th>
                                            <?php foreach ($equipment_taxes as $tax) { ?>
                                            <th><?= e($tax['taxname']); ?> <small><?= e($tax['taxrate']); ?>%</small></th>
                                            <?php } ?>
                                            <th><?= _l('estimate_discount'); ?></th>
                                            <th><?= _l('estimate_adjustment'); ?></th>
                                            <th><?= _l('reference_no'); ?></th>
                                            <th><?= _l('estimate_dt_table_heading_status'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td><td></td><td></td><td></td><td></td><td></td>
                                            <td class="subtotal bold"></td>
                                            <td class="total bold"></td>
                                            <td class="total_tax bold"></td>
                                            <?php foreach ($equipment_taxes as $key => $tax) { ?>
                                            <td class="total_tax_single_<?= e($key); ?> bold"></td>
                                            <?php } ?>
                                            <td class="discount_total bold"></td>
                                            <td class="adjustment bold"></td>
                                            <td></td><td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
var equipments_report_dt;

var equipments_report_params = {
    'report_months':         'select[name="report_months"]',
    'equipment_status':      'select[name="equipment_status"]',
    'sale_agent_equipments': 'select[name="sale_agent_equipments"]',
    'report_from':           'input[name="report_from"]',
    'report_to':             'input[name="report_to"]',
};

function init_equipments_report() {
    var period = $('select[name="report_months"]').val();

    if (period === 'custom') {
        $('.custom-report-dates').removeClass('hide');
    } else {
        $('.custom-report-dates').addClass('hide');
    }

    if ($.fn.DataTable.isDataTable('.table-equipments-report')) {
        $('.table-equipments-report').DataTable().destroy();
    }

    equipments_report_dt = initDataTable(
        '.table-equipments-report',
        admin_url + 'equipments/equipments_report',
        false,
        false,
        equipments_report_params,
        [[3, 'desc'], [0, 'desc']]
    );

    equipments_report_dt.on('draw.dt', function() {
        var json = equipments_report_dt.ajax.json();
        if (json && json.sums) {
            $('.table-equipments-report tfoot td.subtotal').html(json.sums.subtotal);
            $('.table-equipments-report tfoot td.total').html(json.sums.total);
            $('.table-equipments-report tfoot td.total_tax').html(json.sums.total_tax);
            $('.table-equipments-report tfoot td.discount_total').html(json.sums.discount_total);
            $('.table-equipments-report tfoot td.adjustment').html(json.sums.adjustment);
        }
    });

    equipments_report_dt.column(3).visible(false, false).columns.adjust();
}

$(function() {
    init_equipments_report();
});
</script>
</body>
</html>

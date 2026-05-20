<div class="form-group">
    <label class="control-label"
        for="equipment_prefix"><?= _l('settings_sales_equipment_prefix'); ?></label>
    <input type="text" name="settings[equipment_prefix]" class="form-control"
        value="<?= get_option('equipment_prefix'); ?>">
</div>
<hr />
<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
    data-title="<?= _l('settings_sales_next_equipment_number_tooltip'); ?>"></i>
<?= render_input('settings[next_equipment_number]', 'settings_sales_next_equipment_number', get_option('next_equipment_number'), 'number', ['min' => 1]); ?>
<hr />

<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
    data-title="<?= _l('invoice_due_after_help'); ?>"></i>
<?= render_input('settings[equipment_due_after]', 'equipment_due_after', get_option('equipment_due_after')); ?>
<hr />
<?php render_yes_no_option('delete_only_on_last_equipment', 'settings_delete_only_on_last_equipment'); ?>
<hr />
<?php render_yes_no_option('equipment_number_decrement_on_delete', 'settings_sales_decrement_equipment_number_on_delete', 'settings_sales_decrement_equipment_number_on_delete_tooltip'); ?>
<hr />
<?= render_yes_no_option('allow_staff_view_equipments_assigned', 'allow_staff_view_equipments_assigned'); ?>
<hr />

<?php render_yes_no_option('view_equipment_only_logged_in', 'settings_sales_require_client_logged_in_to_view_equipment'); ?>
<hr />
<?php render_yes_no_option('show_sale_agent_on_equipments', 'settings_show_sale_agent_on_equipments'); ?>
<hr />
<?php render_yes_no_option('show_project_on_equipment', 'show_project_on_equipment'); ?>
<hr />
<hr />
<?php render_yes_no_option('exclude_equipment_from_client_area_with_draft_status', 'settings_exclude_equipment_from_client_area_with_draft_status'); ?>
<hr />
<div class="form-group">
    <label for="equipment_number_format"
        class="control-label clearfix"><?= _l('settings_sales_equipment_number_format'); ?></label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[equipment_number_format]" value="1" id="e_number_based"
            <?= get_option('equipment_number_format') == '1' ? 'checked' : '' ?>>
        <label
            for="e_number_based"><?= _l('settings_sales_equipment_number_format_number_based'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[equipment_number_format]" value="2" id="e_year_based"
            <?= get_option('equipment_number_format') == '2' ? 'checked' : '' ?>>
        <label
            for="e_year_based"><?= _l('settings_sales_equipment_number_format_year_based'); ?>
            (YYYY/000001)</label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[equipment_number_format]" value="3" id="e_short_year_based"
            <?= get_option('equipment_number_format') == '3' ? 'checked' : '' ?>>
        <label for="e_short_year_based">000001-YY</label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[equipment_number_format]" value="4" id="e_year_month_based"
            <?= get_option('equipment_number_format') == '4' ? 'checked' : '' ?>>
        <label for="e_year_month_based">000001/MM/YYYY</label>
    </div>
    <hr />
</div>
<div class="row">
    <div class="col-md-12">
        <?= render_input('settings[equipments_pipeline_limit]', 'pipeline_limit_status', get_option('equipments_pipeline_limit')); ?>
    </div>
    <div class="col-md-7">
        <label for="default_proposals_pipeline_sort"
            class="control-label"><?= _l('default_pipeline_sort'); ?></label>
        <select name="settings[default_equipments_pipeline_sort]" id="default_equipments_pipeline_sort"
            class="selectpicker" data-width="100%"
            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
            <option value="datecreated" <?= get_option('default_equipments_pipeline_sort') == 'datecreated' ? 'selected' : '' ?>>
                <?= _l('equipments_sort_datecreated'); ?>
            </option>
            <option value="date" <?= get_option('default_equipments_pipeline_sort') == 'date' ? 'selected' : '' ?>>
                <?= _l('equipments_sort_equipment_date'); ?>
            </option>
            <option value="pipeline_order" <?= get_option('default_equipments_pipeline_sort') == 'pipeline_order' ? 'selected' : '' ?>>
                <?= _l('equipments_sort_pipeline'); ?>
            </option>
            <option value="expirydate" <?= get_option('default_equipments_pipeline_sort') == 'expirydate' ? 'selected' : '' ?>>
                <?= _l('equipments_sort_expiry_date'); ?>
            </option>
        </select>
    </div>
    <div class="col-md-5">
        <div class="mtop30 text-right">
            <div class="radio radio-inline radio-primary">
                <input type="radio" id="k_desc_equipment" name="settings[default_equipments_pipeline_sort_type]"
                    value="asc"
                    <?= get_option('default_equipments_pipeline_sort_type') == 'asc' ? 'checked' : '' ?>>
                <label
                    for="k_desc_equipment"><?= _l('order_ascending'); ?></label>
            </div>
            <div class="radio radio-inline radio-primary">
                <input type="radio" id="k_asc_equipment" name="settings[default_equipments_pipeline_sort_type]"
                    value="desc"
                    <?= get_option('default_equipments_pipeline_sort_type') == 'desc' ? 'checked' : '' ?>>
                <label
                    for="k_asc_equipment"><?= _l('order_descending'); ?></label>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<hr />
<?= render_textarea('settings[predefined_clientnote_equipment]', 'settings_predefined_clientnote', get_option('predefined_clientnote_equipment'), ['rows' => 6]); ?>
<?= render_textarea('settings[predefined_terms_equipment]', 'settings_predefined_predefined_term', get_option('predefined_terms_equipment'), ['rows' => 6]); ?>

<hr />
<?php
$_CI        = &get_instance();
$_all_roles = $_CI->db->select('roleid, name')->order_by('roleid', 'ASC')
    ->get(db_prefix() . 'roles')->result_array();
?>
<h5 class="tw-font-semibold tw-mb-1"><?= _l('settings_equipment_menu_access'); ?></h5>
<p class="tw-text-sm tw-text-neutral-500 tw-mb-3"><?= _l('settings_equipment_menu_access_help'); ?></p>
<div class="row">
    <?php foreach ($_all_roles as $_role) {
        $_rid     = (int) $_role['roleid'];
        $_key     = 'equipment_view_role_' . $_rid;
        $_checked = (get_option($_key) == '1');
    ?>
    <div class="col-md-3">
        <div class="tw-flex tw-items-center tw-gap-2 tw-mb-2">
            <input type="hidden" name="settings[<?= $_key; ?>]" value="0">
            <input type="checkbox"
                name="settings[<?= $_key; ?>]"
                id="<?= $_key; ?>"
                value="1"
                <?= $_checked ? 'checked' : ''; ?>>
            <label for="<?= $_key; ?>" class="tw-mb-0 tw-font-normal">
                <?= e($_role['name']); ?>
            </label>
        </div>
    </div>
    <?php } ?>
</div>
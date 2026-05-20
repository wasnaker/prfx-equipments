<?php defined('BASEPATH') or exit('No direct script access allowed');
$_prefix      = get_option('equipment_number_prefix');
$_length      = get_option('equipment_code_length') ?: '8';
$_unit_prefix = get_option('unit_code_prefix') ?: 'UNIT-';
$_unit_next   = get_option('next_unit_code_number') ?: '1';
$_unit_pad    = get_option('number_padding_prefixes') ?: '6';
$_CI        = &get_instance();
$_all_roles = $_CI->db->select('roleid, name')->order_by('roleid','ASC')
    ->get(db_prefix() . 'roles')->result_array();
?>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="equipment_number_prefix" class="control-label">
                <?= _l('settings_equipment_number_prefix'); ?>
            </label>
            <input type="text" name="settings[equipment_number_prefix]" id="equipment_number_prefix"
                class="form-control"
                value="<?= e($_prefix); ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="equipment_code_length" class="control-label">
                <?= _l('settings_equipment_code_length'); ?>
            </label>
            <input type="number" min="4" max="16" name="settings[equipment_code_length]" id="equipment_code_length"
                class="form-control"
                value="<?= e($_length); ?>">
            <span class="help-block"><?= _l('settings_equipment_code_length_help'); ?></span>
        </div>
    </div>
</div>

<hr />

<h5 class="tw-font-semibold tw-mb-2"><?= _l('settings_unit_code'); ?></h5>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="unit_code_prefix" class="control-label">
                <?= _l('settings_unit_code_prefix'); ?>
            </label>
            <input type="text" name="settings[unit_code_prefix]" id="unit_code_prefix"
                class="form-control" value="<?= e($_unit_prefix); ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="next_unit_code_number" class="control-label">
                <?= _l('settings_next_unit_code_number'); ?>
            </label>
            <input type="number" min="1" name="settings[next_unit_code_number]" id="next_unit_code_number"
                class="form-control" value="<?= e($_unit_next); ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="unit_code_padding" class="control-label">
                <?= _l('settings_unit_code_padding'); ?>
            </label>
            <input type="number" min="1" max="10" name="settings[number_padding_prefixes]" id="unit_code_padding"
                class="form-control" value="<?= e($_unit_pad); ?>">
        </div>
    </div>
</div>

<hr />

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

<hr />

<h5 class="tw-font-semibold tw-mb-1"><?= _l('settings_equipment_rate_visibility'); ?></h5>
<p class="tw-text-sm tw-text-neutral-500 tw-mb-3"><?= _l('settings_equipment_rate_visibility_help'); ?></p>
<div class="row">
    <?php foreach ($_all_roles as $_role) {
        $_key     = 'equipment_rate_show_role_' . (int)$_role['roleid'];
        $_opt     = get_option($_key);
        $_checked = ($_opt == '1');
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

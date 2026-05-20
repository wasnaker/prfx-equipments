<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s accounting-template equipment">
    
    <div class="panel-body">

        <h4 class="tw-my-0 tw-font-bold tw-text-xl tw-mb-4">
            <?= isset($equipment) ? _l('edit_equipment') : _l('new_equipment'); ?>
        </h4>

        <?= form_open(admin_url('equipments/add_edit_equipment'), ['autocomplete' => 'off']); ?>

        <?php if (isset($equipment)) { ?>
        <?= form_hidden('itemid', $equipment->itemid); ?>
        <?php } ?>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="code" class="control-label"><?= _l('equipment_code'); ?></label>
                    <input type="text" id="code" name="code" class="form-control"
                        value="<?= isset($equipment) ? e($equipment->code ?? '') : e($next_code ?? ''); ?>"
                        <?= isset($equipment) ? '' : 'placeholder="' . e($next_code ?? '') . '"'; ?>>
                    <span class="help-block"><?= _l('equipment_code_help'); ?></span>
                </div>

                <?php $value = isset($equipment) ? $equipment->description : ''; ?>
                <?= render_input('description', 'equipment_name', $value, 'text', ['required' => true, 'autofocus' => !isset($equipment)]); ?>

                <?php $value = isset($equipment) ? $equipment->long_description : ''; ?>
                <?= render_textarea('long_description', 'long_description', $value); ?>
            </div>

            <div class="col-md-6">
                <?php
                $selected_group    = isset($equipment) ? $equipment->group_id : '';
                $selected_subgroup = isset($equipment) ? ($equipment->subgroup_id ?? '') : '';
                echo render_select('group_id', $groups, ['id', 'name'], 'equipment_group', $selected_group);
                ?>

                <?php
                $subgroups_for_select = $selected_group
                    ? array_values(array_filter($subgroups, function($sg) use ($selected_group) {
                        return $sg['group_id'] == $selected_group;
                    }))
                    : $subgroups;
                echo render_select('subgroup_id', $subgroups_for_select, ['id', 'name'], 'subgroup_name', $selected_subgroup,
                    ['data-none-selected-text' => '— ' . _l('select_subgroup') . ' —'], [], '', '', true);
                ?>

                <?php
                // Category — AJAX search filtered by subgroup_id
                $selected_category = isset($equipment) ? ($equipment->category_id ?? '') : '';
                $prefill_category  = [];
                if ($selected_category) {
                    $cat = get_instance()->db->get_where(db_prefix() . 'items_categories', ['id' => $selected_category])->row();
                    if ($cat) { $prefill_category = [['id' => $cat->id, 'name' => $cat->name]]; }
                }
                echo render_select('category_id', $prefill_category, ['id', 'name'], 'equipment_category',
                    $selected_category,
                    ['data-none-selected-text' => '— ' . _l('select_category') . ' —', 'class' => 'ajax-search'],
                    [], '', '', true);
                ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rate" class="control-label"><?= _l('equipment_rate'); ?></label>
                            <input type="number" step="0.01" id="rate" name="rate" class="form-control"
                                value="<?= isset($equipment) ? e($equipment->rate) : ''; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?= render_input('unit', 'equipment_unit', isset($equipment) ? $equipment->unit : ''); ?>
                    </div>
                </div>
            </div>
        </div>

        <?= form_close(); ?>

    </div>

    <hr class="hr-panel-separator" />

    <?php $this->load->view('admin/equipments/_add_edit_jobs'); ?>

    <hr class="hr-panel-separator" />

    <div class="panel-body">
        <?php
            $value = (isset($equipment) ? $equipment->clientnote : get_option('predefined_clientnote_equipment'));
            echo render_textarea('clientnote', 'equipment_add_edit_client_note', $value);
            $value = (isset($equipment) ? $equipment->terms : get_option('predefined_terms_equipment'));
            echo render_textarea('terms', 'terms_and_conditions', $value, [], [], 'mtop15');
        ?>
    </div>
</div>

<div class="btn-bottom-pusher"></div>
<div class="btn-bottom-toolbar text-right">
    <div class="btn-group dropup">
        <button type="button" class="btn-tr btn btn-primary equipment-form-submit transaction-submit">
            <?php echo _l('submit'); ?>
        </button>
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right width200">
            <li>
                <a href="#" class="equipment-form-submit save-and-send transaction-submit">
                    <?php echo _l('save_and_send'); ?>
                </a>
            </li>
            <?php if (!isset($equipment)) { ?>
            <li>
                <a href="#" class="equipment-form-submit save-and-send-later transaction-submit">
                    <?php echo _l('save_and_send_later'); ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>
</div>

<script>
var _all_subgroups = <?= json_encode(array_values($subgroups)); ?>;

$(function() {
    $('#group_id').on('changed.bs.select', function() {
        _refresh_subgroups($(this).val(), '');
        // Reset category when group changes
        $('#category_id').selectpicker('val', '');
    });

    $('#subgroup_id').on('changed.bs.select', function() {
        // Reset category when subgroup changes
        $('#category_id').selectpicker('val', '');
    });

    // Init AJAX search for category filtered by subgroup
    init_ajax_search_categories_by_subgroup_id();
});

function _refresh_subgroups(group_id, selected_id) {
    var filtered = group_id
        ? _all_subgroups.filter(function(sg) { return sg.group_id == group_id; })
        : _all_subgroups;

    var $sel = $('#subgroup_id');
    $sel.find('option:not([value=""])').remove();

    $.each(filtered, function(i, sg) {
        var opt = $('<option>').val(sg.id).text(sg.name);
        if (selected_id && sg.id == selected_id) { opt.attr('selected', true); }
        $sel.append(opt);
    });
    $sel.selectpicker('refresh');
}
</script>

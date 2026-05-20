<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s"><div class="panel-body">

    <h4 class="tw-my-0 tw-font-bold tw-text-xl tw-mb-4">
        <?= isset($category) ? _l('edit_category') : _l('new_category'); ?>
    </h4>

    <?= form_open(admin_url('equipments/categories/add_edit_category'), ['id' => 'category-form', 'autocomplete' => 'off']); ?>

    <?php if (isset($category)) { ?>
    <?= form_hidden('id', $category->id); ?>
    <?php } ?>

    <div class="row">
        <div class="col-md-6">
            <?= render_input('name', 'category_name', isset($category) ? $category->name : '', 'text', ['required' => true, 'autofocus' => true]); ?>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="code" class="control-label"><?= _l('code'); ?></label>
                <input type="text" id="code" name="code" class="form-control"
                    value="<?= isset($category) ? e($category->code ?? '') : ''; ?>"
                    placeholder="CRN, HST, FRK...">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="sort_order" class="control-label"><?= _l('sort_order'); ?></label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" min="0"
                    value="<?= isset($category) ? e($category->sort_order ?? 0) : '0'; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <?php
        $selected_group    = isset($category) ? ($category->group_id ?? '') : '';
        $selected_subgroup = isset($category) ? ($category->subgroup_id ?? '') : '';
        ?>
        <div class="col-md-6">
            <?= render_select('group_id', $groups, ['id', 'name'], 'equipment_group', $selected_group,
                ['data-none-selected-text' => '— ' . _l('select') . ' —']); ?>
        </div>
        <div class="col-md-6">
            <?php
            $subgroups_for_select = $selected_group
                ? array_values(array_filter($subgroups, function($sg) use ($selected_group) {
                    return $sg['group_id'] == $selected_group;
                }))
                : $subgroups;
            echo render_select('subgroup_id', $subgroups_for_select, ['id', 'name'], 'equipment_subgroup',
                $selected_subgroup,
                ['data-none-selected-text' => '— ' . _l('select_subgroup') . ' —'],
                [], '', '', false, ['required' => true]);
            ?>
        </div>
    </div>

    <div class="tw-mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save tw-mr-1"></i><?= _l('save'); ?>
        </button>
        <a href="<?= admin_url('equipments/categories'); ?>" class="btn btn-default tw-ml-2">
            <?= _l('cancel'); ?>
        </a>
    </div>

    <?= form_close(); ?>

</div></div>

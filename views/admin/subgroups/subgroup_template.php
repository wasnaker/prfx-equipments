<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s"><div class="panel-body">

    <h4 class="tw-my-0 tw-font-bold tw-text-xl tw-mb-4">
        <?= isset($subgroup) ? _l('edit_subgroup') : _l('new_subgroup'); ?>
    </h4>

    <?= form_open(admin_url('equipments/subgroups/add_edit_subgroup'), ['id' => 'subgroup-form', 'autocomplete' => 'off']); ?>

    <?php if (isset($subgroup)) { ?>
    <?= form_hidden('id', $subgroup->id); ?>
    <?php } ?>

    <div class="row">
        <div class="col-md-6">
            <?= render_input('name', 'subgroup_name', isset($subgroup) ? $subgroup->name : '', 'text', ['required' => true, 'autofocus' => true]); ?>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="code" class="control-label"><?= _l('code'); ?></label>
                <input type="text" id="code" name="code" class="form-control"
                    value="<?= isset($subgroup) ? e($subgroup->code ?? '') : ''; ?>"
                    placeholder="BB, PU, TT...">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="color" class="control-label"><?= _l('color'); ?></label>
                <input type="color" id="color" name="color" class="form-control"
                    value="<?= isset($subgroup) ? e($subgroup->color ?? '#3b82f6') : '#3b82f6'; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= render_select('group_id', $groups, ['id', 'name'], 'equipment_group', isset($subgroup) ? $subgroup->group_id : '', [], [], '', '', false, ['required' => true]); ?>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="sort_order" class="control-label"><?= _l('sort_order'); ?></label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" min="0"
                    value="<?= isset($subgroup) ? e($subgroup->sort_order ?? 0) : '0'; ?>">
            </div>
        </div>
    </div>

    <div class="tw-mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save tw-mr-1"></i><?= _l('save'); ?>
        </button>
        <a href="<?= admin_url('equipments/subgroups'); ?>" class="btn btn-default tw-ml-2">
            <?= _l('cancel'); ?>
        </a>
    </div>

    <?= form_close(); ?>

</div></div>

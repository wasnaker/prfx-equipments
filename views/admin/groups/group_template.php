<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s">
    <div class="panel-body">

        <h4 class="tw-my-0 tw-font-bold tw-text-xl tw-mb-4">
            <?= isset($group) ? _l('edit_group') : _l('new_group'); ?>
        </h4>

        <?= form_open(admin_url('equipments/groups/add_edit_group'), ['id' => 'group-form', 'autocomplete' => 'off']); ?>

        <?php if (isset($group)) { ?>
        <?= form_hidden('id', $group->id); ?>
        <?php } ?>

        <div class="row">
            <div class="col-md-6">
                <?= render_input('name', 'group_name', isset($group) ? $group->name : '', 'text', ['required' => true, 'autofocus' => true]); ?>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="code" class="control-label"><?= _l('group_code'); ?></label>
                    <input type="text" id="code" name="code" class="form-control"
                        value="<?= isset($group) ? e($group->code ?? '') : ''; ?>"
                        placeholder="PAA, PUBT, ILP...">
                </div>
            </div>
            <div class="col-md-3">
                <?= render_select('status', [[1, _l('active')], [0, _l('inactive')]], ['0', '1'], 'group_statuses', isset($group) ? $group->status : 1); ?>
            </div>
        </div>

        <div class="tw-mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save tw-mr-1"></i><?= _l('save'); ?>
            </button>
            <a href="<?= admin_url('equipments/groups'); ?>" class="btn btn-default tw-ml-2">
                <?= _l('cancel'); ?>
            </a>
        </div>

        <?= form_close(); ?>

    </div>
</div>

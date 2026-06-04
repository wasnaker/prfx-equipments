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

        <?= form_close(); ?>
    </div>
</div>
<div class="btn-bottom-pusher"></div>
<div class="btn-bottom-toolbar text-right">
    <button type="submit" form="group-form" class="btn btn-primary"><?= _l('submit'); ?></button>
    <a href="<?= isset($group) ? admin_url('equipments/groups#' . $group->id) : admin_url('equipments/groups'); ?>" class="btn btn-secondary"><?= _l('cancel'); ?></a>
</div>

    </div>
</div>

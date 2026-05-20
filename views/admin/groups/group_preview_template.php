<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 no-padding">
    <div class="panel_s">
        <div class="panel-body">

            <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_group_overview" aria-controls="tab_group_overview" role="tab" data-toggle="tab">
                                <?= _l('overview'); ?>
                            </a>
                        </li>
                        <li role="presentation" data-toggle="tooltip" data-title="<?= _l('toggle_full_view'); ?>" class="tab-separator toggle_view">
                            <a href="#" onclick="small_table_full_view(); return false;"><i class="fa fa-expand"></i></a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Entity name — always visible across all tabs -->
            <div class="row mtop20 mbot10">
                <div class="col-xs-12">
                    <div class="tw-flex tw-items-start tw-justify-between">
                        <div class="tw-flex-1">
                            <div class="tw-mb-1">
                                <?php if (!empty($group->status)): ?>
                                <span class="label label-success"><?= _l('active'); ?></span>
                                <?php else: ?>
                                <span class="label label-default"><?= _l('inactive'); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="tw-flex tw-items-baseline tw-gap-2">
                                <span class="tw-font-mono tw-text-xs tw-text-neutral-400 tw-bg-neutral-100 tw-px-1.5 tw-py-0.5 tw-rounded">
                                    <?= e($group->code ?? '—'); ?>
                                </span>
                                <span class="tw-font-bold tw-text-neutral-800"><?= e($group->name); ?></span>
                            </div>
                        </div>
                        <div class="tw-flex-shrink-0 tw-ml-2">
                            <?php if (staff_can('edit', 'equipments')) { ?>
                            <a href="<?= admin_url('equipments/groups/group/' . $group->id); ?>"
                                class="btn btn-default btn-with-tooltip sm:!tw-px-3"
                                data-toggle="tooltip" data-placement="bottom" title="<?= _l('edit'); ?>">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="tw-my-3" />

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab_group_overview">
                    <table class="table table-condensed no-border no-mtop">
                        <tr>
                            <td class="tw-w-1/3 tw-text-neutral-500"><i class="fa fa-layer-group fa-fw"></i> <?= _l('equipment_subgroup'); ?></td>
                            <td><span class="badge"><?= e($totalSubs); ?></span></td>
                        </tr>
                        <tr>
                            <td class="tw-text-neutral-500"><i class="fa fa-cubes fa-fw"></i> <?= _l('equipments'); ?></td>
                            <td><span class="badge"><?= e($totalItems); ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    init_btn_with_tooltips();
    init_tabs_scrollable();
</script>

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 no-padding">
    <div class="panel_s"><div class="panel-body">

        <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tab_subgroup_overview" aria-controls="tab_subgroup_overview" role="tab" data-toggle="tab">
                            <?= _l('overview'); ?>
                        </a>
                    </li>
                    <?php
                    $sg_id    = (int)$subgroup->id;
                    $tab_meta = function_exists('_inspections_tab_meta') ? _inspections_tab_meta() : [];
                    $sg_tabs  = function_exists('_inspections_get_subgroup_tabs') ? _inspections_get_subgroup_tabs($sg_id) : [];
                    $cl_keys  = function_exists('_inspections_get_checklist_tab_keys') ? _inspections_get_checklist_tab_keys($sg_id) : [];

                    foreach ($sg_tabs as $tab) {
                        $tid = 'tab_sg_' . $tab['id'];
                        echo '<li role="presentation" data-toggle="tooltip" data-title="' . e($tab['tab_label']) . '">'
                           . '<a href="#' . $tid . '" aria-controls="' . $tid . '" role="tab" data-toggle="tab">'
                           . '<i class="' . e($tab['tab_icon']) . '"></i></a></li>';
                    }

                    foreach ($cl_keys as $tab_key) {
                        $meta = $tab_meta[$tab_key] ?? ['label' => $tab_key, 'icon' => 'fa fa-list'];
                        $tid  = 'tab_cl_' . $tab_key;
                        echo '<li role="presentation" data-toggle="tooltip" data-title="' . e($meta['label']) . '">'
                           . '<a href="#' . $tid . '" aria-controls="' . $tid . '" role="tab" data-toggle="tab">'
                           . '<i class="' . e($meta['icon']) . '"></i></a></li>';
                    }
                    ?>
                    <?php hooks()->do_action('subgroup_extra_tabs_nav', $subgroup); ?>
                    <li role="presentation" data-toggle="tooltip" data-title="<?= _l('toggle_full_view'); ?>" class="tab-separator toggle_view">
                        <a href="#" onclick="small_table_full_view(); return false;"><i class="fa fa-expand"></i></a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Entity name — always visible across all tabs -->
        <div class="row mtop20 mbot10">
            <div class="col-xs-12">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <span class="tw-inline-block tw-w-5 tw-h-5 tw-rounded tw-border tw-flex-shrink-0"
                          style="background:<?= e($subgroup->color ?? '#3b82f6'); ?>"></span>
                    <div>
                        <div class="tw-text-xs tw-text-neutral-400 tw-uppercase tw-tracking-wide tw-font-medium tw-mb-0.5">
                            <?= e($subgroup->group_name ?? '—'); ?>
                        </div>
                        <span class="tw-font-bold tw-text-neutral-800 tw-text-sm"><?= e($subgroup->name); ?></span>
                    </div>
                    <div class="tw-ml-auto tw-flex tw-items-center tw-gap-2">
                        <?php if (staff_can('edit', 'equipments')) { ?>
                        <a href="<?= admin_url('equipments/subgroups/subgroup/' . $subgroup->id); ?>"
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
            <div role="tabpanel" class="tab-pane active" id="tab_subgroup_overview">
                <table class="table table-condensed no-border no-mtop">
                    <tr>
                        <td class="tw-w-1/3 tw-text-neutral-500"><i class="fa fa-code fa-fw"></i> <?= _l('code'); ?></td>
                        <td><span class="tw-font-mono"><?= e($subgroup->code ?? '—'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="tw-text-neutral-500"><i class="fa fa-tag fa-fw"></i> <?= _l('equipment_group'); ?></td>
                        <td><?= e($subgroup->group_name ?? '—'); ?></td>
                    </tr>
                    <tr>
                        <td class="tw-text-neutral-500"><i class="fa fa-cubes fa-fw"></i> <?= _l('equipments'); ?></td>
                        <td><span class="badge"><?= e($totalItems); ?></span></td>
                    </tr>
                </table>
            </div>

            <?php foreach ($sg_tabs as $tab) {
                $tid    = 'tab_sg_' . $tab['id'];
                $fields = json_decode($tab['fields_json'], true) ?: [];
            ?>
            <div role="tabpanel" class="tab-pane" id="<?= $tid; ?>">
                <p class="tw-font-semibold tw-mb-2"><?= e($tab['tab_label']); ?></p>
                <?php if (!empty($fields)) { ?>
                <table class="table table-condensed table-bordered tw-text-xs">
                    <thead><tr><th>Field</th><th>Unit</th></tr></thead>
                    <tbody>
                    <?php foreach ($fields as $f) { ?>
                        <tr>
                            <td><?= e($f['label'] ?? $f); ?></td>
                            <td><?= e($f['unit'] ?? '—'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <p class="tw-text-neutral-400 tw-text-xs"><?= _l('no_data'); ?></p>
                <?php } ?>
            </div>
            <?php } ?>

            <?php foreach ($cl_keys as $tab_key) {
                $meta  = $tab_meta[$tab_key] ?? ['label' => $tab_key, 'icon' => 'fa fa-list'];
                $tid   = 'tab_cl_' . $tab_key;
                $items = $checklist_tabs[$tab_key] ?? [];
                $sections = [];
                foreach ($items as $item) {
                    $sections[$item['section_label'] ?? ''][] = $item;
                }
            ?>
            <div role="tabpanel" class="tab-pane" id="<?= $tid; ?>">
                <p class="tw-font-semibold tw-mb-2"><i class="<?= e($meta['icon']); ?> tw-mr-1"></i><?= e($meta['label']); ?></p>
                <?php foreach ($sections as $section_label => $section_items) { ?>
                <?php if ($section_label) { ?>
                <div class="tw-text-xs tw-font-semibold tw-uppercase tw-text-neutral-500 tw-tracking-wide tw-mt-3 tw-mb-1"><?= e($section_label); ?></div>
                <?php } ?>
                <table class="table table-condensed table-bordered tw-text-xs">
                    <thead>
                        <tr>
                            <th style="width:36px">No</th>
                            <th>Komponen</th>
                            <th>Ketentuan</th>
                            <th style="width:80px">Tipe</th>
                            <th style="width:56px">Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($section_items as $item) { ?>
                        <tr>
                            <td class="tw-text-neutral-400"><?= e($item['item_no'] ?? ''); ?></td>
                            <td><?= e($item['komponen']); ?></td>
                            <td class="tw-text-neutral-500"><?= e($item['ketentuan'] ?? ''); ?></td>
                            <td><span class="label label-default"><?= e($item['field_type']); ?></span></td>
                            <td><?= e($item['unit'] ?? '—'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
                <div class="tw-mt-4 tw-pt-3 tw-border-t tw-border-neutral-200">
                    <div class="tw-text-xs tw-font-semibold tw-uppercase tw-text-neutral-500 tw-tracking-wide tw-mb-2">
                        Hasil &amp; Rekomendasi
                    </div>
                    <div class="tw-grid tw-grid-cols-2 tw-gap-3">
                        <div>
                            <div class="tw-text-xs tw-text-neutral-400 tw-mb-1">Hasil Keseluruhan</div>
                            <div class="tw-text-xs tw-text-neutral-300 tw-italic tw-bg-neutral-50 tw-rounded tw-p-2">
                                Diisi otomatis saat inspeksi selesai
                            </div>
                        </div>
                        <div>
                            <div class="tw-text-xs tw-text-neutral-400 tw-mb-1">Saran / Rekomendasi</div>
                            <div class="tw-text-xs tw-text-neutral-300 tw-italic tw-bg-neutral-50 tw-rounded tw-p-2">
                                Diisi otomatis saat inspeksi selesai
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php hooks()->do_action('subgroup_extra_tabs_content', $subgroup); ?>

        </div>

    </div></div>
</div>
<script>init_btn_with_tooltips(); init_tabs_scrollable();</script>

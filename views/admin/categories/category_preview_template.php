<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 no-padding">
    <div class="panel_s"><div class="panel-body">

        <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
                <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tab_category_overview" aria-controls="tab_category_overview" role="tab" data-toggle="tab">
                            <?= _l('overview'); ?>
                        </a>
                    </li>
                    <?php
                    $tab_meta = function_exists('_inspections_tab_meta') ? _inspections_tab_meta() : [];
                    $cl_keys  = array_keys($checklist_tabs);
                    foreach ($cl_keys as $tab_key) {
                        $meta = $tab_meta[$tab_key] ?? ['label' => $tab_key, 'icon' => 'fa fa-list'];
                        $tid  = 'tab_cl_cat_' . $tab_key;
                        echo '<li role="presentation" data-toggle="tooltip" data-title="' . e($meta['label']) . '">'
                           . '<a href="#' . $tid . '" aria-controls="' . $tid . '" role="tab" data-toggle="tab">'
                           . '<i class="' . e($meta['icon']) . '"></i></a></li>';
                    }
                    ?>
                    <?php hooks()->do_action('category_extra_tabs_nav', $category); ?>
                    <li role="presentation" data-toggle="tooltip" data-title="<?= _l('toggle_full_view'); ?>" class="tab-separator toggle_view">
                        <a href="#" onclick="small_table_full_view(); return false;"><i class="fa fa-expand"></i></a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row mtop20 mbot10">
            <div class="col-xs-12">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <div>
                        <div class="tw-text-xs tw-text-neutral-400 tw-uppercase tw-tracking-wide tw-font-medium tw-mb-0.5">
                            <?= e($category->group_name ?? '—'); ?> &rsaquo; <?= e($category->subgroup_name ?? '—'); ?>
                        </div>
                        <span class="tw-font-bold tw-text-neutral-800 tw-text-sm"><?= e($category->name); ?></span>
                    </div>
                    <div class="tw-ml-auto tw-flex tw-items-center tw-gap-2">
                        <?php if (staff_can('edit', 'equipments')) { ?>
                        <a href="<?= admin_url('equipments/categories/category/' . $category->id); ?>"
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

            <div role="tabpanel" class="tab-pane active" id="tab_category_overview">
                <table class="table table-condensed no-border no-mtop">
                    <tr>
                        <td class="tw-w-1/3 tw-text-neutral-500"><i class="fa fa-code fa-fw"></i> <?= _l('code'); ?></td>
                        <td><span class="tw-font-mono"><?= e($category->code ?? '—'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="tw-text-neutral-500"><i class="fa fa-tag fa-fw"></i> <?= _l('equipment_group'); ?></td>
                        <td><?= e($category->group_name ?? '—'); ?></td>
                    </tr>
                    <tr>
                        <td class="tw-text-neutral-500"><i class="fa fa-layer-group fa-fw"></i> <?= _l('equipment_subgroup'); ?></td>
                        <td><?= e($category->subgroup_name ?? '—'); ?></td>
                    </tr>
                    <tr>
                        <td class="tw-text-neutral-500"><i class="fa fa-sort-numeric-asc fa-fw"></i> <?= _l('sort_order'); ?></td>
                        <td><?= e($category->sort_order ?? 0); ?></td>
                    </tr>
                    <tr>
                        <td class="tw-text-neutral-500"><i class="fa fa-cubes fa-fw"></i> <?= _l('equipments'); ?></td>
                        <td><span class="badge"><?= e($totalItems); ?></span></td>
                    </tr>
                </table>
            </div>

            <?php foreach ($cl_keys as $tab_key) {
                $meta     = $tab_meta[$tab_key] ?? ['label' => $tab_key, 'icon' => 'fa fa-list'];
                $tid      = 'tab_cl_cat_' . $tab_key;
                $items    = $checklist_tabs[$tab_key] ?? [];
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

            <?php hooks()->do_action('category_extra_tabs_content', $category); ?>

        </div>

    </div></div>
</div>
<script>init_btn_with_tooltips(); init_tabs_scrollable();</script>

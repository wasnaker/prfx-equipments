<?php
$CI     = &get_instance();
$_total = (int) $CI->db->count_all(db_prefix() . 'items_categories');

$_group_counts = $CI->db->select('subgroup_id, COUNT(*) as total')
    ->group_by('subgroup_id')
    ->get(db_prefix() . 'items_categories')->result_array();
$_group_map = array_column($_group_counts, 'total', 'subgroup_id');
?>
<div class="quick-top-stats tw-mb-6">
    <div class="tw-grid tw-grid-cols-4 md:tw-grid-cols-6 lg:tw-grid-cols-8 tw-mb-0 tw-gap-2">

        <button type="button"
            @click="extra.categoriesRules = []"
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2 tw-px-3.5 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-100 tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
            <div class="tw-flex tw-items-center">
                <span class="tw-font-normal tw-text-base tw-text-neutral-700"><?= _l('all'); ?></span>
            </div>
            <div class="tw-mt-0.5 tw-text-neutral-600">
                <span class="tw-font-semibold"><?= e($_total); ?></span>
                <span class="tw-text-xs tw-text-neutral-400 tw-ml-1"><?= _l('equipment_categories'); ?></span>
            </div>
        </button>

        <?php foreach ($subgroups as $g):
            $g_id    = (int) $g['id'];
            $g_total = (int) ($_group_map[$g_id] ?? 0);
            $percent = $_total > 0 ? round($g_total / $_total * 100) : 0;
        ?>
        <button type="button"
            @click="extra.categoriesRules = <?= app\services\utilities\Js::from($categories_table->findRule('subgroup_id')->setValue([$g_id])); ?>"
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2 tw-px-3.5 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-100 tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
            <div class="tw-flex tw-items-center">
                <span class="tw-font-normal tw-text-sm tw-text-neutral-700"><?= e($g['name']); ?></span>
                <span class="tw-ml-1 tw-text-xs tw-text-neutral-400">(<?= $percent; ?>%)</span>
            </div>
            <div class="tw-mt-0.5 tw-text-neutral-600">
                <span class="tw-font-semibold"><?= e($g_total); ?></span>
                <span class="tw-text-xs tw-text-neutral-400 tw-ml-1">/ <?= e($_total); ?></span>
            </div>
        </button>
        <?php endforeach; ?>

    </div>
</div>

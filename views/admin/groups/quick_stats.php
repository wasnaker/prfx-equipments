<?php
$CI     = &get_instance();
$_total = (int) $CI->db->count_all(db_prefix() . 'items_groups');

$_status_counts = $CI->db->select('status, COUNT(*) as total')
    ->group_by('status')
    ->get(db_prefix() . 'items_groups')->result_array();
$_status_map = array_column($_status_counts, 'total', 'status');

$_status_labels = [
    1 => ['label' => _l('active'),   'color' => 'success'],
    0 => ['label' => _l('inactive'), 'color' => 'default'],
];
?>
<div class="quick-top-stats tw-mb-6">
    <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 tw-mb-0 tw-gap-2">

        <button type="button"
            @click="extra.groupsRules = []"
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2 tw-px-3.5 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-100 tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
            <div class="tw-flex tw-items-center">
                <span class="tw-font-normal tw-text-base tw-inline-flex tw-items-center tw-text-neutral-700">
                    <?= _l('all'); ?>
                </span>
            </div>
            <div class="tw-mt-0.5 tw-text-neutral-600">
                <span class="tw-font-semibold"><?= e($_total); ?></span>
                <span class="tw-text-xs tw-text-neutral-400 tw-ml-1"><?= _l('groups'); ?></span>
            </div>
        </button>

        <?php foreach ($group_statuses as $status):
            $status_total = (int) ($_status_map[$status] ?? 0);
            $percent      = $_total > 0 ? round($status_total / $_total * 100) : 0;
            $info         = $_status_labels[$status] ?? ['label' => $status, 'color' => 'default'];
        ?>
        <button type="button"
            @click="extra.groupsRules = <?= app\services\utilities\Js::from($groups_table->findRule('status')->setValue([$status])); ?>"
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2 tw-px-3.5 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-100 tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
            <div class="tw-flex tw-items-center">
                <span class="tw-font-normal tw-text-base tw-inline-flex tw-items-center text-<?= $info['color']; ?>">
                    <?= $info['label']; ?>
                </span>
                <span class="tw-ml-2 tw-text-xs tw-text-neutral-500 tw-mt-px">(<?= $percent; ?>%)</span>
            </div>
            <div class="tw-mt-0.5 tw-text-neutral-600">
                <span class="tw-font-semibold"><?= e($status_total); ?> / <?= e($_total); ?></span>
            </div>
        </button>
        <?php endforeach; ?>

    </div>
</div>

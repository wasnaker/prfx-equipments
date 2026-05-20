<?php
$CI    = &get_instance();
$_total = (int) $CI->db->count_all(db_prefix() . 'customer_equipment');

$_counts = $CI->db->select('status, COUNT(*) as total')
    ->group_by('status')
    ->get(db_prefix() . 'customer_equipment')->result_array();
$_map = array_column($_counts, 'total', 'status');

$_statuses = $equipment_statuses;
?>
<div class="quick-top-stats tw-mb-6">
    <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-5 tw-mb-0 tw-gap-2">

        <button type="button"
            @click="extra.myequipmentsRules = []"
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2 tw-px-3.5 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-100 tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
            <div class="tw-flex tw-items-center">
                <span class="tw-font-normal tw-text-base tw-text-neutral-700"><?= _l('all'); ?></span>
            </div>
            <div class="tw-mt-0.5 tw-text-neutral-600">
                <span class="tw-font-semibold"><?= e($_total); ?></span>
                <span class="tw-text-xs tw-text-neutral-400 tw-ml-1"><?= _l('my_equipment_units'); ?></span>
            </div>
        </button>

        <?php foreach ($_statuses as $status):
            $count   = (int) ($_map[$status] ?? 0);
            $percent = $_total > 0 ? round($count / $_total * 100) : 0;
        ?>
        <button type="button"
            @click="extra.myequipmentsRules = <?= app\services\utilities\Js::from($myequipments_table->findRule('status')->setValue([$status])); ?>"
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2 tw-px-3.5 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-100 tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
            <div class="tw-flex tw-items-center">
                <span class="tw-font-normal tw-text-base tw-inline-flex tw-items-center text-<?= equipment_status_color_class($status); ?>">
                    <?= format_equipment_status($status, '', false); ?>
                </span>
                <span class="tw-ml-2 tw-text-xs tw-text-neutral-500 tw-mt-px">(<?= $percent; ?>%)</span>
            </div>
            <div class="tw-mt-0.5 tw-text-neutral-600">
                <span class="tw-font-semibold"><?= e($count); ?> / <?= e($_total); ?></span>
            </div>
        </button>
        <?php endforeach; ?>

    </div>
</div>

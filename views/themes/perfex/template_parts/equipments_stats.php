<?php defined('BASEPATH') or exit('No direct script access allowed');
$where_total = ['client_id' => get_client_user_id()];

if (get_option('exclude_equipment_from_client_area_with_draft_status') == 1) {
    $where_total['status !='] = 1;
}
$total_equipments = total_rows(db_prefix() . 'equipments', $where_total);

$total_sent       = total_rows(db_prefix() . 'equipments', ['status' => 2, 'client_id' => get_client_user_id()]);
$total_declined   = total_rows(db_prefix() . 'equipments', ['status' => 3, 'client_id' => get_client_user_id()]);
$total_accepted   = total_rows(db_prefix() . 'equipments', ['status' => 4, 'client_id' => get_client_user_id()]);
$total_expired    = total_rows(db_prefix() . 'equipments', ['status' => 5, 'client_id' => get_client_user_id()]);
$percent_sent     = ($total_equipments > 0 ? number_format(($total_sent * 100) / $total_equipments, 2) : 0);
$percent_declined = ($total_equipments > 0 ? number_format(($total_declined * 100) / $total_equipments, 2) : 0);
$percent_accepted = ($total_equipments > 0 ? number_format(($total_accepted * 100) / $total_equipments, 2) : 0);
$percent_expired  = ($total_equipments > 0 ? number_format(($total_expired * 100) / $total_equipments, 2) : 0);
if (get_option('exclude_equipment_from_client_area_with_draft_status') == 0) {
    $col_class     = 'col-md-5ths col-xs-12';
    $total_draft   = total_rows(db_prefix() . 'equipments', ['status' => 1, 'client_id' => get_client_user_id()]);
    $percent_draft = ($total_equipments > 0 ? number_format(($total_draft * 100) / $total_equipments, 2) : 0);
} else {
    $col_class = 'col-md-3';
}
?>
<div class="row text-left equipments-stats">
    <?php if (get_option('exclude_equipment_from_client_area_with_draft_status') == 0) { ?>
    <div class="<?= e($col_class); ?> equipments-stats-draft">
        <div class="row">
            <div class="col-md-8 stats-status">
                <a href="<?= site_url('clients/equipments/1'); ?>"
                    class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                    <?= _l('equipment_status_draft'); ?>
                </a>
            </div>
            <div class="col-md-4 text-right bold stats-numbers">
                <?= e($total_draft); ?> /
                <?= e($total_equipments); ?>
            </div>
            <div class="col-md-12 tw-mt-1.5">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?= equipment_status_color_class(1); ?>"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent_draft); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="<?= e($col_class); ?> equipments-stats-sent">
        <div class="row">
            <div class="col-md-8 stats-status">
                <a href="<?= site_url('clients/equipments/2'); ?>"
                    class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                    <?= _l('equipment_status_sent'); ?>
                </a>
            </div>
            <div class="col-md-4 text-right bold stats-numbers">
                <?= e($total_sent); ?> /
                <?= e($total_equipments); ?>
            </div>
            <div class="col-md-12 tw-mt-1.5">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?= equipment_status_color_class(2); ?>"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent_sent); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="<?= e($col_class); ?> equipments-stats-expired">
        <div class="row">
            <div class="col-md-8 stats-status">
                <a href="<?= site_url('clients/equipments/5'); ?>"
                    class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                    <?= _l('equipment_status_expired'); ?>
                </a>
            </div>
            <div class="col-md-4 text-right bold stats-numbers">
                <?= e($total_expired); ?> /
                <?= e($total_equipments); ?>
            </div>
            <div class="col-md-12 tw-mt-1.5">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?= equipment_status_color_class(5); ?>"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent_expired); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="<?= e($col_class); ?> equipments-stats-declined">
        <div class="row">
            <div class="col-md-8 stats-status">
                <a href="<?= site_url('clients/equipments/3'); ?>"
                    class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                    <?= _l('equipment_status_declined'); ?>
                </a>
            </div>
            <div class="col-md-4 text-right bold stats-numbers">
                <?= e($total_declined); ?> /
                <?= e($total_equipments); ?>
            </div>
            <div class="col-md-12 tw-mt-1.5">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?= equipment_status_color_class(3); ?>"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent_declined); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="<?= e($col_class); ?> equipments-stats-accepted">
        <div class="row">
            <div class="col-md-8 stats-status">
                <a href="<?= site_url('clients/equipments/4'); ?>"
                    class="tw-text-neutral-600 hover:tw-text-neutral-800 active:tw-text-neutral-800 tw-font-medium">
                    <?= _l('equipment_status_accepted'); ?>
                </a>
            </div>
            <div class="col-md-4 text-right bold stats-numbers">
                <?= e($total_accepted); ?> /
                <?= e($total_equipments); ?>
            </div>
            <div class="col-md-12 tw-mt-1.5">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?= equipment_status_color_class(4); ?>"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent_accepted); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
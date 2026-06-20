<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?= form_hidden('_attachment_sale_id', $equipment->id); ?>
<?= form_hidden('_attachment_sale_type', 'equipment'); ?>
<div class="col-md-12 no-padding">
    <div class="panel_s">
        <div class="panel-body">
            <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_equipment" aria-controls="tab_equipment" role="tab" data-toggle="tab">
                                <?= _l('equipment'); ?>
                            </a>
                        </li>
                        <?php if (is_module_active('jobs')): ?>
                        <li role="presentation">
                            <a href="#tab_equipment_jobs"
                                onclick="initDataTable('.table-equipment-jobs', admin_url + 'equipments/get_jobs_table/' + <?= (int)$equipment->id; ?>, [0], [0], undefined, [1,'asc']); return false;"
                                aria-controls="tab_equipment_jobs" role="tab" data-toggle="tab">
                                <?php if (! is_mobile()) { ?>
                                <i class="fa fa-briefcase" aria-hidden="true"></i>
                                <?php } else { ?>
                                <?= _l('jobs'); ?>
                                <?php } ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li role="presentation" data-toggle="tooltip"
                            title="<?= _l('equipment_view_activity_tooltip'); ?>">
                            <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                                <?php if (! is_mobile()) { ?>
                                <i class="fa fa-history" aria-hidden="true"></i>
                                <?php } else { ?>
                                <?= _l('equipment_view_activity_tooltip'); ?>
                                <?php } ?>
                            </a>
                        </li>
                        <li role="presentation" data-toggle="tooltip"
                            title="<?= _l('equipment_reminders'); ?>">
                            <a href="#tab_reminders"
                                onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?= $equipment->id; ?> + '/' + 'equipment', undefined, undefined, undefined,[1,'asc']); return false;"
                                aria-controls="tab_reminders" role="tab" data-toggle="tab">
                                <?php if (! is_mobile()) { ?>
                                <i class="fa-regular fa-bell" aria-hidden="true"></i>
                                <?php } else { ?>
                                <?= _l('equipment_reminders'); ?>
                                <?php } ?>
                                <?php
                        $total_reminders = total_rows(
                            db_prefix() . 'reminders',
                            [
                                'isnotified' => 0,
                                'staff'      => get_staff_user_id(),
                                'rel_type'   => 'equipment',
                                'rel_id'     => $equipment->id,
                            ]
                        );
if ($total_reminders > 0) {
    echo '<span class="badge">' . $total_reminders . '</span>';
}
?>
                            </a>
                        </li>
                        <li role="presentation" class="tab-separator" data-toggle="tooltip"
                            title="<?= _l('equipment_notes'); ?>">
                            <a href="#tab_notes"
                                onclick="get_sales_notes(<?= e($equipment->id); ?>,'equipments'); return false"
                                aria-controls="tab_notes" role="tab" data-toggle="tab">
                                <?php if (! is_mobile()) { ?>
                                <i class="fa-regular fa-sticky-note" aria-hidden="true"></i>
                                <?php } else { ?>
                                <?= _l('equipment_notes'); ?>
                                <?php } ?>
                                <span class="notes-total">
                                    <?php if ($totalNotes > 0) { ?>
                                    <span
                                        class="badge"><?= e($totalNotes); ?></span>
                                    <?php } ?>
                                </span>
                            </a>
                        </li>
                        <li role="presentation" data-toggle="tooltip"
                            title="<?= _l('emails_tracking'); ?>"
                            class="tab-separator">
                            <a href="#tab_emails_tracking" aria-controls="tab_emails_tracking" role="tab"
                                data-toggle="tab">
                                <?php if (! is_mobile()) { ?>
                                <i class="fa-regular fa-envelope-open" aria-hidden="true"></i>
                                <?php } else { ?>
                                <?= _l('emails_tracking'); ?>
                                <?php } ?>
                            </a>
                        </li>
                        <li role="presentation" data-toggle="tooltip"
                            data-title="<?= _l('view_tracking'); ?>"
                            class="tab-separator">
                            <a href="#tab_views" aria-controls="tab_views" role="tab" data-toggle="tab">
                                <?php if (! is_mobile()) { ?>
                                <i class="fa fa-eye"></i>
                                <?php } else { ?>
                                <?= _l('view_tracking'); ?>
                                <?php } ?>
                            </a>
                        </li>
                        <li role="presentation" data-toggle="tooltip"
                            data-title="<?= _l('toggle_full_view'); ?>"
                            class="tab-separator toggle_view">
                            <a href="#" onclick="small_table_full_view(); return false;">
                                <i class="fa fa-expand"></i>
                            </a>
                        </li>
                        <?php hooks()->do_action('after_admin_equipment_preview_template_tab_menu_last_item', $equipment); ?>
                    </ul>
                </div>
            </div>
            <?php
            $_me_sc    = get_staff(get_staff_user_id());
            $_client_type = $_me_sc ? ($_me_sc->client_type ?? '') : '';
            ?>
            <div class="row mtop20">
                <div class="col-md-3">
                    <span id="equipment-status-badge"><?= format_equipment_status($equipment->status, 'mtop5 inline-block'); ?></span>
                </div>
                <div class="col-md-9">
                    <div class="visible-xs">
                        <div class="mtop10"></div>
                    </div>
                    <div class="pull-right _buttons">
                        <?php if (staff_can('edit', 'equipments')) { ?>
                        <a href="<?= admin_url('equipments/equipment/' . $equipment->id); ?>"
                            class="btn btn-default btn-with-tooltip sm:!tw-px-3" data-toggle="tooltip"
                            title="<?= _l('edit_equipment_tooltip'); ?>"
                            data-placement="bottom"><i class="fa-regular fa-pen-to-square"></i></a>
                        <?php } ?>
                        <?php if (staff_can('create', 'inspections') || staff_can('convert_to_inspection', 'equipments')) { ?>
                        <a href="<?= admin_url('inspections/inspection?equipment_id=' . $equipment->id . '&customer_id=' . $equipment->client_id); ?>"
                            class="btn btn-default btn-with-tooltip sm:!tw-px-3"
                            data-toggle="tooltip" data-placement="bottom"
                            title="<?= _l('convert_equipment_to_inspection'); ?>">
                            <i class="fa-regular fa-clipboard-check"></i>
                        </a>
                        <?php } ?>
                        <?php if (staff_can('create', 'myequipments')) { ?>
                        <a href="<?= admin_url('equipments/myequipments/myequipment?item_id=' . $equipment->id); ?>"
                            class="btn btn-primary btn-with-tooltip sm:!tw-px-3"
                            data-membership-guard="register_my_unit"
                            data-toggle="tooltip" data-placement="bottom"
                            title="<?= _l('register_my_unit'); ?>">
                            <i class="fa-regular fa-plus"></i>
                            <?= is_mobile() ? '' : ' ' . _l('register_my_unit'); ?>
                        </a>
                        <?php } ?>
                        <div class="btn-group">
                            <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"><i
                                    class="fa-regular fa-file-pdf"></i><?= is_mobile() ? ' PDF' : ''; ?>
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="hidden-xs">
                                    <a
                                        href="<?= admin_url('equipments/pdf/' . $equipment->id . '?output_type=I'); ?>">
                                        <?= _l('view_pdf'); ?>
                                    </a>
                                </li>
                                <li class="hidden-xs">
                                    <a href="<?= admin_url('equipments/pdf/' . $equipment->id . '?output_type=I'); ?>"
                                        target="_blank">
                                        <?= _l('view_pdf_in_new_window'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="<?= admin_url('equipments/pdf/' . $equipment->id); ?>">
                                        <?= _l('download'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= admin_url('equipments/pdf/' . $equipment->id . '?print=true'); ?>"
                                        target="_blank">
                                        <?= _l('print'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <?php
                     $_tooltip = _l('equipment_sent_to_email_tooltip');
$_tooltip_already_send         = '';
if ($equipment->sent == 1) {
    $_tooltip_already_send = _l('equipment_already_send_to_client_tooltip', time_ago($equipment->datesend));
}
?>
                        <?php if (! empty($equipment->client_id)) { ?>
                        <a href="#" class="equipment-send-to-client btn btn-default btn-with-tooltip sm:!tw-px-3"
                            data-toggle="tooltip"
                            title="<?= e($_tooltip); ?>"
                            data-placement="bottom"><span data-toggle="tooltip"
                                data-title="<?= e($_tooltip_already_send); ?>"><i
                                    class="fa-regular fa-envelope"></i></span></a>
                        <?php } ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default pull-left dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?= _l('more'); ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="<?= site_url('equipment/' . $equipment->id . '/' . $equipment->hash) ?>"
                                        target="_blank">
                                        <?= _l('view_equipment_as_client'); ?>
                                    </a>
                                </li>
                                <?php hooks()->do_action('after_equipment_view_as_client_link', $equipment); ?>
                                <?php if ((! empty($equipment->expirydate) && date('Y-m-d') < $equipment->expirydate && ($equipment->status == 2 || $equipment->status == 5)) && is_equipments_expiry_reminders_enabled()) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url('equipments/send_expiry_reminder/' . $equipment->id); ?>">
                                        <?= _l('send_expiry_reminder'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <li>
                                    <a href="#" data-toggle="modal"
                                        data-target="#sales_attach_file"><?= _l('invoice_attach_file'); ?></a>
                                </li>
                                <?php if (staff_can('create', 'projects') && $equipment->project_id == 0) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url("projects/project?via_equipment_id={$equipment->id}&customer_id={$equipment->client_id}") ?>">
                                        <?= _l('equipment_convert_to_project'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if ($_client_type === 'surveyor') {
                                        $_sc_mark_statuses = [EQUIPMENT_STATUS_DRAFT];
                                    } elseif ($_client_type === 'customer') {
                                        $_sc_mark_statuses = [EQUIPMENT_STATUS_EXPIRED];
                                    } elseif (staff_can('edit', 'equipments')) {
                                        $_sc_mark_statuses = $equipment_statuses;
                                    } else {
                                        $_sc_mark_statuses = [];
                                    }
                                    foreach ($_sc_mark_statuses as $status) { ?>
                                <li data-mark-status="<?= e($status); ?>"<?= $equipment->status == $status ? ' style="display:none"' : ''; ?>>
                                    <a href="#" onclick="equipment_mark_status(event, <?= e($equipment->id); ?>, <?= e($status); ?>)">
                                        <?= e(_l('equipment_mark_as', format_equipment_status($status, '', false))); ?></a>
                                </li>
                                <?php } ?>
                                <?php if (staff_can('create', 'equipments')) { ?>
                                <li>
                                    <a
                                        href="<?= admin_url('equipments/copy/' . $equipment->id); ?>">
                                        <?= _l('copy_equipment'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if (! empty($equipment->signature) && staff_can('delete', 'equipments')) { ?>
                                <li>
                                    <a href="<?= admin_url('equipments/clear_signature/' . $equipment->id); ?>"
                                        class="_delete">
                                        <?= _l('clear_signature'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if (staff_can('delete', 'equipments')) { ?>
                                <?php
                                               if ((get_option('delete_only_on_last_equipment') == 1 && is_last_equipment($equipment->id))
                                                   || (get_option('delete_only_on_last_equipment') == 0)) { ?>
                                <li>
                                    <a href="<?= admin_url('equipments/delete/' . $equipment->id); ?>"
                                        class="text-danger delete-text _delete">
                                        <?= _l('delete_equipment_tooltip'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr class="hr-panel-separator" />

            <!-- Entity name — always visible across all tabs -->
            <div class="tw-mb-3">
                <div class="tw-text-xs tw-text-neutral-400 tw-uppercase tw-tracking-wide tw-font-medium tw-mb-0.5">
                    <?= e($equipment->subgroup_name ?? '—'); ?>
                </div>
                <span class="tw-font-bold tw-text-neutral-800"><?= e($equipment->description); ?></span>
            </div>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="tab_equipment">

                    <table class="table table-condensed no-border no-mtop">
                        <?php if (!empty($equipment->long_description)) { ?>
                        <tr>
                            <td class="tw-w-1/3 tw-text-neutral-500"><i class="fa fa-align-left fa-fw"></i> <?= _l('long_description'); ?></td>
                            <td><?= nl2br(e($equipment->long_description)); ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td class="tw-text-neutral-500"><i class="fa fa-tag fa-fw"></i> <?= _l('equipment_group'); ?></td>
                            <td><?= e($equipment->group_name ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td class="tw-text-neutral-500"><i class="fa fa-sitemap fa-fw"></i> <?= _l('equipment_subgroup'); ?></td>
                            <td><?= e($equipment->subgroup_name ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td class="tw-text-neutral-500"><i class="fa fa-folder fa-fw"></i> <?= _l('category'); ?></td>
                            <td><?= e($equipment->category_name ?? '—'); ?></td>
                        </tr>
                        <?php if (!empty($show_rate)) { ?>
                        <tr>
                            <td class="tw-text-neutral-500"><i class="fa fa-money fa-fw"></i> <?= _l('equipment_rate'); ?></td>
                            <td><?= app_format_number($equipment->rate); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($equipment->unit)) { ?>
                        <tr>
                            <td class="tw-text-neutral-500"><i class="fa fa-cube fa-fw"></i> <?= _l('equipment_unit'); ?></td>
                            <td><?= e($equipment->unit); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($equipment->taxname)) { ?>
                        <tr>
                            <td class="tw-text-neutral-500"><i class="fa fa-percent fa-fw"></i> <?= _l('tax_1'); ?></td>
                            <td><?= e($equipment->taxname); ?> (<?= e($equipment->taxrate); ?>%)</td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($equipment->taxname_2)) { ?>
                        <tr>
                            <td class="tw-text-neutral-500"><i class="fa fa-percent fa-fw"></i> <?= _l('tax_2'); ?></td>
                            <td><?= e($equipment->taxname_2); ?> (<?= e($equipment->taxrate_2); ?>%)</td>
                        </tr>
                        <?php } ?>
                    </table>
                

                </div>
                <div role="tabpanel" class="tab-pane" id="tab_equipment_jobs">
                    <hr />
                    <?php render_datatable([
                        ['name' => '<span class="hide">-</span>', 'th_attrs' => ['class' => '']],
                        _l('the_number_sign'),
                        _l('equipment_job_name'),
                        _l('equipment_equipment_item_name'),
                        _l('equipment_equipment_unit_code'),
                        _l('equipment_equipment_serial_number'),
                        _l('equipment_equipment_location'),
                        _l('equipment_equipment_cert_expired'),
                    ], 'equipment-jobs'); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_reminders">
                    <a href="#" data-toggle="modal" class="btn btn-primary"
                        data-target=".reminder-modal-equipment-<?= e($equipment->id); ?>"><i
                            class="fa-regular fa-bell"></i>
                        <?= _l('equipment_set_reminder_title'); ?></a>
                    <hr />
                    <?php render_datatable([_l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders'); ?>
                    <?php $this->load->view('admin/includes/modals/reminder', ['id' => $equipment->id, 'name' => 'equipment', 'members' => $members, 'reminder_title' => _l('equipment_set_reminder_title')]); ?>
                </div>
                <div role="tabpanel" class="tab-pane ptop10" id="tab_emails_tracking">
                    <?php $this->load->view('admin/includes/emails_tracking', [
                        'tracked_emails' => get_tracked_emails($equipment->id, 'equipment'),
                    ]); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_notes">
                    <?= form_open(admin_url('equipments/add_note/' . $equipment->id), ['id' => 'equipment-notes', 'class' => 'equipment-notes-form']); ?>
                    <?= render_textarea('description'); ?>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary mtop15 mbot15">
                            <?= _l('equipment_add_note'); ?>
                        </button>
                    </div>
                    <?= form_close(); ?>
                    <hr />
                    <div class="mtop20" id="sales_notes_area">
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_activity">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="activity-feed">
                                <?php foreach ($activity as $activity) {
                                    $_custom_data = false; ?>
                                <div class="feed-item"
                                    data-sale-activity-id="<?= e($activity['id']); ?>">
                                    <div class="date">
                                        <span class="text-has-action" data-toggle="tooltip"
                                            data-title="<?= e(_dt($activity['date'])); ?>">
                                            <?= e(time_ago($activity['date'])); ?>
                                        </span>
                                    </div>
                                    <div class="text">
                                        <?php if (is_numeric($activity['staffid']) && $activity['staffid'] != 0) { ?>
                                        <a
                                            href="<?= admin_url('profile/' . $activity['staffid']); ?>">
                                            <?= staff_profile_image($activity['staffid'], ['staff-profile-xs-image pull-left mright5']);
                                            ?>
                                        </a>
                                        <?php } ?>
                                        <?php
                                            $additional_data = '';
                                    if (! empty($activity['additional_data'])) {
                                        $additional_data = app_unserialize($activity['additional_data']);
                                        $i               = 0;

                                        foreach ($additional_data as $data) {
                                            if (strpos($data, '<original_status>') !== false) {
                                                $original_status     = get_string_between($data, '<original_status>', '</original_status>');
                                                $additional_data[$i] = format_equipment_status($original_status, '', false);
                                            } elseif (strpos($data, '<new_status>') !== false) {
                                                $new_status          = get_string_between($data, '<new_status>', '</new_status>');
                                                $additional_data[$i] = format_equipment_status($new_status, '', false);
                                            } elseif (strpos($data, '<status>') !== false) {
                                                $status              = get_string_between($data, '<status>', '</status>');
                                                $additional_data[$i] = format_equipment_status($status, '', false);
                                            } elseif (strpos($data, '<custom_data>') !== false) {
                                                $_custom_data = get_string_between($data, '<custom_data>', '</custom_data>');
                                                unset($additional_data[$i]);
                                            }
                                            $i++;
                                        }
                                    }

                                    $_formatted_activity = _l($activity['description'], $additional_data);

                                    if ($_custom_data !== false) {
                                        $_formatted_activity .= ' - ' . $_custom_data;
                                    }

                                    if (! empty($activity['full_name'])) {
                                        $_formatted_activity = e($activity['full_name']) . ' - ' . $_formatted_activity;
                                    }

                                    echo $_formatted_activity;

                                    if (is_admin()) {
                                        echo '<a href="#" class="pull-right text-muted" onclick="delete_sale_activity(' . $activity['id'] . '); return false;"><i class="fa-regular fa-trash-can"></i></a>';
                                    } ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane ptop10" id="tab_views">
                    <?php
                  $views_activity = get_views_tracking('equipment', $equipment->id);
if (count($views_activity) === 0) {
    echo '<h4 class="tw-m-0 tw-text-base tw-font-medium tw-text-neutral-500">' . _l('not_viewed_yet', _l('equipment_lowercase')) . '</h4>';
}

foreach ($views_activity as $activity) { ?>
                    <p class="text-success no-margin">
                        <?= _l('view_date') . ': ' . _dt($activity['date']); ?>
                    </p>
                    <p class="text-muted">
                        <?= _l('view_ip') . ': ' . $activity['view_ip']; ?>
                    </p>
                    <hr />
                    <?php } ?>
                </div>
                <?php hooks()->do_action('after_admin_equipment_preview_template_tab_content_last_item', $equipment); ?>
            </div>
        </div>
    </div>
</div>
<script>
    init_items_sortable(true);
    init_btn_with_tooltips();
    init_datepicker();
    init_selectpicker();
    init_form_reminder();
    init_tabs_scrollable();
    init_equipment_notes();
</script>
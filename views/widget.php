<?php defined('BASEPATH') or exit('No direct script access allowed');

$_CI    = &get_instance();
$_me    = get_staff(get_staff_user_id());
$_ctype = $_me ? ($_me->client_type ?? '') : '';

// Resolve all conditions BEFORE building the query to avoid
// CI active-record reset caused by DB calls inside staff_can() / get_option().
$_extra_where = null;
if ($_ctype === 'customer' && ! empty($_me->client_id)) {
    $_extra_where = ['s.client_id' => (int) $_me->client_id];
} elseif ($_ctype === 'surveyor' && ! empty($_me->client_id)) {
    $_extra_where = 's.addedfrom IN (SELECT staffid FROM ' . db_prefix() . 'staff WHERE client_id = ' . (int) $_me->client_id . ' AND client_type = "surveyor")';
} elseif (! empty($_ctype)) {
    $_extra_where = '1 = 0';
} elseif (! staff_can('view', 'equipments')) {
    $_extra_where = get_equipments_where_sql_for_staff(get_staff_user_id());
}

// Build and execute query fresh — no prior ar_ state interference.
$_CI->db
    ->select('s.id, s.formatted_number, s.date, s.expirydate, s.status, c.company as client_company,
        (SELECT GROUP_CONCAT(CONCAT(st.firstname," ",st.lastname) ORDER BY st.firstname SEPARATOR ", ")
         FROM ' . db_prefix() . 'equipment_members sm
         JOIN ' . db_prefix() . 'staff st ON st.staffid = sm.staff_id
         WHERE sm.equipment_id = s.id) as members', false)
    ->from(db_prefix() . 'equipments s')
    ->join(db_prefix() . 'clients c', 'c.userid = s.client_id', 'left')
    ->where('s.date >=', date('Y-m-d'));

if ($_extra_where !== null) {
    if (is_array($_extra_where)) {
        foreach ($_extra_where as $k => $v) { $_CI->db->where($k, $v); }
    } else {
        $_CI->db->where($_extra_where, null, false);
    }
}

$_equipments = $_CI->db->order_by('s.date', 'asc')->limit(15)->get()->result_array();
?>
<div class="widget<?= count($_equipments) === 0 ? ' hide' : '' ?>"
     id="widget-<?= create_widget_id('equipments_widget') ?>"
     data-name="<?= _l('equipments') ?>">
    <div class="panel_s">
        <div class="panel-body padding-10">
            <div class="widget-dragger"></div>
            <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">
                <p class="tw-font-semibold tw-mb-0"><?= _l('equipments') ?></p>
                <a href="<?= admin_url('equipments/list_equipments') ?>"><?= _l('home_widget_view_all') ?></a>
            </div>
            <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">
            <div class="tw-overflow-x-auto">
                <table class="table dt-table" data-order-col="2" data-order-type="asc">
                    <thead>
                        <tr>
                            <th><?= _l('equipment_pdf_heading') ?></th>
                            <th><?= _l('equipment_to') ?></th>
                            <th><?= _l('equipment_data_date') ?></th>
                            <th><?= _l('equipment_data_expiry_date') ?></th>
                            <th><?= _l('equipment_members') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_equipments as $_ws) { ?>
                        <tr>
                            <td>
                                <a href="<?= admin_url('equipments/list_equipments/' . $_ws['id']) ?>">
                                    <?= e($_ws['formatted_number']) ?>
                                </a>
                            </td>
                            <td><?= e($_ws['client_company']) ?></td>
                            <td><?= date('d M Y', strtotime($_ws['date'])) ?></td>
                            <td><?= ! empty($_ws['expirydate']) ? date('d M Y', strtotime($_ws['expirydate'])) : '—' ?></td>
                            <td><?= e($_ws['members'] ?? '—') ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

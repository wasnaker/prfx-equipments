<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

// ── Resolve surveyor entity early (needed for kop surat header) ───────────────
$_CI              = &get_instance();
$_creator_staff   = get_staff($equipment->addedfrom);
$_surveyor_client = null;
if ($_creator_staff && ($_creator_staff->client_type ?? '') === 'surveyor' && !empty($_creator_staff->client_id)) {
    $_CI->load->model('clients_model');
    $_surveyor_client = $_CI->clients_model->get($_creator_staff->client_id);
}

// ── KOP SURAT — full-width letterhead ────────────────────────────────────────
// Left cell : surveyor logo (max 20mm tall)
// Right cell: company name + address + phone (right-aligned)
$_kop_logo = $_surveyor_client ? equipment_pdf_logo_html($_surveyor_client) : pdf_logo_url();

$_kop_right = '';
if ($_surveyor_client) {
    $_kop_right .= '<b style="font-size:13px;">' . e($_surveyor_client->company) . '</b><br />';
    if (!empty($_surveyor_client->address)) {
        $_kop_right .= '<span style="color:#555;">' . e($_surveyor_client->address) . '</span><br />';
    }
    if (!empty($_surveyor_client->city)) {
        $_kop_right .= '<span style="color:#555;">' . e($_surveyor_client->city);
        if (!empty($_surveyor_client->zip)) { $_kop_right .= ' ' . e($_surveyor_client->zip); }
        $_kop_right .= '</span><br />';
    }
    if (!empty($_surveyor_client->phonenumber)) {
        $_kop_right .= '<span style="color:#555;">' . e($_surveyor_client->phonenumber) . '</span>';
    }
} else {
    $_kop_right .= format_organization_info();
}

$_kop_left_cell  = '<table width="100%"><tr>'
    . '<td width="30%" style="vertical-align:middle;">' . $_kop_logo . '</td>'
    . '<td width="70%" style="vertical-align:middle;text-align:right;">' . $_kop_right . '</td>'
    . '</tr></table>';

$pdf->writeHTML($_kop_left_cell, true, false, false, false, '');

// Horizontal divider line
$pdf->SetLineWidth(0.4);
$pdf->SetDrawColor(180, 180, 180);
$pdf->Line($dimensions['lm'], $pdf->GetY() + 2, $dimensions['wk'] - $dimensions['rm'], $pdf->GetY() + 2);
$pdf->Ln(6);

// ── Customer (To) — left column ───────────────────────────────────────────────
$equipment_info = '<b>' . _l('equipment_to') . '</b>';
$equipment_info .= '<div style="color:#424242;">';
$equipment_info .= apps_format_customer_info($equipment, 'equipment', 'billing');
$equipment_info .= '</div>';

if ($equipment->include_shipping == 1 && $equipment->show_shipping_on_equipment == 1) {
    $equipment_info .= '<br /><b>' . _l('ship_to') . '</b>';
    $equipment_info .= '<div style="color:#424242;">';
    $equipment_info .= apps_format_customer_info($equipment, 'equipment', 'shipping');
    $equipment_info .= '</div>';
}

// ── Equipment title + number — right column (replaces "From") ──────────────────
$doc_info  = '<div style="text-align:right;">';
$doc_info .= '<span style="font-weight:bold;font-size:22px;">' . _l('equipment_pdf_heading') . '</span><br />';
$doc_info .= '<b style="color:#4e4e4e;"># ' . $equipment_number . '</b>';
if (get_option('show_status_on_pdf_ei') == 1) {
    $doc_info .= '<br /><span style="color:rgb(' . equipment_status_color_pdf($status) . ');text-transform:uppercase;">'
        . format_equipment_status($status, '', false) . '</span>';
}
$doc_info .= '</div>';

$doc_info = hooks()->apply_filters('equipmentpdf_organization_info', $doc_info, $equipment);

pdf_multi_row($equipment_info, $doc_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->Ln(6);

// ── Dates (left) | Personnel (right) ─────────────────────────────────────────
$meta_left = '<div style="color:#424242;">';
$meta_left .= _l('equipment_data_date') . ': <b>' . date('d F Y', strtotime($equipment->date)) . '</b><br />';
if (! empty($equipment->expirydate)) {
    $meta_left .= _l('equipment_data_expiry_date') . ': <b>' . date('d F Y', strtotime($equipment->expirydate)) . '</b><br />';
}
if (! empty($equipment->reference_no)) {
    $meta_left .= _l('reference_no') . ': ' . $equipment->reference_no . '<br />';
}
if ($equipment->sale_agent && get_option('show_sale_agent_on_equipments') == 1) {
    $meta_left .= _l('sale_agent_string') . ': ' . get_staff_full_name($equipment->sale_agent) . '<br />';
}
if ($equipment->project_id && get_option('show_project_on_equipment') == 1) {
    $meta_left .= _l('project') . ': ' . get_project_name_by_id($equipment->project_id) . '<br />';
}
foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($equipment->id, $field['id'], 'equipment');
    if ($value == '') { continue; }
    $meta_left .= $field['name'] . ': ' . $value . '<br />';
}
$meta_left .= '</div>';

$members     = $equipment->members ?? [];
$meta_right  = '<div style="color:#424242;">';
if (!empty($members)) {
    $meta_right .= '<b>' . _l('equipment_members') . '</b><br />';
    $meta_right .= implode('<br />', array_column($members, 'full_name'));
}
$meta_right .= '</div>';

pdf_multi_row($meta_left, $meta_right, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// ── Jobs table ────────────────────────────────────────────────────────────────
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

$jobs     = $equipment->jobs ?? [];
$tblhtml  = '<table border="1" cellpadding="4" cellspacing="0" style="font-size:' . $font_size . 'px;width:100%;">';
$tblhtml .= '<tr style="background-color:#f0f0f0;">';
$tblhtml .= '<th>' . _l('equipment_job_name') . '</th>';
$tblhtml .= '<th>' . _l('equipment_equipment_item_name') . '</th>';
$tblhtml .= '<th>' . _l('equipment_equipment_unit_code') . '</th>';
$tblhtml .= '<th>' . _l('equipment_equipment_serial_number') . '</th>';
$tblhtml .= '<th>' . _l('equipment_equipment_location') . '</th>';
$tblhtml .= '<th>' . _l('equipment_equipment_cert_expired') . '</th>';
$tblhtml .= '</tr>';
foreach ($jobs as $job) {
    $tblhtml .= '<tr>';
    $tblhtml .= '<td>' . e($job['job_name'] ?? '') . '</td>';
    $tblhtml .= '<td>' . e($job['item_name'] ?? '') . '</td>';
    $tblhtml .= '<td>' . e($job['unit_code'] ?? '') . '</td>';
    $tblhtml .= '<td>' . e($job['serial_number'] ?? '') . '</td>';
    $tblhtml .= '<td>' . e($job['location'] ?? '') . '</td>';
    $tblhtml .= '<td>' . e($job['cert_expired_date'] ?? '') . '</td>';
    $tblhtml .= '</tr>';
}
$tblhtml .= '</table>';

$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->Ln(8);

// ── Assigned To — signature block with QR (right half) ───────────────────────
// Layout (right 50% of page):
//   [city, date]
//   [QR CODE]
//   ______________
//   [assign_to name]
if (!empty($equipment->assign_to)) {
    $_assign_staff = get_staff($equipment->assign_to);
    if ($_assign_staff) {
        // QR content
        $_qr_lines   = [];
        $_qr_lines[] = 'Equipment : ' . $equipment_number;
        $_qr_lines[] = 'Assigned : ' . $_assign_staff->firstname . ' ' . $_assign_staff->lastname;
        $_qr_lines[] = 'Date     : ' . _d($equipment->date);

        // Layout coordinates
        $_content_w    = $dimensions['wk'] - $dimensions['lm'] - $dimensions['rm'];
        $_rh_x         = $dimensions['lm'] + ($_content_w / 2);   // right-half start
        $_rh_w         = $_content_w / 2;
        $_qr_sz        = 32;                                       // mm
        $_qr_x         = $_rh_x + ($_rh_w / 2) - ($_qr_sz / 2);  // centered in right half

        // City from surveyor client, fallback to system setting
        $_sig_city = (!empty($_surveyor_client) && !empty($_surveyor_client->city))
            ? $_surveyor_client->city
            : get_option('company_city');

        $_y0 = $pdf->GetY();

        // Row 1: "[city], [date]"
        $pdf->SetXY($_rh_x, $_y0);
        $pdf->SetFont($font_name, '', $font_size);
        $pdf->Cell($_rh_w, 5, e($_sig_city . ', ' . date('d F Y', strtotime($equipment->date))), 0, 0, 'C');

        // Row 2: QR code
        $_y_qr = $_y0 + 6;
        $pdf->write2DBarcode(implode("\n", $_qr_lines), 'QRCODE,H', $_qr_x, $_y_qr, $_qr_sz, $_qr_sz);

        // Row 3: underline
        $_y_line = $_y_qr + $_qr_sz + 3;
        $pdf->SetXY($_rh_x, $_y_line);
        $pdf->SetFont($font_name, '', $font_size);
        $pdf->Cell($_rh_w, 5, '______________________', 0, 0, 'C');

        // Row 4: name
        $pdf->SetXY($_rh_x, $_y_line + 6);
        $pdf->SetFont($font_name, 'B', $font_size);
        $pdf->Cell($_rh_w, 5, e($_assign_staff->firstname . ' ' . $_assign_staff->lastname), 0, 0, 'C');
        $pdf->SetFont($font_name, '', $font_size);

        $pdf->SetY($_y_line + 14);
        $pdf->Ln(4);
    }
}

if (! empty($equipment->clientnote)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('equipment_note'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', nl2br(e($equipment->clientnote)), 0, 1, false, true, 'L', true);
}

if (! empty($equipment->terms)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('terms_and_conditions') . ':', 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', nl2br(e($equipment->terms)), 0, 1, false, true, 'L', true);
}

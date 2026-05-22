<?php

defined('BASEPATH') or exit('No direct script access allowed');

// ── Logo constraints for equipment PDF ────────────────────────────────────────
define('EQUIPMENT_PDF_LOGO_MAX_W_PX', 200);   // ≈ 53 mm
define('EQUIPMENT_PDF_LOGO_MAX_H_PX', 75);    // ≈ 20 mm

/**
 * Return an <img> HTML string for a client entity logo suitable for TCPDF.
 * Prefers logo_dark over logo_light (better contrast on white PDF background).
 * Resizes the source file if it exceeds EQUIPMENT_PDF_LOGO_MAX_W/H_PX and
 * caches the result so subsequent PDF generations are fast.
 *
 * @param  object|null $client  tblclients row
 * @return string  HTML img tag with absolute src path, or empty string
 */
function equipment_pdf_logo_html($client): string
{
    if (!$client) { return ''; }

    // Prefer dark logo on white PDF background
    $logo_file = !empty($client->logo_dark)  ? $client->logo_dark
               : (!empty($client->logo_light) ? $client->logo_light : null);

    if (!$logo_file) { return ''; }

    $src = FCPATH . 'uploads/client_logos/' . $client->userid . '/' . $logo_file;
    if (!file_exists($src)) { return ''; }

    $use_path = _equipment_pdf_logo_resize($src);

    // height="20mm" — TCPDF interprets mm unit correctly in writeHTML
    return '<img src="' . $use_path . '" height="20mm">';
}

/**
 * Resize a logo image to fit within EQUIPMENT_PDF_LOGO_MAX_W/H_PX.
 * Returns the path of the (possibly resized) image.
 * Resized copies are cached in uploads/temp/pdf_logo_cache/.
 */
function _equipment_pdf_logo_resize(string $src): string
{
    $info = @getimagesize($src);
    if (!$info) { return $src; }

    [$orig_w, $orig_h] = $info;
    $max_w = EQUIPMENT_PDF_LOGO_MAX_W_PX;
    $max_h = EQUIPMENT_PDF_LOGO_MAX_H_PX;

    if ($orig_w <= $max_w && $orig_h <= $max_h) { return $src; }

    // Need resize — check GD available
    if (!function_exists('imagecreatetruecolor')) { return $src; }

    $cache_dir = FCPATH . 'uploads/temp/pdf_logo_cache/';
    if (!is_dir($cache_dir)) { @mkdir($cache_dir, 0755, true); }

    $ext        = strtolower(pathinfo($src, PATHINFO_EXTENSION));
    $cache_file = $cache_dir . md5($src . filemtime($src) . $max_w . $max_h) . '.' . $ext;

    if (file_exists($cache_file)) { return $cache_file; }

    // Calculate proportional dimensions
    $ratio = min($max_w / $orig_w, $max_h / $orig_h);
    $new_w = (int) round($orig_w * $ratio);
    $new_h = (int) round($orig_h * $ratio);

    $src_img = match($ext) {
        'jpg', 'jpeg' => @imagecreatefromjpeg($src),
        'png'         => @imagecreatefrompng($src),
        'gif'         => @imagecreatefromgif($src),
        default       => null,
    };
    if (!$src_img) { return $src; }

    $dst_img = imagecreatetruecolor($new_w, $new_h);

    if (in_array($ext, ['png', 'gif'])) {
        imagealphablending($dst_img, false);
        imagesavealpha($dst_img, true);
        imagefilledrectangle($dst_img, 0, 0, $new_w, $new_h,
            imagecolorallocatealpha($dst_img, 255, 255, 255, 127));
    } else {
        // White background for JPEG
        imagefilledrectangle($dst_img, 0, 0, $new_w, $new_h,
            imagecolorallocate($dst_img, 255, 255, 255));
    }

    imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_w, $new_h, $orig_w, $orig_h);

    match($ext) {
        'jpg', 'jpeg' => imagejpeg($dst_img, $cache_file, 90),
        'png'         => imagepng($dst_img, $cache_file),
        'gif'         => imagegif($dst_img, $cache_file),
        default       => null,
    };

    imagedestroy($src_img);
    imagedestroy($dst_img);

    return file_exists($cache_file) ? $cache_file : $src;
}

/**
 * Generate equipment PDF
 * @param  object $equipment equipment object from database
 * @param  string $tag       tag for bulk pdf exporter
 * @return object
 */
function equipment_pdf($equipment, $tag = '')
{
    return app_pdf('equipment', FCPATH . 'modules/equipments/libraries/pdf/Equipment_pdf', $equipment, $tag);
}

/**
 * Get Equipment short_url
 * @since  Version 2.7.3
 * @param  object $equipment
 * @return string Url
 */
function get_equipment_shortlink($equipment)
{
    $long_url = site_url("equipment/{$equipment->id}/{$equipment->hash}");
    if (!get_option('bitly_access_token')) {
        return $long_url;
    }

    // Check if equipment has short link, if yes return short link
    if (!empty($equipment->short_link)) {
        return $equipment->short_link;
    }

    // Create short link and return the newly created short link
    $short_link = app_generate_short_link([
        'long_url' => $long_url,
        'title'    => format_equipment_number($equipment->id),
    ]);

    if ($short_link) {
        $CI = &get_instance();
        $CI->db->where('id', $equipment->id);
        $CI->db->update(db_prefix() . 'equipments', [
            'short_link' => $short_link,
        ]);

        return $short_link;
    }

    return $long_url;
}

/**
 * Check equipment restrictions - hash, client_id
 * @param  mixed $id   equipment id
 * @param  string $hash equipment hash
 */
function check_equipment_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('equipments_model');
    if (!$hash || !$id) {
        show_404();
    }
    if (!is_client_logged_in() && !is_staff_logged_in()) {
        if (get_option('view_equipment_only_logged_in') == 1) {
            redirect_after_login_to_current_url();
            redirect(site_url('authentication/login'));
        }
    }
    $equipment = $CI->equipments_model->get($id);
    if (!$equipment || ($equipment->hash != $hash)) {
        show_404();
    }
    // Do one more check
    if (!is_staff_logged_in()) {
        if (get_option('view_equipment_only_logged_in') == 1) {
            if ($equipment->client_id != get_client_user_id()) {
                show_404();
            }
        }
    }
}

/**
 * Check if equipment email template for expiry reminders is enabled
 * @return boolean
 */
function is_equipments_email_expiry_reminder_enabled()
{
    return total_rows(db_prefix() . 'emailtemplates', ['slug' => 'equipment-expiry-reminder', 'active' => 1]) > 0;
}

/**
 * Check if there are sources for sending equipment expiry reminders
 * Will be either email or SMS
 * @return boolean
 */
function is_equipments_expiry_reminders_enabled()
{
    return is_equipments_email_expiry_reminder_enabled() || is_sms_trigger_active(SMS_TRIGGER_EQUIPMENT_EXP_REMINDER);
}

/**
 * Return RGBa equipment status color for PDF documents
 * @param  mixed $status_id current equipment status
 * @return string
 */
function equipment_status_color_pdf($status_id)
{
    if ($status_id == 1) {
        $statusColor = '119, 119, 119';
    } elseif ($status_id == 2) {
        // Sent
        $statusColor = '3, 169, 244';
    } elseif ($status_id == 3) {
        // Declined
        $statusColor = '252, 45, 66';
    } elseif ($status_id == 4) {
        // Accepted
        $statusColor = '0, 191, 54';
    } else {
        // Expired
        $statusColor = '255, 111, 0';
    }

    return hooks()->apply_filters('equipment_status_pdf_color', $statusColor, $status_id);
}

/**
 * Format equipment status
 * @param  integer  $status
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_equipment_status($status, $classes = '', $label = true)
{
    $id          = $status;
    $label_class = equipment_status_color_class($status);
    $status      = equipment_status_by_id($status);
    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status equipment-status-' . $id . ' equipment-status-' . $label_class . '">' . $status . '</span>';
    }

    return $status;
}

/**
 * Return equipment status translated by passed status id
 * @param  mixed $id equipment status id
 * @return string
 */
function equipment_status_by_id($id)
{
    $status = '';
    if ($id == 1) {
        $status = _l('equipment_status_draft');
    } elseif ($id == 2) {
        $status = _l('equipment_status_sent');
    } elseif ($id == 3) {
        $status = _l('equipment_status_declined');
    } elseif ($id == 4) {
        $status = _l('equipment_status_accepted');
    } elseif ($id == 5) {
        // status 5
        $status = _l('equipment_status_expired');
    } else {
        if (!is_numeric($id)) {
            if ($id == 'not_sent') {
                $status = _l('not_sent_indicator');
            }
        }
    }

    return hooks()->apply_filters('equipment_status_label', $status, $id);
}

/**
 * Return equipment status color class based on twitter bootstrap
 * @param  mixed  $id
 * @param  boolean $replace_default_by_muted
 * @return string
 */
function equipment_status_color_class($id, $replace_default_by_muted = false)
{
    $class = '';
    if ($id == 1) {
        $class = 'default';
        if ($replace_default_by_muted == true) {
            $class = 'muted';
        }
    } elseif ($id == 2) {
        $class = 'info';
    } elseif ($id == 3) {
        $class = 'danger';
    } elseif ($id == 4) {
        $class = 'success';
    } elseif ($id == 5) {
        // status 5
        $class = 'warning';
    } else {
        if (!is_numeric($id)) {
            if ($id == 'not_sent') {
                $class = 'default';
                if ($replace_default_by_muted == true) {
                    $class = 'muted';
                }
            }
        }
    }

    return hooks()->apply_filters('equipment_status_color_class', $class, $id);
}

/**
 * Check if the equipment id is last invoice
 * @param  mixed  $id equipmentid
 * @return boolean
 */
function is_last_equipment($id)
{
    $CI = &get_instance();
    $CI->db->select('id')->from(db_prefix() . 'equipments')->order_by('id', 'desc')->limit(1);
    $query            = $CI->db->get();
    $last_equipment_id = $query->row()->id;
    if ($last_equipment_id == $id) {
        return true;
    }

    return false;
}

/**
 * Format equipment number based on description
 * @param  mixed $id
 * @return string
 */
function format_equipment_number($id)
{
    $CI = &get_instance();

    if (is_object($id)) {
        $code = $id->code ?? '';
        $id   = $id->id ?? $id->itemid ?? '';
    } else {
        $row  = $CI->db->select('code')->get_where(db_prefix() . 'items', ['id' => $id])->row();
        $code = $row ? ($row->code ?? '') : '';
    }

    $number = !empty($code) ? $code : 'EQP-' . str_pad((int)$id, 6, '0', STR_PAD_LEFT);

    return hooks()->apply_filters('format_equipment_number', $number, [
        'id' => $id,
    ]);
}

function format_myequipment_number($myequipment)
{
    if (is_object($myequipment)) {
        return e($myequipment->unit_code ?? '');
    }
    $CI  = &get_instance();
    $row = $CI->db->select('unit_code')->get_where(db_prefix() . 'customer_equipment', ['id' => (int)$myequipment])->row();
    return $row ? e($row->unit_code) : '';
}

function format_myequipment_status($status, $classes = '', $label = true)
{
    return format_equipment_status($status, $classes, $label);
}


/**
 * Function that return equipment item taxes based on passed item id
 * @param  mixed $itemid
 * @return array
 */
function get_equipment_item_taxes($itemid)
{
    $CI = &get_instance();
    $CI->db->where('itemid', $itemid);
    $CI->db->where('rel_type', 'equipment');
    $taxes = $CI->db->get(db_prefix() . 'item_tax')->result_array();
    $i     = 0;
    foreach ($taxes as $tax) {
        $taxes[$i]['taxname'] = $tax['taxname'] . '|' . $tax['taxrate'];
        $i++;
    }

    return $taxes;
}

/**
 * Calculate equipments percent by status
 * @param  mixed $status          equipment status
 * @return array
 */
function get_equipments_percent_by_status($status, $project_id = null)
{
    $has_permission_view = staff_can('view',  'equipments');
    $where               = '';

    if (isset($project_id)) {
        $where .= 'project_id=' . get_instance()->db->escape_str($project_id) . ' AND ';
    }
    if (!$has_permission_view) {
        $where .= get_equipments_where_sql_for_staff(get_staff_user_id());
    }

    $where = trim($where);

    if (endsWith($where, ' AND')) {
        $where = substr_replace($where, '', -3);
    }

    $total_equipments = total_rows(db_prefix() . 'equipments', $where);

    $data            = [];
    $total_by_status = 0;

    if (!is_numeric($status)) {
        if ($status == 'not_sent') {
            $total_by_status = total_rows(db_prefix() . 'equipments', 'sent=0 AND status NOT IN(2,3,4)' . ($where != '' ? ' AND (' . $where . ')' : ''));
        }
    } else {
        $whereByStatus = 'status=' . $status;
        if ($where != '') {
            $whereByStatus .= ' AND (' . $where . ')';
        }
        $total_by_status = total_rows(db_prefix() . 'equipments', $whereByStatus);
    }

    $percent                 = ($total_equipments > 0 ? number_format(($total_by_status * 100) / $total_equipments, 2) : 0);
    $data['total_by_status'] = $total_by_status;
    $data['percent']         = $percent;
    $data['total']           = $total_equipments;

    return $data;
}

function get_equipments_where_sql_for_staff($staff_id)
{
    $CI                                  = &get_instance();
    $has_permission_view_own             = staff_can('view_own',  'equipments');
    $allow_staff_view_equipments_assigned = get_option('allow_staff_view_equipments_assigned');
    $whereUser                           = '';
    if ($has_permission_view_own) {
        $whereUser = '((' . db_prefix() . 'equipments.addedfrom=' . $CI->db->escape_str($staff_id) . ' AND ' . db_prefix() . 'equipments.addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature = "equipments" AND capability="view_own"))';
        if ($allow_staff_view_equipments_assigned == 1) {
            $whereUser .= ' OR sale_agent=' . $CI->db->escape_str($staff_id);
        }
        $whereUser .= ')';
    } else {
        $whereUser .= 'sale_agent=' . $CI->db->escape_str($staff_id);
    }

    return $whereUser;
}
/**
 * Check if staff member have assigned equipments / added as sale agent
 * @param  mixed $staff_id staff id to check
 * @return boolean
 */
function staff_has_assigned_equipments($staff_id = '')
{
    $CI       = &get_instance();
    $staff_id = is_numeric($staff_id) ? $staff_id : get_staff_user_id();
    $cache    = $CI->app_object_cache->get('staff-total-assigned-equipments-' . $staff_id);

    if (is_numeric($cache)) {
        $result = $cache;
    } else {
        $result = total_rows(db_prefix() . 'equipments', ['sale_agent' => $staff_id]);
        $CI->app_object_cache->add('staff-total-assigned-equipments-' . $staff_id, $result);
    }

    return $result > 0 ? true : false;
}
/**
 * Check if staff member can view equipment
 * @param  mixed $id equipment id
 * @param  mixed $staff_id
 * @return boolean
 */
function user_can_view_equipment($id, $staff_id = false)
{
    $CI = &get_instance();

    $staff_id = $staff_id ? $staff_id : get_staff_user_id();

    $CI->db->select('id, addedfrom');
    $CI->db->from(db_prefix() . 'items');
    $CI->db->where('id', $id);
    $equipment = $CI->db->get()->row();

    if (!$equipment) {
        return false;
    }

    $_me = get_staff($staff_id);
    if (!$_me) { return false; }

    // All entity types can view catalog
    if (!empty($_me->client_type)) {
        return true;
    }

    // Platform staff
    if (has_permission('equipments', $staff_id, 'view')) {
        return true;
    }

    if ((has_permission('equipments', $staff_id, 'view_own') && $equipment->addedfrom == $staff_id)
    ) {
        return true;
    }

    return false;
}
/*
 * =============================================
 * ini batas helper schedule
 * =============================================
 */

hooks()->add_filter('equipment_status_label', function($status, $id) {
    if ($id == 2) { return _l('equipment_status_active'); }
    if ($id == 3) { return _l('equipment_status_inactive'); }
    return $status;
}, 10, 2);

hooks()->add_filter('equipment_status_color_class', function($class, $id) {
    if ($id == 4) { return 'default'; }
    return $class;
}, 10, 2);

hooks()->add_filter('before_set_equipment_statuses', function($statuses) {
    return [
        EQUIPMENT_STATUS_DRAFT,
        EQUIPMENT_STATUS_ACTIVE,
        EQUIPMENT_STATUS_INACTIVE,
        EQUIPMENT_STATUS_EXPIRED,
    ];
});

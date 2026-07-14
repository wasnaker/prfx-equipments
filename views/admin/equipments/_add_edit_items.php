<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel-body">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mbot25 select-placeholder">
                <select id="equipment_task_select" class="ajax-search selectpicker"
                    data-width="100%"
                    data-none-selected-text="<?= _l('add_item'); ?>"
                    data-live-search="true">
                    <option value=""></option>
                </select>
            </div>
        </div>
        <div class="col-md-8 text-right show_quantity_as_wrapper">
            <div class="mtop10">
                <span><?= _l('show_quantity_as'); ?></span>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" value="1" id="1" name="show_quantity_as"
                        data-text="<?= _l('equipment_table_quantity_heading'); ?>"
                        <?= isset($equipment) && $equipment->show_quantity_as == 1 ? 'checked' : 'checked'; ?>>
                    <label
                        for="1"><?= _l('quantity_as_qty'); ?></label>
                </div>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" value="2" id="2" name="show_quantity_as"
                        data-text="<?= _l('equipment_table_hours_heading'); ?>"
                        <?= isset($equipment) && $equipment->show_quantity_as == 2 ? 'checked' : ''; ?>>
                    <label
                        for="2"><?= _l('quantity_as_hours'); ?></label>
                </div>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" id="3" value="3" name="show_quantity_as"
                        data-text="<?= _l('equipment_table_quantity_heading'); ?>/<?= _l('equipment_table_hours_heading'); ?>"
                        <?= isset($equipment) && $equipment->show_quantity_as == 3 ? 'checked' : ''; ?>>
                    <label for="3">
                        <?= _l('equipment_table_quantity_heading'); ?>/<?= _l('equipment_table_hours_heading'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive s_table">
        <table class="table equipment-items-table items table-main-equipment-edit no-mtop">
            <thead>
                <tr>
                    <th></th>
                    <th width="20%" align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1" aria-hidden="true"
                            data-toggle="tooltip"
                            data-title="<?= _l('item_description_new_lines_notice'); ?>"></i>
                        <?= _l('equipment_table_item_heading'); ?>
                    </th>
                    <th width="25%" align="left">
                        <?= _l('equipment_table_item_description'); ?>
                    </th>
                    <?php
                  $custom_fields = get_custom_fields('items');

foreach ($custom_fields as $cf) {
    echo '<th width="15%" align="left" class="custom_field">' . e($cf['name']) . '</th>';
}

$qty_heading = _l('equipment_table_quantity_heading');
if (isset($equipment) && $equipment->show_quantity_as == 2) {
    $qty_heading = _l('equipment_table_hours_heading');
} elseif (isset($equipment) && $equipment->show_quantity_as == 3) {
    $qty_heading = _l('equipment_table_quantity_heading') . '/' . _l('equipment_table_hours_heading');
}
?>
                    <th width="10%" class="qty" align="right">
                        <?= e($qty_heading); ?>
                    </th>
                    <th align="center"><i class="fa fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($equipment) || isset($add_items)) {
                    $i               = 1;
                    $items_indicator = 'newitems';
                    if (isset($equipment)) {
                        $add_items       = $equipment->items;
                        $items_indicator = 'items';
                    }

                    foreach ($add_items as $item) {
                        $manual    = false;
                        $table_row = '<tr class="sortable item">';
                        $table_row .= '<td class="dragger">';
                        if ($item['qty'] == '' || $item['qty'] == 0) {
                            $item['qty'] = 1;
                        }
                        if ($item['id'] == 0) {
                            $manual = true;
                        }
                        $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);
                        // order input
                        $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';
                        $table_row .= '</td>';
                        $table_row .= '<td class="bold description"><textarea name="' . $items_indicator . '[' . $i . '][description]" class="form-control" rows="5">' . clear_textarea_breaks($item['description']) . '</textarea>';

                        $table_row .= '<div class="tw-mt-1.5">';
                        $table_row .= '<div class="checkbox checkbox-info">';
                        $table_row .= '<input class="optional-item-checkbox" ' . ($item['is_optional'] ? ' checked' : '') . ' value="1" id="' . $i . '-optional" type="checkbox" name="' . $items_indicator . '[' . $i . '][is_optional]" data-index="' . $i . '" />';

                        $table_row .= '<label for="' . $i . '-optional">' . _l('item_is_optional') . '</label>';
                        $table_row .= '</div>';

                        $table_row .= '<div class="checkbox" style="' . (! ($item['is_optional'] ?? false) ? 'display: none; ' : '') . '">';
                        $table_row .= '<input class="optional-choose-item-checkbox" ' . ($item['is_selected'] ? ' checked' : '') . ' value="1" id="' . $i . '-optional-choosen" type="checkbox" name="' . $items_indicator . '[' . $i . '][is_selected]" />';

                        $table_row .= '<label for="' . $i . '-optional-choosen">' . _l('item_is_selected') . '</label>';
                        $table_row .= '</div>';
                        $table_row .= '</div>';

                        $table_row .= '</td>';
                        $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][long_description]" class="form-control" rows="5">' . clear_textarea_breaks($item['long_description']) . '</textarea></td>';
                        $table_row .= render_custom_fields_items_table_in($item, $items_indicator . '[' . $i . ']');
                        $table_row .= '<td><input type="number" min="0" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $item['qty'] . '" class="form-control">';
                        $unit_placeholder = '';
                        if (! $item['unit']) {
                            $unit_placeholder = _l('unit');
                            $item['unit']     = '';
                        }
                        $table_row .= '<input type="text" placeholder="' . $unit_placeholder . '" name="' . $items_indicator . '[' . $i . '][unit]" class="form-control input-transparent text-right" value="' . $item['unit'] . '">';
                        $table_row .= '</td>';
                        $table_row .= '<td><a href="#" class="btn btn-danger pull-left !tw-px-3" onclick="delete_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
                        $table_row .= '</tr>';
                        echo $table_row;
                        $i++;
                    }
                }
?>
            </tbody>
        </table>
    </div>
    <div class="col-md-8 col-md-offset-4" style="display:none">
        <table class="table text-right">
            <tbody>
                <tr id="subtotal">
                    <td><span
                            class="bold tw-text-neutral-700"><?= _l('equipment_subtotal'); ?>
                            :</span>
                    </td>
                    <td class="subtotal">
                    </td>
                </tr>
                <tr id="discount_area">
                    <td>
                        <div class="row">
                            <div class="col-md-7">
                                <span
                                    class="bold tw-text-neutral-700"><?= _l('equipment_discount'); ?></span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group" id="discount-total">

                                    <input type="number"
                                        value="<?= isset($equipment) ? $equipment->discount_percent : 0; ?>"
                                        class="form-control pull-left input-discount-percent<?php if (isset($equipment) && ! is_sale_discount($equipment, 'percent') && is_sale_discount_applied($equipment)) {
                                            echo ' hide';
                                        } ?>" min="0" max="100" name="discount_percent">

                                    <input type="number" data-toggle="tooltip"
                                        data-title="<?= _l('numbers_not_formatted_while_editing'); ?>"
                                        value="<?= isset($equipment) ? $equipment->discount_total : 0; ?>"
                                        class="form-control pull-left input-discount-fixed<?php if (! isset($equipment) || (isset($equipment) && ! is_sale_discount($equipment, 'fixed'))) {
                                            echo ' hide';
                                        } ?>" min="0" name="discount_total">

                                    <div class="input-group-addon">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" href="#" id="dropdown_menu_tax_total_type"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <span class="discount-total-type-selected">
                                                    <?php if (! isset($equipment) || isset($equipment) && (is_sale_discount($equipment, 'percent') || ! is_sale_discount_applied($equipment))) {
                                                        echo '%';
                                                    } else {
                                                        echo _l('discount_fixed_amount');
                                                    }
?>
                                                </span>
                                                <span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu" id="discount-total-type-dropdown"
                                                aria-labelledby="dropdown_menu_tax_total_type">
                                                <li>
                                                    <a href="#" class="discount-total-type discount-type-percent<?php if (! isset($equipment) || (isset($equipment) && is_sale_discount($equipment, 'percent')) || (isset($equipment) && ! is_sale_discount_applied($equipment))) {
                                                        echo ' selected';
                                                    } ?>">%</a>
                                                </li>
                                                <li>
                                                    <a href="#" class="discount-total-type discount-type-fixed<?php if (isset($equipment) && is_sale_discount($equipment, 'fixed')) {
                                                        echo ' selected';
                                                    } ?>">
                                                        <?= _l('discount_fixed_amount'); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="discount-total"></td>
                </tr>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-7">
                                <span
                                    class="bold tw-text-neutral-700"><?= _l('equipment_adjustment'); ?></span>
                            </div>
                            <div class="col-md-5">
                                <input type="number" data-toggle="tooltip"
                                    data-title="<?= _l('numbers_not_formatted_while_editing'); ?>"
                                    value="<?php if (isset($equipment)) {
                                        echo $equipment->adjustment;
                                    } else {
                                        echo 0;
                                    } ?>" class="form-control pull-left" name="adjustment">
                            </div>
                        </div>
                    </td>
                    <td class="adjustment"></td>
                </tr>
                <tr>
                    <td><span
                            class="bold tw-text-neutral-700"><?= _l('equipment_total'); ?>
                            :</span>
                    </td>
                    <td class="total">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="removed-items"></div>
</div>
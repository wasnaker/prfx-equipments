<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('equipments/equipments_model', 'equipments_model');

return App_table::find('equipments')
    ->outputUsing(function ($params) {
        $client_id            = $params['client_id'];
        $customFieldsColumns = $params['customFieldsColumns'];

        $program_id = $this->ci->input->post('program_id');

        $aColumns = [
            db_prefix() . 'items.code',
            db_prefix() . 'items.description',
            'cat.name as category_name',
            'sg.name as subgroup_name',
            'grp.name as group_name',
            db_prefix() . 'items.status',
        ];

        $join = [
            'LEFT JOIN ' . db_prefix() . 'items_categories cat ON cat.id = ' . db_prefix() . 'items.category_id',
            'LEFT JOIN ' . db_prefix() . 'items_subgroups sg ON sg.id = '    . db_prefix() . 'items.subgroup_id',
            'LEFT JOIN ' . db_prefix() . 'items_groups grp ON grp.id = '     . db_prefix() . 'items.group_id',
        ];

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'items';

        $custom_fields = get_table_custom_fields('equipment');

        foreach ($custom_fields as $key => $field) {
            $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
            array_push($customFieldsColumns, $selectAs);
            array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
            array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'equipments.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
        }

        $where = [];
        $_me   = get_staff(get_staff_user_id());

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        // Equipment catalog (tblitems) is global master data — no per-staff scoping needed

        $join = hooks()->apply_filters('equipments_table_sql_join', $join);
        $aColumns = hooks()->apply_filters('equipments_table_sql_columns', $aColumns);
        $where = hooks()->apply_filters('equipments_table_sql_where', $where);

        // Fix for big queries. Some hosting have max_join_limit
        if (count($custom_fields) > 4) {
            @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            db_prefix() . 'items.id',
            db_prefix() . 'items.group_id',
            db_prefix() . 'items.subgroup_id',
            db_prefix() . 'items.category_id',
        ]);

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $formattedNumber = format_equipment_number($aRow['id']);

            $row = [];

            $codeOutput = '<span class="tw-font-mono tw-text-sm tw-text-neutral-500">' . e($aRow[db_prefix() . 'items.code'] ?? '—') . '</span>';

            $descOutput = '<a href="' . admin_url('equipments/list_equipments/' . $aRow['id']) . '"'
                . ' onclick="init_equipment(' . $aRow['id'] . '); return false;" class="tw-font-medium">'
                . e($aRow[db_prefix() . 'items.description'])
                . '</a>'
                . '<div class="row-options">';
            if (staff_can('edit', 'equipments')) {
                $descOutput .= '<a href="' . admin_url('equipments/equipment/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (staff_can('edit', 'equipments') && staff_can('delete', 'equipments')) {
                $descOutput .= ' | ';
            }
            if (staff_can('delete', 'equipments')) {
                $descOutput .= '<a href="' . admin_url('equipments/delete/' . $aRow['id']) . '" class="_delete">' . _l('delete') . '</a>';
            }
            $descOutput .= '</div>';

            $cat_id   = (int) ($aRow['category_id'] ?? 0);
            $sg_id    = (int) ($aRow['subgroup_id'] ?? 0);
            $grp_id   = (int) ($aRow['group_id'] ?? 0);
            $cat_name = $aRow['category_name'] ?? '';
            $sg_name  = $aRow['subgroup_name'] ?? '';
            $grp_name = $aRow['group_name'] ?? '';

            $row[] = $codeOutput;
            $row[] = $descOutput;
            $row[] = $cat_name !== ''
                ? '<a href="' . admin_url('equipments/categories/list_categories/' . $cat_id) . '" class="label label-warning">' . e($cat_name) . '</a>'
                : '—';
            $row[] = $sg_name !== ''
                ? '<a href="' . admin_url('equipments/subgroups/list_subgroups/' . $sg_id) . '" class="label label-info">' . e($sg_name) . '</a>'
                : '—';
            $row[] = $grp_name !== ''
                ? '<a href="' . admin_url('equipments/groups/list_groups/' . $grp_id) . '" class="label label-default">' . e($grp_name) . '</a>'
                : '—';
            $row[] = format_equipment_status($aRow[db_prefix() . 'items.status'] ?? EQUIPMENT_STATUS_ACTIVE);

            // Custom fields add values
            foreach ($customFieldsColumns as $customFieldColumn) {
                $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
            }

            $row['DT_RowClass'] = 'has-row-options';

            $row = hooks()->apply_filters('equipments_table_row_data', $row, $aRow);

            $output['aaData'][] = $row;
        }

        return $output;
    })->setRules([
        App_table_filter::new('number', 'NumberRule')->label(_l('equipment_add_edit_number')),
        App_table_filter::new('reference_no', 'TextRule')->label(_l('reference_no')),
        App_table_filter::new('total', 'NumberRule')->label(_l('equipment_total')),
        App_table_filter::new('subtotal', 'NumberRule')->label(_l('equipment_subtotal')),
        App_table_filter::new('date', 'DateRule')->label(_l('equipment_data_date')),
        App_table_filter::new('expirydate', 'DateRule')
            ->label(_l('equipment_dt_table_heading_expirydate'))
            ->withEmptyOperators(),
        App_table_filter::new('sent', 'BooleanRule')->label(_l('equipment_status_sent'))->raw(function ($value) {
            if ($value == '1') {
                return 'sent = 1';
            }

            return 'sent = 0 and ' . db_prefix() . 'equipments.status NOT IN (2,3,4)';
        }),
        App_table_filter::new('signed', 'BooleanRule')->label(_l('contracts_view_signed'))
            ->raw(function ($value) {
                return $value == '1' ? 'signature IS NOT NULL' : 'signature IS NULL';
            }),
        App_table_filter::new('sale_agent', 'SelectRule')->label(_l('sale_agent_string'))
            ->withEmptyOperators()
            ->emptyOperatorValue(0)
            ->isVisible(fn () => staff_can('view', 'equipments'))
            ->options(function ($ci) {
                return [];
            }),

        App_table_filter::new('status', 'MultiSelectRule')
            ->label(_l('equipment_status'))
            ->column(db_prefix() . 'items.status')
            ->options(function ($ci) {
                return collect($ci->equipments_model->get_statuses())->map(fn ($status) => [
                    'value' => (string) $status,
                    'label' => format_equipment_status($status, '', false),
                ])->all();
            }),

        App_table_filter::new('year', 'MultiSelectRule')
            ->label(_l('year'))
            ->raw(function ($value, $operator) {
                if ($operator == 'in') {
                    return 'YEAR(date) IN (' . implode(',', $value) . ')';
                }

                return 'YEAR(date) NOT IN (' . implode(',', $value) . ')';
            })
            ->options(function ($ci) {
                return collect($ci->equipments_model->get_equipments_years())->map(fn ($data) => [
                    'value' => $data['year'],
                    'label' => $data['year'],
                ])->all();
            }),

        App_table_filter::new('group_id', 'MultiSelectRule')
            ->label(_l('equipment_group'))
            ->raw(function ($value, $operator) {
                $pfx = db_prefix();
                $in  = implode(',', array_map('intval', $value));
                return $operator == 'in'
                    ? "{$pfx}items.group_id IN ($in)"
                    : "{$pfx}items.group_id NOT IN ($in)";
            })
            ->options(function ($ci) {
                $rows = $ci->db->order_by('name', 'ASC')->get(db_prefix() . 'items_groups')->result_array();
                return collect($rows)->map(fn ($r) => ['value' => $r['id'], 'label' => $r['name']])->all();
            }),

        App_table_filter::new('subgroup_id', 'MultiSelectRule')
            ->label(_l('equipment_subgroup'))
            ->raw(function ($value, $operator) {
                $pfx = db_prefix();
                $in  = implode(',', array_map('intval', $value));
                return $operator == 'in'
                    ? "{$pfx}items.subgroup_id IN ($in)"
                    : "{$pfx}items.subgroup_id NOT IN ($in)";
            })
            ->options(function ($ci) {
                $rows = $ci->db->order_by('name', 'ASC')->get(db_prefix() . 'items_subgroups')->result_array();
                return collect($rows)->map(fn ($r) => ['value' => $r['id'], 'label' => $r['name']])->all();
            }),
    ]);

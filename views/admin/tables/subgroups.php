<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('equipments/Subgroups_model', 'subgroups_model');

return App_table::find('subgroups')
    ->outputUsing(function ($params) {

        $aColumns = [
            db_prefix() . 'items_subgroups.name',
            db_prefix() . 'items_subgroups.code',
            'grp.name as group_name',
            db_prefix() . 'items_subgroups.color',
            db_prefix() . 'items_subgroups.sort_order',
            '(SELECT COUNT(*) FROM ' . db_prefix() . 'items WHERE subgroup_id=' . db_prefix() . 'items_subgroups.id) as total_items',
        ];

        $join = [
            'LEFT JOIN ' . db_prefix() . 'items_groups grp ON grp.id = ' . db_prefix() . 'items_subgroups.group_id',
        ];
        $where = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        $aColumns = hooks()->apply_filters('subgroups_table_sql_columns', $aColumns);

        $result = data_tables_init(
            $aColumns,
            'id',
            db_prefix() . 'items_subgroups',
            $join,
            $where,
            [db_prefix() . 'items_subgroups.id', db_prefix() . 'items_subgroups.group_id']
        );

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];

            $nameOutput = '<a href="#" onclick="init_subgroup(' . $aRow['id'] . '); return false;" class="tw-font-medium">'
                . e($aRow[db_prefix() . 'items_subgroups.name'])
                . '</a>'
                . '<div class="row-options">';
            if (staff_can('edit', 'equipments')) {
                $nameOutput .= '<a href="' . admin_url('equipments/subgroups/subgroup/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (staff_can('edit', 'equipments') && staff_can('delete', 'equipments')) {
                $nameOutput .= ' | ';
            }
            if (staff_can('delete', 'equipments')) {
                $nameOutput .= '<a href="#" onclick="delete_subgroup_confirm(' . $aRow['id'] . '); return false;" class="text-danger">' . _l('delete') . '</a>';
            }
            $nameOutput .= '</div>';

            $color = e($aRow[db_prefix() . 'items_subgroups.color'] ?? '#3b82f6');

            $row[] = $nameOutput;
            $row[] = '<span class="tw-font-mono tw-text-sm tw-text-neutral-500">' . e($aRow[db_prefix() . 'items_subgroups.code'] ?? '—') . '</span>';
            $grp_id   = (int) ($aRow['group_id'] ?? 0);
            $grp_name = $aRow['group_name'] ?? '';
            $row[] = $grp_name !== ''
                ? '<a href="' . admin_url('equipments/groups/list_groups/' . $grp_id) . '" class="label label-default">' . e($grp_name) . '</a>'
                : '—';
            $row[] = '<span class="tw-inline-block tw-w-5 tw-h-5 tw-rounded tw-border" style="background:' . $color . '"></span>';
            $row[] = e($aRow[db_prefix() . 'items_subgroups.sort_order']);
            $row[] = '<span class="badge">' . (int)$aRow['total_items'] . '</span>';

            $row['DT_RowClass'] = 'has-row-options';
            $row['DT_RowData']  = ['id' => $aRow['id']];

            $row = hooks()->apply_filters('subgroups_table_row_data', $row, $aRow);

            $output['aaData'][] = $row;
        }

        return $output;
    })
    ->setRules([
        App_table_filter::new('name', 'TextRule')->label(_l('subgroup_name')),
        App_table_filter::new('code', 'TextRule')->label(_l('group_code')),
        App_table_filter::new('group_id', 'MultiSelectRule')
            ->label(_l('equipment_group'))
            ->column(db_prefix() . 'items_subgroups.group_id')
            ->options(function ($ci) {
                return collect($ci->db->order_by('name')->get(db_prefix() . 'items_groups')->result_array())
                    ->map(fn ($g) => ['value' => $g['id'], 'label' => $g['name']])
                    ->all();
            }),
    ]);

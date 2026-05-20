<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('equipments/Groups_model', 'groups_model');

return App_table::find('groups')
    ->outputUsing(function ($params) {

        $aColumns = [
            db_prefix() . 'items_groups.name',
            db_prefix() . 'items_groups.code',
            db_prefix() . 'items_groups.status',
            '(SELECT COUNT(*) FROM ' . db_prefix() . 'items_subgroups WHERE group_id=' . db_prefix() . 'items_groups.id) as total_subgroups',
            '(SELECT COUNT(*) FROM ' . db_prefix() . 'items WHERE group_id=' . db_prefix() . 'items_groups.id) as total_items',
        ];

        $join  = [];
        $where = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        $aColumns = hooks()->apply_filters('groups_table_sql_columns', $aColumns);

        $result = data_tables_init(
            $aColumns,
            'id',
            db_prefix() . 'items_groups',
            $join,
            $where,
            [db_prefix() . 'items_groups.id']
        );

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];

            $nameOutput = '<a href="#" onclick="init_group(' . $aRow['id'] . '); return false;" class="tw-font-medium">'
                . e($aRow[db_prefix() . 'items_groups.name'])
                . '</a>'
                . '<div class="row-options">';
            if (staff_can('edit', 'equipments')) {
                $nameOutput .= '<a href="' . admin_url('equipments/groups/group/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (staff_can('edit', 'equipments') && staff_can('delete', 'equipments')) {
                $nameOutput .= ' | ';
            }
            if (staff_can('delete', 'equipments')) {
                $nameOutput .= '<a href="#" onclick="delete_group_confirm(' . $aRow['id'] . '); return false;" class="text-danger">' . _l('delete') . '</a>';
            }
            $nameOutput .= '</div>';

            $status    = (int)($aRow[db_prefix() . 'items_groups.status'] ?? 1);
            $badgeClass = $status == 1 ? 'label-success' : 'label-default';
            $badgeLabel = $status == 1 ? _l('active') : _l('inactive');

            $row[] = $nameOutput;
            $row[] = '<span class="tw-font-mono tw-text-sm tw-text-neutral-500">' . e($aRow[db_prefix() . 'items_groups.code'] ?? '—') . '</span>';
            $row[] = '<span class="label ' . $badgeClass . '">' . $badgeLabel . '</span>';
            $row[] = '<span class="badge">' . (int)$aRow['total_subgroups'] . '</span>';
            $row[] = '<span class="badge">' . (int)$aRow['total_items'] . '</span>';

            $row['DT_RowClass'] = 'has-row-options';
            $row['DT_RowData']  = ['id' => $aRow['id']];

            $row = hooks()->apply_filters('groups_table_row_data', $row, $aRow);

            $output['aaData'][] = $row;
        }

        return $output;
    })
    ->setRules([
        App_table_filter::new('name', 'TextRule')->label(_l('group_name')),
        App_table_filter::new('code', 'TextRule')->label(_l('group_code')),
        App_table_filter::new('status', 'MultiSelectRule')
            ->label(_l('group_statuses'))
            ->column(db_prefix() . 'items_groups.status')
            ->options(function ($ci) {
                return [
                    ['value' => '1', 'label' => _l('active')],
                    ['value' => '0', 'label' => _l('inactive')],
                ];
            }),
    ]);

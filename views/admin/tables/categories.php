<?php defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('equipments/Categories_model', 'categories_model');

return App_table::find('categories')
    ->outputUsing(function ($params) {

        $aColumns = [
            db_prefix() . 'items_categories.name',
            'grp.name as group_name',
            'sg.name as subgroup_name',
            db_prefix() . 'items_categories.sort_order',
            '(SELECT COUNT(*) FROM ' . db_prefix() . 'items WHERE category_id=' . db_prefix() . 'items_categories.id) as total_items',
        ];

        $join = [
            'LEFT JOIN ' . db_prefix() . 'items_subgroups sg  ON sg.id  = ' . db_prefix() . 'items_categories.subgroup_id',
            'LEFT JOIN ' . db_prefix() . 'items_groups    grp ON grp.id = sg.group_id',
        ];
        $where = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        $result = data_tables_init(
            $aColumns,
            'id',
            db_prefix() . 'items_categories',
            $join,
            $where,
            [db_prefix() . 'items_categories.id', db_prefix() . 'items_categories.subgroup_id', 'sg.group_id']
        );

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];

            $nameOutput = '<a href="#" onclick="init_category(' . $aRow['id'] . '); return false;" class="tw-font-medium">'
                . e($aRow[db_prefix() . 'items_categories.name']) . '</a>'
                . '<div class="row-options">';
            if (staff_can('edit', 'equipments')) {
                $nameOutput .= '<a href="' . admin_url('equipments/categories/category/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (staff_can('edit', 'equipments') && staff_can('delete', 'equipments')) {
                $nameOutput .= ' | ';
            }
            if (staff_can('delete', 'equipments')) {
                $nameOutput .= '<a href="#" onclick="delete_category_confirm(' . $aRow['id'] . '); return false;" class="text-danger">' . _l('delete') . '</a>';
            }
            $nameOutput .= '</div>';

            $sg_id  = (int) ($aRow['subgroup_id'] ?? 0);
            $grp_id = (int) ($aRow['group_id'] ?? 0);
            $sg_name  = $aRow['subgroup_name'] ?? '';
            $grp_name = $aRow['group_name'] ?? '';

            $row[] = $nameOutput;
            $row[] = $grp_name !== ''
                ? '<a href="' . admin_url('equipments/groups/list_groups/' . $grp_id) . '" class="label label-default">' . e($grp_name) . '</a>'
                : '—';
            $row[] = $sg_name !== ''
                ? '<a href="' . admin_url('equipments/subgroups/list_subgroups/' . $sg_id) . '" class="label label-info">' . e($sg_name) . '</a>'
                : '—';
            $row[] = e($aRow[db_prefix() . 'items_categories.sort_order']);
            $row[] = '<span class="badge">' . (int)$aRow['total_items'] . '</span>';

            $row['DT_RowClass'] = 'has-row-options';
            $row['DT_RowData']  = ['id' => $aRow['id']];

            $output['aaData'][] = $row;
        }

        return $output;
    })
    ->setRules([
        App_table_filter::new('name', 'TextRule')->label(_l('category_name')),
        App_table_filter::new('code', 'TextRule')->label(_l('group_code')),
        App_table_filter::new('subgroup_id', 'MultiSelectRule')
            ->label(_l('equipment_subgroup'))
            ->column(db_prefix() . 'items_categories.subgroup_id')
            ->options(function ($ci) {
                return collect($ci->db->order_by('name')->get(db_prefix() . 'items_subgroups')->result_array())
                    ->map(fn ($s) => ['value' => $s['id'], 'label' => $s['name']])
                    ->all();
            }),
    ]);

<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('equipments/Myequipments_model', 'myequipments_model');

return App_table::find('myequipments')
    ->outputUsing(function ($params) {

        $me          = get_staff(get_staff_user_id());
        $client_type = $me ? ($me->client_type ?? null) : null;
        $client_id   = $me ? (int) ($me->client_id ?? 0) : 0;

        $aColumns = [
            db_prefix() . 'customer_equipment.unit_code',
            db_prefix() . 'customer_equipment.customer_equipment_name',
            db_prefix() . 'items.description as item_name',
            db_prefix() . 'clients.company as client_company',
            db_prefix() . 'customer_equipment.serial_number',
            db_prefix() . 'customer_equipment.location',
            db_prefix() . 'customer_equipment.procurement_year',
            db_prefix() . 'customer_equipment.manufacture_year',
            db_prefix() . 'customer_equipment.cert_expired_date',
        ];

        $join = [
            'LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'items.id = ' . db_prefix() . 'customer_equipment.item_id',
            'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'customer_equipment.client_id',
        ];

        $where = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }

        // Entity scope
        if ($client_type === 'customer' && $client_id) {
            $scope = entity_scope_where($me, db_prefix() . 'customer_equipment.client_id', 'customer', 'myequipments', 'view');
            $where[] = 'AND ' . $scope;
        } elseif ($client_type === 'surveyor' && $client_id) {
            // Surveyor: only equipment that appears in their order items (has received PO)
            $sv_ids     = get_entity_client_ids((int) $client_id, 'surveyor');
            $sv_ids_str = implode(',', array_map('intval', $sv_ids));
            $where[] = 'AND ' . db_prefix() . 'customer_equipment.id IN (
                SELECT oi.customer_equipment_id
                FROM ' . db_prefix() . 'order_items oi
                JOIN ' . db_prefix() . 'orders o ON o.id = oi.order_id
                WHERE o.surveyor_id IN (' . $sv_ids_str . ')
                AND oi.customer_equipment_id IS NOT NULL
            )';
        } elseif (!is_admin() && staff_cant('view', 'myequipments')) {
            $where[] = 'AND 1=0';
        }

        $aColumns = hooks()->apply_filters('myequipments_table_sql_columns', $aColumns);

        $result = data_tables_init(
            $aColumns,
            'id',
            db_prefix() . 'customer_equipment',
            $join,
            $where,
            [
                db_prefix() . 'customer_equipment.id',
                db_prefix() . 'customer_equipment.client_id',
                db_prefix() . 'customer_equipment.item_id',
                db_prefix() . 'customer_equipment.cert_expired_date as _cert_expired',
            ]
        );

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];
            $id  = $aRow['id'];

            $row[] = '<div class="checkbox"><input type="checkbox" value="' . $id . '"><label></label></div>';

            // Derive status from cert_expired_date
            $cert_status = $this->ci->myequipments_model->get_cert_status($aRow['_cert_expired'] ?? '');

            $unitOutput = '<a href="#" onclick="init_myequipment(' . $id . '); return false;" class="tw-font-medium tw-font-mono">'
                . e($aRow[db_prefix() . 'customer_equipment.unit_code'] ?: '—')
                . '</a>'
                . '<div class="row-options">';
            if (staff_can('edit', 'myequipments') || staff_can('edit_own', 'myequipments')) {
                $unitOutput .= '<a href="' . admin_url('equipments/myequipments/myequipment/' . $id) . '">' . _l('edit') . '</a>';
            }
            if ((staff_can('edit', 'myequipments') || staff_can('edit_own', 'myequipments')) && staff_can('delete', 'myequipments')) {
                $unitOutput .= ' | ';
            }
            if (staff_can('delete', 'myequipments')) {
                $unitOutput .= '<a href="' . admin_url('equipments/myequipments/delete_myequipment/' . $id) . '" class="_delete text-danger">' . _l('delete') . '</a>';
            }
            $unitOutput .= '</div>';

            $row[] = $unitOutput;
            $row[] = e($aRow[db_prefix() . 'customer_equipment.customer_equipment_name'] ?? '—');
            $row[] = '<span class="label label-info">' . e($aRow['item_name'] ?? '—') . '</span>';
            $row[] = e($aRow['client_company'] ?? '—');

            $row[] = e($aRow[db_prefix() . 'customer_equipment.serial_number'] ?? '—');
            $row[] = e($aRow[db_prefix() . 'customer_equipment.location'] ?? '—');
            $row[] = e($aRow[db_prefix() . 'customer_equipment.procurement_year'] ?? '—');
            $row[] = e($aRow[db_prefix() . 'customer_equipment.manufacture_year'] ?? '—');

            $cert_date = $aRow[db_prefix() . 'customer_equipment.cert_expired_date'] ?? '';
            $row[] = ($cert_date && $cert_date !== '0000-00-00')
                ? '<span class="label label-' . equipment_status_color_class($cert_status) . '">' . e(_d($cert_date)) . '</span>'
                : '—';
            $row[] = format_equipment_status($cert_status);

            $row['DT_RowClass'] = 'has-row-options';
            $row['DT_RowData']  = ['id' => $id];

            $row = hooks()->apply_filters('myequipments_table_row_data', $row, $aRow);

            $output['aaData'][] = $row;
        }

        return $output;
    })
    ->setRules([
        App_table_filter::new('unit_code', 'TextRule')
            ->label(_l('equipment_unit_code'))
            ->column(db_prefix() . 'customer_equipment.unit_code'),

        App_table_filter::new('item_id', 'SelectRule')
            ->label(_l('equipment_type'))
            ->column(db_prefix() . 'customer_equipment.item_id')
            ->withEmptyOperators()
            ->options(function ($ci) {
                return collect($ci->db->order_by('description')->get(db_prefix() . 'items')->result_array())
                    ->map(fn ($i) => ['value' => $i['id'], 'label' => $i['description']])
                    ->all();
            }),

        App_table_filter::new('cert_expired_date', 'DateRule')
            ->label(_l('cert_expired_date'))
            ->column(db_prefix() . 'customer_equipment.cert_expired_date')
            ->withEmptyOperators(),

        App_table_filter::new('location', 'TextRule')
            ->label(_l('location'))
            ->column(db_prefix() . 'customer_equipment.location'),

        App_table_filter::new('status', 'MultiSelectRule')
            ->label(_l('equipment_status'))
            ->column('CASE
                WHEN ' . db_prefix() . 'customer_equipment.cert_expired_date IS NULL OR ' . db_prefix() . 'customer_equipment.cert_expired_date = \'0000-00-00\' THEN ' . EQUIPMENT_STATUS_DRAFT . '
                WHEN ' . db_prefix() . 'customer_equipment.cert_expired_date < CURDATE() THEN ' . EQUIPMENT_STATUS_EXPIRED . '
                WHEN ' . db_prefix() . 'customer_equipment.cert_expired_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN ' . EQUIPMENT_STATUS_INACTIVE . '
                ELSE ' . EQUIPMENT_STATUS_ACTIVE . '
            END')
            ->options(function ($ci) {
                return collect($ci->myequipments_model->get_statuses())->map(fn ($s) => [
                    'value' => (string) $s,
                    'label' => format_equipment_status($s, '', false),
                ])->all();
            }),
    ]);

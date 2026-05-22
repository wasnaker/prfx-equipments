<?php defined('BASEPATH') or exit('No direct script access allowed');

return App_table::find('equipments_widget')
    ->outputUsing(function ($params) {
        $_me = get_staff(get_staff_user_id());

        $aColumns = [
            db_prefix() . 'equipments.formatted_number',
            get_sql_select_client_company(),
            db_prefix() . 'equipments.date',
            db_prefix() . 'equipments.expirydate',
            db_prefix() . 'equipments.status',
        ];

        $join = [
            'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'equipments.client_id',
        ];

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'equipments';

        $where = [];

        // Widget filter: only upcoming (start date >= today)
        array_push($where, 'AND ' . db_prefix() . 'equipments.date >= CURDATE()');

        if (staff_cant('view', 'equipments')) {
            array_push($where, 'AND ' . get_equipments_where_sql_for_staff(get_staff_user_id()));
        }

        // Actor-based row filtering
        if ($_me->client_type === 'surveyor' && $_me->client_id) {
            array_push($where, 'AND ' . db_prefix() . 'equipments.addedfrom IN (
                SELECT staffid FROM ' . db_prefix() . 'staff WHERE client_id = ' . (int) $_me->client_id . '
            )');
        } elseif ($_me->client_type === 'customer' && $_me->client_id) {
            array_push($where, 'AND ' . db_prefix() . 'equipments.client_id IN (
                SELECT userid FROM ' . db_prefix() . 'clients
                WHERE (userid = ' . (int) $_me->client_id . ' OR company_id = ' . (int) $_me->client_id . ')
                AND client_type = "customer"
            )');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            db_prefix() . 'equipments.id',
            db_prefix() . 'equipments.hash',
            // Personnel as subquery — not a sortable column, fetched as extra select
            '(SELECT GROUP_CONCAT(CONCAT(st.firstname," ",st.lastname) ORDER BY st.firstname SEPARATOR ", ")
              FROM ' . db_prefix() . 'equipment_members sm
              JOIN ' . db_prefix() . 'staff st ON st.staffid = sm.staff_id
              WHERE sm.equipment_id = ' . db_prefix() . 'equipments.id) as members',
        ]);

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row    = [];
            $row[]  = '<a href="' . admin_url('equipments/list_equipments/' . $aRow['id']) . '">'
                    . e($aRow['formatted_number']) . '</a>';
            $row[]  = e($aRow['company'] ?? '');
            $row[]  = date('d M Y', strtotime($aRow['date']));
            $row[]  = ! empty($aRow['expirydate']) ? date('d M Y', strtotime($aRow['expirydate'])) : '—';
            $row[]  = e($aRow['members'] ?? '—');
            $row[]  = format_equipment_status($aRow[db_prefix() . 'equipments.status']);
            $output['aaData'][] = $row;
        }

        return $output;
    });

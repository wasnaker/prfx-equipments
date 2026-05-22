<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ── Hook registrations ────────────────────────────────────────────────────────

if (!function_exists('myequipments_sql_columns_remove_client_for_customer')) {

hooks()->add_filter('myequipments_table_sql_columns', 'myequipments_sql_columns_remove_client_for_customer');
hooks()->add_filter('myequipments_table_columns',     'myequipments_columns_remove_client_for_customer');

// ── Hook callbacks ────────────────────────────────────────────────────────────

function myequipments_sql_columns_remove_client_for_customer(array $columns): array
{
    if (datatable_actor_is('customer')) {
        $columns = datatable_sql_columns_remove_match($columns, 'clients.company');
    }
    return $columns;
}

function myequipments_columns_remove_client_for_customer(array $columns): array
{
    if (datatable_actor_is('customer')) {
        // index 4: 0=checkbox,1=unit,2=name,3=type,4=client
        $columns = datatable_columns_remove_index($columns, 4);
    }
    return $columns;
}

} // end function_exists guard

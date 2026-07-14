<?php

defined('BASEPATH') or exit('No direct script access allowed');

$route['equipments/(:num)/(:any)']      = 'equipment/index/$1/$2';

$route['equipments/groups']                     = 'groups/index';
$route['equipments/groups/(:num)']              = 'groups/index/$1';
$route['equipments/groups/list_groups/(:num)']  = 'groups/list_groups/$1';
$route['equipments/groups/list_groups']         = 'groups/list_groups';
$route['equipments/groups/add_edit_group']      = 'groups/add_edit_group';
$route['equipments/groups/group']               = 'groups/group';
$route['equipments/groups/group/(:num)']        = 'groups/group/$1';
$route['equipments/groups/delete_group/(:num)'] = 'groups/delete_group/$1';
$route['equipments/groups/get_group/(:num)']    = 'groups/get_group/$1';
$route['equipments/groups/table']                        = 'groups/table';
$route['equipments/groups/get_group_data_ajax/(:num)']   = 'groups/get_group_data_ajax/$1';
$route['equipments/groups/get_group_data_ajax/(:num)/(:any)'] = 'groups/get_group_data_ajax/$1/$2';

$route['equipments/subgroups']                                      = 'subgroups/index';
$route['equipments/subgroups/(:num)']                               = 'subgroups/index/$1';
$route['equipments/subgroups/list_subgroups/(:num)']                = 'subgroups/list_subgroups/$1';
$route['equipments/subgroups/list_subgroups']                       = 'subgroups/list_subgroups';
$route['equipments/subgroups/table']                        = 'subgroups/table';
$route['equipments/subgroups/subgroup']                     = 'subgroups/subgroup';
$route['equipments/subgroups/subgroup/(:num)']              = 'subgroups/subgroup/$1';
$route['equipments/subgroups/add_edit_subgroup']            = 'subgroups/add_edit_subgroup';
$route['equipments/subgroups/delete_subgroup/(:num)']       = 'subgroups/delete_subgroup/$1';
$route['equipments/subgroups/get_subgroup/(:num)']          = 'subgroups/get_subgroup/$1';
$route['equipments/subgroups/get_subgroup_data_ajax/(:num)']       = 'subgroups/get_subgroup_data_ajax/$1';
$route['equipments/subgroups/get_subgroup_data_ajax/(:num)/(:any)'] = 'subgroups/get_subgroup_data_ajax/$1/$2';

$route['equipments/categories']                                 = 'categories/index';
$route['equipments/categories/(:num)']                          = 'categories/index/$1';
$route['equipments/categories/table']                           = 'categories/table';
$route['equipments/categories/category']                        = 'categories/category';
$route['equipments/categories/category/(:num)']                 = 'categories/category/$1';
$route['equipments/categories/add_edit_category']               = 'categories/add_edit_category';
$route['equipments/categories/delete_category/(:num)']          = 'categories/delete_category/$1';
$route['equipments/categories/get_category/(:num)']             = 'categories/get_category/$1';
$route['equipments/categories/get_category_data_ajax/(:num)']   = 'categories/get_category_data_ajax/$1';

$route['equipments/myequipments']                                      = 'myequipments/index';
$route['equipments/myequipments/(:num)']                               = 'myequipments/list_myequipments/$1';
$route['equipments/myequipments/table']                                = 'myequipments/table';
$route['equipments/myequipments/myequipment']                          = 'myequipments/myequipment';
$route['equipments/myequipments/myequipment/(:num)']                   = 'myequipments/myequipment/$1';
$route['equipments/myequipments/add_edit_myequipment']                 = 'myequipments/add_edit_myequipment';
$route['equipments/myequipments/delete_myequipment/(:num)']            = 'myequipments/delete_myequipment/$1';
$route['equipments/myequipments/get_myequipment_data_ajax/(:num)']     = 'myequipments/get_myequipment_data_ajax/$1';
$route['equipments/myequipments/get_myequipment_data_ajax/(:num)/(:any)'] = 'myequipments/get_myequipment_data_ajax/$1/$2';
$route['equipments/myequipments/get_tasks_table/(:num)']                = 'myequipments/get_tasks_table/$1';
$route['equipments/myequipments/get_history_table/(:num)']             = 'myequipments/get_history_table/$1';
$route['equipments/myequipments/get_notes/(:num)']                     = 'myequipments/get_notes/$1';
$route['equipments/myequipments/add_note/(:num)']                      = 'myequipments/add_note/$1';
$route['myequipments/get_tasks_table/(:num)']                           = 'myequipments/get_tasks_table/$1';
$route['myequipments/get_notes/(:num)']                                = 'myequipments/get_notes/$1';
$route['myequipments/add_note/(:num)']                                 = 'myequipments/add_note/$1';

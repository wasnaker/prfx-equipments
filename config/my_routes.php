<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Customer Equipment Portal API — JWT-based
$route['api/v1/customer/equipment']        = 'equipments/myequipments_api/index';
$route['api/v1/customer/equipment/(:num)'] = 'equipments/myequipments_api/show/$1';

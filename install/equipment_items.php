<?php
defined('BASEPATH') or exit('No direct script access allowed');
$CI = &get_instance();

$CI->db->query("CREATE TABLE IF NOT EXISTS `" . db_prefix() . "equipment_items` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `equipment_id` BIGINT NOT NULL,
  `job_id` BIGINT DEFAULT NULL,
  `customer_equipment_id` BIGINT DEFAULT NULL,
  `customer_equipment_name` varchar(255) DEFAULT NULL,
  `item_id` BIGINT DEFAULT NULL,
  `qty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `rate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(50) DEFAULT NULL,
  `long_description` longtext DEFAULT NULL,
  `item_order` BIGINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `equipment_id` (`equipment_id`),
  KEY `job_id` (`job_id`),
  KEY `customer_equipment_id` (`customer_equipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");

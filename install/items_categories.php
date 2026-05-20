<?php defined('BASEPATH') or exit('No direct script access allowed');

$CI->db->query("CREATE TABLE IF NOT EXISTS `" . db_prefix() . "items_categories` (
  `id`          BIGINT      NOT NULL AUTO_INCREMENT,
  `subgroup_id` BIGINT      NOT NULL,
  `name`        varchar(100) NOT NULL,
  `code`        varchar(20)  DEFAULT NULL,
  `sort_order`  BIGINT      NOT NULL DEFAULT 0,
  `datecreated` datetime     NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subgroup_id` (`subgroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Add category_id to tblitems
if (!$CI->db->field_exists('category_id', db_prefix() . 'items')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'items`
        ADD COLUMN `category_id` BIGINT DEFAULT NULL AFTER `subgroup_id`,
        ADD INDEX `category_id` (`category_id`)');
}

// Add category_id to tblinspection_checklist_items
if (!$CI->db->field_exists('category_id', db_prefix() . 'inspection_checklist_items')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'inspection_checklist_items`
        ADD COLUMN `category_id` BIGINT DEFAULT NULL AFTER `subgroup_id`,
        ADD INDEX `category_id` (`category_id`)');
}

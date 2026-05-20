<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

$CI->db->query("CREATE TABLE IF NOT EXISTS `" . db_prefix() . "equipment_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `equipmentid` int(11) NOT NULL,
  `full_name` varchar(200) DEFAULT NULL,
  `staffid` int(11) NOT NULL DEFAULT 0,
  `activity` mediumtext DEFAULT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `equipmentid` (`equipmentid`)
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");

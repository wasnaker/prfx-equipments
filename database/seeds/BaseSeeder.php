<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Minimal base for production/live seeders in the equipments module.
 * No dependency on demo module. Only provides $db and $now.
 */
abstract class EquipmentsBaseSeeder
{
    protected $db;
    protected string $now;

    public function __construct($db)
    {
        $this->db  = $db;
        $this->now = date('Y-m-d H:i:s');
    }

    abstract public function run(): array;

    protected function find_subgroup(string $name): ?int
    {
        $row = $this->db->get_where(db_prefix() . 'items_subgroups', ['name' => $name])->row();
        return $row ? (int) $row->id : null;
    }

    protected function find_category(string $name, int $subgroup_id): ?int
    {
        $row = $this->db->get_where(db_prefix() . 'items_categories', [
            'name'       => $name,
            'subgroup_id' => $subgroup_id,
        ])->row();
        return $row ? (int) $row->id : null;
    }
}

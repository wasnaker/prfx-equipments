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

    protected function find_subgroup_with_group(string $name): ?object
    {
        $row = $this->db->get_where(db_prefix() . 'items_subgroups', ['name' => $name])->row();
        return $row ? (object) ['id' => (int) $row->id, 'group_id' => (int) $row->group_id] : null;
    }

    protected function find_category(string $name, int $subgroup_id): ?int
    {
        $row = $this->db->get_where(db_prefix() . 'items_categories', [
            'name'       => $name,
            'subgroup_id' => $subgroup_id,
        ])->row();
        return $row ? (int) $row->id : null;
    }

    protected function insert(string $table, array $data): void
    {
        $this->db->insert(db_prefix() . $table, $data);
    }

    protected function upsert(string $table, string $key, array $data): void
    {
        $this->db->where($key, $data[$key])->delete(db_prefix() . $table);
        $this->db->insert(db_prefix() . $table, $data);
    }

    protected function no_debug(callable $callback): void
    {
        $callback();
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/BaseSeeder.php';

/**
 * Seeds tblitems_groups and tblitems_subgroups with K3 master data.
 * Production data — runs from equipments/install.php on every install.
 * Safe to re-run: lookup by name before insert.
 */
class SubgroupsSeeder extends EquipmentsBaseSeeder
{
    private array $groups = [
        ['code' => 'PAA', 'name' => 'Pesawat Angkat dan Angkut',             'subgroups' => [
            ['name' => 'Pesawat Angkat',              'sort_order' => 1],
            ['name' => 'Pesawat Angkut',              'sort_order' => 2],
        ]],
        ['code' => 'PTP', 'name' => 'Pesawat Tenaga dan Produksi',            'subgroups' => [
            ['name' => 'Penggerak Mula',              'sort_order' => 1],
            ['name' => 'Mesin Perkakas',              'sort_order' => 2],
            ['name' => 'Mesin Produksi',              'sort_order' => 3],
            ['name' => 'Transmisi Tenaga Mekanik',    'sort_order' => 4],
            ['name' => 'Tanur',                       'sort_order' => 5],
            ['name' => 'Turbin',                      'sort_order' => 6],
            ['name' => 'Kincir Angin',                'sort_order' => 7],
            ['name' => 'Motor Listrik',               'sort_order' => 8],
        ]],
        ['code' => 'PUBT', 'name' => 'Pesawat Uap Bejana Tekan dan Tangki Timbun', 'subgroups' => [
            ['name' => 'Pesawat Uap',                 'sort_order' => 1],
            ['name' => 'Bejana Bertekanan',           'sort_order' => 2],
            ['name' => 'Tangki Timbun',               'sort_order' => 3],
        ]],
        ['code' => 'IPK', 'name' => 'Sarana Proteksi Kebakaran',              'subgroups' => [
            ['name' => 'APAR',                        'sort_order' => 1],
            ['name' => 'Instalasi Hydrant',           'sort_order' => 2],
            ['name' => 'Fire Alarm',                  'sort_order' => 3],
        ]],
        ['code' => 'ILP', 'name' => 'Instalasi Listrik dan Penyalur Petir',   'subgroups' => [
            ['name' => 'Instalasi Listrik',           'sort_order' => 1],
            ['name' => 'Penyalur Petir',              'sort_order' => 2],
        ]],
        ['code' => 'LIE', 'name' => 'Elevator dan Eskalator',                 'subgroups' => [
            ['name' => 'Lift',                        'sort_order' => 1],
            ['name' => 'Eskalator',                   'sort_order' => 2],
        ]],
    ];

    public function run(): array
    {
        $result = [];

        foreach ($this->groups as $g) {
            // Insert group if not exists (match by name)
            $existing_group = $this->db
                ->get_where(db_prefix() . 'items_groups', ['name' => $g['name']])
                ->row();

            if ($existing_group) {
                $group_id = (int) $existing_group->id;
                // Update code if missing
                if (empty($existing_group->code)) {
                    $this->db->where('id', $group_id)
                             ->update(db_prefix() . 'items_groups', ['code' => $g['code']]);
                }
            } else {
                $this->db->insert(db_prefix() . 'items_groups', [
                    'name' => $g['name'],
                    'code' => $g['code'],
                ]);
                $group_id = (int) $this->db->insert_id();
            }

            foreach ($g['subgroups'] as $sg) {
                $existing_sg = $this->db
                    ->get_where(db_prefix() . 'items_subgroups', [
                        'name'     => $sg['name'],
                        'group_id' => $group_id,
                    ])->row();

                if ($existing_sg) {
                    $sg_id = (int) $existing_sg->id;
                } else {
                    $this->db->insert(db_prefix() . 'items_subgroups', [
                        'name'       => $sg['name'],
                        'group_id'   => $group_id,
                        'sort_order' => $sg['sort_order'],
                        'color'      => '#3b82f6',
                    ]);
                    $sg_id = (int) $this->db->insert_id();
                }

                $result[$sg['name']] = $sg_id;
            }
        }

        return $result;
    }
}

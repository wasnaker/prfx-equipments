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
        ['code' => 'PAA', 'abbr' => 'PA', 'name' => 'Pesawat Angkat dan Angkut',             'subgroups' => [
            ['name' => 'Pesawat Angkat',              'sort_order' => 1, 'abbr' => 'PA'],
            ['name' => 'Pesawat Angkut',              'sort_order' => 2, 'abbr' => 'PK'],
        ]],
        ['code' => 'PTP', 'abbr' => 'PT', 'name' => 'Pesawat Tenaga dan Produksi',            'subgroups' => [
            ['name' => 'Penggerak Mula',              'sort_order' => 1, 'abbr' => 'GM'],
            ['name' => 'Mesin Perkakas',              'sort_order' => 2, 'abbr' => 'MP'],
            ['name' => 'Mesin Produksi',              'sort_order' => 3, 'abbr' => 'MR'],
            ['name' => 'Transmisi Tenaga Mekanik',    'sort_order' => 4, 'abbr' => 'TM'],
            ['name' => 'Tanur',                       'sort_order' => 5, 'abbr' => 'TN'],
            ['name' => 'Turbin',                      'sort_order' => 6, 'abbr' => 'TB'],
            ['name' => 'Kincir Angin',                'sort_order' => 7, 'abbr' => 'KA'],
            ['name' => 'Motor Listrik',               'sort_order' => 8, 'abbr' => 'ML'],
        ]],
        ['code' => 'PUBT', 'abbr' => 'UB', 'name' => 'Pesawat Uap Bejana Tekan dan Tangki Timbun', 'subgroups' => [
            ['name' => 'Pesawat Uap',                 'sort_order' => 1, 'abbr' => 'PU'],
            ['name' => 'Bejana Bertekanan',           'sort_order' => 2, 'abbr' => 'BT'],
            ['name' => 'Tangki Timbun',               'sort_order' => 3, 'abbr' => 'TT'],
        ]],
        ['code' => 'IPK', 'abbr' => 'PK', 'name' => 'Sarana Proteksi Kebakaran',              'subgroups' => [
            ['name' => 'APAR',                        'sort_order' => 1, 'abbr' => 'AP'],
            ['name' => 'Instalasi Hydrant',           'sort_order' => 2, 'abbr' => 'IH'],
            ['name' => 'Fire Alarm',                  'sort_order' => 3, 'abbr' => 'FA'],
        ]],
        ['code' => 'ILP', 'abbr' => 'IL', 'name' => 'Instalasi Listrik dan Penyalur Petir',   'subgroups' => [
            ['name' => 'Instalasi Listrik',           'sort_order' => 1, 'abbr' => 'IL'],
            ['name' => 'Penyalur Petir',              'sort_order' => 2, 'abbr' => 'PP'],
        ]],
        ['code' => 'LIE', 'abbr' => 'LE', 'name' => 'Elevator dan Eskalator',                 'subgroups' => [
            ['name' => 'Lift',                        'sort_order' => 1, 'abbr' => 'LF'],
            ['name' => 'Eskalator',                   'sort_order' => 2, 'abbr' => 'ES'],
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
                // Update abbr if provided but missing
                if (!empty($g['abbr']) && empty($existing_group->abbr)) {
                    $this->db->where('id', $group_id)
                             ->update(db_prefix() . 'items_groups', ['abbr' => $g['abbr']]);
                }
            } else {
                $this->db->insert(db_prefix() . 'items_groups', [
                    'name' => $g['name'],
                    'code' => $g['code'],
                    'abbr' => $g['abbr'] ?? null,
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
                    // Update abbr if provided but missing
                    if (!empty($sg['abbr']) && empty($existing_sg->abbr)) {
                        $this->db->where('id', $sg_id)
                                 ->update(db_prefix() . 'items_subgroups', ['abbr' => $sg['abbr']]);
                    }
                } else {
                    $this->db->insert(db_prefix() . 'items_subgroups', [
                        'name'       => $sg['name'],
                        'group_id'   => $group_id,
                        'sort_order' => $sg['sort_order'],
                        'color'      => '#3b82f6',
                        'abbr'       => $sg['abbr'] ?? null,
                    ]);
                    $sg_id = (int) $this->db->insert_id();
                }

                $result[$sg['name']] = $sg_id;
            }
        }

        return $result;
    }
}

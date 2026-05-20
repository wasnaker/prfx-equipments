<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/BaseSeeder.php';

/**
 * Seeds tblitems_categories with K3 master data.
 * Production data — runs from equipments/install.php on every install.
 * Each category justified by specific NDT method differences (UT/RT/ET/MFL).
 * Safe to re-run: lookup by name+subgroup_id before insert.
 */
class CategoriesSeeder extends EquipmentsBaseSeeder
{
    private array $categories = [

        // ── Subgroup: Pesawat Angkat ──────────────────────────────────────────
        // Justifikasi: komponen UT berbeda (girder vs drum vs wire rope)
        'Pesawat Angkat' => [
            ['code' => 'CRN', 'name' => 'Crane',    'sort_order' => 1],  // UT girder/boom/mast + MT hook
            ['code' => 'HST', 'name' => 'Hoist',    'sort_order' => 2],  // UT drum + wire rope/chain diameter
            ['code' => 'GND', 'name' => 'Gondola',  'sort_order' => 3],  // UT platform + wire rope diameter
        ],

        // ── Subgroup: Pesawat Angkut ──────────────────────────────────────────
        // Justifikasi: komponen UT berbeda (fork heel vs mast chain vs scissor arm)
        'Pesawat Angkut' => [
            ['code' => 'FRK', 'name' => 'Fork',         'sort_order' => 1],  // UT fork heel/blade ISO 2330 min 90%
            ['code' => 'HLF', 'name' => 'Gudang Tinggi','sort_order' => 2],  // UT mast channel + reach arm + chain gauge
            ['code' => 'AWP', 'name' => 'Scissor/AWP',  'sort_order' => 3],  // UT scissor arm (pin hole) + hydraulic cylinder
        ],

        // ── Subgroup: Pesawat Uap ─────────────────────────────────────────────
        // Justifikasi: UT berbeda + RT berbeda (FTB: furnace/tube; WTB: drum/waterwall)
        // Shell & Tube HX: UT + ET tube bundle (eddy current)
        'Pesawat Uap' => [
            ['code' => 'FTB', 'name' => 'Fire Tube Boiler',  'sort_order' => 1],  // UT shell/furnace/tube + RT critical welds
            ['code' => 'WTB', 'name' => 'Water Tube Boiler', 'sort_order' => 2],  // UT drum/waterwall/header + RT drum welds
            ['code' => 'SHX', 'name' => 'Shell & Tube HX',   'sort_order' => 3],  // UT shell/channel + ET tube bundle
        ],

        // ── Subgroup: Bejana Bertekanan ───────────────────────────────────────
        // Justifikasi: Pressure Vessel = UT 4 posisi; HEX = UT + ET tube bundle; Autoclave = UT door/locking ring unik
        'Bejana Bertekanan' => [
            ['code' => 'PVS', 'name' => 'Pressure Vessel', 'sort_order' => 1],  // UT 4 posisi shell + heads + nozzle
            ['code' => 'HEX', 'name' => 'Heat Exchanger',  'sort_order' => 2],  // UT shell + ET tube bundle
            ['code' => 'ATC', 'name' => 'Autoclave',       'sort_order' => 3],  // UT chamber + door + locking ring
        ],

        // ── Subgroup: Tangki Timbun ───────────────────────────────────────────
        // Justifikasi: Flammable = API 653 + MFL bottom; Chemical = UT + lining; Water = UT standar ringan
        'Tangki Timbun' => [
            ['code' => 'FLT', 'name' => 'Flammable Tank', 'sort_order' => 1],  // UT shell per course (API 653) + MFL bottom scan
            ['code' => 'CHT', 'name' => 'Chemical Tank',  'sort_order' => 2],  // UT corrosion mapping + lining inspection
            ['code' => 'WTK', 'name' => 'Water Tank',     'sort_order' => 3],  // UT standar ringan, tidak wajib API 653
        ],
    ];

    public function run(): array
    {
        $result = [];

        foreach ($this->categories as $subgroup_name => $cats) {
            $sg = $this->db
                ->get_where(db_prefix() . 'items_subgroups', ['name' => $subgroup_name])
                ->row();

            if (!$sg) { continue; }

            $sg_id = (int) $sg->id;

            foreach ($cats as $cat) {
                $existing = $this->db
                    ->get_where(db_prefix() . 'items_categories', [
                        'name'       => $cat['name'],
                        'subgroup_id' => $sg_id,
                    ])->row();

                if ($existing) {
                    $cat_id = (int) $existing->id;
                    if (empty($existing->code)) {
                        $this->db->where('id', $cat_id)
                                 ->update(db_prefix() . 'items_categories', ['code' => $cat['code']]);
                    }
                } else {
                    $this->db->insert(db_prefix() . 'items_categories', [
                        'subgroup_id' => $sg_id,
                        'name'        => $cat['name'],
                        'code'        => $cat['code'],
                        'sort_order'  => $cat['sort_order'],
                        'datecreated' => $this->now,
                    ]);
                    $cat_id = (int) $this->db->insert_id();
                }

                $result[$cat['name']] = $cat_id;
            }
        }

        return $result;
    }
}

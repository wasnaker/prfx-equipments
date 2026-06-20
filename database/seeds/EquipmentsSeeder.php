<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/BaseSeeder.php';

/**
 * Seeds tblitems (equipment catalog) with K3 master data.
 * Production data — runs from equipments/install.php on every install.
 * Assigns category_id where NDT-justified categories exist.
 * Safe to re-run: lookup by description+subgroup_id before insert.
 * Returns ['item_ids' => [...], 'by_name' => [description => id]].
 */
class EquipmentsSeeder extends EquipmentsBaseSeeder
{
    private array $catalog = [

        // ── Subgroup: Pesawat Angkat ──────────────────────────────────────────
        'Pesawat Angkat' => [
            ['description' => 'Overhead Crane',  'category' => 'Crane'],
            ['description' => 'Gantry Crane',    'category' => 'Crane'],
            ['description' => 'Tower Crane',     'category' => 'Crane'],
            ['description' => 'Mobile Crane',    'category' => 'Crane'],
            ['description' => 'Jib Crane',       'category' => 'Crane'],
            ['description' => 'Electric Hoist',  'category' => 'Hoist'],
            ['description' => 'Manual Hoist',    'category' => 'Hoist'],
            ['description' => 'Chain Hoist',     'category' => 'Hoist'],
            ['description' => 'Gondola',         'category' => 'Gondola'],
        ],

        // ── Subgroup: Pesawat Angkut ──────────────────────────────────────────
        'Pesawat Angkut' => [
            ['description' => 'Forklift',              'category' => 'Fork'],
            ['description' => 'Reach Stacker',         'category' => 'Fork'],
            ['description' => 'Electric Stacker',      'category' => 'Fork'],
            ['description' => 'Hand Stacker',          'category' => 'Fork'],
            ['description' => 'Reach Truck',           'category' => 'Gudang Tinggi'],
            ['description' => 'Order Picker',          'category' => 'Gudang Tinggi'],
            ['description' => 'VNA Truck',             'category' => 'Gudang Tinggi'],
            ['description' => 'Scissor Lift',          'category' => 'Scissor/AWP'],
            ['description' => 'Aerial Work Platform',  'category' => 'Scissor/AWP'],
            ['description' => 'Conveyor Belt',         'category' => null],
            ['description' => 'Lift Barang',           'category' => null],
        ],

        // ── Subgroup: Penggerak Mula ──────────────────────────────────────────
        'Penggerak Mula' => [
            ['description' => 'Motor Diesel',    'category' => null],
            ['description' => 'Motor Bakar',     'category' => null],
            ['description' => 'Kompresor Udara', 'category' => null],
            ['description' => 'Generator Set',   'category' => null],
            ['description' => 'Pompa Industri',  'category' => null],
        ],

        // ── Subgroup: Mesin Perkakas ──────────────────────────────────────────
        'Mesin Perkakas' => [
            ['description' => 'Mesin Bubut',  'category' => null],
            ['description' => 'Mesin Milling','category' => null],
            ['description' => 'Mesin Asah',   'category' => null],
            ['description' => 'Mesin Bor',    'category' => null],
            ['description' => 'Mesin Sekrap', 'category' => null],
        ],

        // ── Subgroup: Mesin Produksi ──────────────────────────────────────────
        'Mesin Produksi' => [
            ['description' => 'Mesin Press',             'category' => null],
            ['description' => 'Mesin Tempa',             'category' => null],
            ['description' => 'Mesin Crusher',           'category' => null],
            ['description' => 'Mesin Cor',               'category' => null],
            ['description' => 'Mesin Injection Moulding','category' => null],
        ],

        // ── Subgroup: Transmisi Tenaga Mekanik ────────────────────────────────
        'Transmisi Tenaga Mekanik' => [
            ['description' => 'Transmisi Sabuk',    'category' => null],
            ['description' => 'Transmisi Rantai',   'category' => null],
            ['description' => 'Transmisi Roda Gigi','category' => null],
        ],

        // ── Subgroup: Tanur ───────────────────────────────────────────────────
        'Tanur' => [
            ['description' => 'Furnace',      'category' => null],
            ['description' => 'Blast Furnace','category' => null],
            ['description' => 'Oven Industri','category' => null],
            ['description' => 'Kiln',         'category' => null],
        ],

        // ── Subgroup: Turbin ──────────────────────────────────────────────────
        'Turbin' => [
            ['description' => 'Turbin Uap','category' => null],
            ['description' => 'Turbin Gas', 'category' => null],
        ],

        // ── Subgroup: Kincir Angin ────────────────────────────────────────────
        'Kincir Angin' => [
            ['description' => 'Kincir Angin','category' => null],
        ],

        // ── Subgroup: Motor Listrik ───────────────────────────────────────────
        'Motor Listrik' => [
            ['description' => 'Motor Listrik AC','category' => null],
            ['description' => 'Motor Listrik DC','category' => null],
        ],

        // ── Subgroup: Pesawat Uap ─────────────────────────────────────────────
        'Pesawat Uap' => [
            ['description' => 'Boiler Fire Tube', 'category' => 'Fire Tube Boiler'],
            ['description' => 'Boiler Water Tube','category' => 'Water Tube Boiler'],
            ['description' => 'Steam Drum',       'category' => 'Water Tube Boiler'],
            ['description' => 'Mud Drum',         'category' => 'Water Tube Boiler'],
            ['description' => 'HRSG',             'category' => 'Water Tube Boiler'],
            ['description' => 'Reboiler',         'category' => 'Shell & Tube HX'],
            ['description' => 'Condenser',        'category' => 'Shell & Tube HX'],
        ],

        // ── Subgroup: Bejana Bertekanan ───────────────────────────────────────
        'Bejana Bertekanan' => [
            ['description' => 'Bejana Tekan',  'category' => 'Pressure Vessel'],
            ['description' => 'Air Receiver',  'category' => 'Pressure Vessel'],
            ['description' => 'Separator',     'category' => 'Pressure Vessel'],
            ['description' => 'Accumulator',   'category' => 'Pressure Vessel'],
            ['description' => 'Heat Exchanger','category' => 'Heat Exchanger'],
            ['description' => 'Superheater',   'category' => 'Heat Exchanger'],
            ['description' => 'Economizer',    'category' => 'Heat Exchanger'],
            ['description' => 'Deaerator',     'category' => 'Heat Exchanger'],
            ['description' => 'Autoclave',     'category' => 'Autoclave'],
        ],

        // ── Subgroup: Tangki Timbun ───────────────────────────────────────────
        'Tangki Timbun' => [
            ['description' => 'Storage Tank', 'category' => 'Flammable Tank'],
            ['description' => 'Tangki Solar',  'category' => 'Flammable Tank'],
            ['description' => 'Tangki Minyak', 'category' => 'Flammable Tank'],
            ['description' => 'Tangki Kimia',  'category' => 'Chemical Tank'],
            ['description' => 'Tangki Air',    'category' => 'Water Tank'],
        ],

        // ── Subgroup: APAR ────────────────────────────────────────────────────
        'APAR' => [
            ['description' => 'APAR CO2',   'category' => null],
            ['description' => 'APAR Powder','category' => null],
            ['description' => 'APAR Foam',  'category' => null],
        ],

        // ── Subgroup: Instalasi Hydrant ───────────────────────────────────────
        'Instalasi Hydrant' => [
            ['description' => 'Instalasi Hydrant',       'category' => null],
            ['description' => 'Sprinkler System',        'category' => null],
            ['description' => 'Fire Suppression System', 'category' => null],
        ],

        // ── Subgroup: Fire Alarm ──────────────────────────────────────────────
        'Fire Alarm' => [
            ['description' => 'Fire Alarm System','category' => null],
        ],

        // ── Subgroup: Instalasi Listrik ───────────────────────────────────────
        'Instalasi Listrik' => [
            ['description' => 'Panel MDP', 'category' => null],
            ['description' => 'Panel SDP', 'category' => null],
            ['description' => 'Transformator','category' => null],
            ['description' => 'Genset',    'category' => null],
            ['description' => 'ATS',       'category' => null],
            ['description' => 'UPS',       'category' => null],
        ],

        // ── Subgroup: Penyalur Petir ──────────────────────────────────────────
        'Penyalur Petir' => [
            ['description' => 'Instalasi Penyalur Petir','category' => null],
            ['description' => 'Grounding System',        'category' => null],
            ['description' => 'SPD',                     'category' => null],
        ],

        // ── Subgroup: Lift ────────────────────────────────────────────────────
        'Lift' => [
            ['description' => 'Elevator Penumpang','category' => null],
            ['description' => 'Elevator Barang',  'category' => null],
            ['description' => 'Bed Lift',         'category' => null],
            ['description' => 'Service Lift',     'category' => null],
            ['description' => 'Dumbwaiter',       'category' => null],
            ['description' => 'Car Lift',         'category' => null],
        ],

        // ── Subgroup: Eskalator ───────────────────────────────────────────────
        'Eskalator' => [
            ['description' => 'Eskalator',     'category' => null],
            ['description' => 'Moving Walkway','category' => null],
        ],
    ];

    public function run(): array
    {
        $item_ids = [];
        $by_name  = [];

        foreach ($this->catalog as $subgroup_name => $items) {
            $sg_id = $this->find_subgroup($subgroup_name);
            if (!$sg_id) { continue; }

            foreach ($items as $item) {
                $cat_id = null;
                if ($item['category']) {
                    $cat_id = $this->find_category($item['category'], $sg_id);
                }

                $existing = $this->db->get_where(db_prefix() . 'items', [
                    'description' => $item['description'],
                    'subgroup_id' => $sg_id,
                ])->row();

                if ($existing) {
                    $item_id = (int) $existing->id;
                    if ($cat_id && empty($existing->category_id)) {
                        $this->db->where('id', $item_id)
                                 ->update(db_prefix() . 'items', ['category_id' => $cat_id]);
                    }
                } else {
                    $this->db->insert(db_prefix() . 'items', [
                        'description' => $item['description'],
                        'subgroup_id' => $sg_id,
                        'category_id' => $cat_id,
                        'rate'        => 0,
                        'unit'        => '',
                        'addedfrom'   => 1,
                    ]);
                    $item_id = (int) $this->db->insert_id();
                }

                $item_ids[]                     = $item_id;
                $by_name[$item['description']]  = $item_id;
            }
        }

        return ['item_ids' => $item_ids, 'by_name' => $by_name];
    }
}

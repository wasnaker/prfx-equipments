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
            ['description' => 'Overhead Crane',  'category' => 'Crane', 'long_description' => <<<'MD'
## Overhead Crane

Overhead Crane (Crane Jembatan) adalah pesawat angkat yang bergerak di atas rel (runway) untuk memindahkan beban berat secara horizontal dan vertikal di dalam gedung pabrik, bengkel, atau area produksi.

### Prinsip Kerja

Crane terdiri dari **girder** (balok jembatan) yang berjalan di atas rel sejajar, dengan **trolley** (kereta) yang bergerak sepanjang girder. **Hoist** (alat angkat) dipasang pada trolley untuk mengangkat dan menurunkan beban.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Girder | Balok jembatan utama |
| End Truck | Penopang girder + motor travel |
| Trolley | Kereta pembawa hoist |
| Hoist | Alat angkat (wire rope/chain) |
| Hook | Kait pengangkat beban |
| Pendant/Remote | Kontrol operator |
| Limit Switch | Pengaman batas angkat & travel |

### Aplikasi

1. Bengkel fabrikasi
2. Pabrik manufaktur
3. Gudang logistik
4. Workshop maintenance

> Overhead crane dapat mengangkat beban dari 1 ton hingga 500+ ton dengan span hingga 40 meter.
MD,
            ],
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
            ['description' => 'Forklift',              'category' => 'Fork', 'long_description' => <<<'MD'
## Forklift

Forklift adalah kendaraan industri bertenaga yang dilengkapi dengan **garpu (fork)** untuk mengangkat, memindahkan, dan menumpuk material berat. Merupakan salah satu pesawat angkat-angkut paling umum digunakan di gudang dan pabrik.

### Prinsip Kerja

Forklift menggunakan **sistem hidrolik** untuk mengangkat garpu. Mast (tiang) bergerak vertikal dengan bantuan silinder hidrolik, sementara garpu yang terpasang pada carriage dapat diatur jaraknya sesuai ukuran pallet.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Mast | Tiang angkat (2-stage/3-stage) |
| Fork (Garpu) | Pengangkat material |
| Carriage | Penopang garpu pada mast |
| Counterweight | Penyeimbang beban |
| Mesin/Motor | Sumber tenaga (diesel/baterai/LPG) |
| Hydraulic System | Penggerak mast & tilt |
| Overhead Guard | Pelindung operator |

### Jenis Forklift

- **Counterbalance** — standar, beban di depan
- **Reach Truck** — untuk gudang tinggi
- **Order Picker** — picking manual
- **Pallet Jack** — ringan, manual/elektrik

> Forklift tersedia dalam kapasitas 1-50 ton dengan tinggi angkat hingga 12+ meter.
MD,
            ],
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
            ['description' => 'Generator Set',   'category' => null, 'long_description' => <<<'MD'
## Generator Set

Generator Set (Genset) adalah peralatan yang mengubah **energi mekanik** dari mesin penggerak menjadi **energi listrik**. Terdiri dari engine (motor diesel/bensin/gas) yang dikopel langsung dengan generator (alternator).

### Prinsip Kerja

Engine menggerakkan rotor generator yang berputar di dalam medan magnet stator. Perputaran rotor menghasilkan **induksi elektromagnetik** pada kumparan stator, menghasilkan arus listrik AC.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Engine | Sumber tenaga mekanik |
| Alternator | Konversi energi mekanik ke listrik |
| AVR (Auto Voltage Regulator) | Pengatur tegangan |
| Panel Kontrol | Monitoring & kontrol operasi |
| Radiator | Sistem pendingin engine |
| Aki | Starting baterai |
| Tangki Bahan Bakar | Suplai fuel |

### Aplikasi

1. **Standby power** — gedung, rumah sakit, hotel
2. **Prime power** — daerah tanpa grid listrik
3. **Peak shaving** — pengurangan beban puncak
4. **Base load** — industri terpencil

> Genset tersedia dari 5 kVA hingga 5.000+ kVA dengan berbagai jenis bahan bakar (diesel, gas, dual fuel).
MD,
            ],
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
            ['description' => 'Turbin Uap', 'category' => null, 'long_description' => <<<'MD'
## Turbin Uap

Turbin Uap adalah mesin panas berputar yang mengubah energi potensial uap menjadi energi mekanik untuk menggerakkan generator listrik atau peralatan mekanis lainnya.

### Prinsip Kerja

Prinsip kerja turbin uap didasarkan pada **konversi energi uap bertekanan tinggi** menjadi energi rotasi melalui nozzle dan baling-baling (blade). Uap masuk pada tekanan dan temperatur tinggi, kemudian mengembang di dalam turbin, menghasilkan daya putar yang diteruskan ke poros (shaft) utama.

### Tahap Tekanan

Turbin uap biasanya terdiri dari beberapa tahap (stage):

- **High Pressure (HP)** — tahap tekanan tinggi
- **Intermediate Pressure (IP)** — tahap tekanan menengah
- **Low Pressure (LP)** — tahap tekanan rendah

Tahapan ini dirancang untuk mengoptimalkan efisiensi energi di setiap tekanan.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Rotor | Elemen berputar |
| Stator | Elemen tetap (nozzle) |
| Casing | Pelindung luar |
| Bearing | Penopang poros |
| Seal | Pencegah kebocoran uap |
| Sistem Kontrol | Pengaturan operasi |

### Aplikasi Industri

Turbin uap digunakan pada:

1. Pembangkit Listrik Tenaga Uap (PLTU)
2. Pabrik pulp dan kertas
3. Industri petrokimia
4. Instalasi cogeneration (combined heat and power)

> Turbin uap merupakan salah satu penggerak utama dalam industri tenaga dan manufaktur modern.
MD,
            ],
            ['description' => 'Turbin Gas', 'category' => null, 'long_description' => <<<'MD'
## Turbin Gas

Turbin Gas adalah mesin panas yang mengubah energi kinetik gas bertekanan tinggi menjadi energi mekanik melalui nozzle dan baling-baling (blade). Berbeda dari turbin uap, turbin gas menggunakan **gas pembakaran** (bukan uap) sebagai fluida kerja.

### Prinsip Kerja

Udara masuk melalui kompresor, dikompresi hingga tekanan tinggi, kemudian dicampur dengan bahan bakar di ruang pembakaran (combustor). Gas panas bertekanan tinggi mengembang melalui turbin, menghasilkan daya putar untuk poros utama.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Kompresor | Memampatkan udara masuk |
| Combustor | Ruang pembakaran (udara + bahan bakar) |
| Turbin | Mengubah energi gas menjadi rotasi |
| Poros | Meneruskan daya ke generator/load |
| Sistem Bahan Bakar | Suplai & kontrol bahan bakar |
| Sistem Kontrol | Pengaturan operasi & proteksi |

### Aplikasi Industri

1. Pembangkit Listrik Tenaga Gas (PLTG)
2. Instalasi cogeneration
3. Kompresor gas pipeline
4. Propulsi pesawat jet (turbojet/turbofan)

> Turbin gas memiliki rasio daya-terhadap-berat yang sangat tinggi, menjadikannya ideal untuk aplikasi yang membutuhkan daya besar dengan bobot ringan.
MD,
            ],
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
            ['description' => 'Boiler Fire Tube', 'category' => 'Fire Tube Boiler', 'long_description' => <<<'MD'
## Boiler Fire Tube

Boiler Fire Tube (Ketel Pipa Api) adalah jenis ketel uap di mana **gas panas mengalir di dalam pipa** yang dikelilingi air. Merupakan salah satu tipe boiler paling umum digunakan pada industri skala kecil hingga menengah.

### Prinsip Kerja

Pembakaran terjadi di dalam ruang bakar (furnace), menghasilkan gas panas yang mengalir melalui pipa-pipa (fire tubes) yang terendam dalam air. Panas dari gas berpindah ke air melalui dinding pipa, menghasilkan uap bertekanan di ruang uap (steam dome).

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Shell | Badan boiler berisi air |
| Fire Tubes | Pipa gas panas |
| Furnace | Ruang pembakaran |
| Steam Dome | Pengumpul uap |
| Safety Valve | Pelepas tekanan berlebih |
| Water Level Gauge | Indikator muka air |
| Blowdown Valve | Pembuangan lumpur |

### Aplikasi

1. Industri makanan & minuman
2. Rumah sakit (sterilisasi)
3. Laundry industri
4. Proses pemanasan pabrik tekstil

> Boiler fire tube cocok untuk tekanan rendah hingga menengah (≤ 20 bar) dan kapasitas uap hingga 20 ton/jam.
MD,
            ],
            ['description' => 'Boiler Water Tube','category' => 'Water Tube Boiler', 'long_description' => <<<'MD'
## Boiler Water Tube

Boiler Water Tube (Ketel Pipa Air) adalah jenis ketel uap di mana **air mengalir di dalam pipa** dan gas panas berada di luar pipa. Digunakan untuk aplikasi tekanan tinggi dan kapasitas besar.

### Prinsip Kerja

Air dipompa ke dalam pipa-pipa kecil yang tersusun dalam panel (water walls, superheater, economizer). Gas panas dari pembakaran memanaskan pipa dari luar, mengubah air menjadi uap. Uap terkumpul di drum uap (steam drum).

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Steam Drum | Pemisah uap dari air |
| Mud Drum | Pengumpul endapan |
| Water Walls | Panel pipa pemanas |
| Superheater | Pemanasan lanjutan uap |
| Economizer | Pemanasan awal air umpan |
| Air Heater | Pemanasan udara pembakaran |
| Soot Blower | Pembersih jelaga |

### Aplikasi

1. Pembangkit Listrik Tenaga Uap (PLTU)
2. Industri petrokimia
3. Pabrik gula
4. Refinery & kilang minyak

> Boiler water tube mampu beroperasi pada tekanan hingga 200+ bar dan kapasitas ratusan ton uap per jam.
MD,
            ],
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
            ['description' => 'Heat Exchanger','category' => 'Heat Exchanger', 'long_description' => <<<'MD'
## Heat Exchanger

Heat Exchanger (Penukar Panas) adalah peralatan yang digunakan untuk **mentransfer energi panas** dari satu fluida ke fluida lainnya tanpa mencampurkannya. Sangat penting dalam proses industri yang membutuhkan pemanasan atau pendinginan.

### Prinsip Kerja

Dua fluida dengan suhu berbeda mengalir secara berseberangan (counter-current) atau sejajar (parallel) melalui jalur terpisah. Panas berpindah dari fluida panas ke fluida dingin melalui **media pengantar** (pipa/lempengan).

### Tipe Utama

| Tipe | Kelebihan | Kekurangan |
|------|-----------|------------|
| **Shell & Tube** | Tahan tekanan tinggi | Mahal, berat |
| **Plate** | Efisiensi tinggi, ringan | Tidak untuk tekanan sangat tinggi |
| **Double Pipe** | Sederhana, murah | Kapasitas kecil |
| **Air Cooled** | Tidak butuh air pendingin | Efisiensi lebih rendah |

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Shell | Badan luar (shell & tube) |
| Tube Bundle | Pipa pengantar panas |
| Tubesheet | Penyambung pipa ke shell |
| Baffle | Pengatur aliran shell side |
| Nozzle | Titik masuk/keluar fluida |

### Aplikasi

1. Pemakaian panas limbah (waste heat recovery)
2. Pendingin oli mesin
3. Kondenser pada sistem HVAC
4. Pemanas/pendingin proses kimia

> Pemilihan tipe heat exchanger tergantung pada suhu, tekanan, jenis fluida, dan kapasitas panas yang dibutuhkan.
MD,
            ],
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
            $subgroup = $this->find_subgroup_with_group($subgroup_name);
            if (!$subgroup) { continue; }
            $sg_id = $subgroup->id;
            $group_id = $subgroup->group_id;

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
                    $update_data = [];
                    if ($cat_id && empty($existing->category_id)) {
                        $update_data['category_id'] = $cat_id;
                    }
                    if ($group_id && empty($existing->group_id)) {
                        $update_data['group_id'] = $group_id;
                    }
                    if (!empty($item['long_description']) && empty($existing->long_description)) {
                        $update_data['long_description'] = $item['long_description'];
                    }
                    if (!empty($update_data)) {
                        $this->db->where('id', $item_id)
                                 ->update(db_prefix() . 'items', $update_data);
                    }
                } else {
                    $insert_data = [
                        'description' => $item['description'],
                        'subgroup_id' => $sg_id,
                        'group_id'    => $group_id,
                        'category_id' => $cat_id,
                        'rate'        => 0,
                        'unit'        => '',
                        'addedfrom'   => 1,
                    ];
                    if (!empty($item['long_description'])) {
                        $insert_data['long_description'] = $item['long_description'];
                    }
                    $this->db->insert(db_prefix() . 'items', $insert_data);
                    $item_id = (int) $this->db->insert_id();
                }

                $item_ids[]                     = $item_id;
                $by_name[$item['description']]  = $item_id;
            }
        }

        return ['item_ids' => $item_ids, 'by_name' => $by_name];
    }
}

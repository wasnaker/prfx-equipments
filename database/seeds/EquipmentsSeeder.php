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
            ['description' => 'Gantry Crane',    'category' => 'Crane', 'long_description' => <<<'MD'
## Gantry Crane

Gantry Crane adalah crane yang berjalan di atas jalur rel atau trek yang dipasang di tanah, dengan struktur utama berbentuk portal yang menopang hoist dan mekanisme pengangkatan. Crane ini memiliki dua kaki penyangga yang menopang gelagar utama (main girder) di atasnya, membentuk seperti huruf "U" atau "A". Gantry Crane sangat efektif untuk area luar ruangan (outdoor) di galangan kapal, pelabuhan, dan area penyimpanan material berat di mana pier (tiang penyangga) tidak memungkinkan untuk dipasang.

### Prinsip Kerja

Gantry Crane beroperasi dengan menggerakkan seluruh struktur portal di atas jalur rel yang terpasang di tanah menggunakan motor penggerak yang terpasang pada kaki crane. Hoist yang tergantung pada gelagar utama bergerak bolak-balik (traversing) untuk menjangkau posisi beban secara lateral, sementara trolley bergerak sepanjang gelagar untuk posisi melintang. Beban diangkat dan diturunkan melalui sistem hoist dengan kontrol yang dioperasikan dari panel kontrol atau remote control.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Main Girder (Gelagar Utama) | Menopang trolley dan hoist, menahan beban yang diangkat |
| Leg (Kaki Crane) | Menyangga gelagar utama dan menghubungkan ke trek rel di tanah |
| End Truck (Travelling Bogie) | Mekanisme penggerak yang memungkinkan crane bergerak di atas rel |
| Trolley | Bergerak menyilang di atas gelagar utama untuk menentukan posisi beban |
| Hoist | Mekanisme pengangkat beban dengan sistem kawat sling atau rantai |
| Rail Track (Jalur Rel) | Jalur pandu yang terpasang di tanah untuk pergerakan gantry crane |
| Control Panel (Panel Kontrol) | Mengatur semua fungsi operasi crane termasuk travel, trolley, dan hoist |

### Aplikasi

1. Pelabuhan dan galangan kapal untuk bongkar muat kontainer dan material berat
2. Area penyimpanan besi baja dan material konstruksi di lapangan terbuka
3. Pabrik metalurgi dan manufaktur untuk memindahkan produk jadi atau bahan baku berukuran besar

> Gantry Crane memerlukan perawatan berkala pada sistem rel, roda end truck, dan kawat sling untuk menjamin keselamatan operasional. Inspeksi harus dilakukan sesuai standar SNI dan peraturan Kementerian Ketenagakerjaan RI.
MD,
            ],
            ['description' => 'Tower Crane',     'category' => 'Crane', 'long_description' => <<<'MD'
## Tower Crane

Tower Crane adalah jenis crane menara yang biasa digunakan dalam proyek konstruksi gedung tinggi, dengan struktur vertikal (mast) yang terdiri dari segmen-segmen yang disusun bertingkat. Crane ini memiliki jib horizontal panjang yang berputar 360 derajat di sekitar sumbu mast, memungkinkan jangkauan area kerja yang sangat luas. Tower Crane merupakan salah satu alat berat konstruksi paling esensial untuk pembangunan gedung bertingkat, jembatan, dan infrastruktur besar lainnya.

### Prinsip Kerja

Tower Crane bekerja dengan memanfaatkan prinsip tuas (lever) di mana beban diangkat oleh hoist dan dipindahkan secara horizontal oleh trolley yang bergerak di sepanjang jib. Seluruh struktur jib berputar 360 derajat di atas mast menggunakan slewing unit (meja putar). Counter jib di sisi berlawanan membawa counterweight untuk menyeimbangkan momen beban agar crane tetap stabil dan tidak mengalami kelebihan beban (overload).

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Mast (Menara) | Struktur vertikal utama yang menopang seluruh komponen crane di ketinggian |
| Jib (Keranjang/Katir) | Lengan horizontal tempat trolley bergerak untuk menjangkau beban |
| Counter Jib | Lengan penyeimbang di sisi berlawanan jib yang membawa counterweight |
| Counterweight | Beban penyeimbang berupa beton atau besi untuk menjaga stabilitas crane |
| Trolley | Mekanisme penggerak yang bergerak di atas jib untuk menentukan jarak beban |
| Hoist (Pengangkat) | Sistem kawat sling dan motor untuk mengangkat dan menurunkan beban |
| Slewing Unit (Meja Putar) | Mekanisme berputar yang memungkinkan jib bergerak 360 derajat |

### Aplikasi

1. Konstruksi gedung bertingkat dan menara untuk pengangkatan beton, besi, dan material bangunan
2. Pembangunan jembatan besar untuk memindahkan material konstruksi di atas ketinggian
3. Proyek infrastruktur seperti pembangkit listrik dan kilang minyak yang membutuhkan pengangkatan di area yang luas

> Tower Crane harus dipasang dan dibongkar oleh tenaga ahli bersertifikat. Setiap perubahan ketinggian (jumping) memerlukan perhitungan struktur dan inspeksi menyeluruh sebelum operasi dilanjutkan.
MD,
            ],
            ['description' => 'Mobile Crane',    'category' => 'Crane', 'long_description' => <<<'MD'
## Mobile Crane

Mobile Crane adalah crane yang terpasang pada kendaraan beroda (truk atau chassis khusus) sehingga dapat berpindah lokasi secara mandiri tanpa perlu perakitan ulang. Crane ini dilengkapi dengan boom (lengan teleskopik atau lattice) yang dapat dipanjang-pendekkan dan ditegakkan sesuai kebutuhan operasi. Mobile Crane sangat fleksibel untuk digunakan di berbagai lokasi proyek karena kemampuan mobilitasnya yang tinggi dan waktu setup yang relatif singkat.

### Prinsip Kerja

Mobile Crane mengangkat beban menggunakan sistem kawat sling yang melalui sheave (katrol) di ujung boom, ditarik oleh drum winch. Boom dapat dipanjangkan secara teleskopik atau ditegakkan/diturunkan menggunakan silinder hidrolik atau motor listrik. Untuk menjaga stabilitas, crane dilengkapi outrigger (kaki penyangga) yang diperluas ke sisi kendaraan guna memperlebar area tumpuan dan mencegah crane terguling saat mengangkat beban berat.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Boom (Lengan Crane) | Struktur teleskopik atau lattice yang menjadi jalur kawat sling pengangkat beban |
| Winch (Dere) | Motor dan drum penggulung kawat sling untuk mengangkat dan menurunkan beban |
| Outrigger (Kaki Penyangga) | Kaki hidrolik yang diperluas untuk menstabilkan crane saat operasi pengangkatan |
| Superstructure (Badan Atas) | Bagian crane yang berputar di atas chassis, menampung boom dan operator |
| Counterweight | Beban penyeimbang yang terpasang di bagian belakang superstructure |
| Chassis/Kendaraan | Basis kendaraan beroda yang memungkinkan crane berpindah lokasi |
| Hydraulic System | Sistem penggerak hidrolik untuk operasi boom, winch, dan outrigger |

### Aplikasi

1. Proyek konstruksi bangunan dan infrastruktur yang membutuhkan fleksibilitas perpindahan lokasi
2. Pekerjaan instalasi dan maintenance di pabrik, kilang minyak, dan pembangkit listrik
3. Operasi darurat (emergency rescue) untuk pengangkatan material di lokasi bencana alam

> Mobile Crane memiliki berbagai varian kapasitas dari 20 ton hingga lebih dari 500 ton. Pemilihan tipe crane harus disesuaikan dengan jenis beban, radius jangkau, dan kondisi lapangan untuk menjamin keselamatan operasi.
MD,
            ],
            ['description' => 'Jib Crane',       'category' => 'Crane', 'long_description' => <<<'MD'
## Jib Crane

Jib Crane adalah crane dengan lengan horizontal (jib atau boom) yang terpasang pada tiang vertikal (mast/column) atau dinding, dan dapat berputar untuk memindahkan beban dalam area kerja tertentu. Crane ini memiliki desain sederhana namun sangat efektif untuk pengangkatan beban di area kerja lokal yang terbatas. Jib Crane biasanya digunakan sebagai pelengkap crane utama di area pemuatan, perakitan, atau workstation pabrik.

### Prinsip Kerja

Jib Crane beroperasi dengan memutar lengan jib di sekitar titik poros yang terpasang pada kolom atau bracket dinding, memungkinkan beban dipindahkan dalam bentuk busur lingkaran. Hoist digantung pada jib dan dapat digerakkan (traversing) sepanjang lengan untuk menyesuaikan posisi beban secara radial. Penggerakan bisa dilakukan secara manual atau motoris tergantung pada kapasitas beban dan frekuensi penggunaan.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Mast/Kolom | Tiang vertikal penyangga utama yang menopang seluruh struktur jib |
| Jib (Boom) | Lengan horizontal yang berputar di sekitar mast, menempuh area operasi |
| Trolley/Travelling Beam | Mekanisme bergerak di atas jib yang membawa hoist bolak-balik |
| Hoist | Unit pengangkat beban berupa chain hoist atau wire rope hoist |
| Pivot/Slewing Bearing | Titik poros putar yang menghubungkan jib ke mast atau bracket dinding |
| Foundation/Basi Beton | Pondasi yang menahan beban dan momen puntir dari kolom jib crane |
| Control Pendant | Panel kontrol gantung untuk mengoperasikan hoist dan slewing |

### Aplikasi

1. Workstation pabrik dan bengkel manufaktur untuk pengangkatan komponen mesin dan produk jadi
2. Area pemuatan dan bongkar muat truk di gudang dan distribusi logistik
3. Pabrik logam dan manufaktur baja untuk memindahkan material pada proses perakitan

> Jib Crane harus dipasang pada struktur penyangga yang memadai (kolom atau dinding) dengan perhitungan beban yang cermat. Radius putar dan kapasitas beban harus diperhatikan untuk mencegah kelebihan beban dan kerusakan struktur.
MD,
            ],
            ['description' => 'Electric Hoist',  'category' => 'Hoist', 'long_description' => <<<'MD'
## Electric Hoist

Electric Hoist adalah unit pengangkat beban yang digerakkan oleh motor listrik, digunakan untuk mengangkat, menurunkan, dan memindahkan beban secara vertikal maupun horizontal. Hoist ini bekerja menggunakan sistem kawat sling yang digulung pada drum oleh motor listrik, atau menggunakan rantai yang ditarik oleh sprocket bermotor. Electric Hoist banyak digunakan di pabrik, bengkel, gudang, dan area kerja yang membutuhkan pengangkatan beban secara rutin dan efisien.

### Prinsip Kerja

Electric Hoist mengubah energi listrik menjadi energi mekanik melalui motor listrik yang terhubung ke gearbox reduksi. Gearbox mengurangi kecepatan putaran motor sementara meningkatkan torsi yang diperlukan untuk mengangkat beban berat. Kawat sling atau rantai digulung pada drum atau sprocket, mengangkat atau menurunkan beban sesuai arah putaran motor yang dikontrol melalui panel kontrol atau pendant control.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Motor Listrik | Penggerak utama yang menghasilkan tenaga untuk mengangkat beban |
| Gearbox (Transmisi) | Mereduksi kecepatan motor dan meningkatkan torsi pengangkat |
| Drum/Sprocket | Komponen penggulung kawat sling atau rantai pengangkat beban |
| Kawat Sling/Rantai | Media pengikat beban yang kuat dan fleksibel |
| Brake (Rem) | Sistem pengereman otomatis untuk menahan beban saat motor berhenti |
| Limit Switch | Pengaman yang membatasi pergerakan hoist agar tidak melewati batas atas/bawah |
| Pendant Control | Panel kontrol gantung untuk mengoperasikan hoist dari bawah |

### Aplikasi

1. Pabrik manufaktur dan perakitan untuk pengangkatan komponen berat pada lini produksi
2. Bengkel las dan fabrikasi logam untuk memindahkan material baja dan besi
3. Gudang dan pusat distribusi logistik untuk operasi pemuatan dan penyimpanan vertikal

> Electric Hoist harus dipasang pada struktur penyangga yang memadai seperti overhead crane, jib crane, atau beam crane. Perawatan berkala meliputi inspeksi kawat sling, pemeriksaan rem, dan penggantian oli gearbox sesuai jadwal pemeliharaan.
MD,
            ],
            ['description' => 'Manual Hoist',    'category' => 'Hoist', 'long_description' => <<<'MD'
## Manual Hoist

Manual Hoist adalah unit pengangkat beban yang dioperasikan sepenuhnya oleh tenaga manusia tanpa memerlukan sumber energi eksternal seperti listrik atau hidrolik. Hoist ini menggunakan sistem roda gigi (gear) dan tuas (lever) yang diketik atau ditarik rantai oleh operator untuk mengangkat beban. Manual Hoist cocok untuk lokasi kerja yang tidak memiliki akses listrik atau untuk pengangkatan beban ringan hingga sedang dengan frekuensi operasi yang tidak terlalu tinggi.

### Prinsip Kerja

Manual Hoist bekerja berdasarkan prinsip mekanik di mana tenaga tangan operator ditransmisikan melalui sistem roda gigi reduksi untuk menghasilkan gaya angkat yang lebih besar. Operator menarik rantai hand chain yang memutar sprocket, yang terhubung ke gearbox untuk menggulung load chain (rantai beban). Sistem ratchet dan brake otomatis menahan beban agar tidak terjatuh saat operator berhenti menarik rantai.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Hand Chain | Rantai yang ditarik oleh operator untuk menggerakkan mekanisme hoist |
| Load Chain | Rantai pengangkat beban yang kuat dari baja berkekerasan |
| Sprocket | Roda gigi yang menghubungkan hand chain ke gearbox |
| Gearbox Mekanik | Sistem roda gigi reduksi untuk mengubah tenaga tangan menjadi gaya angkat |
| Brake Mekanik (Ratchet) | Sistem pengereman otomatis yang menahan beban saat operasi berhenti |
| Hook (Kait) | Pengait baja berputar untuk menggantung beban yang akan diangkat |
| Housing (Casing) | Rumah proteksi yang menampung seluruh mekanisme internal hoist |

### Aplikasi

1. Lokasi konstruksi atau area kerja tanpa akses listrik yang membutuhkan pengangkatan beban
2. Maintenance dan instalasi mesin di pabrik dengan beban ringan hingga sedang
3. Operasi di area berbahaya (hazardous area) di mana penggunaan peralatan listrik terbatas

> Manual Hoist memerlukan tenaga operator yang cukup dan memiliki batasan kapasitas serta kecepatan pengangkatan dibandingkan hoist listrik. Pemeriksaan berkala pada load chain, brake, dan hook sangat penting untuk keselamatan.
MD,
            ],
            ['description' => 'Chain Hoist',     'category' => 'Hoist', 'long_description' => <<<'MD'
## Chain Hoist

Chain Hoist adalah unit pengangkat beban yang menggunakan rantai baja sebagai media pengangkat, tersedia dalam varian manual, listrik, dan pneumatik. Rantai baja yang digunakan pada hoist ini memiliki kekuatan tarik tinggi dan ketahanan terhadap abrasi, sehingga sangat cocok untuk lingkungan industri berat. Chain Hoist umumnya lebih ringan dan kompak dibandingkan wire rope hoist dengan kapasitas yang setara, menjadikannya pilihan populer untuk berbagai aplikasi industri.

### Prinsip Kerja

Chain Hoist bekerja dengan menggerakkan rantai baja melalui sistem sprocket dan gearbox reduksi untuk mengangkat beban. Pada tipe listrik, motor listrik menggerakkan gearbox yang memutar sprocket untuk menggulung load chain. Pada tipe manual, operator menarik hand chain yang memutar sprocket secara mekanik. Sistem brake otomatis terintegrasi untuk menahan beban pada posisi tertentu saat pengangkatan dihentikan.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Load Chain | Rantai baja berkekerasan tinggi yang menahan dan mengangkat beban |
| Hand Chain | Rantai penggerak untuk tipe manual, ditarik oleh operator |
| Sprocket | Roda gigi bergerigi yang mencengkeram dan menggerakkan rantai |
| Gearbox | Sistem reduksi roda gigi untuk meningkatkan gaya angkat |
| Motor Listrik | Penggerak pada tipe elektrik yang menghasilkan tenaga untuk mengangkat |
| Hook Assembly | Kait baja berputar dengan safety latch untuk pengaman beban |
| Brake System | Sistem pengereman otomatis yang menahan beban saat operasi berhenti |

### Aplikasi

1. Workshop dan bengkel las untuk pengangkatan komponen baja dan produk fabrikasi
2. Assembly line di pabrik otomotif dan elektronik untuk perakitan komponen berat
3. Gudang industri untuk operasi pemuatan dan penyimpanan material pada rak tinggi

> Chain Hoist harus menggunakan rantai baja dengan spesifikasi yang tepat sesuai kapasitas beban nominal. Penggunaan rantai yang aus atau rusak sangat berbahaya dan harus segera diganti sesuai standar inspeksi yang berlaku.
MD,
            ],
            ['description' => 'Gondola',         'category' => 'Gondola', 'long_description' => <<<'MD'
## Gondola

Gondola (juga dikenal sebagai Building Maintenance Unit/BMU atau gondola akses kerja) adalah platform tergantung yang digunakan untuk mengakses fasad bangunan tinggi guna keperluan pembersihan kaca, perawatan fasad, dan inspeksi struktur bangunan. Gondola terdiri dari platform kerja yang digantung oleh kawat sling baja dari rigging di atas atap gedung, dengan sistem winch untuk menaikkan dan menurunkan platform. Peralatan ini merupakan solusi akses kerja ketinggian yang efisien dan relatif aman untuk bangunan bertingkat.

### Prinsip Kerja

Gondola bekerja dengan menggantung platform kerja pada kawat sling baja yang dihubungkan ke rigging (penyangga) di atas atap gedung. Operator mengontrol pergerakan platform naik-turun dan bolak-balik menggunakan motor winch yang terpasang pada rigging, serta traverse motor untuk pergerakan horizontal di sepanjang fasad. Sistem pengaman termasuk overspeed device, upper dan lower limit switch, serta emergency brake yang berfungsi otomatis jika terjadi kegagalan sistem.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Platform Kerja | Area kerja tempat operator berdiri saat melakukan pekerjaan di ketinggian |
| Wire Rope (Kawat Sling) | Kawat baja yang menahan berat platform dan operator dari rigging atas |
| Rigging/Davit Arm | Struktur penyangga di atas atap yang menopang seluruh sistem gondola |
| Winch Motor | Motor penggulung kawat sling untuk menaikkan dan menurunkan platform |
| Traverse Motor | Motor penggerak horizontal untuk memindahkan platform di sepanjang fasad |
| Safety Device | Sistem pengaman termasuk overspeed brake, limit switch, dan emergency stop |
| Control Panel | Panel kontrol yang terpasang pada platform untuk mengoperasikan pergerakan |

### Aplikasi

1. Pembersihan dan perawatan kaca gedung (curtain wall) pada gedung bertingkat
2. Inspeksi dan maintenance fasad bangunan termasuk perbaikan sealant dan cat
3. Pemasangan dan perbaikan panel reklame, spanduk, atau elemen arsitektural di ketinggian

> Gondola merupakan peralatan akses kerja ketinggian yang diatur oleh peraturan Kementerian Ketenagakerjaan RI. Operator harus memiliki sertifikasi kompetensi dan gondola harus menjalani inspeksi berkala oleh lembaga sertifikasi yang terakreditasi.
MD,
            ],
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
            ['description' => 'Reach Stacker',         'category' => 'Fork', 'long_description' => <<<'MD'
## Reach Stacker

Reach Stacker adalah alat angkut dan penumpukan kontainer yang dirancang khusus untuk menangani kontainer di terminal peti kemas, pelabuhan, dan depo. Alat ini mampu mengangkat kontainer hingga beberapa tumpukan dengan stabilitas tinggi.

### Prinsip Kerja

Reach Stacker menggunakan mekanisme teleskopik pada lengan angkatnya (boom) yang dapat menjangkau kontainer dari baris kedua atau ketiga. Dengan sistem hidrolik yang kuat, alat ini dapat mengangkat dan memindahkan kontainer ke lokasi yang diinginkan dengan presisi tinggi.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Boom Teleskopik | Menjangkau dan mengangkat kontainer dari berbagai posisi |
| Sistem Hidrolik | Menggerakkan boom dan mekanisme angkat |
| Spreader | Alat penjepit kontainer yang dapat disesuaikan ukurannya |
| Kabin Operator | Tempat operator mengendalikan seluruh operasi |
| Mesin Diesel | Sumber tenaga utama penggerak alat |
| Ban Pneumatik | Memberikan mobilitas di berbagai permukaan |
| Sistem Stabilizer | Menjaga kestabilan saat mengangkat beban berat |

### Aplikasi

1. Penumpukan kontainer di terminal peti kemas
2. Bongkar muat kontainer dari truk atau trailer
3. Penataan kontainer di depo penyimpanan

> Reach Stacker merupakan solusi efisien untuk penanganan kontainer dengan produktivitas tinggi dan fleksibilitas operasional yang optimal.
MD,
            ],
            ['description' => 'Electric Stacker',      'category' => 'Fork', 'long_description' => <<<'MD'
## Electric Stacker

Electric Stacker adalah alat angkut bertenaga listrik yang digunakan untuk mengangkat dan memindahkan palet atau beban di gudang dan area industri. Alat ini dirancang ergonomis dengan emisi nol dan kebisingan rendah.

### Prinsip Kerja

Electric Stacker menggunakan motor listrik untuk menggerakkan roda dan sistem hidrolik untuk mengangkat garpu. Operator mengendalikan alat melalui stang kendali yang dilengkapi tombol untuk maju, mundur, dan mengangkat beban.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Motor Listrik | Sumber tenaga penggerak roda dan hidrolik |
| Baterai | Menyimpan energi listrik untuk operasional |
| Sistem Hidrolik | Menggerakkan silinder angkat pada garpu |
| Garpu (Fork) | Menyangga dan mengangkat palet |
| Stang Kendali | Mengontrol pergerakan dan fungsi angkat |
| Roda Beban | Roda depan yang menopang beban |
| Panel Kontrol | Mengatur kecepatan dan arah gerakan |

### Aplikasi

1. Pemindahan barang di gudang penyimpanan
2. Bongkar muat barang dari truk pengangkut
3. Penataan palet di rak penyimpanan

> Electric Stacker menawarkan solusi ramah lingkungan dan efisien untuk operasional gudang dengan biaya perawatan yang rendah.
MD,
            ],
            ['description' => 'Hand Stacker',          'category' => 'Fork', 'long_description' => <<<'MD'
## Hand Stacker

Hand Stacker adalah alat angkut manual yang digunakan untuk mengangkat dan memindahkan beban ringan hingga sedang di area gudang atau toko. Alat ini dioperasikan secara manual dengan tenaga manusia.

### Prinsip Kerja

Hand Stacker menggunakan sistem hidrolik manual yang dioperasikan dengan pompa tangan untuk mengangkat garpu. Gerakan maju mundur dilakukan dengan mendorong alat secara manual oleh operator.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Garpu (Fork) | Menyangga palet atau beban |
| Pompa Hidrolik Manual | Menghasilkan tekanan untuk mengangkat garpu |
| Stang Dorong | Digunakan untuk mendorong dan mengarahkan alat |
| Roda Depan | Mendukung beban dan memudahkan manuver |
| Roda Belakang | Roda putar untuk kemudahan belok |
| Tuas Angkat | Mengontrol naik turunnya garpu |
| Rangka Baja | Struktur utama penopang beban |

### Aplikasi

1. Pemindahan barang di gudang skala kecil
2. Bongkar muat barang di toko atau swalayan
3. Penataan palet di area produksi

> Hand Stacker adalah solusi ekonomis untuk kebutuhan angkat ringan tanpa memerlukan sumber daya listrik.
MD,
            ],
            ['description' => 'Reach Truck',           'category' => 'Gudang Tinggi', 'long_description' => <<<'MD'
## Reach Truck

Reach Truck adalah alat angkut gudang yang dirancang khusus untuk menjangkau rak penyimpanan tinggi dengan lorong sempit. Alat ini memiliki mekanisme reach yang memungkinkan garpu bergerak maju mundur secara independen.

### Prinsip Kerja

Reach Truck menggunakan mekanisme reach (pantograf) yang memungkinkan garpu menjangkau ke dalam rak tanpa menggerakkan seluruh badan alat. Sistem hidrolik dan motor listrik mengontrol pergerakan garpu dan ketinggian angkat.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Mekanisme Pantograf | Memperpanjang jangkauan garpu ke dalam rak |
| Garpu (Fork) | Menyangga palet |
| Tiang Mast | Struktur vertikal untuk mengangkat garpu |
| Motor Listrik | Menggerakkan roda dan sistem hidrolik |
| Baterai | Sumber tenaga listrik |
| Kabin Operator | Tempat duduk dan kontrol operator |
| Roda Beban | Roda khusus untuk stabilitas tinggi |

### Aplikasi

1. Penyimpanan dan pengambilan barang di rak tinggi
2. Operasi gudang dengan lorong sempit (narrow aisle)
3. Sistem penyimpanan palet bervolume tinggi

> Reach Truck meningkatkan kapasitas penyimpanan gudang dengan memanfaatkan ruang vertikal secara optimal.
MD,
            ],
            ['description' => 'Order Picker',          'category' => 'Gudang Tinggi', 'long_description' => <<<'MD'
## Order Picker

Order Picker adalah alat angkut yang dirancang untuk mengambil barang secara individual dari rak penyimpanan tinggi. Operator naik bersama platform untuk menjangkau barang di ketinggian.

### Prinsip Kerja

Order Picker mengangkat operator beserta platform ke ketinggian rak yang dituju. Setelah mencapai posisi yang tepat, operator dapat mengambil barang secara manual dan meletakkannya di platform.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Platform Kerja | Tempat operator berdiri dan menampung barang |
| Tiang Mast | Menopang dan mengangkat platform secara vertikal |
| Motor Listrik | Menggerakkan roda dan sistem angkat |
| Baterai | Sumber tenaga operasional |
| Kontrol Operasi | Panel kendali pergerakan dan ketinggian |
| Roda | Memberikan mobilitas di lorong gudang |
| Sistem Pengaman | Sabuk pengaman dan sensor keselamatan |

### Aplikasi

1. Pengambilan barang eceran (piece picking) dari rak tinggi
2. Persiapan pesanan (order fulfillment) di gudang
3. Inventarisasi dan pengecekan stok di ketinggian

> Order Picker meningkatkan efisiensi picking dan mengurangi risiko cedera operator saat bekerja di ketinggian.
MD,
            ],
            ['description' => 'VNA Truck',             'category' => 'Gudang Tinggi', 'long_description' => <<<'MD'
## VNA Truck (Very Narrow Aisle Truck)

VNA Truck adalah alat angkut khusus yang dirancang untuk beroperasi di lorong sangat sempit (Very Narrow Aisle) dengan memaksimalkan pemanfaatan ruang penyimpanan vertikal dan horizontal.

### Prinsip Kerja

VNA Truck menggunakan sistem navigasi khusus, seperti rel atau panduan optik, untuk bergerak lurus di lorong sempit. Mekanisme rotasi garpu 180 derajat memungkinkan pengambilan palet dari kedua sisi tanpa memutar badan alat.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Garpu Berputar | Memutar palet 180° untuk penempatan di rak |
| Tiang Mast Tinggi | Menjangkau rak hingga ketinggian maksimal |
| Sistem Panduan | Memandu alat tetap pada jalur di lorong sempit |
| Motor Listrik | Menggerakkan semua fungsi operasional |
| Baterai Tegangan Tinggi | Menyediakan tenaga untuk operasi berat |
| Kabin Operator | Dilengkapi kontrol canggih dan visibilitas |
| Sensor Keselamatan | Mendeteksi halangan dan menjaga jarak aman |

### Aplikasi

1. Gudang dengan lorong sangat sempit (VNA)
2. Sistem penyimpanan palet densitas tinggi
3. Distribusi barang dengan rotasi cepat

> VNA Truck memaksimalkan kapasitas penyimpanan gudang hingga 50% lebih banyak dibandingkan gudang konvensional.
MD,
            ],
            ['description' => 'Scissor Lift',          'category' => 'Scissor/AWP', 'long_description' => <<<'MD'
## Scissor Lift

Scissor Lift adalah platform kerja yang dapat dinaikkan secara vertikal menggunakan mekanisme gunting (scissor) untuk mendukung pekerjaan di ketinggian. Alat ini memberikan platform yang stabil dan luas.

### Prinsip Kerja

Scissor Lift menggunakan sistem hidrolik untuk mengembangkan mekanisme gunting yang mengangkat platform secara vertikal. Platform tetap horizontal selama pengangkatan berlangsung.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Platform Kerja | Tempat pekerja berdiri dan menaruh peralatan |
| Mekanisme Gunting (Scissor) | Struktur lipat yang mengangkat platform |
| Silinder Hidrolik | Menggerakkan mekanisme gunting |
| Motor Penggerak | Sumber tenaga untuk sistem hidrolik |
| Panel Kontrol | Mengontrol naik turun platform |
| Roda | Untuk mobilitas alat |
| Stabilisator | Menjaga kestabilan saat platform diangkat |

### Aplikasi

1. Instalasi dan perawatan listrik di ketinggian
2. Pekerjaan konstruksi bangunan
3. Pengecatan dan perbaikan langit-langit

> Scissor Lift memberikan akses aman dan stabil ke area kerja di ketinggian dengan kapasitas angkat yang besar.
MD,
            ],
            ['description' => 'Aerial Work Platform',  'category' => 'Scissor/AWP', 'long_description' => <<<'MD'
## Aerial Work Platform (AWP)

Aerial Work Platform atau Platform Kerja Udara adalah alat yang digunakan untuk mengangkat pekerja ke ketinggian tertentu guna melakukan berbagai tugas. AWP memiliki desain yang fleksibel dengan lengan artikulasi atau teleskopik.

### Prinsip Kerja

Aerial Work Platform menggunakan lengan artikulasi atau teleskopik yang digerakkan secara hidrolik untuk menjangkau posisi kerja yang sulit dijangkau. Platform dapat dimiringkan dan diputar untuk memberikan akses optimal.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Platform Kerja | Tempat pekerja berdiri, dilengkapi pengaman |
| Lengan Artikulasi/Teleskopik | Menjangkau area kerja vertikal dan horizontal |
| Silinder Hidrolik | Menggerakkan lengan dan platform |
| Sistem Rotasi | Memutar platform 360 derajat |
| Panel Kontrol | Mengendalikan semua gerakan alat |
| Stabilisator Kaki | Menjaga kestabilan alat saat operasi |
| Mesin atau Motor | Sumber tenaga penggerak |

### Aplikasi

1. Pembersihan dan perawatan gedung bertingkat
2. Instalasi penerangan dan papan reklame
3. Pekerjaan penyelamatan dan pemeliharaan infrastruktur

> Aerial Work Platform memberikan fleksibilitas tinggi dalam menjangkau area kerja yang rumit dengan aman.
MD,
            ],
            ['description' => 'Conveyor Belt',         'category' => null, 'long_description' => <<<'MD'
## Conveyor Belt

Conveyor Belt adalah sistem transportasi kontinu yang digunakan untuk memindahkan barang atau material dari satu titik ke titik lainnya secara otomatis. Alat ini sangat umum di industri manufaktur, pertambangan, dan logistik.

### Prinsip Kerja

Conveyor Belt bekerja dengan menggunakan sabuk (belt) yang berputar terus menerus melalui drum penggerak (drive pulley). Barang ditempatkan di atas sabuk dan bergerak mengikuti arah putaran sabuk.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Sabuk (Belt) | Media pembawa material yang bergerak |
| Drum Penggerak | Memutar sabuk dengan motor |
| Drum Pembalik | Mengarahkan sabuk kembali ke jalur |
| Motor Listrik | Sumber tenaga penggerak drum |
| Roller Penyangga | Menyangga sabuk dan muatan |
| Rangka Baja | Struktur penopang seluruh sistem |
| Sistem Pengencang | Menjaga ketegangan sabuk tetap optimal |

### Aplikasi

1. Pemindahan material curah di pertambangan
2. Distribusi barang di jalur produksi industri
3. Proses sortasi dan pengemasan di gudang logistik

> Conveyor Belt meningkatkan efisiensi transportasi material secara kontinu dengan biaya operasional yang rendah.
MD,
            ],
            ['description' => 'Lift Barang',           'category' => null, 'long_description' => <<<'MD'
## Lift Barang

Lift Barang adalah alat angkat vertikal yang digunakan untuk memindahkan barang antar lantai dalam gedung atau pabrik. Lift ini dirancang khusus untuk membawa beban berat dengan aman.

### Prinsip Kerja

Lift Barang bekerja dengan sistem tali baja dan motor penggerak yang mengangkat kabin barang secara vertikal di sepanjang rel panduan. Sistem rem dan sensor keamanan memastikan pergerakan yang aman.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Kabin Barang | Ruang yang membawa muatan |
| Tali Baja | Mengangkat dan menurunkan kabin |
| Motor Penggerak | Sumber tenaga untuk menggerakkan sistem |
| Rel Panduan | Memandu kabin tetap pada lintasan vertikal |
| Sistem Rem | Menghentikan kabin pada posisi yang diinginkan |
| Panel Kontrol | Mengatur operasi naik turun |
| Sensor Beban | Mendeteksi kelebihan beban dan mengaktifkan alarm |

### Aplikasi

1. Pemindahan barang antar lantai di gudang atau pabrik
2. Loading dan unloading barang di area bongkar muat
3. Distribusi material di gedung bertingkat

> Lift Barang meningkatkan efisiensi logistik vertikal dengan keamanan dan kapasitas angkat yang besar.
MD,
            ],
        ],

        // ── Subgroup: Penggerak Mula ──────────────────────────────────────────
        'Penggerak Mula' => [
            ['description' => 'Motor Diesel',    'category' => null, 'long_description' => <<<'MD'
## Motor Diesel

Motor diesel adalah jenis motor bakar torak yang menggunakan prinsip pembakaran kompresi (compression ignition) untuk mengubah energi kimia bahan bakar menjadi energi mekanik dalam bentuk putaran poros. Mesin ini dikenal memiliki efisiensi termal tinggi serta keandalan yang baik pada operasi kontinu beban berat.

### Prinsip Kerja

Udara dikompresi di dalam silinder hingga mencapai temperatur tinggi, kemudian bahan bakar diesel diinjeksikan ke dalam ruang bakar dan terbakar secara spontan akibat temperatur tinggi tersebut. Energi hasil pembakaran mendorong torak yang dihubungkan ke poros engkol sehingga menghasilkan putaran.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Blok Silinder | Tempat berlangsungnya proses pembakaran dan rumah bagi komponen utama mesin |
| Torak (Piston) | Menerima tekanan gas pembakaran dan meneruskannya ke batang penggerak |
| Batang Penggerak (Connecting Rod) | Meneruskan gerak translasi torak ke poros engkol |
| Poros Engkol (Crankshaft) | Mengubah gerak translasi torak menjadi gerak rotasi |
| Kepala Silinder (Cylinder Head) | Menutup silinder serta menjadi dudukan katup dan injektor |
| Injektor Bahan Bakar | Menyemprotkan bahan bakar diesel ke dalam ruang bakar pada tekanan tinggi |
| Sistem Pelumasan | Melumasi komponen bergerak untuk mengurangi gesekan dan keausan |

### Aplikasi

1. Pembangkit listrik skala kecil dan menengah (generator set) untuk cadangan maupun utama
2. Penggerak kapal nelayan dan kapal barang sebagai mesin induk
3. Penggerak alat berat seperti bulldozer, excavator, dan truk tambang

> Motor diesel dikenal memiliki efisiensi termal tinggi dan torsi besar pada putaran rendah sehingga sangat cocok untuk aplikasi berat dan operasi kontinu.
MD,
            ],
            ['description' => 'Motor Bakar',     'category' => null, 'long_description' => <<<'MD'
## Motor Bakar

Motor bakar (motor bensin) adalah jenis motor bakar torak yang menggunakan busi untuk menyalakan campuran udara dan bahan bakar di dalam silinder (spark ignition). Mesin ini mengubah energi kimia bahan bakar menjadi energi mekanik melalui proses pembakaran terkendali dan banyak digunakan pada aplikasi yang membutuhkan putaran tinggi dan respons cepat.

### Prinsip Kerja

Campuran udara dan bahan bakar dikompresi oleh torak di dalam silinder, kemudian dinyalakan oleh percikan busi sehingga terjadi pembakaran. Tekanan gas hasil pembakaran mendorong torak turun, dan gerak translasi ini diubah menjadi putaran poros engkol melalui batang penggerak.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Blok Silinder | Tempat proses pembakaran dan rumah bagi komponen utama mesin |
| Torak (Piston) | Menerima tekanan gas pembakaran dan meneruskannya ke batang penggerak |
| Batang Penggerak (Connecting Rod) | Meneruskan gerak torak ke poros engkol |
| Poros Engkol (Crankshaft) | Mengubah gerak translasi torak menjadi gerak rotasi |
| Busi (Spark Plug) | Menyalakan campuran udara dan bahan bakar pada akhir langkah kompresi |
| Karburator/Sistem Injeksi | Mencampur udara dan bahan bakar dalam rasio yang tepat |
| Sistem Pendinginan | Menjaga temperatur mesin tetap optimal selama operasi |

### Aplikasi

1. Penggerak kendaraan ringan seperti mobil, sepeda motor, dan kendaraan niaga kecil
2. Penggerak pompa, generator darurat, dan peralatan portable lainnya
3. Penggerak peralatan pertanian dan perkebunan seperti traktor dan mesin pemotong

> Motor bakar bensin menawarkan putaran tinggi, responsif, dan bobot ringan sehingga ideal untuk aplikasi transportasi dan peralatan yang memerlukan mobilitas.
MD,
            ],
            ['description' => 'Kompresor Udara', 'category' => null, 'long_description' => <<<'MD'
## Kompresor Udara

Kompresor udara adalah peralatan mekanik yang mengubah daya dari motor listrik atau motor bakar menjadi energi potensial yang tersimpan dalam bentuk udara bertekanan. Udara bertekanan ini kemudian digunakan sebagai sumber tenaga untuk berbagai peralatan pneumatik di industri, konstruksi, dan perbengkelan.

### Prinsip Kerja

Udara atmosfer dihisap masuk ke dalam ruang kompresi melalui katup isap, kemudian volume ruang diperkecil oleh gerakan torak atau elemen putar sehingga tekanan udara meningkat. Udara bertekanan selanjutnya dialirkan ke tangki penampung untuk distribusi ke peralatan pengguna.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Motor Penggerak | Menyediakan tenaga mekanik untuk menggerakkan elemen kompresi |
| Elemen Kompresi (Screw/Resiprokal) | Bagian yang melakukan proses kompresi udara secara mekanik |
| Tangki Udara (Air Receiver) | Menampung udara bertekanan dan menstabilkan fluktuasi tekanan |
| Katup Pengaman (Safety Valve) | Melepaskan tekanan berlebih secara otomatis untuk keselamatan |
| Pengatur Tekanan (Pressure Switch) | Mengontrol hidup/mati motor berdasarkan tekanan operasi |
| Pengering Udara (Air Dryer) | Menghilangkan kadar air dari udara bertekanan untuk melindungi peralatan |
| Filter Udara | Menyaring partikel debu dan kotoran dari udara yang masuk |

### Aplikasi

1. Suplai udara tekan untuk alat-alat pneumatik (bor, impact wrench, spray gun, nailer)
2. Sistem kontrol pneumatik di pabrik dan industri manufaktur
3. Proses pengemasan, pembersihan, pengecatan, dan instrumentasi di berbagai sektor

> Kompresor udara merupakan sumber daya utama untuk sistem pneumatik dan menjadi bagian vital dalam operasi industri modern, bengkel, serta fasilitas konstruksi.
MD,
            ],
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
            ['description' => 'Pompa Industri',  'category' => null, 'long_description' => <<<'MD'
## Pompa Industri

Pompa industri adalah peralatan mekanik yang digunakan untuk memindahkan fluida cair dari satu tempat ke tempat lain dengan cara memberikan energi pada fluida tersebut. Pompa berperan penting dalam sistem sirkulasi, distribusi, dan transfer fluida di berbagai sektor industri dan utilitas.

### Prinsip Kerja

Pompa mengubah energi mekanik dari motor penggerak menjadi energi kinetik dan tekanan pada fluida. Fluida dihisap masuk ke dalam rumah pompa melalui saluran isap, kemudian didorong keluar menuju sistem perpipaan oleh impeler atau mekanisme pemindah positif lainnya.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Impeler (Impeller) | Komponen berputar yang memberikan energi kinetik pada fluida |
| Rumah Pompa (Casing) | Melindungi komponen internal dan mengarahkan aliran fluida |
| Poros Pompa (Shaft) | Meneruskan putaran dari motor ke impeler |
| Bantalan (Bearing) | Menopang poros dan mengurangi gesekan saat berputar |
| Mechanical Seal | Mencegah kebocoran fluida di sepanjang poros yang menembus rumah pompa |
| Motor Penggerak | Sumber tenaga listrik atau mekanik untuk memutar poros pompa |
| Sistem Perpipaan Sambungan | Menghubungkan pompa ke sumber fluida dan jaringan distribusi |

### Aplikasi

1. Sistem penyediaan air bersih dan distribusi air pada jaringan PDAM
2. Sirkulasi air pendingin (cooling water) di industri, pembangkit listrik, dan HVAC
3. Transfer bahan kimia, minyak, dan fluida proses pada pabrik dan kilang

> Pompa industri tersedia dalam berbagai jenis seperti sentrifugal, gear, diaphragm, dan submersible; pemilihannya disesuaikan dengan karakteristik fluida, kapasitas aliran, dan tekanan operasi yang dibutuhkan.
MD,
            ],
        ],

        // ── Subgroup: Mesin Perkakas ──────────────────────────────────────────
        'Mesin Perkakas' => [
            ['description' => 'Mesin Bubut',  'category' => null, 'long_description' => <<<'MD'
## Mesin Bubut

Mesin bubut adalah alat mekanik yang digunakan untuk memproses benda kerja berbentuk silinder atau melengkung dengan cara memutar benda kerja dan menggerakkan pahat pemotong secara linear.

### Prinsip Kerja

Benda kerja dipasang pada chuck dan berputar pada poros utama, sementara pahat dipindahkan secara maju atau mundur sehingga material terpotong secara bertahap.

### Komponen Utama

| Komponen          | Fungsi                                     |
|-------------------|--------------------------------------------|
| Chuck             | Menjepit dan memegang benda kerja          |
| Poros Utama       | Memutar benda kerja                         |
| Slide (Carriage) | Menggerakkan pahat secara linear            |
| Pahat (Tool)      | Memotong material                           |
| Panel Kontrol     | Mengatur kecepatan dan gerakan            |
| Motor Penggerak   | Menyediakan tenaga putar pada poros utama  |

### Aplikasi

1. Pembuatan poros mesin
2. Produksi komponen otomotif
3. Pengolahan bahan logam presisi

> Mesin bubut merupakan inti dari banyak industri manufaktur karena kemampuannya menghasilkan permukaan halus dan akurasi tinggi.
MD,
            ],
            ['description' => 'Mesin Milling','category' => null, 'long_description' => <<<'MD'
## Mesin Milling

Mesin milling adalah peralatan pemotongan yang berfungsi menghilangkan material dari permukaan benda kerja dengan pahat berputar secara aksial atau radial.

### Prinsip Kerja

Pahat berputar pada spindle dan bergerak melintasi permukaan benda kerja yang biasanya tetap, menghasilkan profil, potongan, atau lubang sesuai program.

### Komponen Utama

| Komponen          | Fungsi                                   |
|-------------------|------------------------------------------|
| Spindle           | Memutar pahat dengan kecepatan tinggi   |
| Table (Meja)      | Menahan dan memposisikan benda kerja      |
| Kolom (Column)    | Menyokong struktur mesin                 |
| Pahat (Tool)      | Memotong material                         |
| Sistem CNC        | Mengontrol gerakan secara numerik        |
| Pendingin (Coolant) | Mengurangi suhu dan mengeluarkan chip    |

### Aplikasi

1. Penggilingan slot dan alur pada rangka mesin
2. Pembuatan gear dan roda gigi
3. Pembentukan permukaan datar pada blok mesin

> Dengan kemampuan CNC, mesin milling dapat menghasilkan bentuk kompleks secara otomatis dengan toleransi tinggi.
MD,
            ],
            ['description' => 'Mesin Asah',   'category' => null, 'long_description' => <<<'MD'
## Mesin Asah

Mesin asah (grinding machine) digunakan untuk menghaluskan dan memberi bentuk akhir pada permukaan benda kerja melalui abrasif berputar.

### Prinsip Kerja

Roda abrasif berputar pada spindle; benda kerja dipindahkan mendekati atau menjauhi roda sehingga material terangkat secara progresif.

### Komponen Utama

| Komponen          | Fungsi                                   |
|-------------------|------------------------------------------|
| Roda Abrasif      | Menghilangkan material dengan gesekan    |
| Spindle           | Memutar roda abrasif                     |
| Meja Penggerak    | Menyesuaikan posisi dan kedalaman pemotongan |
| Sistem Pendingin | Mendinginkan area kerja dan mengurangi panas |
| Motor Penarik    | Menggerakkan meja secara linear          |
| Panel Kontrol     | Mengatur kecepatan spindle dan feed rate |

### Aplikasi

1. Pengasahan poros dan shaft
2. Pembuatan permukaan datar pada cetakan
3. Penyelesaian akhir pada komponen presisi

> Mesin asah sangat penting dalam proses finishing karena dapat menghasilkan permukaan dengan kehalusan mikron.
MD,
            ],
            ['description' => 'Mesin Bor',    'category' => null, 'long_description' => <<<'MD'
## Mesin Bor

Mesin bor adalah peralatan yang menghasilkan lubang pada bahan kerja dengan cara memutar mata bor dan menekannya ke dalam material.

### Prinsip Kerja

Mata bor berputar cepat pada spindle, sementara tekanan axial diberikan sehingga mata bor menembus material dan menghasilkan lubang berdiameter sesuai.

### Komponen Utama

| Komponen          | Fungsi                                   |
|-------------------|------------------------------------------|
| Spindle           | Memutar mata bor dengan kecepatan variabel |
| Meja (Table)      | Menahan dan mengarahkan posisi benda kerja |
| Pin (Chuck)       | Menjepit mata bor dengan kuat              |
| Motor Penggerak   | Menyediakan tenaga rotasi untuk spindle    |
| Sistem Pendingin  | Mencegah panas berlebih pada mata bor     |
| Panel Kontrol      | Mengatur kecepatan dan kedalaman pengeboran |

### Aplikasi

1. Pengeboran lubang pemasangan baut pada rangka mesin
2. Pembuatan lubang penyesuaian pada panel logam
3. Pengeboran material non-logam seperti kayu dan plastik

> Dengan pilihan mata bor yang beragam, mesin bor dapat melayani kebutuhan produksi yang sangat luas.
MD,
            ],
            ['description' => 'Mesin Sekrap', 'category' => null, 'long_description' => <<<'MD'
## Mesin Sekrap

Mesin sekrap (shaper machine) berfungsi untuk menghilangkan material pada permukaan benda kerja dengan menggunakan pahat yang bergerak bolak-balik secara linear.

### Prinsip Kerja

Pahat sekrap bergerak melintasi permukaan benda kerja yang tetap, mengikis lapisan tipis material sehingga menghasilkan permukaan datar atau profil yang diinginkan.

### Komponen Utama

| Komponen          | Fungsi                                   |
|-------------------|------------------------------------------|
| Pahat Sekrap      | Mengikis material secara presisi         |
| Meja Penggerak    | Menyediakan gerakan linear pahat        |
| Poros Utama       | Menyokong mekanisme gerakan pahat        |
| Sistem Penekan   | Mengatur tekanan pahat pada benda kerja   |
| Panel Kontrol     | Mengatur kecepatan dan kedalaman pemotongan |
| Sistem Pendingin | Mengurangi suhu akibat gesekan            |

### Aplikasi

1. Pengerjaan permukaan datar pada blok cetakan
2. Pembentukan profil pada bagian mesin presisi
3. Finishing akhir pada komponen metalurgi

> Mesin sekrap memberikan kontrol yang sangat baik pada penghilangan material tipis, cocok untuk proses finishing presisi tinggi.
MD,
            ],
        ],

        // ── Subgroup: Mesin Produksi ──────────────────────────────────────────
        'Mesin Produksi' => [
            ['description' => 'Mesin Press',             'category' => null, 'long_description' => <<<'MD'
## Mesin Press

Mesin Press adalah peralatan manufaktur yang memanfaatkan gaya tekan tinggi untuk membentuk, memotong, atau memadatkan material (seperti logam, plastik, atau karet) sesuai dengan cetakan yang digunakan.

### Prinsip Kerja

Mesin press bekerja dengan memberikan tekanan tinggi melalui ram (pendorong) yang bergerak secara vertikal atau horizontal ke arah cetakan. Material ditempatkan di antara ram dan cetakan, kemudian ditekan dengan gaya yang besar sehingga material mengalir mengikuti bentuk cetakan.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Ram (Pendorong) | Menekan material dengan gaya tinggi |
| Cetakan (Die) | Membentuk material sesuai profil yang diinginkan |
| Sistem Hidrolik/Mekanik | Menghasilkan gaya tekan yang besar |
| Meja Kerja | Tempat cetakan dipasang dan material diletakkan |
| Panel Kontrol | Mengatur tekanan, kecepatan, dan siklus operasi |
| Sistem Pengaman | Melindungi operator dari cedera saat operasi |

### Aplikasi

1. Stamping dan blanking lembaran logam untuk komponen otomotif
2. Deep drawing untuk pembentukan komponen cekung seperti kaleng dan casing
3. Powder metallurgy untuk pemadatan serbuk logam menjadi komponen padat

> Mesin press memungkinkan produksi massal dengan presisi tinggi dan kecepatan yang dapat disesuaikan dengan kebutuhan.
MD,
            ],
            ['description' => 'Mesin Tempa',             'category' => null, 'long_description' => <<<'MD'
## Mesin Tempa

Mesin Tempa (Forging Machine) digunakan untuk membentuk logam padat dengan memberikan gaya tekan atau pukulan berulang. Proses ini mengubah struktur mikro logam sehingga menghasilkan komponen dengan kekuatan mekanik dan ketahanan fatik yang jauh lebih tinggi.

### Prinsip Kerja

Billet logam dipanaskan hingga temperatur tempa, kemudian ditempatkan di atas cetakan bawah. Ram atau hammer memberikan tekanan atau pukulan berulang sehingga logam mengisi rongga cetakan dan membentuk komponen yang diinginkan.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Hammer/Ram | Memberikan pukulan atau tekanan pada material |
| Cetakan (Die) | Membentuk material sesuai profil yang diinginkan |
| Tungku Pemanas | Memanaskan billet logam hingga temperatur tempa |
| Sistem Hidrolik/Pneumatik | Menggerakkan hammer dengan gaya yang dapat diatur |
| Manipulator | Memegang dan memposisikan billet logam |
| Pondasi | Menyerap getaran dan dampak dari proses penempaan |

### Aplikasi

1. Pembuatan poros engkol dan connecting rod untuk mesin otomotif
2. Produksi flange dan fitting pipa bertekanan tinggi
3. Komponen alat berat seperti track link, gear, dan shaft

> Penempaan menghasilkan komponen dengan kekuatan lebih tinggi dibanding pengecoran karena struktur butir logam yang lebih rapat dan aliran serat yang kontinu.
MD,
            ],
            ['description' => 'Mesin Crusher',           'category' => null, 'long_description' => <<<'MD'
## Mesin Crusher

Mesin Crusher adalah alat industri yang dirancang untuk mereduksi ukuran material padat (seperti batuan, mineral, limbah konstruksi, atau plastik) menjadi ukuran yang lebih kecil, kerikil, atau serbuk.

### Prinsip Kerja

Material dimasukkan ke dalam ruang penghancuran melalui hopper, kemudian dihancurkan oleh mekanisme seperti rahang bergerak (jaw), kerucut berputar (cone), atau rotor berkecepatan tinggi (impact) yang memberikan gaya tekan atau benturan hingga material pecah.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Rahang Tetap/Rahang Bergerak | Menghancurkan material dengan gaya tekan (Jaw Crusher) |
| Mantle/Concave | Menghancurkan material secara bertahap (Cone Crusher) |
| Rotor/Blow Bar | Memberikan benturan kecepatan tinggi (Impact Crusher) |
| Hopper | Tempat masuknya material yang akan dihancurkan |
| Motor Penggerak | Sumber tenaga untuk memutar rotor atau menggerakkan rahang |
| Screen/Saringan | Memisahkan material berdasarkan ukuran hasil penghancuran |

### Aplikasi

1. Penghancuran batu andesit, granit, dan batuan tambang untuk material konstruksi
2. Daur ulang limbah beton dan aspal untuk recycled aggregate
3. Penghancuran plastik dan karet untuk proses daur ulang

> Pemilihan tipe crusher disesuaikan dengan karakteristik material, kapasitas produksi, dan ukuran output yang diinginkan.
MD,
            ],
            ['description' => 'Mesin Cor',               'category' => null, 'long_description' => <<<'MD'
## Mesin Cor

Mesin Cor (Casting Machine) digunakan dalam proses manufaktur untuk melelehkan logam dan menuangkannya ke dalam cetakan (mold) hingga memadat dan membentuk komponen dengan geometri yang kompleks.

### Prinsip Kerja

Logam padat dilelehkan dalam tungku hingga mencapai temperatur tuang, kemudian dituangkan ke dalam rongga cetakan melalui sistem saluran tuang. Setelah logam membeku, cetakan dibuka dan komponen hasil cor dikeluarkan untuk proses finishing.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Tungku Peleburan | Melelehkan logam hingga temperatur yang diinginkan |
| Cetakan (Mold) | Rongga yang membentuk profil komponen hasil cor |
| Sistem Saluran Tuang | Mengalirkan logam cair ke rongga cetakan |
| Sistem Pendingin | Mempercepat pembekuan logam dalam cetakan |
| Mekanisme Buka/Tutup | Membuka dan menutup cetakan secara otomatis |
| Panel Kontrol | Mengatur temperatur, tekanan, dan waktu siklus |

### Aplikasi

1. Die casting untuk komponen aluminium dan zinc produksi massal
2. Sand casting untuk komponen berukuran besar volume rendah
3. Investment casting untuk komponen presisi dengan permukaan halus

> Mesin cor mampu memproduksi bentuk 3D yang sangat kompleks dalam satu siklus, menjadikannya esensial untuk industri manufaktur komponen.
MD,
            ],
            ['description' => 'Mesin Injection Moulding','category' => null, 'long_description' => <<<'MD'
## Mesin Injection Moulding

Mesin Injection Moulding adalah peralatan utama dalam industri plastik yang bekerja dengan cara melelehkan pelet polimer dan menyuntikkannya ke dalam cetakan (mold) bertekanan tinggi untuk menghasilkan komponen plastik secara massal.

### Prinsip Kerja

Pelet plastik dimasukkan ke dalam barrel melalui hopper, kemudian dipanaskan dan dilelehkan oleh heater bands. Sekrup berputar mendorong material leleh ke depan hingga terkumpul di depan screw tip, kemudian diinjeksikan ke dalam rongga cetakan dengan tekanan tinggi. Setelah plastik mendingin dan membeku, cetakan dibuka dan komponen dikeluarkan oleh ejector pin.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Hopper | Tempat masuknya pelet plastik sebelum diproses |
| Barrel dan Screw | Melelehkan dan mendorong material ke cetakan |
| Heater Bands | Memanaskan barrel untuk melelehkan plastik |
| Cetakan (Mold) | Rongga yang membentuk profil komponen plastik |
| Unit Clamping | Menjepit cetakan dengan gaya besar saat injeksi |
| Ejector Pin | Mendorong komponen keluar dari cetakan setelah selesai |

### Aplikasi

1. Produksi komponen interior dan eksterior otomotif (dashboard, bumper, panel)
2. Pembuatan wadah, tutup botol, dan kemasan plastik untuk FMCG
3. Komponen elektronik seperti casing, konektor, dan switch

> Injection moulding memungkinkan produksi massal komponen plastik dengan toleransi dimensi ketat, siklus cepat, dan limbah material minimal.
MD,
            ],
        ],

        // ── Subgroup: Transmisi Tenaga Mekanik ────────────────────────────────
        'Transmisi Tenaga Mekanik' => [
            ['description' => 'Transmisi Sabuk',    'category' => null, 'long_description' => <<<'MD'
## Transmisi Sabuk

Transmisi sabuk (belt drive) adalah sistem pemindah tenaga mekanik yang menggunakan sabuk (belt) yang dipasang pada dua atau lebih puli. Sistem ini digunakan untuk mentransmisikan daya dari satu poros ke poros lainnya dengan jarak yang relatif jauh dan memerlukan kecepatan putaran yang bervariasi.

### Prinsip Kerja

Daya ditransmisikan melalui gesekan antara permukaan sabuk dan alur puli. Sabuk yang terpasang pada puli penggerak (driver) akan menggerakkan puli yang digerakkan (driven) sehingga terjadi pemindahan daya rotasi antara dua poros.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Sabuk (Belt) | Komponen fleksibel yang mentransmisikan daya melalui gesekan |
| Puli (Pulley) | Roda beralur yang berfungsi sebagai tempat sabuk dipasang |
| Tensioner | Alat penegang sabuk untuk menjaga ketegangan optimal |
| Poros (Shaft) | Menyokong puli dan menghubungkan dengan mesin |
| Bantalan (Bearing) | Menopang poros agar berputar dengan lancar |
| Pelindung (Guard) | Melindungi operator dari komponen bergerak |

### Aplikasi

1. Sistem pemindah daya pada mesin-mesin industri (pompa, kipas, kompresor)
2. Sistem alternator dan AC pada kendaraan bermotor
3. Konveyor dan sistem material handling

> Transmisi sabuk sangat umum digunakan karena operasi yang halus, senyap, dapat menyerap getaran, dan biaya perawatan yang rendah.
MD,
            ],
            ['description' => 'Transmisi Rantai',   'category' => null, 'long_description' => <<<'MD'
## Transmisi Rantai

Transmisi rantai (chain drive) adalah sistem pemindah tenaga mekanik yang menggunakan rantai yang dipasang pada sproket (sprocket) untuk mentransmisikan daya dan gerakan rotasi. Sistem ini mampu memindahkan daya dengan efisiensi tinggi tanpa slip.

### Prinsip Kerja

Rantai yang terpasang pada sproket penggerak (driver) akan menggerakkan sproket yang digerakkan (driven) karena mata rantai berinterlock dengan gigi sproket. Sistem ini merupakan positive drive yang tidak mengalami slip seperti sabuk.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Rantai (Chain) | Rangkaian mata rantai yang mentransmisikan daya |
| Sproket | Roda bergigi yang berfungsi sebagai penggerak dan penggerak |
| Tensioner | Alat penegang rantai untuk menjaga kekencangan yang tepat |
| Poros (Shaft) | Menyokong sproket dan menghubungkan dengan mesin |
| Bantalan (Bearing) | Menopang poros agar berputar dengan lancar |
| Pelumas | Mengurangi gesekan dan keausan pada mata rantai dan sproket |

### Aplikasi

1. Sistem penggerak pada sepeda motor dan mesin industri
2. Sistem konveyor untuk material handling
3. Mesin pertanian dan alat berat

> Transmisi rantai memiliki efisiensi tinggi (95-98%), tidak terjadi slip, dan mampu mentransmisikan daya yang besar pada berbagai jarak poros.
MD,
            ],
            ['description' => 'Transmisi Roda Gigi','category' => null, 'long_description' => <<<'MD'
## Transmisi Roda Gigi

Transmisi roda gigi (gear drive) adalah sistem pemindah tenaga mekanik yang menggunakan roda gigi yang saling berkaitan (meshing) untuk mentransmisikan daya, mengubah kecepatan, torsi, dan arah putaran. Sistem ini merupakan metode transmisi yang paling presisi dan efisien.

### Prinsip Kerja

Roda gigi penggerak (driver) berputar dan bertaut dengan roda gigi penggerak (driven) sehingga terjadi pemindahan daya rotasi. Perbandingan jumlah gigi menentukan rasio kecepatan dan torsi antara kedua poros.

### Komponen Utama

| Komponen | Fungsi |
|----------|--------|
| Roda Gigi (Gear) | Komponen bergigi yang saling berkaitan untuk mentransmisikan daya |
| Poros (Shaft) | Tempat roda gigi dipasang dan berputar |
| Bantalan (Bearing) | Menopang poros agar berputar dengan lancar |
| Gigi Helical/Spur | Menentukan karakteristik operasi dan rasio kecepatan |
| Sistem Pelumasan | Melumasi permukaan gigi untuk mengurangi gesekan |
| Rumah Gearbox | Melindungi komponen internal dan menahan pelumas |

### Aplikasi

1. Transmisi kendaraan bermotor (otomotif, kereta api, kapal)
2. Mesin industri (pompa, kipas, conveyor, turbine)
3. Robotika dan sistem otomasi pabrik

> Transmisi roda gigi memiliki efisiensi sangat tinggi (98-99%), tidak terjadi slip, dan mampu mentransmisikan daya serta torsi yang sangat besar dengan presisi tinggi.
MD,
            ],
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

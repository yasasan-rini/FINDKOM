<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FoundReport;
use App\Models\HandoverEvidence;
use App\Models\Location;
use App\Models\LostReport;
use App\Models\Notification;
use App\Models\ReportMatch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pw = Hash::make('findkom123');
        $U = fn ($nama, $nim, $email, $username, $kontak, $role) => User::create([
            'namaLengkap' => $nama, 'nim' => $nim, 'email' => $email, 'username' => $username,
            'passwordHash' => $pw, 'kontak' => $kontak, 'role' => $role, 'createdAt' => now()->subDays(40),
        ]);
        $rina = $U('Rina Wulandari', '255150401111099', 'rina.wulandari@student.ub.ac.id', 'rinawulan', '081234567890', 'CIVITAS');
        $budi = $U('Budi Santoso', null, 'budi.santoso@ub.ac.id', 'budisecurity', '081333445566', 'SECURITY');
        $dimas = $U('Dimas Prasetyo', '255150407111042', 'dimas.prasetyo@student.ub.ac.id', 'dimasp', '082145678901', 'CIVITAS');
        $siti = $U('Siti Aminah', '198705122010122001', 'siti.aminah@ub.ac.id', 'sitiaminah', '085712345678', 'CIVITAS');
        $aldi = $U('Aldi Nugraha', '255150400111017', 'aldi.nugraha@student.ub.ac.id', 'aldinug', '081398765432', 'CIVITAS');

        $loc = collect(['Gedung F', 'Gedung G', 'Gedung A', 'Gedung B', 'Kantin FILKOM', 'Area Parkir F', 'Perpustakaan', 'Mushola'])
            ->mapWithKeys(fn ($n) => [$n => Location::create(['name' => $n])->id]);
        $cat = collect(['Dompet', 'Elektronik', 'Kunci', 'Botol Minum', 'Tas & Ransel', 'Dokumen', 'Charger & Kabel', 'Aksesoris', 'Kartu & KTM', 'Lainnya'])
            ->mapWithKeys(fn ($n) => [$n => Category::create(['name' => $n])->id]);

        $R = function (string $kelas, string $kode, $user, string $kategori, string $gedung, string $lantai,
            Carbon $waktu, string $desc, string $status, string $foto, array $extra = []) use ($loc, $cat) {
            $isLost = $kelas === LostReport::class;
            return $kelas::create(array_merge([
                'reportCode' => $kode, 'reporterId' => $user->id,
                'locationId' => $loc[$gedung], 'floorRoom' => $lantai,
                ($isLost ? 'lostDate' : 'foundDate') => $waktu->toDateString(),
                ($isLost ? 'lostTime' : 'foundTime') => $waktu->format('H:i'),
                'categoryId' => $cat[$kategori], 'description' => $desc,
                'photoUrl' => 'img/items/'.$foto.'.svg', 'status' => $status,
                'createdAt' => $waktu->copy()->addMinutes(20),
            ], $isLost ? [] : ['reporterName' => $user->namaLengkap], $extra));
        };
        $M = fn ($kode, $lost, $found, $status, Carbon $d, array $extra = []) => ReportMatch::create(array_merge([
            'matchCode' => $kode, 'lostReportId' => $lost->id, 'foundReportId' => $found->id,
            'status' => $status, 'createdAt' => $d, 'updatedAt' => $d,
        ], $extra));
        $N = fn ($user, $msg, Carbon $d, $read = false, $matchId = null) => Notification::create([
            'userId' => $user->id, 'matchId' => $matchId, 'message' => $msg, 'isRead' => $read, 'createdAt' => $d]);
        $E = fn ($match, Carbon $d) => HandoverEvidence::create([
            'matchId' => $match->id, 'photoHolding' => 'img/evidence/holding.svg', 'photoKtm' => 'img/evidence/ktm-card.svg',
            'securityId' => $GLOBALS['budiId'], 'handoverAt' => $d, 'createdAt' => $d]);
        $GLOBALS['budiId'] = $budi->id;
        $d = fn ($n, $h = 10, $m = 0) => now()->subDays($n)->setTime($h, $m);

        // Arsip selesai
        $l1 = $R(LostReport::class, 'L-0001', $siti, 'Tas & Ransel', 'Perpustakaan', 'Rak baca lantai 1', $d(20, 9, 15), 'Ransel abu-abu merek Eiger berisi buku catatan dan botol kecil.', 'CLOSED', 'bag');
        $f1 = $R(FoundReport::class, 'F-0001', $dimas, 'Tas & Ransel', 'Perpustakaan', 'Meja baca pojok', $d(19, 15, 40), 'Ransel abu-abu ditemukan di meja baca pojok perpustakaan.', 'CLOSED', 'bag', ['receivedBy' => $budi->id, 'receivedAt' => $d(19, 16, 20)]);
        $m1 = $M('M-0001', $l1, $f1, 'CLOSED', $d(19, 15, 45), ['verifiedBy' => $budi->id, 'verifiedAt' => $d(18, 9)]); $E($m1, $d(17, 10));
        $l2 = $R(LostReport::class, 'L-0002', $aldi, 'Kartu & KTM', 'Gedung F', 'Lantai 2, Ruang F2.1', $d(15, 11), 'Kartu ATM BCA dan KTM terselip di dompet kecil transparan.', 'CLOSED', 'ktm');
        $f2 = $R(FoundReport::class, 'F-0002', $siti, 'Kartu & KTM', 'Gedung F', 'Koridor F2 dekat tangga', $d(14, 8, 30), 'Kartu ATM + KTM ditemukan di lantai koridor F2 dekat tangga.', 'CLOSED', 'ktm', ['receivedBy' => $budi->id, 'receivedAt' => $d(14, 9, 10)]);
        $m2 = $M('M-0002', $l2, $f2, 'CLOSED', $d(14, 8, 35), ['verifiedBy' => $budi->id, 'verifiedAt' => $d(13, 10)]); $E($m2, $d(12, 13));
        $l3 = $R(LostReport::class, 'L-0003', $dimas, 'Charger & Kabel', 'Perpustakaan', 'Meja baca lantai 2', $d(8, 13, 30), 'Charger MacBook 61W putih beserta kabel USB-C tergulung rapi.', 'CLOSED', 'charger');
        $f3 = $R(FoundReport::class, 'F-0003', $dimas, 'Charger & Kabel', 'Perpustakaan', 'Meja baca lantai 2', $d(8, 16), 'Charger Apple 61W ditemukan tertinggal di meja baca lantai 2.', 'CLOSED', 'charger', ['receivedBy' => $budi->id, 'receivedAt' => $d(8, 16, 40)]);
        $m3 = $M('M-0003', $l3, $f3, 'CLOSED', $d(8, 16, 5), ['verifiedBy' => $budi->id, 'verifiedAt' => $d(7, 9)]); $E($m3, $d(6, 10));

        // Aktif
        $l4 = $R(LostReport::class, 'L-0004', $aldi, 'Elektronik', 'Gedung F', 'Lantai 2, Ruang F2.3', $d(6, 15, 20), 'Earbuds Soundcore hitam, case ada goresan di sudut kanan atas.', 'MATCH_CANDIDATE', 'earbuds');
        $l5 = $R(LostReport::class, 'L-0005', $rina, 'Botol Minum', 'Kantin FILKOM', 'Meja panjang tengah', $d(5, 12, 15), 'Tumbler putih 500ml, ada stiker nama "Rina" di bagian bawah.', 'NOTIFIED', 'tumbler');
        $f4 = $R(FoundReport::class, 'F-0004', $siti, 'Botol Minum', 'Kantin FILKOM', 'Meja 4 dekat kasir', $d(4, 13), 'Tumbler putih tertinggal di meja kantin dekat kasir, masih terisi air.', 'NOTIFIED', 'tumbler', ['receivedBy' => $budi->id, 'receivedAt' => $d(4, 14, 10)]);
        $m5 = $M('M-0005', $l5, $f4, 'NOTIFIED', $d(4, 13, 5), ['verifiedBy' => $budi->id, 'verifiedAt' => $d(3, 9, 30)]);
        $f6 = $R(FoundReport::class, 'F-0006', $rina, 'Elektronik', 'Gedung F', 'Lantai 2, Ruang F2.3', $d(5, 16, 10), 'Earbuds hitam ditemukan di kursi baris ketiga kelas F2.3.', 'MATCH_CANDIDATE', 'earbuds', ['receivedBy' => $budi->id, 'receivedAt' => $d(5, 16, 50)]);
        $m4 = $M('M-0004', $l4, $f6, 'MATCH_CANDIDATE', $d(5, 16, 15));
        $l6 = $R(LostReport::class, 'L-0006', $siti, 'Kartu & KTM', 'Gedung A', 'Lobby Gedung A', $d(3, 10, 45), 'KTM atas nama Siti Aminah, kemungkinan jatuh di sekitar lobby.', 'MATCH_CANDIDATE', 'ktm');
        $f8 = $R(FoundReport::class, 'F-0008', $aldi, 'Kartu & KTM', 'Gedung A', 'Lobby dekat papan info', $d(3, 9, 40), 'KTM nama "Siti Aminah" ditemukan di lantai lobby Gedung A.', 'MATCH_CANDIDATE', 'ktm');
        $m6 = $M('M-0006', $l6, $f8, 'MATCH_CANDIDATE', $d(3, 10, 50));
        $l7 = $R(LostReport::class, 'L-0007', $rina, 'Dompet', 'Gedung F', 'Lantai 2, Ruang F2.1', $d(2, 10, 30), 'Dompet kulit hitam berisi KTM dan kartu ATM, resleting samping silver.', 'MATCH_CANDIDATE', 'wallet');
        $f5 = $R(FoundReport::class, 'F-0005', $siti, 'Dompet', 'Gedung F', 'Lantai 2, Koridor F2', $d(2, 13, 15), 'Dompet hitam ditemukan di bawah bangku koridor F2.', 'MATCH_CANDIDATE', 'wallet', ['receivedBy' => $budi->id, 'receivedAt' => $d(2, 14), 'receiptNote' => 'Kondisi barang sesuai deskripsi.']);
        $m7 = $M('M-0007', $l7, $f5, 'MATCH_CANDIDATE', $d(2, 13, 20));
        $R(LostReport::class, 'L-0008', $dimas, 'Elektronik', 'Gedung G', 'Lab G3.2', $d(1, 14), 'Laptop ASUS VivoBook silver, ada stiker himpunan di cover belakang.', 'LOST_REPORTED', 'laptop');
        $R(LostReport::class, 'L-0009', $rina, 'Aksesoris', 'Gedung B', 'Ruang B1.1', $d(1, 16, 30), 'Payung lipat navy, gagang kayu, tersarung kain motif garis.', 'LOST_REPORTED', 'umbrella');
        $R(FoundReport::class, 'F-0007', $siti, 'Botol Minum', 'Gedung B', 'Ruang B1.2', $d(1, 11, 20), 'Botol minum kaca hijau dengan sarung silikon, tertinggal di meja dosen.', 'FOUND_REPORTED', 'bottle');
        $l10 = $R(LostReport::class, 'L-0010', $aldi, 'Kunci', 'Area Parkir F', 'Parkir blok B', now()->subHours(4), 'Kunci motor Honda dengan gantungan karakter biru dan remote alarm.', 'MATCH_CANDIDATE', 'keys');
        $f10 = $R(FoundReport::class, 'F-0010', $dimas, 'Kunci', 'Area Parkir F', 'Aspal dekat gerbang', now()->subHours(3), 'Kunci motor gantungan biru ditemukan di aspal dekat gerbang parkir.', 'MATCH_CANDIDATE', 'keys');
        $m8 = $M('M-0008', $l10, $f10, 'MATCH_CANDIDATE', now()->subHours(3)->addMinutes(5));
        $f9 = $R(FoundReport::class, 'F-0009', $aldi, 'Elektronik', 'Gedung F', 'Ruang F2.5', now()->subHours(2), 'Laptop ASUS silver ditemukan di meja pojok F2.5 setelah kelas selesai.', 'FOUND_REPORTED', 'laptop');
        $R(FoundReport::class, 'F-0011', $rina, 'Lainnya', 'Area Parkir F', 'Parkir blok A', now()->subHour(), 'Helm half-face hitam tanpa kaca, tertinggal di atas jok motor.', 'FOUND_REPORTED', 'helm');

        // Notifikasi
        $N($rina, 'Kemungkinan barangmu sudah ditemukan! Tumbler putih (L-0005) siap diambil di pos Security Gedung F. Bawa KTM.', $d(3, 9, 31), false, $m5->id);
        $N($rina, 'Sistem menemukan kandidat kecocokan untuk laporan dompet hitam kamu (L-0007). Security sedang meninjau.', $d(2, 13, 21), true, $m7->id);
        $N($dimas, 'Laporan L-0003 telah selesai. Charger MacBook telah diserahkan kembali. Terima kasih menggunakan FINDKOM!', $d(6, 10), true, $m3->id);
        $N($siti, 'Sistem menemukan kandidat kecocokan untuk laporan KTM kamu (L-0006). Security sedang meninjau.', $d(3, 10, 51), false, $m6->id);
        $N($budi, "Kandidat baru {$m8->matchCode} menunggu verifikasi — Kunci di Area Parkir F.", now()->subHours(3), false, $m8->id);
        $N($budi, "Barang temuan {$f9->reportCode} (Laptop ASUS) belum diserahkan fisik ke pos Security.", now()->subHours(2), false);
        $N($budi, "Kandidat baru {$m7->matchCode} menunggu verifikasi — Dompet di Gedung F.", $d(2, 13, 20), false, $m7->id);
        $N($budi, "Kandidat baru {$m6->matchCode} menunggu verifikasi — Kartu/KTM di Gedung A.", $d(3, 10, 50), true, $m6->id);
    }
}
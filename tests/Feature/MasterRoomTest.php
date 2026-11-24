<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterRoomTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $u = User::where('email', 'budi.santoso@ub.ac.id')->firstOrFail();
        $this->post('/login', ['email' => $u->email, 'password' => 'findkom123', 'peran' => 'SECURITY']);
    }

    public function test_security_dapat_menambah_ruangan_pada_gedung(): void
    {
        $gedung = Location::where('name', 'Gedung F')->value('id');
        $this->post('/master', ['jenis' => 'ruangan', 'name' => 'Ruang F3.7', 'locationId' => $gedung])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('Room', ['name' => 'Ruang F3.7', 'locationId' => $gedung]);
    }

    public function test_nama_ruangan_duplikat_dalam_gedung_sama_ditolak(): void
    {
        $gedung = Location::where('name', 'Gedung F')->value('id');
        $this->post('/master', ['jenis' => 'ruangan', 'name' => 'Lantai 2, Ruang F2.1', 'locationId' => $gedung])
            ->assertSessionHas('error');
        $this->assertSame(1, Room::where('locationId', $gedung)->where('name', 'Lantai 2, Ruang F2.1')->count());
    }

    public function test_nama_ruangan_sama_boleh_di_gedung_berbeda(): void
    {
        $a = Location::where('name', 'Gedung A')->value('id');
        $b = Location::where('name', 'Gedung B')->value('id');
        $this->post('/master', ['jenis' => 'ruangan', 'name' => 'Ruang Seminar', 'locationId' => $a])->assertSessionHas('success');
        $this->post('/master', ['jenis' => 'ruangan', 'name' => 'Ruang Seminar', 'locationId' => $b])->assertSessionHas('success');
        $this->assertSame(2, Room::where('name', 'Ruang Seminar')->count());
    }

    public function test_ruangan_yang_dipakai_laporan_tidak_bisa_dihapus(): void
    {
        $gedung = Location::where('name', 'Gedung F')->value('id');
        $room = Room::where('locationId', $gedung)->where('name', 'Lantai 2, Ruang F2.1')->firstOrFail();
        $this->assertGreaterThan(0, $room->usage);
        $this->delete("/master/{$room->id}", ['jenis' => 'ruangan'])->assertSessionHas('error');
        $this->assertDatabaseHas('Room', ['id' => $room->id]);
    }

    public function test_ruangan_bebas_dapat_dihapus(): void
    {
        $gedung = Location::where('name', 'Gedung G')->value('id');
        $room = Room::create(['locationId' => $gedung, 'name' => 'Ruang Uji Kosong']);
        $this->delete("/master/{$room->id}", ['jenis' => 'ruangan'])->assertSessionHas('success');
        $this->assertDatabaseMissing('Room', ['id' => $room->id]);
    }

    public function test_daftar_ruangan_tersedia_pada_form_pelaporan(): void
    {
        $this->post('/logout');
        $rina = User::where('email', 'rina.wulandari@student.ub.ac.id')->firstOrFail();
        $this->post('/login', ['email' => $rina->email, 'password' => 'findkom123', 'peran' => 'CIVITAS']);
        $this->get('/lapor/kehilangan')->assertOk()
            ->assertSee('Lantai 2, Ruang F2.1')
            ->assertSee('room-list', false);
    }
}
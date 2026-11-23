<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\Room;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    private function kelas(string $jenis): string
    {
        abort_unless(in_array($jenis, ['gedung', 'kategori', 'ruangan']), 404);
        return match ($jenis) {
            'gedung' => Location::class,
            'kategori' => Category::class,
            'ruangan' => Room::class,
        };
    }

    // PC020 - validasiData(jenis, nama[, locationId]) : Boolean (false = duplikat)
    private function validasiData(string $jenis, string $nama, ?string $locationId = null, ?string $kecualiId = null): bool
    {
        $kelas = $this->kelas($jenis);
        return ! $kelas::whereRaw('LOWER(name) = ?', [strtolower(trim($nama))])
            ->when($jenis === 'ruangan', fn ($q) => $q->where('locationId', $locationId))
            ->when($kecualiId, fn ($q) => $q->where('id', '!=', $kecualiId))
            ->exists();
    }

    // PC018 - requestDaftar()
    public function index(Request $request)
    {
        $jenis = $request->query('jenis', 'gedung');
        $kelas = $this->kelas($jenis);
        $data = $jenis === 'ruangan'
            ? Room::with('location')->orderBy('locationId')->orderBy('name')->get()
            : $kelas::orderBy('name')->get();

        return view('master.index', [
            'jenis' => $jenis,
            'data' => $data,
            'daftarGedung' => Location::orderBy('name')->get(),
            'jumlah' => ['gedung' => Location::count(), 'kategori' => Category::count(), 'ruangan' => Room::count()],
        ]);
    }

    // PC019 - tambahData(jenis, nama)
    public function tambah(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:gedung,kategori,ruangan',
            'name' => 'required|string|min:2|max:50',
            'locationId' => 'required_if:jenis,ruangan|nullable|exists:Location,id',
        ], ['locationId.required_if' => 'Pilih gedung induk untuk ruangan ini.']);

        $jenis = $request->input('jenis');
        $nama = trim($request->input('name'));
        $locationId = $request->input('locationId');

        if (! $this->validasiData($jenis, $nama, $locationId)) {
            return back()->with('error', 'Data sudah terdaftar.')->withInput();
        }

        $this->kelas($jenis)::create($jenis === 'ruangan'
            ? ['name' => $nama, 'locationId' => $locationId]
            : ['name' => $nama]);

        return redirect()->route('master.index', ['jenis' => $jenis])
            ->with('success', "\"{$nama}\" berhasil ditambahkan.");
    }

    public function ubah(Request $request, string $id)
    {
        $request->validate([
            'jenis' => 'required|in:gedung,kategori,ruangan',
            'name' => 'required|string|min:2|max:50',
            'locationId' => 'required_if:jenis,ruangan|nullable|exists:Location,id',
        ]);
        $jenis = $request->input('jenis');
        $nama = trim($request->input('name'));
        $locationId = $request->input('locationId');
        $data = $this->kelas($jenis)::findOrFail($id);

        if (! $this->validasiData($jenis, $nama, $locationId, $id)) {
            return back()->with('error', 'Data sudah terdaftar.');
        }

        $data->update($jenis === 'ruangan'
            ? ['name' => $nama, 'locationId' => $locationId]
            : ['name' => $nama]);

        return back()->with('success', 'Data berhasil diubah.');
    }

    // PC021 (konfirmasi di view) + PC022 - konfirmasiHapus(jenis, id)
    public function hapus(Request $request, string $id)
    {
        $request->validate(['jenis' => 'required|in:gedung,kategori,ruangan']);
        $data = $this->kelas($request->input('jenis'))::findOrFail($id);

        if ($data->usage > 0) {
            return back()->with('error', "\"{$data->name}\" masih digunakan oleh {$data->usage} laporan — penghapusan dibatalkan.");
        }
        $nama = $data->name;
        $data->delete();

        return back()->with('success', "\"{$nama}\" dihapus dari daftar.");
    }
}
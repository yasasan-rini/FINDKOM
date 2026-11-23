<?php

namespace App\Http\Controllers;

use App\Models\FoundReport;
use App\Models\Notification;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    // PC010 + PC011 - requestHalaman() / cariLaporanPenemuan(keyword)
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('q'));
        $hasil = FoundReport::with(['location', 'category'])
            ->where('status', '!=', 'CLOSED')->whereNull('receivedAt')
            ->when($keyword !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('reportCode', 'like', "%{$keyword}%")
                ->orWhere('reporterName', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")))
            ->orderByDesc('createdAt')->limit(12)->get();

        $terpilih = $request->filled('id')
            ? FoundReport::with(['location', 'category', 'reporter'])->whereNull('receivedAt')->find($request->query('id'))
            : null;

        $diPos = FoundReport::with(['category', 'receiver'])->whereNotNull('receivedAt')
            ->where('status', '!=', 'CLOSED')->orderByDesc('receivedAt')->limit(6)->get();

        return view('receipt.index', compact('hasil', 'terpilih', 'diPos', 'keyword'));
    }

    // PC012 - konfirmasiPenerimaan(reportId, securityId, catatan)
    public function konfirmasi(Request $request, string $id)
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);
        $laporan = FoundReport::whereNull('receivedAt')->where('status', '!=', 'CLOSED')->findOrFail($id);
        $laporan->update([
            'receivedBy' => auth()->id(), 'receivedAt' => now(),
            'receiptNote' => $request->input('catatan') ?: null,
        ]);
        Notification::create(['userId' => $laporan->reporterId, 'matchId' => null,
            'message' => "Barang temuanmu ({$laporan->reportCode}) telah resmi diterima & dititipkan di Pos Security Gedung F.",
            'isRead' => false]);
        return redirect()->route('receipt.index')
            ->with('success', "Penerimaan {$laporan->reportCode} tercatat — petugas & waktu tersimpan.");
    }
}
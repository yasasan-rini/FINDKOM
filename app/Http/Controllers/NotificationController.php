<?php

namespace App\Http\Controllers;

use App\Models\FoundReport;
use App\Models\LostReport;
use App\Models\Notification;
use App\Models\ReportMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    public function baca(string $id)
    {
        $n = Notification::where('userId', auth()->id())->find($id);
        if (!$n) {
            abort(404);
        }
        $n->update(['isRead' => true]);
        
        $user = auth()->user();
        if ($user->isSecurity()) {
            if ($n->matchId && $n->match) {
                return redirect()->route($n->match->status === 'NOTIFIED' ? 'handover.form' : 'match.detail', $n->matchId);
            }
            return redirect()->route('dashboard');
        }
        return redirect()->route('laporan.status');
    }

    public function bacaSemua()
    {
        Notification::where('userId', auth()->id())->where('isRead', false)->update(['isRead' => true]);
        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    /** Ringkasan untuk pembaruan langsung di sisi klien (tanpa reload halaman). */
    public function ringkas(Request $request): JsonResponse
    {
        $u = $request->user();

        $data = [
            'unread' => Notification::where('userId', $u->id)->where('isRead', false)->count(),
            'notifikasi' => Notification::where('userId', $u->id)->orderByDesc('createdAt')->limit(6)->get()
                ->map(fn ($n) => [
                    'id' => $n->id, 'pesan' => $n->message, 'baru' => ! $n->isRead,
                    'waktu' => Carbon::parse($n->createdAt)->diffForHumans(),
                    'url' => route('notifikasi.baca', $n->id),
                ])->values(),
            'waktu' => now()->format('H:i:s'),
        ];

        if ($u->isSecurity()) {
            $data['stat'] = [
                'kandidat' => ReportMatch::where('status', 'MATCH_CANDIDATE')->count(),
                'hilang' => LostReport::where('status', '!=', 'CLOSED')->count(),
                'pos' => FoundReport::whereNotNull('receivedAt')->where('status', '!=', 'CLOSED')->count(),
                'selesai' => ReportMatch::where('status', 'CLOSED')->count(),
            ];
        } else {
            $data['laporan'] = LostReport::where('reporterId', $u->id)->get(['reportCode', 'status'])
                ->concat(FoundReport::where('reporterId', $u->id)->get(['reportCode', 'status']))
                ->map(fn ($r) => [
                    'kode' => $r->reportCode, 'status' => $r->status,
                    'label' => $r->labelStatus, 'badge' => $r->badgeClass,
                ])->values();
        }

        return response()->json($data)->header('Cache-Control', 'no-store');
    }
}
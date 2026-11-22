<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function baca(string $id)
    {
        $n = Notification::where('userId', auth()->id())->findOrFail($id);
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
}
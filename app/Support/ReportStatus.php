<?php

namespace App\Support;

class ReportStatus
{
    public const LABEL = [
        'LOST_REPORTED' => 'Dilaporkan', 'FOUND_REPORTED' => 'Dilaporkan',
        'MATCH_CANDIDATE' => 'Kandidat Cocok', 'VERIFIED_MATCH' => 'Terverifikasi',
        'NOTIFIED' => 'Menunggu Diambil', 'RETURNED' => 'Diserahkan',
        'CLOSED' => 'Selesai', 'REJECTED' => 'Ditolak',
    ];
    public const BADGE = [
        'MATCH_CANDIDATE' => 'cand', 'VERIFIED_MATCH' => 'ver', 'NOTIFIED' => 'wait',
        'RETURNED' => 'done', 'CLOSED' => 'closed', 'REJECTED' => 'rej',
    ];
    public const INDEX = [
        'LOST_REPORTED' => 0, 'FOUND_REPORTED' => 0, 'MATCH_CANDIDATE' => 1,
        'VERIFIED_MATCH' => 2, 'NOTIFIED' => 3, 'RETURNED' => 3, 'CLOSED' => 4,
    ];
    public const STEPS = ['Dilaporkan', 'Kandidat', 'Terverifikasi', 'Menunggu Diambil', 'Selesai'];
}
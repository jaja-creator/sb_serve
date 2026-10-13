<?php

namespace App\Support;

class StatusColors
{
    /**
     * Background / text pair used by <x-status-pill>.
     */
    public static function pill(string $status): array
    {
        return match ($status) {
            'Enacted' => ['bg' => '#1B4332', 'text' => '#F5F0E6'],
            'Approved', 'Third Reading', 'Second Reading' => ['bg' => '#E8EFEA', 'text' => '#1B4332'],
            'For Committee Reporting', 'First Reading', 'Filed' => ['bg' => '#F5EBCB', 'text' => '#8A6D1A'],
            'Vetoed' => ['bg' => '#8B2635', 'text' => '#F5F0E6'],
            'Returned' => ['bg' => '#F3E1E3', 'text' => '#8B2635'],
            'Draft', 'Archived' => ['bg' => '#E5E7E6', 'text' => '#525252'],
            default => ['bg' => '#E5E7E6', 'text' => '#6B7A75'],
        };
    }
    
    public static function accent(string $status): string
    {
        return match ($status) {
            'Enacted' => '#1B4332',
            'Approved', 'Third Reading', 'Second Reading' => '#2F6B4F',
            'For Committee Reporting', 'First Reading', 'Filed' => '#C9A227',
            'Vetoed' => '#8B2635',
            'Returned' => '#C4485C',
            'Draft', 'Archived' => '#6B7A75',
            default => '#A3A3A3',
        };
    }
}
<?php

use App\Livewire\Reports\Search;
use App\Models\Document;
use App\Models\User;
use Livewire\Livewire;

test('guests cannot access reports', function () {
    $this->get(route('reports.search'))->assertRedirect(route('login'));
});

test('users can search and filter legislative reports', function () {
    $user = User::factory()->create(['role' => 'viewer']);
    $ordinance = Document::factory()->create([
        'title' => 'Comprehensive Land Use Plan',
        'document_type' => 'Ordinance',
        'status' => 'Enacted',
    ]);
    $resolution = Document::factory()->create([
        'title' => 'Resolution on Budget Surplus',
        'document_type' => 'Resolution',
        'status' => 'Approved',
    ]);

    $this->actingAs($user);

    Livewire::test(Search::class)
        ->set('typeFilter', 'Ordinance')
        ->assertSee('Comprehensive Land Use Plan')
        ->assertDontSee('Resolution on Budget Surplus');
});

test('grouped search query correctly respects status and type filters', function () {
    $user = User::factory()->create(['role' => 'viewer']);

    // Document with matching search keyword but different type
    Document::factory()->create([
        'title' => 'Zoning Regulation Resolution',
        'series_number' => 'SB-2026-901',
        'document_type' => 'Resolution',
        'status' => 'Approved',
    ]);

    // Document with matching search keyword AND matching type
    Document::factory()->create([
        'title' => 'Zoning Regulation Ordinance',
        'series_number' => 'SB-2026-902',
        'document_type' => 'Ordinance',
        'status' => 'Enacted',
    ]);

    $this->actingAs($user);

    Livewire::test(Search::class)
        ->set('search', 'Zoning')
        ->set('typeFilter', 'Ordinance')
        ->assertSee('SB-2026-902')
        ->assertDontSee('SB-2026-901');
});

test('admins can export reports to CSV while non-admins are forbidden', function () {
    $admin = User::factory()->create(['role' => 'administrator']);
    $viewer = User::factory()->create(['role' => 'viewer']);

    Document::factory()->create([
        'series_number' => 'SB-2026-001',
        'title' => 'Environmental Code',
    ]);

    $this->actingAs($viewer);
    Livewire::test(Search::class)->call('exportCsv')->assertForbidden();

    $this->actingAs($admin);
    Livewire::test(Search::class)
        ->call('exportCsv')
        ->assertFileDownloaded();
});

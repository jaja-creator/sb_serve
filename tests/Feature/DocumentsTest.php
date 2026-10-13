<?php

use App\Livewire\Documents\Form;
use App\Livewire\Documents\Index;
use App\Livewire\Documents\Show;
use App\Models\Document;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected when visiting documents', function () {
    $this->get(route('documents.index'))->assertRedirect(route('login'));
    $this->get(route('documents.create'))->assertRedirect(route('login'));
});

test('authenticated users can see documents list', function () {
    $user = User::factory()->create(['role' => 'encoder']);
    $doc = Document::factory()->create([
        'title' => 'An Ordinance on Public Cleanliness',
        'series_number' => 'SB-2026-101',
    ]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->assertSee('An Ordinance on Public Cleanliness')
        ->assertSee('SB-2026-101');
});

test('encoders can create a new document', function () {
    $encoder = User::factory()->create(['role' => 'encoder']);
    $this->actingAs($encoder);

    Livewire::test(Form::class)
        ->set('series_number', 'SB-2026-050')
        ->set('title', 'Resolution of Commendation')
        ->set('document_type', 'Resolution')
        ->set('subject_matter', 'Commendation')
        ->set('sponsor', 'Hon. Maria Santos')
        ->set('date_filed', '2026-05-10')
        ->call('save')
        ->assertRedirect(route('documents.index'));

    $this->assertDatabaseHas('documents', [
        'series_number' => 'SB-2026-050',
        'title' => 'Resolution of Commendation',
        'document_type' => 'Resolution',
        'encoded_by' => $encoder->id,
    ]);
});

test('encoders can save a draft without required series number', function () {
    $encoder = User::factory()->create(['role' => 'encoder']);
    $this->actingAs($encoder);

    Livewire::test(Form::class)
        ->set('title', 'Working Draft on Traffic Management')
        ->set('document_type', 'Ordinance')
        ->call('saveDraft')
        ->assertRedirect(route('documents.index'));

    $this->assertDatabaseHas('documents', [
        'title' => 'Working Draft on Traffic Management',
        'status' => 'Draft',
    ]);
});

test('locked documents cannot be edited by non-admins', function () {
    $encoder = User::factory()->create(['role' => 'encoder']);
    $admin = User::factory()->create(['role' => 'administrator']);

    $enactedDoc = Document::factory()->create([
        'document_type' => 'Ordinance',
        'status' => 'Enacted',
    ]);

    $this->actingAs($encoder);
    Livewire::test(Form::class, ['document' => $enactedDoc])
        ->assertForbidden();

    $this->actingAs($admin);
    Livewire::test(Form::class, ['document' => $enactedDoc])
        ->assertOk();
});

test('status transitions and history can be recorded', function () {
    $admin = User::factory()->create(['role' => 'administrator']);
    $doc = Document::factory()->create([
        'document_type' => 'Ordinance',
        'status' => 'Filed',
    ]);

    $this->actingAs($admin);

    Livewire::test(Show::class, ['document' => $doc])
        ->set('newStatus', 'First Reading')
        ->set('date_calendared', '2026-06-01')
        ->set('statusRemarks', 'Read during regular session')
        ->call('changeStatus');

    $this->assertDatabaseHas('documents', [
        'document_id' => $doc->document_id,
        'status' => 'First Reading',
    ]);

    expect($doc->fresh()->date_calendared->format('Y-m-d'))->toBe('2026-06-01');

    $this->assertDatabaseHas('status_history', [
        'document_id' => $doc->document_id,
        'old_status' => 'Filed',
        'new_status' => 'First Reading',
    ]);
});

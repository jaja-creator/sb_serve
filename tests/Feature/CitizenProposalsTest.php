<?php

use App\Livewire\CitizenProposals\Index;
use App\Livewire\Public\Propose;
use App\Mail\CitizenProposalStatusUpdated;
use App\Models\CitizenProposal;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('citizens can submit a proposal successfully', function () {
    Mail::fake();

    Livewire::test(Propose::class)
        ->set('proposal_type', 'Ordinance')
        ->set('title', 'Proposed Community Garden Act')
        ->set('description', 'This is a detailed rationale proposing urban gardening in public vacant lots.')
        ->set('proposer_name', 'Maria Makiling')
        ->set('proposer_email', 'maria@example.com')
        ->set('proposer_barangay', 'Centro 01')
        ->call('submit')
        ->assertSet('submitted', true);

    $this->assertDatabaseHas('citizen_proposals', [
        'title' => 'Proposed Community Garden Act',
        'proposer_name' => 'Maria Makiling',
        'status' => 'Pending',
    ]);
});

test('bot submissions with honeypot are dropped without saving', function () {
    Mail::fake();

    Livewire::test(Propose::class)
        ->set('website', 'http://spam-link.ru')
        ->set('title', 'Buy Cheap Meds Online')
        ->set('description', 'Spam content that should not be saved in database.')
        ->call('submit')
        ->assertSet('submitted', true);

    $this->assertDatabaseMissing('citizen_proposals', [
        'title' => 'Buy Cheap Meds Online',
    ]);
});

test('unauthorized users cannot access citizen proposals index', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    $this->actingAs($viewer);

    Livewire::test(Index::class)->assertForbidden();

    $this->post(route('logout'));
    $this->get(route('citizen-proposals.index'))->assertRedirect(route('login'));
});

test('admins can update proposal status with remarks and notify the citizen', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'administrator']);
    $proposal = CitizenProposal::factory()->create([
        'status' => 'Pending',
        'proposer_email' => 'citizen@example.com',
    ]);

    $this->actingAs($admin);

    Livewire::test(Index::class)
        ->call('updateStatus', $proposal->proposal_id, 'Forwarded to SB', 'Endorsed for 1st Reading in regular session.');

    $fresh = $proposal->fresh();
    expect($fresh->status)->toBe('Forwarded to SB');
    expect($fresh->reviewed_by)->toBe($admin->id);
    expect($fresh->admin_remarks)->toBe('Endorsed for 1st Reading in regular session.');

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $admin->id,
        'action' => "Marked citizen proposal '{$proposal->title}' as Forwarded to SB",
    ]);

    Mail::assertSent(CitizenProposalStatusUpdated::class, function ($mail) use ($proposal) {
        return $mail->hasTo($proposal->proposer_email);
    });
});

test('citizens can track their submitted proposal by reference and email', function () {
    $proposal = CitizenProposal::factory()->create([
        'title' => 'Drainage Infrastructure Improvement',
        'proposer_email' => 'citizen.allacapan@example.com',
        'status' => 'Under Review',
        'admin_remarks' => 'Referred to Committee on Public Works.',
    ]);

    Livewire::test(Propose::class)
        ->set('activeTab', 'track')
        ->set('trackReference', 'SB-P-'.$proposal->proposal_id)
        ->set('trackEmail', 'citizen.allacapan@example.com')
        ->call('track')
        ->assertSee('Drainage Infrastructure Improvement')
        ->assertSee('Under Review')
        ->assertSee('Referred to Committee on Public Works.');
});

test('proposal tracking rejects non-matching email address', function () {
    $proposal = CitizenProposal::factory()->create([
        'title' => 'Tree Planting Ordinance',
        'proposer_email' => 'realowner@example.com',
        'status' => 'Pending',
    ]);

    Livewire::test(Propose::class)
        ->set('activeTab', 'track')
        ->set('trackReference', (string) $proposal->proposal_id)
        ->set('trackEmail', 'wrongemail@example.com')
        ->call('track')
        ->assertSee('No proposal was found matching Reference No.');
});

<?php

use App\Livewire\AuditLogs\Index as AuditLogsIndex;
use App\Livewire\Dashboard;
use App\Livewire\Documents\Form as DocumentsForm;
use App\Livewire\Documents\Index as DocumentsIndex;
use App\Livewire\Documents\Show as DocumentsShow;
use App\Livewire\Reports\Search as ReportsSearch;
use App\Livewire\Users\Form as UsersForm;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\AlphabeticalIndex;
use App\Livewire\Backup;
use App\Livewire\Settings;
use App\Livewire\Documents\Trash as DocumentsTrash;
use Illuminate\Support\Facades\Route;
use App\Livewire\Public\Home as PublicHome;
use App\Livewire\Public\Documents\Index as PublicDocumentsIndex;
use App\Livewire\Public\Documents\Show as PublicDocumentsShow;
use App\Livewire\Public\Propose as PublicPropose;
use App\Livewire\CitizenProposals\Index as CitizenProposalsIndex;

Route::livewire('/', PublicHome::class)->name('home');
Route::livewire('/browse', PublicDocumentsIndex::class)->name('public.documents.index');
Route::livewire('/browse/{document}', PublicDocumentsShow::class)->name('public.documents.show');
Route::livewire('/propose', PublicPropose::class)->name('public.propose');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');

    Route::livewire('/documents', DocumentsIndex::class)->name('documents.index');
    Route::livewire('/documents/create', DocumentsForm::class)->name('documents.create');
    Route::livewire('/documents/trash', DocumentsTrash::class)->name('documents.trash');
    Route::livewire('/documents/{document}', DocumentsShow::class)->name('documents.show');
    Route::livewire('/documents/{document}/edit', DocumentsForm::class)->name('documents.edit');
    
    Route::livewire('/users', UsersIndex::class)->name('users.index');
    Route::livewire('/users/create', UsersForm::class)->name('users.create');
    Route::livewire('/users/{user}/edit', UsersForm::class)->name('users.edit');

    Route::livewire('/reports', ReportsSearch::class)->name('reports.search');

    Route::livewire('/audit-logs', AuditLogsIndex::class)->name('audit-logs.index');

    Route::livewire('/topic-index', AlphabeticalIndex::class)->name('topic-index');

    Route::livewire('/backup', Backup::class)->name('backup.index');

    Route::livewire('/settings/system', Settings::class)->name('settings.system');

   Route::livewire('/citizen-proposals', CitizenProposalsIndex::class)->name('citizen-proposals.index');
});

require __DIR__.'/settings.php';

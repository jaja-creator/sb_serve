<?php

namespace Database\Seeders;

use App\Models\Amendment;
use App\Models\CommitteeReport;
use App\Models\Document;
use App\Models\DocumentLink;
use App\Models\Ordinance;
use App\Models\Resolution;
use App\Models\StatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users ---
        $admin = User::create([
            'name' => 'Maria Santos',
            'username' => 'msantos',
            'email' => 'admin@sbserve.test',
            'password' => Hash::make('password'),
            'role' => 'administrator',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $encoder = User::create([
            'name' => 'Juan Dela Cruz',
            'username' => 'jdelacruz',
            'email' => 'encoder@sbserve.test',
            'password' => Hash::make('password'),
            'role' => 'encoder',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $viewer = User::create([
            'name' => 'Councilor Pedro Reyes',
            'username' => 'preyes',
            'email' => 'viewer@sbserve.test',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // --- Document 1: Ordinance, Enacted, full lifecycle ---
        $doc1 = Document::create([
            'series_number' => 'SB-2026-001',
            'title' => 'An Ordinance Regulating Solid Waste Management in the Municipality of Allacapan',
            'document_type' => 'Ordinance',
            'subject_matter' => 'Waste Management',
            'sponsor' => 'SB Member Ramon Villanueva',
            'status' => 'Enacted',
            'date_filed' => now()->subDays(45),
            'date_approved_mayor' => now()->subDays(5),
            'encoded_by' => $encoder->id,
        ]);

        Ordinance::create([
            'document_id' => $doc1->document_id,
            'stage' => 'Final',
            'version_number' => '2.0',
            'author' => 'SB Member Ramon Villanueva',
            'committee_referred' => 'Committee on Environment and Sanitation',
            'date_prepared' => now()->subDays(45),
            'date_enacted' => now()->subDays(5),
            'implementing_agency' => 'Municipal Environment and Natural Resources Office',
            'target_beneficiaries' => 'Residents of Allacapan',
            'remarks' => 'Approved after two rounds of committee review.',
            'encoded_by' => $encoder->id,
        ]);

        CommitteeReport::create([
            'document_id' => $doc1->document_id,
            'committee_name' => 'Committee on Environment and Sanitation',
            'report_title' => 'Report on SB-2026-001',
            'report_date' => now()->subDays(30),
            'recommendation' => 'For Approval',
            'findings' => 'Committee finds the ordinance sound and recommends approval by the Sanggunian.',
            'prepared_by' => $encoder->id,
        ]);

        StatusHistory::create([
            'document_id' => $doc1->document_id,
            'old_status' => 'Filed',
            'new_status' => 'Under Committee Review',
            'remarks' => 'Referred to committee for evaluation.',
            'changed_by' => $encoder->id,
            'changed_at' => now()->subDays(40),
        ]);
        StatusHistory::create([
            'document_id' => $doc1->document_id,
            'old_status' => 'Under Committee Review',
            'new_status' => 'For Plenary Deliberation',
            'remarks' => 'Committee report submitted and endorsed.',
            'changed_by' => $encoder->id,
            'changed_at' => now()->subDays(25),
        ]);
        StatusHistory::create([
            'document_id' => $doc1->document_id,
            'old_status' => 'For Plenary Deliberation',
            'new_status' => 'Approved',
            'remarks' => 'Approved on third reading.',
            'changed_by' => $admin->id,
            'changed_at' => now()->subDays(10),
        ]);
        StatusHistory::create([
            'document_id' => $doc1->document_id,
            'old_status' => 'Approved',
            'new_status' => 'Enacted',
            'remarks' => 'Signed by the Municipal Mayor.',
            'changed_by' => $admin->id,
            'changed_at' => now()->subDays(5),
        ]);

        DocumentLink::create([
            'document_id' => $doc1->document_id,
            'link_type' => 'Legal Basis',
            'reference_title' => 'Republic Act 9003 - Ecological Solid Waste Management Act',
            'reference_number' => 'RA 9003',
            'added_by' => $encoder->id,
        ]);

        Amendment::create([
            'document_id' => $doc1->document_id,
            'section_amended' => 'Section 5',
            'amendment_description' => 'Clarify penalty schedule for repeat violations.',
            'amendment_date' => now()->subDays(15),
            'amendment_status' => 'Approved',
            'submitted_by' => $encoder->id,
            'reviewed_by' => $admin->id,
        ]);

        // --- Document 2: Resolution, Filed (early stage) ---
        $doc2 = Document::create([
            'series_number' => 'SB-2026-002',
            'title' => 'A Resolution Requesting the Department of Public Works and Highways for Road Repair Along Barangay Poblacion',
            'document_type' => 'Resolution',
            'subject_matter' => 'Infrastructure',
            'sponsor' => 'SB Member Ana Marquez',
            'status' => 'Filed',
            'date_filed' => now()->subDays(3),
            'encoded_by' => $encoder->id,
        ]);

        StatusHistory::create([
            'document_id' => $doc2->document_id,
            'old_status' => null,
            'new_status' => 'Filed',
            'remarks' => 'Initial filing.',
            'changed_by' => $encoder->id,
            'changed_at' => now()->subDays(3),
        ]);

        // --- Document 3: Ordinance, Under Committee Review ---
        $doc3 = Document::create([
            'series_number' => 'SB-2026-003',
            'title' => 'An Ordinance Establishing a Youth Development Program for Out-of-School Youth',
            'document_type' => 'Ordinance',
            'subject_matter' => 'Youth Development',
            'sponsor' => 'SB Member Ramon Villanueva',
            'status' => 'Under Committee Review',
            'date_filed' => now()->subDays(20),
            'encoded_by' => $encoder->id,
        ]);

        Ordinance::create([
            'document_id' => $doc3->document_id,
            'stage' => 'Committee Review',
            'version_number' => '1.0',
            'author' => 'SB Member Ramon Villanueva',
            'committee_referred' => 'Committee on Youth and Sports Development',
            'date_prepared' => now()->subDays(20),
            'implementing_agency' => 'Municipal Social Welfare and Development Office',
            'target_beneficiaries' => 'Out-of-school youth of Allacapan',
            'encoded_by' => $encoder->id,
        ]);

        StatusHistory::create([
            'document_id' => $doc3->document_id,
            'old_status' => 'Filed',
            'new_status' => 'Under Committee Review',
            'remarks' => 'Referred to committee.',
            'changed_by' => $encoder->id,
            'changed_at' => now()->subDays(18),
        ]);

        // --- Document 4: Resolution, Enacted ---
        $doc4 = Document::create([
            'series_number' => 'SB-2026-004',
            'title' => 'A Resolution Expressing Support for the Annual Allacapan Founding Anniversary Celebration',
            'document_type' => 'Resolution',
            'subject_matter' => 'Community Events',
            'sponsor' => 'SB Member Ana Marquez',
            'status' => 'Enacted',
            'date_filed' => now()->subDays(60),
            'date_approved_mayor' => now()->subDays(50),
            'encoded_by' => $admin->id,
        ]);

        Resolution::create([
            'document_id' => $doc4->document_id,
            'stage' => 'Final',
            'version_number' => '1.0',
            'author' => 'SB Member Ana Marquez',
            'purpose' => 'To formally support and allocate resources for the town founding anniversary.',
            'date_prepared' => now()->subDays(60),
            'date_enacted' => now()->subDays(50),
            'implementing_agency' => 'Municipal Tourism Office',
            'encoded_by' => $admin->id,
        ]);

        // --- Document 5: Ordinance, Vetoed (edge case for demo) ---
        $doc5 = Document::create([
            'series_number' => 'SB-2026-005',
            'title' => 'An Ordinance Increasing Business Permit Fees for Commercial Establishments',
            'document_type' => 'Ordinance',
            'subject_matter' => 'Business Regulation',
            'sponsor' => 'SB Member Ramon Villanueva',
            'status' => 'Vetoed',
            'date_filed' => now()->subDays(35),
            'encoded_by' => $encoder->id,
        ]);

        StatusHistory::create([
            'document_id' => $doc5->document_id,
            'old_status' => 'Approved',
            'new_status' => 'Vetoed',
            'remarks' => 'Vetoed by the Municipal Mayor pending further economic study.',
            'changed_by' => $admin->id,
            'changed_at' => now()->subDays(8),
        ]);
    }
}

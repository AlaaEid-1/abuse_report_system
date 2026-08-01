<?php

namespace Database\Seeders;

use App\Enums\AuthorType;
use App\Enums\ReportSeverity;
use App\Enums\ReportStatus;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportFile;
use App\Models\ReportUpdate;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $admin = User::where('email', 'admin@safevoice.org')->first();
        $investigator = User::where('email', 'investigator@safevoice.org')->first();

        if ($categories->isEmpty()) {
            return;
        }

        // Sample Report 1: Pending Financial Fraud
        $code1 = 'SV-8K9M-3P2Q-7W4X';
        $report1 = Report::create([
            'category_id' => $categories->where('slug', 'financial-fraud')->first()->id,
            'tracking_code' => $code1,
            'tracking_hash' => hash('sha256', $code1),
            'title' => 'Unauthorized Expense Reimbursements in Regional Accounting Branch',
            'description' => 'Discovered a pattern of falsified travel vouchers submitted over the last two quarters amounting to approximately $45,000.',
            'incident_date' => now()->subDays(12)->toDateString(),
            'incident_location' => 'Building B, 3rd Floor Accounting Dept',
            'severity' => ReportSeverity::HIGH,
            'status' => ReportStatus::PENDING,
            'assigned_admin_id' => null,
            'internal_notes' => null,
        ]);

        ActivityLog::create([
            'user_id' => null,
            'report_id' => $report1->id,
            'action' => 'report.submitted',
            'description' => 'Anonymous report submitted.',
            'properties' => ['category' => 'Financial Fraud'],
        ]);

        // Sample Report 2: Under Investigation Harassment
        $code2 = 'SV-2M4N-9P5R-1W8Z';
        $report2 = Report::create([
            'category_id' => $categories->where('slug', 'harassment-bullying')->first()->id,
            'tracking_code' => $code2,
            'tracking_hash' => hash('sha256', $code2),
            'title' => 'Repeated Intimidation and Verbal Hostility by Supervisor',
            'description' => 'Department supervisor has been verbally abusing junior team members during weekly syncs, creating a hostile environment.',
            'incident_date' => now()->subDays(5)->toDateString(),
            'incident_location' => 'Main Office Annex & Virtual Meetings',
            'severity' => ReportSeverity::CRITICAL,
            'status' => ReportStatus::INVESTIGATING,
            'assigned_admin_id' => $investigator?->id,
            'internal_notes' => 'Preliminary interviews scheduled with team members.',
        ]);

        ReportUpdate::create([
            'report_id' => $report2->id,
            'author_type' => AuthorType::ADMIN,
            'user_id' => $investigator?->id,
            'message' => 'Thank you for reporting. Your case has been assigned to an investigator and active enquiry is underway.',
            'is_public' => true,
        ]);

        ReportFile::create([
            'report_id' => $report2->id,
            'original_name' => 'email_thread_evidence.pdf',
            'stored_path' => 'evidence/sample_evidence_1.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 1024500,
        ]);

        ActivityLog::create([
            'user_id' => $investigator?->id,
            'report_id' => $report2->id,
            'action' => 'report.status_changed',
            'description' => 'Status changed to Investigating.',
            'properties' => ['old_status' => 'pending', 'new_status' => 'investigating'],
        ]);

        // Sample Report 3: Resolved Safety Incident
        $code3 = 'SV-5T7U-1V9W-4X2Y';
        $report3 = Report::create([
            'category_id' => $categories->where('slug', 'safety-health-hazard')->first()->id,
            'tracking_code' => $code3,
            'tracking_hash' => hash('sha256', $code3),
            'title' => 'Exposed High-Voltage Wiring in East Warehouse Bay 4',
            'description' => 'Damaged electrical conduit posing imminent shock hazard near loading dock 4.',
            'incident_date' => now()->subDays(20)->toDateString(),
            'incident_location' => 'East Warehouse Bay 4',
            'severity' => ReportSeverity::HIGH,
            'status' => ReportStatus::RESOLVED,
            'assigned_admin_id' => $admin?->id,
            'internal_notes' => 'Facilities team replaced damaged conduit and passed safety audit.',
        ]);

        ReportUpdate::create([
            'report_id' => $report3->id,
            'author_type' => AuthorType::ADMIN,
            'user_id' => $admin?->id,
            'message' => 'Facilities team has safely repaired the exposed wiring. Case closed.',
            'is_public' => true,
        ]);
    }
}

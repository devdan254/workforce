<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Course;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Resource;
use App\Models\Status;
use App\Models\StudentProfile;
use App\Models\StudyApplication;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\Task;
use App\Models\University;
use App\Models\User;
use App\Models\VisaApplication;
use App\Services\ApplicationStatusService;
use App\Services\DocumentVerificationService;
use App\Services\PaymentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Demo/sample data only — never call this in production. Deliberately separate
 * from DatabaseSeeder's foundation calls (roles, statuses, categories) so those
 * can be safely re-run on a real environment without this ever running alongside.
 *
 * Also acts as a live smoke test: John Kamau's journey below is driven entirely
 * through ApplicationStatusService / DocumentVerificationService / PaymentService,
 * not raw ->update() calls — if those services have a bug, this seeder fails loudly.
 */
class DemoDataSeeder extends Seeder
{
    private ApplicationStatusService $statusService;
    private DocumentVerificationService $documentService;
    private PaymentService $paymentService;

    public function __construct()
    {
        $this->statusService = new ApplicationStatusService;
        $this->documentService = new DocumentVerificationService;
        $this->paymentService = new PaymentService;
    }

    public function run(): void
    {
        $staff = $this->seedStaff();
        [$berlin, $sydney, $pretoria] = $this->seedUniversitiesAndCourses();
        $students = $this->seedStudents();

        // The three applications from the spec's "MY APPLICATIONS" example table.
        $johnsApp = $this->buildJohnKamauJourney($students['john'], $berlin['course'], $berlin['university'], $staff);
        $this->buildApplication($students['amina'], $sydney['course'], $sydney['university'], $staff, 'documents_submitted');
        $this->buildApplication($students['brian'], $pretoria['course'], $pretoria['university'], $staff, 'travel');

        $this->seedResources($staff['education']);

        $this->command?->info('Demo data seeded. Log in as:');
        $this->command?->info('  Student: john.kamau@example.com / password');
        $this->command?->info('  Education Officer: sarah@alturaworkforce.com / password');
    }

    private function seedStaff(): array
    {
        $staff = [];

        $roster = [
            'education' => ['Sarah Njoroge', 'sarah@alturaworkforce.com', 'education_officer'],
            'finance' => ['Peter Otieno', 'finance@alturaworkforce.com', 'finance_officer'],
            'visa' => ['Grace Wambui', 'visa@alturaworkforce.com', 'visa_officer'],
            'support' => ['Daniel Mwangi', 'support@alturaworkforce.com', 'support_officer'],
            'sales' => ['Faith Achieng', 'sales@alturaworkforce.com', 'sales_officer'],
        ];

        foreach ($roster as $key => [$name, $email, $role]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make('password'), 'is_active' => true, 'email_verified_at' => now()]
            );
            $user->assignRole($role);
            $staff[$key] = $user;
        }

        return $staff;
    }

    private function seedUniversitiesAndCourses(): array
    {
        $berlin = University::firstOrCreate(
            ['name' => 'University of Berlin'],
            ['country' => 'Germany', 'city' => 'Berlin', 'is_active' => true]
        );
        $berlinCourse = Course::firstOrCreate(
            ['university_id' => $berlin->id, 'name' => 'Computer Science'],
            ['study_level' => 'bachelor', 'duration_months' => 36, 'tuition_fee' => 12000, 'currency' => 'EUR', 'is_active' => true]
        );

        $sydney = University::firstOrCreate(
            ['name' => 'University of Sydney'],
            ['country' => 'Australia', 'city' => 'Sydney', 'is_active' => true]
        );
        $sydneyCourse = Course::firstOrCreate(
            ['university_id' => $sydney->id, 'name' => 'Business'],
            ['study_level' => 'bachelor', 'duration_months' => 36, 'tuition_fee' => 28000, 'currency' => 'AUD', 'is_active' => true]
        );

        $pretoria = University::firstOrCreate(
            ['name' => 'University of Pretoria'],
            ['country' => 'South Africa', 'city' => 'Pretoria', 'is_active' => true]
        );
        $pretoriaCourse = Course::firstOrCreate(
            ['university_id' => $pretoria->id, 'name' => 'Engineering'],
            ['study_level' => 'bachelor', 'duration_months' => 48, 'tuition_fee' => 90000, 'currency' => 'ZAR', 'is_active' => true]
        );

        return [
            ['university' => $berlin, 'course' => $berlinCourse],
            ['university' => $sydney, 'course' => $sydneyCourse],
            ['university' => $pretoria, 'course' => $pretoriaCourse],
        ];
    }

    private function seedStudents(): array
    {
        $students = [];

        foreach ([
            'john' => ['John Kamau', 'john.kamau@example.com'],
            'amina' => ['Amina Hassan', 'amina.hassan@example.com'],
            'brian' => ['Brian Otieno', 'brian.otieno@example.com'],
        ] as $key => [$name, $email]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make('password'), 'is_active' => true, 'email_verified_at' => now()]
            );
            $user->assignRole('student');

            StudentProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nationality' => 'Kenyan',
                    'country' => 'Kenya',
                    'city' => 'Nairobi',
                    'highest_qualification' => 'High School',
                    'passport_number' => 'P'.random_int(10000000, 99999999),
                    'passport_expiry_date' => now()->addYears(5),
                ]
            );

            $students[$key] = $user;
        }

        return $students;
    }

    /**
     * The spec's named end-to-end test student — driven through the full journey
     * to "admission_accepted" (application) and "documents_verified" (visa, i.e. "Processing"),
     * matching the dashboard mock: Application "In Progress", Visa "Processing",
     * Documents 8/2, Financial 250k total / 180k paid / 70k balance.
     */
    private function buildJohnKamauJourney(User $john, Course $course, University $university, array $staff): StudyApplication
    {
        $educationOfficer = $staff['education'];
        $financeOfficer = $staff['finance'];
        $visaOfficer = $staff['visa'];

        $startStatus = Status::where('type', 'application')->where('slug', 'application_started')->first();

        $application = StudyApplication::create([
            'student_id' => $john->id,
            'university_id' => $university->id,
            'course_id' => $course->id,
            'status_id' => $startStatus->id,
            'intake' => 'September 2026',
            'application_deadline' => now()->addMonths(2),
            'assigned_officer_id' => $educationOfficer->id,
            'application_fee' => 50000,
            'tuition_fee' => 130000,
            'service_fee' => 70000,
            'currency' => 'KES',
            'submitted_at' => now()->subMonths(2),
        ]);

        // Walk it through the happy path up to "Admission Accepted" (== dashboard's "In Progress").
        foreach (['documents_submitted', 'documents_verified', 'university_application_submitted', 'admission_pending', 'admission_received', 'admission_accepted'] as $slug) {
            $status = Status::where('type', 'application')->where('slug', $slug)->first();
            $this->statusService->transition($application, $status->id, $educationOfficer, "Advanced to {$status->label}.");
        }

        // Documents: 10 total, 8 verified, 2 still required — matches "8 Uploaded / 2 Pending".
        $categories = DocumentCategory::all()->keyBy('slug');
        $verifiedDocs = [
            ['Passport', 'identity-documents'],
            ['National ID', 'identity-documents'],
            ['Academic Certificate', 'academic-documents'],
            ['Academic Transcript', 'academic-documents'],
            ['CV', 'academic-documents'],
            ['Recommendation Letter', 'academic-documents'],
            ['Personal Statement', 'academic-documents'],
            ['Passport Photo', 'identity-documents'],
        ];
        foreach ($verifiedDocs as [$name, $categorySlug]) {
            $document = Document::create([
                'student_id' => $john->id,
                'study_application_id' => $application->id,
                'document_category_id' => $categories[$categorySlug]->id,
                'name' => $name,
                'status' => 'required',
            ]);
            $this->documentService->markUploaded($document, "documents/demo/{$application->id}-{$document->id}.pdf", 'application/pdf', 245_000);
            $this->documentService->verify($document, $educationOfficer, 'Looks good.');
        }
        foreach ([['Bank Statement', 'financial-documents'], ['Medical Certificate', 'other-documents']] as [$name, $categorySlug]) {
            Document::create([
                'student_id' => $john->id,
                'study_application_id' => $application->id,
                'document_category_id' => $categories[$categorySlug]->id,
                'name' => $name,
                'status' => 'required',
            ]);
        }

        // Visa application, driven to "Processing" (documents_verified).
        $visaStart = Status::where('type', 'visa')->where('slug', 'documents_submitted')->first();
        $visa = VisaApplication::create([
            'study_application_id' => $application->id,
            'destination_country' => 'Germany',
            'status_id' => $visaStart->id,
        ]);
        $visaVerified = Status::where('type', 'visa')->where('slug', 'documents_verified')->first();
        $this->statusService->transition($visa, $visaVerified->id, $visaOfficer, 'Visa documents verified, preparing application.');

        // Finance: one invoice, 4 line items totalling 250,000 KES, 3 confirmed payments = 180,000 paid.
        $invoice = Invoice::create([
            'student_id' => $john->id,
            'study_application_id' => $application->id,
            'description' => 'University of Berlin — Computer Science',
            'currency' => 'KES',
            'subtotal' => 250000,
            'tax' => 0,
            'total' => 250000,
            'status' => 'sent',
            'due_date' => now()->addMonth(),
            'sent_at' => now()->subMonths(2),
        ]);
        $lineItems = [
            ['Application Fee', 50000, now()->subDays(40)],
            ['University Processing', 80000, now()->subDays(35)],
            ['Visa Processing', 50000, now()->subDays(20)],
            ['Travel Preparation', 70000, null], // stays unpaid -> the 70,000 balance
        ];
        foreach ($lineItems as [$description, $amount, $paidOn]) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $description,
                'quantity' => 1,
                'unit_price' => $amount,
                'line_total' => $amount,
            ]);

            if ($paidOn) {
                $payment = $this->paymentService->recordPayment($invoice, $amount, 'mpesa', $financeOfficer);
                $payment->update(['paid_at' => $paidOn]);
                $this->paymentService->confirmPayment($payment, $financeOfficer);
            }
        }

        // Appointments — matches the dashboard's "Upcoming Appointments" example exactly.
        Appointment::create([
            'student_id' => $john->id, 'staff_id' => $educationOfficer->id, 'study_application_id' => $application->id,
            'type' => 'Student Consultation', 'mode' => 'video', 'scheduled_at' => now()->addDays(2), 'status' => 'confirmed',
        ]);
        Appointment::create([
            'student_id' => $john->id, 'staff_id' => $visaOfficer->id, 'study_application_id' => $application->id,
            'type' => 'Visa Interview', 'mode' => 'physical', 'scheduled_at' => now()->addDays(8), 'status' => 'confirmed',
        ]);
        Appointment::create([
            'student_id' => $john->id, 'staff_id' => $educationOfficer->id, 'study_application_id' => $application->id,
            'type' => 'Pre-Departure Briefing', 'mode' => 'physical', 'scheduled_at' => now()->addDays(15), 'status' => 'requested',
        ]);

        // One open support ticket + one pending task — matches spec's own examples.
        $ticket = SupportTicket::create([
            'student_id' => $john->id, 'category' => 'Documents', 'priority' => 'medium',
            'subject' => 'Question about bank statement format', 'status' => 'open', 'assigned_to' => $staff['support']->id,
        ]);
        SupportMessage::create([
            'support_ticket_id' => $ticket->id, 'user_id' => $john->id,
            'body' => 'Does the bank statement need to be in English, or is a certified translation okay?',
        ]);

        Task::create([
            'taskable_type' => StudentProfile::class,
            'taskable_id' => $john->studentProfile->id,
            'title' => "Verify John's remaining documents (bank statement, medical certificate)",
            'assigned_to' => $educationOfficer->id,
            'due_date' => now()->addDays(3),
            'priority' => 'high',
            'status' => 'pending',
            'created_by' => $staff['education']->id,
        ]);

        return $application;
    }

    /**
     * Lighter-weight applications for the other two demo students, just enough
     * variety for the Admin "Students" list to look like a real operational queue.
     */
    private function buildApplication(User $student, Course $course, University $university, array $staff, string $targetStatusSlug): StudyApplication
    {
        $startStatus = Status::where('type', 'application')->where('slug', 'application_started')->first();

        $application = StudyApplication::create([
            'student_id' => $student->id,
            'university_id' => $university->id,
            'course_id' => $course->id,
            'status_id' => $startStatus->id,
            'intake' => 'January 2027',
            'assigned_officer_id' => $staff['education']->id,
            'application_fee' => 30000,
            'tuition_fee' => 100000,
            'service_fee' => 40000,
            'currency' => 'KES',
            'submitted_at' => now()->subWeeks(3),
        ]);

        $path = ['documents_submitted', 'documents_verified', 'university_application_submitted', 'admission_pending', 'admission_received', 'admission_accepted', 'visa_preparation', 'visa_application', 'visa_appointment', 'visa_decision', 'travel_preparation', 'travel'];
        foreach ($path as $slug) {
            $status = Status::where('type', 'application')->where('slug', $slug)->first();
            $this->statusService->transition($application, $status->id, $staff['education'], null);
            if ($slug === $targetStatusSlug) {
                break;
            }
        }

        return $application;
    }

    /**
     * A handful of published resources so the Resources page isn't empty
     * on first login. Admin-manageable in a later deliverable — for now
     * these exist purely as seed data.
     */
    private function seedResources(User $author): void
    {
        $resources = [
            ['Study Abroad Guide', 'Getting Started', 'guide', 'A complete overview of how the Altura study abroad process works, from consultation to travel.'],
            ['University Selection Guide', 'Getting Started', 'guide', 'How to choose the right university and course for your goals, budget and career plans.'],
            ['Visa Preparation Guide', 'Visa', 'guide', 'What to expect during the student visa process, and how to prepare for your embassy appointment.'],
            ['Student Accommodation Guide', 'Travel', 'guide', 'Options for on-campus and off-campus housing, and how to book before you arrive.'],
            ['Travel Preparation Guide', 'Travel', 'guide', 'Flights, packing, and what to organize in the weeks before you depart.'],
            ['Pre-Departure Checklist', 'Travel', 'checklist', 'Everything you need to confirm before flying out — documents, finances, and logistics.'],
            ['Frequently Asked Questions', 'Getting Started', 'faq', 'Answers to the most common questions students ask during their application.'],
        ];

        foreach ($resources as [$title, $category, $type, $body]) {
            Resource::firstOrCreate(
                ['title' => $title],
                [
                    'category' => $category,
                    'type' => $type,
                    'body' => $body,
                    'is_published' => true,
                    'created_by' => $author->id,
                ]
            );
        }
    }
}

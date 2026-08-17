<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Job Seeker demo data — kept separate from DemoDataSeeder (same reasoning
 * as that seeder's own separation from the foundation seeders): run only
 * when you actually want sample data, never automatically, so production
 * seeding never risks pulling in demo rows.
 *
 * Job postings here are the real job types from Altura's own marketing
 * material (the job poster images from earlier in this project) turned into
 * actual database rows — not invented placeholder jobs.
 */
class JobSeekerDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $staff = $this->seedStaff();
        $categories = $this->seedCategories();
        $this->seedPostings($categories, $staff);
        $this->seedResources($staff['recruitment']);

        $this->command?->info('Job Seeker demo data seeded.');
        $this->command?->info('  Recruitment Officer: james@alturaworkforce.com / password');
        $this->command?->info('  HR/Outsourcing Officer: mercy@alturaworkforce.com / password');
    }

    /**
     * Job-Seeker-specific resource content — distinct titles from Student's
     * own seedResources() in DemoDataSeeder, so both coexist in the same
     * shared `resources` table without collision (Resource has no owner
     * column; it's a global catalog either way).
     */
    private function seedResources(\App\Models\User $author): void
    {
        $resources = [
            ['International CV Guide', 'Getting Started', 'guide', 'How to format your CV for international employers — what to include, what to leave out, and common mistakes to avoid.'],
            ['Interview Preparation Guide', 'Getting Started', 'guide', 'Common interview questions for overseas roles and how to answer them confidently.'],
            ['Overseas Job Search Guide', 'Getting Started', 'guide', 'How the Altura recruitment process works, from application to deployment.'],
            ['Visa Preparation Guide', 'Visa', 'guide', 'What to expect during your work visa process, and how to prepare for your embassy appointment.'],
            ['Pre-Departure Checklist', 'Travel', 'checklist', 'Everything to confirm before you fly out — documents, finances, and logistics.'],
            ['Workplace Culture Guide', 'Getting Started', 'guide', 'What to expect from workplace norms and etiquette in your destination country.'],
            ['Salary & Cost of Living Guide', 'Getting Started', 'guide', 'Typical salary ranges and cost of living for common Altura placement countries.'],
            ['Frequently Asked Questions', 'Getting Started', 'faq', 'Answers to the most common questions candidates ask during the recruitment process.'],
        ];

        foreach ($resources as [$title, $category, $type, $body]) {
            \App\Models\Resource::firstOrCreate(
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

    private function seedStaff(): array
    {
        $staff = [];

        $roster = [
            'recruitment' => ['James Mutua', 'james@alturaworkforce.com', 'recruitment_officer'],
            'hr' => ['Mercy Wanjiru', 'mercy@alturaworkforce.com', 'hr_outsourcing_officer'],
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

    private function seedCategories(): array
    {
        $names = ['Healthcare', 'Construction', 'Hospitality', 'Domestic Work', 'Security', 'Logistics & Driving'];

        $categories = [];
        foreach ($names as $name) {
            $categories[$name] = JobCategory::firstOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name]
            );
        }

        return $categories;
    }

    private function seedPostings(array $categories, array $staff): void
    {
        $postedBy = $staff['recruitment']->id;

        $postings = [
            [
                'title' => 'Registered Nurse', 'category' => 'Healthcare', 'country' => 'Germany', 'city' => 'Berlin',
                'currency' => 'EUR', 'salary_min' => 2500, 'salary_max' => 3000, 'vacancies' => 8,
                'employment_type' => 'contract', 'experience_required' => '2+ years post-qualification',
                'education_requirement' => "Bachelor's Degree in Nursing",
                'description' => 'Altura is recruiting Registered Nurses for hospitals and care facilities across Germany. Full relocation support provided.',
                'requirements' => 'Valid nursing license, German language B1 or willingness to train, clean police record.',
                'benefits' => 'Relocation support, accommodation for first 3 months, health insurance, annual leave.',
                'accommodation' => true, 'meals' => false, 'visa_support' => true, 'air_ticket' => true,
                'working_hours' => '40 hrs/week', 'deadline_days' => 45, 'featured' => true,
            ],
            [
                'title' => 'Caregiver', 'category' => 'Healthcare', 'country' => 'United Kingdom', 'city' => 'London',
                'currency' => 'GBP', 'salary_min' => 2200, 'salary_max' => 2600, 'vacancies' => 12,
                'employment_type' => 'permanent', 'experience_required' => '1+ years caregiving experience',
                'education_requirement' => 'Diploma in Caregiving or equivalent',
                'description' => 'Care assistant roles supporting elderly and vulnerable residents in UK care homes.',
                'requirements' => 'Caregiving certificate, compassionate disposition, ability to pass UK right-to-work checks.',
                'benefits' => 'Sponsored work visa, paid training, pension scheme.',
                'accommodation' => false, 'meals' => true, 'visa_support' => true, 'air_ticket' => true,
                'working_hours' => '37.5 hrs/week', 'deadline_days' => 60, 'featured' => true,
            ],
            [
                'title' => 'Construction Worker', 'category' => 'Construction', 'country' => 'UAE', 'city' => 'Dubai',
                'currency' => 'AED', 'salary_min' => 1800, 'salary_max' => 2400, 'vacancies' => 25,
                'employment_type' => 'contract', 'experience_required' => '1+ years on-site experience',
                'education_requirement' => 'Not required',
                'description' => 'General construction labourers and skilled tradespeople needed for major Dubai infrastructure projects.',
                'requirements' => 'Physical fitness, willingness to work outdoors, basic safety certification an advantage.',
                'benefits' => 'Free accommodation, meals provided, overtime pay available.',
                'accommodation' => true, 'meals' => true, 'visa_support' => true, 'air_ticket' => true,
                'working_hours' => '48 hrs/week', 'deadline_days' => 30, 'featured' => false,
            ],
            [
                'title' => 'Security Officer', 'category' => 'Security', 'country' => 'Qatar', 'city' => 'Doha',
                'currency' => 'QAR', 'salary_min' => 1600, 'salary_max' => 2000, 'vacancies' => 15,
                'employment_type' => 'contract', 'experience_required' => '2+ years security experience preferred',
                'education_requirement' => 'Secondary education',
                'description' => 'Security personnel for commercial and residential sites across Doha.',
                'requirements' => 'Security training certificate an advantage, clean record, physically fit.',
                'benefits' => 'Accommodation, meals, transport to/from site.',
                'accommodation' => true, 'meals' => true, 'visa_support' => true, 'air_ticket' => true,
                'working_hours' => '48 hrs/week', 'deadline_days' => 40, 'featured' => false,
            ],
            [
                'title' => 'Driver', 'category' => 'Logistics & Driving', 'country' => 'Qatar', 'city' => 'Doha',
                'currency' => 'QAR', 'salary_min' => 1500, 'salary_max' => 1800, 'vacancies' => 10,
                'employment_type' => 'contract', 'experience_required' => '3+ years driving experience',
                'education_requirement' => 'Valid driving license (manual transmission)',
                'description' => 'Light and heavy vehicle drivers needed for logistics companies in Qatar.',
                'requirements' => 'Valid international driving permit, clean driving record, defensive driving certificate an advantage.',
                'benefits' => 'Accommodation, meals, medical insurance.',
                'accommodation' => true, 'meals' => true, 'visa_support' => true, 'air_ticket' => true,
                'working_hours' => '48 hrs/week', 'deadline_days' => 35, 'featured' => false,
            ],
            [
                'title' => 'Welder', 'category' => 'Construction', 'country' => 'Poland', 'city' => 'Warsaw',
                'currency' => 'PLN', 'salary_min' => 6500, 'salary_max' => 7500, 'vacancies' => 6,
                'employment_type' => 'contract', 'experience_required' => '3+ years welding experience',
                'education_requirement' => 'Trade certificate in Welding',
                'description' => 'Certified welders needed for manufacturing and construction projects in Poland.',
                'requirements' => 'Welding certification (MIG/TIG/Arc), portfolio of prior work, EU work eligibility support provided.',
                'benefits' => 'Accommodation, relocation bonus, EU work permit sponsorship.',
                'accommodation' => true, 'meals' => false, 'visa_support' => true, 'air_ticket' => true,
                'working_hours' => '40 hrs/week', 'deadline_days' => 50, 'featured' => true,
            ],
            [
                'title' => 'Hotel Housekeeper', 'category' => 'Hospitality', 'country' => 'UAE', 'city' => 'Dubai',
                'currency' => 'AED', 'salary_min' => 1400, 'salary_max' => 1700, 'vacancies' => 20,
                'employment_type' => 'contract', 'experience_required' => 'Entry-level welcome',
                'education_requirement' => 'Not required',
                'description' => 'Housekeeping staff for 4 and 5-star hotels across Dubai.',
                'requirements' => 'Attention to detail, ability to work shifts, hospitality experience an advantage.',
                'benefits' => 'Accommodation, meals, uniform provided, tips.',
                'accommodation' => true, 'meals' => true, 'visa_support' => true, 'air_ticket' => true,
                'working_hours' => '48 hrs/week', 'deadline_days' => 25, 'featured' => false,
            ],
            [
                'title' => 'Nurse Assistant', 'category' => 'Healthcare', 'country' => 'UAE', 'city' => 'Abu Dhabi',
                'currency' => 'AED', 'salary_min' => 5500, 'salary_max' => 6500, 'vacancies' => 9,
                'employment_type' => 'contract', 'experience_required' => '1+ years hospital or clinic experience',
                'education_requirement' => 'Certificate in Nursing Assistance',
                'description' => 'Nurse assistants needed to support medical staff in Abu Dhabi hospitals and clinics.',
                'requirements' => 'Nursing assistant certificate, DataFlow verification support provided by Altura.',
                'benefits' => 'Accommodation, medical insurance, annual flight home.',
                'accommodation' => true, 'meals' => false, 'visa_support' => true, 'air_ticket' => true,
                'working_hours' => '45 hrs/week', 'deadline_days' => 40, 'featured' => true,
            ],
        ];

        foreach ($postings as $posting) {
            JobPosting::firstOrCreate(
                ['title' => $posting['title'], 'country' => $posting['country']],
                [
                    'job_category_id' => $categories[$posting['category']]->id,
                    'city' => $posting['city'],
                    'currency' => $posting['currency'],
                    'salary_min' => $posting['salary_min'],
                    'salary_max' => $posting['salary_max'],
                    'vacancies' => $posting['vacancies'],
                    'employment_type' => $posting['employment_type'],
                    'experience_required' => $posting['experience_required'],
                    'education_requirement' => $posting['education_requirement'],
                    'description' => $posting['description'],
                    'requirements' => $posting['requirements'],
                    'benefits' => $posting['benefits'],
                    'accommodation_provided' => $posting['accommodation'],
                    'meals_provided' => $posting['meals'],
                    'visa_support_provided' => $posting['visa_support'],
                    'air_ticket_provided' => $posting['air_ticket'],
                    'working_hours' => $posting['working_hours'],
                    'application_deadline' => now()->addDays($posting['deadline_days']),
                    'status' => 'open',
                    'is_featured' => $posting['featured'],
                    'posted_by' => $postedBy,
                ]
            );
        }
    }
}

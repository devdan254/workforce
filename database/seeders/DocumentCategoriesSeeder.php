<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Identity Documents',
            'Academic Documents',
            'Financial Documents',
            'Visa Documents',
            'Travel Documents',
            'Other Documents',
            // Stage 2 — Job Seeker specific. Identity/Visa/Other above are
            // already shared/reused as-is; only these two are genuinely new.
            'Employment Documents',
            'Employer Documents',
        ];

        foreach ($categories as $name) {
            DocumentCategory::firstOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name]
            );
        }
    }
}

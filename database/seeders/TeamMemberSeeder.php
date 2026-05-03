<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['name' => 'Dr. Adaeze Okonkwo', 'role' => 'Medical Director, General Medicine', 'bio' => '20+ years leading concierge medical care in Abuja and beyond.'],
            ['name' => 'Dr. Ibrahim Bello', 'role' => 'Consultant, Geriatrics', 'bio' => 'Specialist in elderly comfort care and rehabilitative medicine.'],
            ['name' => 'Nurse Funmi Adeyemi', 'role' => 'Lead Nurse, Skilled Nursing', 'bio' => 'Expert in post-operative and tube-feeding nutrition therapy.'],
            ['name' => 'Dr. Chiamaka Eze', 'role' => 'Consultant, Paediatrics', 'bio' => 'Newborn and family-care specialist with a focus on caregiver training.'],
        ];

        foreach ($members as $i => $m) {
            TeamMember::updateOrCreate(
                ['name' => $m['name']],
                [...$m, 'sort_order' => $i + 1, 'is_active' => true],
            );
        }
    }
}

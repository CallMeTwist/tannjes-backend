<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['name' => 'Dr. Adaeze Okonkwo',     'role' => 'Medical Director, General Medicine',     'bio' => '20+ years leading concierge medical care in Abuja and beyond.',                'credentials' => 'MBBS, FWACP', 'image' => 'https://randomuser.me/api/portraits/women/68.jpg'],
            ['name' => 'Dr. Ibrahim Bello',      'role' => 'Consultant, Geriatrics',                 'bio' => 'Specialist in elderly comfort care and rehabilitative medicine.',              'credentials' => 'MBBS, MSc Geriatrics', 'image' => 'https://randomuser.me/api/portraits/men/32.jpg'],
            ['name' => 'Nurse Funmi Adeyemi',    'role' => 'Lead Nurse, Skilled Nursing',            'bio' => 'Expert in post-operative and tube-feeding nutrition therapy.',                 'credentials' => 'RN, BNSc', 'image' => 'https://randomuser.me/api/portraits/women/44.jpg'],
            ['name' => 'Dr. Chiamaka Eze',       'role' => 'Consultant, Paediatrics',                'bio' => 'Newborn and family-care specialist with a focus on caregiver training.',       'credentials' => 'MBBS, FMCPaed', 'image' => 'https://randomuser.me/api/portraits/women/65.jpg'],
            ['name' => 'Dr. Tunde Akinola',      'role' => 'Consultant, Cardiology',                 'bio' => 'Interventional cardiologist with a focus on hypertension management.',         'credentials' => 'MBBS, FACC', 'image' => 'https://randomuser.me/api/portraits/men/45.jpg'],
            ['name' => 'Dr. Ngozi Umeh',         'role' => 'Consultant, Obstetrics & Gynaecology',   'bio' => 'High-risk pregnancy and women\'s wellness expert.',                            'credentials' => 'MBBS, FWACS', 'image' => 'https://randomuser.me/api/portraits/women/22.jpg'],
            ['name' => 'Dr. Sani Garba',         'role' => 'Consultant, Internal Medicine',          'bio' => 'Diabetes and chronic disease management specialist.',                          'credentials' => 'MBBS, FMCP', 'image' => 'https://randomuser.me/api/portraits/men/12.jpg'],
            ['name' => 'Nurse Blessing Eke',     'role' => 'Senior Nurse, Home Care',                'bio' => 'Leads the home-visit nursing program with 10+ years experience.',              'credentials' => 'RN, RM', 'image' => 'https://randomuser.me/api/portraits/women/33.jpg'],
            ['name' => 'Dr. Yusuf Aliyu',        'role' => 'Consultant, Orthopaedics',               'bio' => 'Sports injury and joint replacement surgeon.',                                 'credentials' => 'MBBS, FRCS', 'image' => 'https://randomuser.me/api/portraits/men/76.jpg'],
            ['name' => 'Dr. Amaka Nwosu',        'role' => 'Consultant, Dermatology',                'bio' => 'Skin health, paediatric dermatology, and aesthetic care.',                     'credentials' => 'MBBS, FMCD', 'image' => 'https://randomuser.me/api/portraits/women/50.jpg'],
            ['name' => 'Dr. Emeka Obi',          'role' => 'Consultant, Neurology',                  'bio' => 'Stroke rehabilitation and movement disorder specialist.',                      'credentials' => 'MBBS, FMCN', 'image' => 'https://randomuser.me/api/portraits/men/52.jpg'],
            ['name' => 'Pharm. Hauwa Musa',      'role' => 'Lead Pharmacist',                        'bio' => 'Oversees medication therapy management and home delivery.',                    'credentials' => 'PharmD', 'image' => 'https://randomuser.me/api/portraits/women/14.jpg'],
            ['name' => 'Dr. Kelechi Anyanwu',    'role' => 'Consultant, Psychiatry',                 'bio' => 'Mental health and family therapy services.',                                   'credentials' => 'MBBS, FWACP (Psych)', 'image' => 'https://randomuser.me/api/portraits/men/85.jpg'],
            ['name' => 'Nurse Aisha Bala',       'role' => 'ICU Nurse Specialist',                   'bio' => 'Critical care and ventilator management at home.',                             'credentials' => 'RN, ICU Cert.', 'image' => 'https://randomuser.me/api/portraits/women/79.jpg'],
            ['name' => 'Dr. Olumide Ade',        'role' => 'Family Physician',                       'bio' => 'Holistic family medicine and preventive care.',                                'credentials' => 'MBBS, MWACP', 'image' => 'https://randomuser.me/api/portraits/men/27.jpg'],
        ];

        foreach ($members as $i => $m) {
            TeamMember::updateOrCreate(
                ['name' => $m['name']],
                [...$m, 'sort_order' => $i + 1, 'is_active' => true],
            );
        }

        // Map seeded doctors to departments by a keyword in their role.
        $map = [
            'General Medicine'              => ['General Medicine', 'Internal Medicine', 'Family Physician'],
            'Paediatrics'                  => ['Paediatrics'],
            'Nephrology'                   => ['Cardiology'], // closest available department
            'Obstetrics & Gynaecology'     => ['Obstetrics'],
            'Dermatology'                  => ['Dermatology'],
            'Neurology'                    => ['Neurology'],
            'Mental Health & Counselling'  => ['Psychiatry'],
            'General Surgery'              => ['Orthopaedics'],
            'Pharmacy'                     => ['Pharmacist'],
        ];

        foreach (\App\Models\Department::all() as $dept) {
            $keywords = $map[$dept->name] ?? [];
            if (empty($keywords)) {
                continue;
            }
            \App\Models\TeamMember::query()
                ->where(function ($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->orWhere('role', 'like', "%{$kw}%");
                    }
                })
                ->update(['department_id' => $dept->id, 'is_consultant' => true]);
        }
    }
}

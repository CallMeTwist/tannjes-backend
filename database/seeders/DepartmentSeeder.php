<?php
namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['General Medicine', 'Stethoscope', 'Comprehensive primary and internal medicine for adults across every stage of life.'],
            ['General Surgery', 'Scissors', 'Expert surgical care from routine procedures to complex operations.'],
            ['Neurology', 'Brain', 'Diagnosis and management of disorders of the brain, spine and nervous system.'],
            ['Haematology', 'Droplets', 'Specialist care for blood disorders, anaemia and clotting conditions.'],
            ['Endocrinology', 'Activity', 'Management of diabetes, thyroid and hormonal conditions.'],
            ['Paediatrics', 'Baby', 'Compassionate care for newborns, children and adolescents.'],
            ['Urology', 'Wind', 'Care for the urinary tract and male reproductive health.'],
            ['Nephrology', 'HeartPulse', 'Kidney health, hypertension and dialysis support.'],
            ['Gastroenterology', 'Pill', 'Digestive system, liver and gut health.'],
            ['Dermatology', 'Smile', 'Skin, hair and nail health for all ages.'],
            ['ENT', 'Ear', 'Ear, nose and throat diagnosis and treatment.'],
            ['Obstetrics & Gynaecology', 'Flower', "Women's health, pregnancy and reproductive care."],
            ['Laboratory', 'FlaskConical', 'Accurate, fast diagnostic testing across all specialties.'],
            ['Radiology Diagnostics', 'ScanLine', 'Advanced imaging for precise diagnosis.'],
            ['Mental Health & Counselling', 'BookHeart', 'Confidential psychiatric and therapeutic support.'],
            ['Pharmacy', 'Microscope', 'Medication therapy management and home delivery.'],
        ];

        foreach ($departments as $i => [$name, $icon, $description]) {
            Department::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'icon' => $icon,
                    'description' => $description,
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}

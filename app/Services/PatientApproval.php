<?php
namespace App\Services;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\TeamMember;

class PatientApproval
{
    public static function approve(Patient $patient, ?int $approverId): void
    {
        $patient->update([
            'status' => Patient::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        $exists = Consultation::where('patient_id', $patient->id)->exists();
        if ($exists) {
            return;
        }

        $doctorId = $patient->department_id
            ? TeamMember::where('department_id', $patient->department_id)
                ->where('is_active', true)
                ->where('is_consultant', true)
                ->orderBy('sort_order')
                ->value('id')
            : null;

        Consultation::create([
            'patient_id' => $patient->id,
            'department_id' => $patient->department_id,
            'doctor_id' => $doctorId,
            'subject' => $patient->department
                ? $patient->department->name.' consultation'
                : 'General consultation',
            'status' => 'open',
        ]);
    }
}

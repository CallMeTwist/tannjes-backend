<?php
namespace Tests\Feature;

use App\Models\Patient;
use App\Models\TestResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestResultModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_result_belongs_to_patient(): void
    {
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'approved']);
        $result = TestResult::create([
            'patient_id' => $patient->id,
            'title' => 'Full Blood Count',
            'file_path' => 'test-results/fbc.pdf',
            'result_date' => '2026-06-01',
        ]);

        $this->assertEquals($patient->id, $result->patient->id);
    }
}

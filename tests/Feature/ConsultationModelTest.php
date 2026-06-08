<?php
namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Message;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultation_has_messages(): void
    {
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'approved']);
        $consultation = Consultation::create(['patient_id' => $patient->id, 'subject' => 'General', 'status' => 'open']);
        $message = Message::create([
            'consultation_id' => $consultation->id,
            'sender_type' => 'patient',
            'sender_id' => $patient->id,
            'body' => 'Hello doctor',
        ]);

        $this->assertTrue($consultation->messages->contains($message));
        $this->assertEquals($patient->id, $consultation->patient->id);
    }
}

<?php
namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationApiTest extends TestCase
{
    use RefreshDatabase;

    private function approvedPatientWithToken(): array
    {
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'approved']);
        return [$patient, $patient->createToken('t')->plainTextToken];
    }

    public function test_lists_only_own_consultations(): void
    {
        [$patient, $token] = $this->approvedPatientWithToken();
        Consultation::create(['patient_id' => $patient->id, 'subject' => 'Mine']);
        $other = Patient::create(['name' => 'X', 'email' => 'x@e.com', 'password' => 'secret123', 'status' => 'approved']);
        Consultation::create(['patient_id' => $other->id, 'subject' => 'Theirs']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/patient/consultations')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.subject', 'Mine');
    }

    public function test_patient_can_post_a_message(): void
    {
        [$patient, $token] = $this->approvedPatientWithToken();
        $c = Consultation::create(['patient_id' => $patient->id, 'subject' => 'General']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/patient/consultations/{$c->id}/messages", ['body' => 'Hello'])
            ->assertCreated()
            ->assertJsonPath('message.body', 'Hello')
            ->assertJsonPath('message.sender_type', 'patient');

        $this->assertDatabaseHas('messages', ['consultation_id' => $c->id, 'body' => 'Hello']);
    }

    public function test_cannot_post_to_another_patients_consultation(): void
    {
        [, $token] = $this->approvedPatientWithToken();
        $other = Patient::create(['name' => 'X', 'email' => 'x@e.com', 'password' => 'secret123', 'status' => 'approved']);
        $c = Consultation::create(['patient_id' => $other->id, 'subject' => 'Theirs']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/patient/consultations/{$c->id}/messages", ['body' => 'Hi'])
            ->assertStatus(403);
    }

    public function test_unapproved_patient_is_blocked(): void
    {
        $patient = Patient::create(['name' => 'J', 'email' => 'j@e.com', 'password' => 'secret123', 'status' => 'pending_approval']);
        $token = $patient->createToken('t')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/patient/consultations')
            ->assertStatus(403);
    }
}

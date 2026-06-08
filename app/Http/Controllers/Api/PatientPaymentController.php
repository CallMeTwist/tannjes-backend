<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;

class PatientPaymentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'reference' => ['nullable', 'string', 'max:255'],
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $patient = $request->user();
        $path = $request->file('proof')->store('payment-proofs', 'public');

        Payment::create([
            'patient_id' => $patient->id,
            'amount' => $data['amount'],
            'reference' => $data['reference'] ?? null,
            'proof_path' => $path,
            'status' => 'submitted',
        ]);

        if ($patient->status === Patient::STATUS_PENDING_PAYMENT) {
            $patient->update(['status' => Patient::STATUS_PENDING_APPROVAL]);
        }

        return response()->json([
            'message' => 'Payment submitted. Awaiting approval.',
            'patient' => [
                'id' => $patient->id,
                'status' => $patient->fresh()->status,
            ],
        ]);
    }
}

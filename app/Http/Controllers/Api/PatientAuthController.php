<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PatientAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:patients,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
            'department_slug' => ['nullable', 'string', 'exists:departments,slug'],
        ]);

        $departmentId = null;
        if (! empty($data['department_slug'])) {
            $departmentId = Department::where('slug', $data['department_slug'])->value('id');
        }

        $patient = Patient::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'status' => Patient::STATUS_PENDING_PAYMENT,
            'department_id' => $departmentId,
        ]);

        return response()->json([
            'token' => $patient->createToken('patient')->plainTextToken,
            'patient' => $this->profile($patient),
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $patient = Patient::where('email', $data['email'])->first();

        if (! $patient || ! Hash::check($data['password'], $patient->password)) {
            throw ValidationException::withMessages(['email' => 'These credentials do not match our records.']);
        }

        return response()->json([
            'token' => $patient->createToken('patient')->plainTextToken,
            'patient' => $this->profile($patient),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        return $this->profile($request->user());
    }

    private function profile(Patient $patient): array
    {
        $patient->loadMissing('department');
        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'email' => $patient->email,
            'phone' => $patient->phone,
            'status' => $patient->status,
            'department' => $patient->department ? [
                'name' => $patient->department->name,
                'slug' => $patient->department->slug,
            ] : null,
        ];
    }
}

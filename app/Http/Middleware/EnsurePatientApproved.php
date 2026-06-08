<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePatientApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $patient = $request->user();

        if (! $patient || $patient->status !== \App\Models\Patient::STATUS_APPROVED) {
            return response()->json([
                'message' => 'Your account is not approved yet.',
                'status' => $patient?->status,
            ], 403);
        }

        return $next($request);
    }
}

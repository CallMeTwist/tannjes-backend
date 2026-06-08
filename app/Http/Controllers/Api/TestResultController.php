<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TestResult;
use Illuminate\Http\Request;

class TestResultController extends Controller
{
    public function index(Request $request)
    {
        $results = TestResult::query()
            ->where('patient_id', $request->user()->id)
            ->orderByDesc('result_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'description' => $r->description,
                'result_date' => optional($r->result_date)->toDateString(),
                'file_url' => rtrim(config('app.url'), '/').'/storage/'.$r->file_path,
                'created_at' => $r->created_at->toIso8601String(),
            ]);

        return ['data' => $results];
    }
}

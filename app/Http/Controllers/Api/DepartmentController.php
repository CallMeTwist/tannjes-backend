<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\TeamMember;

class DepartmentController extends Controller
{
    public function index()
    {
        return Department::query()
            ->where('is_active', true)
            ->withCount(['doctors' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($d) => [
                'name' => $d->name,
                'slug' => $d->slug,
                'description' => $d->description,
                'icon' => $d->icon,
                'doctor_count' => $d->doctors_count,
            ]);
    }

    public function show(string $slug)
    {
        $dept = Department::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $doctors = TeamMember::query()
            ->where('department_id', $dept->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m) => [
                'name' => $m->name,
                'role' => $m->role,
                'bio' => $m->bio,
                'credentials' => $m->credentials,
                'image_url' => $this->imageUrl($m->image),
                'is_consultant' => $m->is_consultant,
            ]);

        return [
            'department' => [
                'name' => $dept->name,
                'slug' => $dept->slug,
                'description' => $dept->description,
                'icon' => $dept->icon,
            ],
            'doctors' => $doctors,
        ];
    }

    private function imageUrl(?string $image): ?string
    {
        if (! $image) {
            return null;
        }
        return preg_match('#^https?://#i', $image)
            ? $image
            : rtrim(config('app.url'), '/').'/storage/'.$image;
    }
}

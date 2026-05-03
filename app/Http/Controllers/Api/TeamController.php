<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;

class TeamController extends Controller
{
    public function index()
    {
        return TeamMember::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m) => [
                'name' => $m->name,
                'role' => $m->role,
                'bio' => $m->bio,
                'credentials' => $m->credentials,
                'image_url' => $m->image
                    ? rtrim(config('app.url'), '/').'/storage/'.$m->image
                    : null,
                'sort_order' => $m->sort_order,
            ]);
    }
}

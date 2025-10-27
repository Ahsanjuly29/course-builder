<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        $course->load('modules.contents');
        return view('modules.index', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'modules' => 'array',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.contents' => 'array',
            'modules.*.contents.*.type' => 'required|string',
            'modules.*.contents.*.value' => 'nullable|string',
        ]);

        // Remove old ones for simplicity (MVP)
        $course->modules()->each(fn($m) => $m->contents()->delete());
        $course->modules()->delete();

        foreach ($request->modules ?? [] as $mData) {
            $module = $course->modules()->create(['title' => $mData['title']]);
            foreach ($mData['contents'] ?? [] as $cData) {
                $module->contents()->create($cData);
            }
        }

        return redirect()->route('courses.modules.index', $course->id)->with('success', 'Modules updated successfully!');
    }
}

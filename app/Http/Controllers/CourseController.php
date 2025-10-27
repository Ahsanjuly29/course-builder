<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::latest()->paginate(10);
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $user = $request->user();

        $courseData = $request->only(['title', 'description', 'category']);
        $courseData['slug'] = Str::slug($courseData['title']) . '-' . Str::random(6);
        $courseData['user_id'] = $user?->id;

        DB::beginTransaction();
        try {
            // handle feature video
            if ($request->hasFile('feature_video')) {
                $fv = $request->file('feature_video');
                $fvName = time() . '_' . Str::slug(pathinfo($fv->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $fv->getClientOriginalExtension();
                $courseData['feature_video_path'] = $fv->storeAs('course_videos', $fvName, 'public');
            }

            $course = Course::create($courseData);
            if (!$course) {
                throw new Exception('Course not created');
            }

            $modules = $request->input('modules', []);
            foreach ($modules as $mIndex => $m) {
                $module = $course->modules()->create([
                    'title' => $m['title'] ?? 'Untitled Module',
                    'description' => $m['description'] ?? null,
                    'position' => $mIndex,
                ]);

                $contents = $m['contents'] ?? [];
                foreach ($contents as $cIndex => $c) {
                    $content = [
                        'title' => $c['title'] ?? null,
                        'body' => $c['body'] ?? null,
                        'content_type' => $c['content_type'] ?? 'text',
                        'external_link' => $c['external_link'] ?? null,
                        'position' => $cIndex,
                    ];

                    $fileKey = "modules.$mIndex.contents.$cIndex.media";
                    if ($request->hasFile($fileKey)) {
                        $f = $request->file($fileKey);
                        $fName = time() . '_' . Str::slug(pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $f->getClientOriginalExtension();
                        $content['media_path'] = $f->storeAs('content_media', $fName, 'public');
                    }

                    $module->contents()->create($content);
                }
            }

            DB::commit();
            return redirect()->route('courses.index')->with('success', 'Course created.');
        } catch (\Throwable $e) {

            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Saving failed: ' . $e->getMessage()]);
        }
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }


    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('success', 'Deleted');
    }
}

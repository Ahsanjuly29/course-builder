@extends('layouts.app')
@section('title', $course->title)
@section('content')
    <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm mb-3">← Back</a>

    <p class="text-muted p-0 m-0">Course Title:</p>
    <h2>{{ $course->title }}</h2>
    <p class="text-muted">Category: {{ $course->category }}</p>

    <br>
    <p>
        <span class="text-muted p-0 m-0">Description:</span>
        {{ $course->description }}
    </p>

    @if ($course->feature_video_path)
        <video width="480" controls>
            <source src="{{ asset('storage/' . $course->feature_video_path) }}" type="video/mp4">
        </video>
    @endif
@endsection

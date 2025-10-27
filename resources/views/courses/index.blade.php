@extends('layouts.app')
@section('title', 'Courses')
@section('content')


    <div class="d-flex justify-content-between mb-3">
        <h3>All Courses</h3>
        <a href="{{ route('courses.create') }}" class="btn btn-primary btn-sm">+ New Course</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Category</th>
                <th>Modules</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($courses as $key => $course)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        <a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a>
                    </td>
                    <td>{{ $course->category }}</td>
                    <td>{{ $course->modules->count() }}</td>
                    <td>{{ $course->created_at->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('courses.modules.index', $course->id) }}" class="btn btn-sm btn-modules">+
                            Modules</a>
                        <form action="{{ route('courses.destroy', $course) }}" method="POST"
                            onsubmit="return confirm('Delete?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">X Course</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No courses yet</td>
                </tr>
            @endforelse
        </tbody>

    </table>

    {{ $courses->links() }}
@endsection

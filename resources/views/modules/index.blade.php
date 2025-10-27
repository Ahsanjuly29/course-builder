@extends('layouts.app')
@section('title', 'Manage Modules - ' . $course->title)

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0 text-primary">Manage Modules – {{ $course->title }}</h2>
            <div class="text-center mt-4">
                <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm">← Back</a>
                <button type="button" id="add-module" class="btn btn-sm btn-success">
                    + Add New Module
                </button>
            </div>
        </div>

        <form action="{{ route('courses.modules.store', $course->id) }}" method="POST">
            @csrf

            <div id="modules" class="row g-3">
                {{-- Existing Modules --}}
                @forelse ($course->modules as $mIndex => $module)
                    <div class="col-12 module-card">
                        <div class="card shadow-sm border-0 module">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Module {{ $mIndex + 1 }}</h5>
                                    <button type="button" class="btn btn-sm btn-danger remove-module">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </div>

                                <div class="mt-3">
                                    <input type="text" class="form-control" name="modules[{{ $mIndex }}][title]"
                                        value="{{ $module->title }}" placeholder="Module Title" required>
                                </div>

                                <div class="contents mt-3">
                                    @foreach ($module->contents as $cIndex => $content)
                                        <div class="border rounded p-3 mb-2 bg-light content">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-md-3">
                                                    <select
                                                        name="modules[{{ $mIndex }}][contents][{{ $cIndex }}][type]"
                                                        class="form-select">
                                                        <option value="text"
                                                            {{ $content->type == 'text' ? 'selected' : '' }}>Text</option>
                                                        <option value="image"
                                                            {{ $content->type == 'image' ? 'selected' : '' }}>Image</option>
                                                        <option value="video"
                                                            {{ $content->type == 'video' ? 'selected' : '' }}>Video
                                                        </option>
                                                        <option value="link"
                                                            {{ $content->type == 'link' ? 'selected' : '' }}>Link</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control"
                                                        name="modules[{{ $mIndex }}][contents][{{ $cIndex }}][value]"
                                                        value="{{ $content->value }}" placeholder="Content Value">
                                                </div>
                                                <div class="col-md-1 text-end">
                                                    <button type="button" class="btn btn-sm btn-danger remove-content">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="btn btn-primary btn-sm add-content mt-2">
                                    + Add Content
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No modules found yet. You can add one below.</p>
                @endforelse
            </div>


            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    💾 Save All Changes
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            let moduleIndex = {{ $course->modules->count() }};

            // Add new module dynamically
            $('#add-module').click(function() {
                let moduleHtml = `
        <div class="col-12 module-card">
            <div class="card shadow-sm border-0 module">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">New Module </h5>
                        <button type="button" class="btn btn-sm btn-danger remove-module">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>

                    <div class="mt-3">
                        <input type="text" class="form-control"
                               name="modules[${moduleIndex}][title]"
                               placeholder="Module Title" required>
                    </div>

                    <div class="contents mt-3"></div>

                    <button type="button" class="btn btn-primary btn-sm add-content mt-2">
                        + Add Content
                    </button>
                </div>
            </div>
        </div>`;
                $('#modules').append(moduleHtml);
                moduleIndex++;
            });

            // Add new content dynamically (inside module)
            $(document).on('click', '.add-content', function() {
                let moduleDiv = $(this).closest('.module');
                let moduleIdx = moduleDiv.closest('.module-card').index();
                let contentCount = moduleDiv.find('.content').length;

                let contentHtml = `
        <div class="border rounded p-3 mb-2 bg-light content">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="modules[${moduleIdx}][contents][${contentCount}][type]" class="form-select">
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                        <option value="link">Link</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control"
                           name="modules[${moduleIdx}][contents][${contentCount}][value]"
                           placeholder="Content Value">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-danger remove-content">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>`;
                moduleDiv.find('.contents').append(contentHtml);
            });

            // Remove module or content
            $(document).on('click', '.remove-module', function() {
                $(this).closest('.module-card').fadeOut(300, function() {
                    $(this).remove();
                });
            });
            $(document).on('click', '.remove-content', function() {
                $(this).closest('.content').fadeOut(200, function() {
                    $(this).remove();
                });
            });
        </script>
    @endpush
@endsection

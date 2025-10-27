@extends('layouts.app')
@section('title', 'Create Course')
@section('content')
    <div class="w-90 mx-auto border border-dark rounded p-2 mb-2">
        <h3>Create Course</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="courseForm" action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-2">
                <label>Title *</label>
                <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
            </div>

            <div class="mb-2">
                <label>Category</label>
                <input type="text" name="category" class="form-control" value="{{ old('category') }}">
            </div>

            <div class="mb-2">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label>Feature Video (mp4/webm)</label>
                <input type="file" name="feature_video" accept="video/*" class="form-control">
                <small class="text-muted">For large/video-heavy apps use direct-to-S3 or chunked upload.</small>
            </div>

            <hr>
            <h5>
                Modules
                <button type="button" id="addModule" class="btn btn-sm btn-primary mt-2">
                    Add Module
                </button>
            </h5>
            <div id="modulesContainer"></div>

            <hr class="my-3">
            <button type="submit" class="btn btn-success">Save Course</button>
        </form>
    </div>

    <!-- templates -->
    <div id="moduleTpl" style="display:none;">
        <div class="card mb-3 module" data-module-index="__M__">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h6>Module <span class="mnum">__MNUM__</span></h6>
                    <button type="button" class="btn btn-danger btn-sm removeModule">Remove Module</button>
                </div>

                <div class="mb-2">
                    <input type="text" name="modules[__M__][title]" class="form-control" placeholder="Module title"
                        required>
                </div>
                <div class="mb-2">
                    <textarea name="modules[__M__][description]" class="form-control" placeholder="Module description"></textarea>
                </div>

                <h6>Contents</h6>
                <div class="contents"></div>
                <button type="button" class="btn btn-primary btn-sm addContent">Add Content</button>
            </div>
        </div>
    </div>

    <div id="contentTpl" style="display:none;">
        <div class="border p-2 mb-2 content" data-content-index="__C__">
            <div class="d-flex justify-content-between">
                <strong>Content <span class="cnum">__CNUM__</span></strong>
                <button type="button" class="btn btn-sm btn-danger removeContent">Remove</button>
            </div>

            <div class="row gy-2">
                <div class="col-md-3">
                    <select name="modules[__M__][contents][__C__][content_type]" class="form-control content-type">
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                        <option value="link">Link</option>
                        <option value="file">File</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <input type="text" name="modules[__M__][contents][__C__][title]" class="form-control"
                        placeholder="Content title">
                </div>
                <div class="col-12 mt-2 content-body">
                    <textarea name="modules[__M__][contents][__C__][body]" class="form-control" placeholder="Text body"></textarea>
                </div>
                <div class="col-12 mt-2 content-media" style="display:none;">
                    <input type="file" name="modules[__M__][contents][__C__][media]" class="form-control">
                </div>
                <div class="col-12 mt-2 content-link" style="display:none;">
                    <input type="url" name="modules[__M__][contents][__C__][external_link]" class="form-control"
                        placeholder="External link">
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            (function($) {
                let mCount = 0;

                function addModule() {
                    const tpl = $('#moduleTpl').html().replace(/__M__/g, mCount).replace(/__MNUM__/g, (mCount + 1));
                    const $m = $(tpl);
                    const $contents = $m.find('.contents');

                    $m.on('click', '.addContent', function() {
                        addContent(mCount, $contents);
                    });
                    $m.on('click', '.removeModule', function() {
                        $m.remove();
                        updateModuleNumbers();
                    });

                    $('#modulesContainer').append($m);
                    // add one content by default
                    addContent(mCount, $contents);
                    mCount++;
                }

                function addContent(mIndex, $container) {
                    const cIndex = $container.children().length;
                    const tpl = $('#contentTpl').html()
                        .replace(/__M__/g, mIndex)
                        .replace(/__C__/g, cIndex)
                        .replace(/__CNUM__/g, cIndex + 1);
                    const $c = $(tpl);

                    // show/hide fields by type
                    $c.find('.content-type').on('change', function() {
                        const t = $(this).val();
                        $c.find('.content-body').toggle(t === 'text');
                        $c.find('.content-media').toggle(t === 'image' || t === 'video' || t === 'file');
                        $c.find('.content-link').toggle(t === 'link');
                    }).trigger('change');

                    $c.on('click', '.removeContent', function() {
                        $c.remove();
                        updateContentNumbers($container);
                    });
                    $container.append($c);
                }

                function updateModuleNumbers() {
                    $('#modulesContainer .module').each(function(i) {
                        $(this).find('.mnum').text(i + 1);
                        $(this).attr('data-module-index', i);
                        // re-name inputs? not necessary unless you reorder modules; okay for add/remove sequence
                    });
                }

                function updateContentNumbers($container) {
                    $container.children().each(function(i) {
                        $(this).find('.cnum').text(i + 1);
                        $(this).attr('data-content-index', i);
                        // renaming inputs when removing is not required for basic flow
                    });
                }

                $('#addModule').on('click', addModule);
                // seed initial module
                addModule();

                // basic front validation — ensure course title present
                $('#courseForm').on('submit', function(e) {
                    if ($.trim($('input[name="title"]').val()) === '') {
                        alert('Course title required');
                        e.preventDefault();
                    }
                });

            })(jQuery);
        </script>
    @endpush
@endsection

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Course App')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')

    <style>
        body {
            background: rgb(224, 224, 224);
            color: rgb(6, 6, 6);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        a,
        button {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn {
            cursor: pointer;
            color: white;
        }

        .btn-edit {
            background: #007bff;
        }

        .btn-modules {
            background: #28a745;
        }

        .btn-delete {
            background: #dc3545;
        }

        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, .25);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">CourseApp</a>
        </div>
    </nav>

    <main class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($errors->any())
            $(function() {
            @foreach ($errors->keys() as $key)
                $('[name="{{ $key }}"]').addClass('is-invalid');
            @endforeach
            });
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')

    <script>
        @if (session('success'))
            setTimeout(() => {
                $('.alert-success').fadeOut('slow');
            }, 3000);
        @endif
    </script>
</body>

</html>

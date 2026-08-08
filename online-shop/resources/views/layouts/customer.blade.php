<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Account')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="dashboard-layout d-flex">
        @include('partials.customer-sidebar')

        <div class="flex-grow-1 d-flex flex-column min-vh-100" style="min-width:0">
            @include('partials.dashboard-header', ['dashboardTitle' => 'My Account'])

            <main class="container-fluid px-4 py-3 flex-grow-1">
                @include('partials.alerts')
                @yield('content')
            </main>

            <footer class="text-center text-muted py-3 small border-top bg-white">
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>

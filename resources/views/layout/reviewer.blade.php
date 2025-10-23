<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reviewer Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f9fafb; color: #1f2937; }
        .navbar-brand { font-weight: bold; }
        .badge-notify { position: absolute; top: 0; right: 0; transform: translate(50%, -50%); }
    </style>
</head>
<body>
    {{-- ===== Navigation Bar ===== --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('reviewer.home') }}">Reviewer Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navmenu">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a href="{{ route('reviewer.home') }}" class="nav-link">Home</a></li>

                    {{-- Safe Assigned Papers link --}}
                    <li class="nav-item">
                        @php
                            $firstConf = isset($assignedConferences) ? $assignedConferences->first() : null;
                        @endphp
                        <a href="{{ $firstConf ? route('reviewer.conference.papers', ['conference_code' => $firstConf->conference_code]) : '#' }}" class="nav-link">
                            Assigned Papers
                        </a>
                    </li>

                    {{-- Notifications Dropdown --}}
                    <li class="nav-item dropdown position-relative">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Notifications
                            @if(isset($unreadNotifications) && $unreadNotifications->count() > 0)
                                <span class="badge bg-danger badge-notify">{{ $unreadNotifications->count() }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if(isset($unreadNotifications) && $unreadNotifications->count() > 0)
                                @foreach($unreadNotifications as $notification)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('reviewer.conference.papers', ['conference_code' => $notification->data['conference_code'] ?? '']) }}">
                                            {{ \Illuminate\Support\Str::limit($notification->data['message'] ?? 'New Notification', 50) }}
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li><span class="dropdown-item text-muted">No new notifications</span></li>
                            @endif
                        </ul>
                    </li>

                    {{-- Logout --}}
                    <li class="nav-item ms-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Page Content --}}
    <div class="container">
        @yield('content')
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</body>
</html>

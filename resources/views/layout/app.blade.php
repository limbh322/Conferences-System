<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Conference System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9fafb;
            color: #1f2937;
            font-family: Arial, sans-serif;
        }
        .topbar {
            background-color: #1f2937;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .topbar .brand {
            font-weight: bold;
            font-size: 20px;
            color: #60a5fa;
        }
        .topbar .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logout-btn, .admin-btn {
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 14px;
            transition: 0.3s;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .logout-btn {
            background-color: #ef4444;
        }
        .logout-btn:hover {
            background-color: #dc2626;
        }
        .admin-btn {
            background-color: #2563eb;
        }
        .admin-btn:hover {
            background-color: #1d4ed8;
        }

     
        /* Notification Bell */
        .notification-bell {
            position: relative;
            font-size: 24px;
            cursor: pointer;
            color: white;
            text-decoration: none;
            transition: transform 0.2s, color 0.2s;
        }
        .notification-bell .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 0.65rem;
            animation: pop 0.5s ease-in-out;
        }

        /* Hover Animations */
        @keyframes bell-shake {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(-15deg); }
            20% { transform: rotate(15deg); }
            30% { transform: rotate(-10deg); }
            40% { transform: rotate(10deg); }
            50% { transform: rotate(-5deg); }
            60% { transform: rotate(5deg); }
            70%, 100% { transform: rotate(0deg); }
        }

        .notification-bell:hover {
            color: #facc15;        /* Light up: bright yellow */
            transform: scale(1.2); /* Zoom slightly */
        }

        .notification-bell:hover i {
            animation: bell-shake 0.8s ease-in-out; /* Shake effect */
        }

        /* Badge pop animation */
        @keyframes pop {
            0% { transform: scale(0); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }


        footer {
            text-align: center;
            padding: 15px 0;
            font-size: 14px;
            color: #6b7280;
            margin-top: 30px;
        }
        .container {
            max-width: 1200px;
        }
    </style>
</head>
<body>

    {{-- ✅ Top Navigation --}}
    <div class="topbar">
        <div class="brand">Conference System</div>
        <div class="user-section">
            @auth
                {{-- Notification Bell --}}
                <a href="{{ route('notifications.index') }}" class="notification-bell me-3">
                    <i class="bi bi-bell"></i>
                    @php
                        $unreadCount = isset($notifications) ? $notifications->whereNull('read_at')->count() : 0;
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                    @endif
                </a>

                {{-- Admin-only Manage button --}}
                @if(Auth::user()->role === 'Admin')
                    @php $currentRoute = Route::currentRouteName(); @endphp
                    @if($currentRoute === 'dashboard')
                        <a href="{{ route('admin.users.index') }}" class="admin-btn">Manage Users</a>
                    @elseif($currentRoute === 'admin.users.index')
                        <a href="{{ route('dashboard') }}" class="admin-btn">Manage Conferences</a>
                    @endif
                @endif

                {{-- User Greeting --}}
                <span>Welcome, <strong>{{ Auth::user()->name }}</strong></span>

                {{-- Logout Button --}}
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Login</a>
            @endauth
        </div>
    </div>

    <div class="container">
        @yield('content')
    </div>

    <footer>
        © {{ date('Y') }} Conference Paper Submission System. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

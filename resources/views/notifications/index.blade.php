@extends('layout.app')

@section('title', 'Notifications')

@section('content')
@php
    // Define home route based on user role
    $role = Auth::user()->role;
    $homeRoute = match($role) {
        'Admin' => 'dashboard',           // Admin dashboard route
        'Author' => 'author.home',        // Author home route
        'Reviewer' => 'reviewer.home',    // Reviewer home route
        default => 'home',
    };
@endphp

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Notifications</h2>
        <a href="{{ route($homeRoute) }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-md-12">

            @forelse($notifications as $notification)
                @php
                    $isRead = !is_null($notification->read_at);
                    $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                    $title = $data['title'] ?? 'Notification';
                    $message = $data['message'] ?? '';
                    $conferenceCode = $data['conference_code'] ?? null;
                    $homeLink = route($homeRoute) . ($conferenceCode ? "?highlight={$conferenceCode}" : '');
                @endphp

                <div class="card mb-3 shadow-sm {{ $isRead ? '' : 'border-primary' }}" id="notification-{{ $notification->id }}">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1 {{ $isRead ? '' : 'fw-bold' }}">
                                @if(!$isRead)<i class="bi bi-envelope-fill text-primary me-2"></i>@endif
                                {{ $title }}
                            </h5>
                            <p class="card-text mb-1">{{ $message }}</p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>

                        <div class="d-flex flex-column align-items-end gap-1">
                            @if($conferenceCode)
                                <a href="{{ $homeLink }}" class="btn btn-sm btn-primary">View</a>
                            @endif
                            @if(!$isRead)
                                <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="btn btn-sm btn-outline-primary">Mark as read</a>
                            @endif

                            {{-- Delete notification --}}
                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 mb-3"></i>
                    <p class="mb-0">No notifications</p>
                </div>
            @endforelse

        </div>
    </div>
</div>

<style>
    .card.border-primary { border-width: 2px !important; }
    .card-title i { font-size: 1rem; }

    /* Highlight animation for conference */
    .highlight {
        animation: highlightBlink 2s ease-in-out 3;
    }
    @keyframes highlightBlink {
        0%, 100% { background-color: #fff; }
        50% { background-color: #fef3c7; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Scroll to and highlight conference if query param exists
    const params = new URLSearchParams(window.location.search);
    const conferenceCode = params.get('highlight');
    if (conferenceCode) {
        const target = document.querySelector(`#conference-${conferenceCode}`);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            target.classList.add('highlight');
            setTimeout(() => {
                target.classList.remove('highlight');
            }, 6000);
        }
    }
});
</script>
@endsection

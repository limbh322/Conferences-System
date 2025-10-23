@extends('layout.app')

@section('title', 'Reviewer Dashboard')

@section('content')
<div class="container mt-4">

    {{-- ===== Welcome Section ===== --}}
    <h2 class="mb-2">Welcome, {{ Auth::user()->name }}</h2>
    <p class="text-muted">Below are your assigned conferences:</p>

    {{-- ===== Search Input ===== --}}
    <div class="mb-4">
        <input type="text" id="conferenceSearch" class="form-control" placeholder="Search by conference code or title...">
    </div>

    @php
        $openConfs = $assignedConferences->filter(fn($conf) => $conf->deadline ? \Carbon\Carbon::parse($conf->deadline)->isFuture() : true);
        $closedConfs = $assignedConferences->filter(fn($conf) => $conf->deadline ? \Carbon\Carbon::parse($conf->deadline)->isPast() : false);
    @endphp

    {{-- ===== Open Conferences ===== --}}
    <div class="mb-5">
        <h4>Open Conferences</h4>
        <div class="row">
            @forelse($openConfs as $conf)
                @php
                    $reviewerId = Auth::id();
                    $allPapers = $conf->papers;
                    $reviewedPapers = $allPapers->filter(fn($paper) => $paper->reviews->contains('reviewer_id', $reviewerId));
                    $assignedCount = $allPapers->count();
                    $reviewedCount = $reviewedPapers->count();
                    $deadline = $conf->deadline ? \Carbon\Carbon::parse($conf->deadline) : null;
                @endphp
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <span>{{ $conf->conference_code }}</span>
                            <span class="badge bg-light text-dark">{{ $deadline ? $deadline->format('Y-m-d') : '-' }}</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $conf->title }}</h5>
                            <p class="card-text">{{ $conf->description }}</p>
                            <p class="mb-1">
                                <strong>Papers Reviewed:</strong> {{ $reviewedCount }}/{{ $assignedCount }}
                            </p>
                            <p class="mb-1">
                                <strong>Deadline:</strong>
                                <span class="{{ $deadline && $deadline->isPast() ? 'text-danger' : 'text-success' }}">
                                    {{ $deadline ? $deadline->format('Y-m-d') : '-' }}
                                </span>
                            </p>
                            <a href="{{ route('reviewer.conference.papers', ['conference_code' => $conf->conference_code]) }}" 
                               class="btn btn-primary btn-sm mt-2">View Papers</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">No open conferences assigned yet.</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ===== Closed Conferences ===== --}}
    <div class="mb-5">
        <h4>Past Conferences</h4>
        <div class="row">
            @forelse($closedConfs as $conf)
                @php
                    $reviewerId = Auth::id();
                    $allPapers = $conf->papers;
                    $reviewedPapers = $allPapers->filter(fn($paper) => $paper->reviews->contains('reviewer_id', $reviewerId));
                    $assignedCount = $allPapers->count();
                    $reviewedCount = $reviewedPapers->count();
                    $deadline = $conf->deadline ? \Carbon\Carbon::parse($conf->deadline) : null;
                @endphp
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <span>{{ $conf->conference_code }}</span>
                            <span class="badge bg-light text-dark">{{ $deadline ? $deadline->format('Y-m-d') : '-' }}</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $conf->title }}</h5>
                            <p class="card-text">{{ $conf->description }}</p>
                            <p class="mb-1">
                                <strong>Papers Reviewed:</strong> {{ $reviewedCount }}/{{ $assignedCount }}
                            </p>
                            <p class="mb-1">
                                <strong>Deadline:</strong>
                                <span class="text-danger">{{ $deadline ? $deadline->format('Y-m-d') : '-' }}</span>
                            </p>
                            <a href="{{ route('reviewer.conference.papers', ['conference_code' => $conf->conference_code]) }}" 
                               class="btn btn-primary btn-sm mt-2">View Papers</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">No past conferences assigned yet.</div>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ===== JS for Search Highlight ===== --}}
<script>
function highlightText(element, keyword) {
    const text = element.textContent;
    if (!keyword) {
        element.innerHTML = text;
        return;
    }
    const regex = new RegExp(`(${keyword})`, 'gi');
    element.innerHTML = text.replace(regex, '<mark style="background-color:#ffeeba;color:#000;">$1</mark>');
}

const searchInput = document.getElementById('conferenceSearch');
const cards = document.querySelectorAll('.card');

searchInput.addEventListener('input', () => {
    const filter = searchInput.value.toLowerCase();
    cards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const code = card.querySelector('.card-header span').textContent.toLowerCase();
        if(title.includes(filter) || code.includes(filter)) {
            card.parentElement.style.display = '';
        } else {
            card.parentElement.style.display = 'none';
        }
    });
});
</script>
@endsection

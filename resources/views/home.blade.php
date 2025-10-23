@extends('layout.app')

@section('title', 'Author Dashboard')

@section('content')
<style>
/* ================= General ================= */
body {
    background-color: #f9fafb;
    color: #1f2937;
    font-family: 'Arial', sans-serif;
}

/* ================= Topbar ================= */
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
.topbar .logout-btn {
    background-color: #ef4444;
    border: none;
    color: white;
    border-radius: 6px;
    padding: 8px 14px;
    transition: 0.3s;
}
.topbar .logout-btn:hover {
    background-color: #dc2626;
}

/* ================= Cards ================= */
.card {
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    margin-bottom: 30px;
}

/* ================= Tables ================= */
.table th {
    background-color: #1f2937 !important;
    color: white !important;
}
.table-hover tbody tr:hover {
    background-color: #f3f4f6;
    transition: background-color 0.2s ease-in-out;
}

/* ================= Search Inputs ================= */
.search-input {
    width: 100%;
    max-width: 350px;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    margin-bottom: 15px;
    outline: none;
    transition: 0.3s;
}
.search-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 5px rgba(59,130,246,0.3);
}

/* ================= Highlights ================= */
mark {
    background-color: #facc15;
    color: #000;
    padding: 0 2px;
    border-radius: 3px;
}

/* ================= Deadlines ================= */
.deadline-passed {
    color: #dc2626;
    font-weight: 600;
}
.deadline-upcoming {
    color: #16a34a;
}

/* ================= Review Box ================= */
.review-box {
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 8px;
    background-color: #f3f4f6;
    border-left: 4px solid #3b82f6;
}
</style>

<div class="container">
    {{-- ===== Welcome ===== --}}
    <div class="text-center mb-5">
        <h1>Welcome to the Conference Management System</h1>
        <p class="lead text-muted">Submit your papers and track their review progress.</p>
    </div>

    {{-- ===== Success Message ===== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ===== My Submitted Papers ===== --}}
    @php
        // Separate papers by status
        $pendingOrOther = $papers->filter(fn($p) => !in_array($p->status, ['Approved', 'Rejected']));
        $approvedOrRejected = $papers->filter(fn($p) => in_array($p->status, ['Approved', 'Rejected']));

        // Sort each group by latest submission
        $pendingOrOther = $pendingOrOther->sortByDesc(fn($p) => $p->created_at);
        $approvedOrRejected = $approvedOrRejected->sortByDesc(fn($p) => $p->created_at);

        // Merge: pending first, approved/rejected last
        $sortedPapers = $pendingOrOther->concat($approvedOrRejected);
    @endphp

    <div class="card shadow">
        <div class="card-header bg-primary text-white">My Submitted Papers</div>
        <div class="card-body">
            <input type="text" id="paperSearch" class="search-input" placeholder="Search papers by title or conference...">

            @if($sortedPapers->isEmpty())
                <div class="alert alert-info">You have not submitted any papers yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-hover" id="paperTable">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Conference</th>
                                <th>Status</th>
                                <th>Reviews</th>
                                <th>Submitted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sortedPapers as $paper)
                                @php
                                    $deadline = \Carbon\Carbon::parse($paper->conference->deadline ?? now());
                                    $isPast = $deadline->isPast();
                                    $hasReview = $paper->reviews->count() > 0;
                                    $status = $paper->status ?? 'Pending';
                                    $badge = match($status) {
                                        'Accepted', 'Approved' => 'success',
                                        'Rejected' => 'danger',
                                        'Reviewed' => 'info',
                                        'Resubmitted' => 'warning',
                                        default => 'secondary',
                                    };
                                @endphp
                                <tr>
                                    <td class="searchable">{{ $paper->title }}</td>
                                    <td class="searchable">{{ $paper->conference->title ?? '-' }}</td>
                                    <td><span class="badge bg-{{ $badge }}">{{ $status }}</span></td>
                                    <td>
                                        @forelse($paper->reviews as $review)
                                            <div class="review-box">
                                                <strong>{{ $review->reviewer->name ?? 'Reviewer' }}:</strong><br>
                                                <span>Comment: {{ $review->comments ?? '-' }}</span><br>
                                                <span>Recommendation: {{ $review->recommendation ?? '-' }}</span>
                                            </div>
                                        @empty
                                            <em>No reviews yet</em>
                                        @endforelse
                                    </td>
                                    <td>{{ $paper->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('paper.show', $paper->paper_id) }}" class="btn btn-sm btn-secondary mb-1">View</a>
                                        @if(in_array($status, ['Accepted', 'Approved']))
                                            <button class="btn btn-sm btn-success mb-1" disabled>Approved</button>
                                        @elseif(!$isPast)
                                            <a href="{{ route('paper.edit', $paper->paper_id) }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                                        @elseif($hasReview)
                                            <a href="{{ route('paper.resubmit', $paper->paper_id) }}" class="btn btn-sm btn-success mb-1">Resubmit</a>
                                        @else
                                            <button class="btn btn-sm btn-secondary mb-1" disabled>Closed</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== Available Conferences ===== --}}
    @php
        $sortedConferences = $conferences->sortBy(fn($conf) => \Carbon\Carbon::parse($conf->deadline)->isPast() ? 1 : 0);
    @endphp
    <div class="card shadow">
        <div class="card-header bg-info text-white">Available Conferences</div>
        <div class="card-body">
            <input type="text" id="conferenceSearch" class="search-input" placeholder="Search by conference code or title...">

            @if($sortedConferences->count())
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="conferenceTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Deadline</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sortedConferences as $conf)
                                @php $isPast = \Carbon\Carbon::parse($conf->deadline)->isPast(); @endphp
                                <tr>
                                    <td class="searchable">{{ $conf->conference_code }}</td>
                                    <td class="searchable">{{ $conf->title }}</td>
                                    <td>{{ $conf->description ?? '-' }}</td>
                                    <td>
                                        <span class="{{ $isPast ? 'deadline-passed' : 'deadline-upcoming' }}">
                                            {{ \Carbon\Carbon::parse($conf->deadline)->format('Y-m-d H:i') }}
                                            {{ $isPast ? '(Closed)' : '(Open)' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!$isPast)
                                            <a href="{{ route('paper.submit', ['conference_code' => $conf->conference_code]) }}" class="btn btn-success btn-sm">Submit Paper</a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>Closed</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center">No conferences available yet.</p>
            @endif
        </div>
    </div>
</div>

{{-- ===== JS: Search & Highlight ===== --}}
<script>
function highlightText(element, keyword) {
    const text = element.textContent;
    element.innerHTML = !keyword ? text : text.replace(new RegExp(`(${keyword})`, 'gi'), '<mark>$1</mark>');
}

function setupSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);

    input.addEventListener('input', () => {
        const filter = input.value.toLowerCase();
        rows.forEach(row => {
            const cells = row.querySelectorAll('.searchable');
            let found = false;
            cells.forEach(cell => {
                if(cell.textContent.toLowerCase().includes(filter)) found = true;
                highlightText(cell, filter);
            });
            row.style.display = found || !filter ? '' : 'none';
        });
    });
}

setupSearch('paperSearch', 'paperTable');
setupSearch('conferenceSearch', 'conferenceTable');
</script>
@endsection

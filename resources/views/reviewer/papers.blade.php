@extends('layout.reviewer')

@section('title', 'Conference Papers')

@section('content')
<div class="container mt-4">

    {{-- Back Button --}}
    <div class="mb-3">
        <a href="{{ route('reviewer.home') }}" class="btn btn-secondary">
            &larr; Back to Dashboard
        </a>
    </div>

    <h2 class="mb-3">Conference: {{ $conference->title ?? $conference->conference_code }}</h2>
    <p class="text-muted">Deadline: {{ $conference->deadline ? \Carbon\Carbon::parse($conference->deadline)->format('Y-m-d') : '-' }}</p>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    {{-- Pending Review --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-warning text-dark">
            Papers Pending Review ({{ $pendingPapers->count() }})
        </div>
        <div class="card-body">
            @if($pendingPapers->isEmpty())
                <div class="alert alert-info text-center">No pending papers.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Submitted At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingPapers as $paper)
                                <tr>
                                    <td>{{ $paper->title }}</td>
                                    <td>{{ $paper->author->name ?? '-' }}</td>
                                    <td>{{ $paper->created_at->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('reviewer.reviewForm', $paper->paper_id) }}" class="btn btn-sm btn-primary mb-1">
                                            Review
                                        </a>
                                        @if($paper->file_path)
                                            <a href="{{ asset('storage/' . $paper->file_path) }}" target="_blank" class="btn btn-sm btn-info mb-1">
                                                View File
                                            </a>
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

    {{-- Reviewed Papers --}}
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            Papers Reviewed ({{ $donePapers->count() }})
        </div>
        <div class="card-body">
            @if($donePapers->isEmpty())
                <div class="alert alert-info text-center">No papers reviewed yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Score</th>
                                <th>Recommendation</th>
                                <th>Comment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donePapers as $paper)
                                @php
                                    $review = $paper->reviews->first();
                                @endphp
                                <tr>
                                    <td>{{ $paper->title }}</td>
                                    <td>{{ $paper->author->name ?? '-' }}</td>
                                    <td>{{ $review->score ?? '-' }}</td>
                                    <td>{{ $review->recommendation ?? '-' }}</td>
                                    <td>{{ $review->comments ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('reviewer.reviewForm', $paper->paper_id) }}" class="btn btn-sm btn-secondary mb-1">
                                            Modify Review
                                        </a>
                                        @if($paper->file_path)
                                            <a href="{{ asset('storage/' . $paper->file_path) }}" target="_blank" class="btn btn-sm btn-info mb-1">
                                                View File
                                            </a>
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
</div>
@endsection

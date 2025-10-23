@extends('layout.reviewer')

@section('title', 'Review Paper')

@section('content')
<div class="container mt-5">
    <h3>Review Paper: {{ $paper->title }}</h3>
    <p><strong>Conference:</strong> {{ $paper->conference->title ?? '-' }}</p>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('reviewer.submitReview', $paper->paper_id) }}" method="POST">
        @csrf

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="">Select Status</option>
                <option value="Submitted" {{ old('status', $review->status ?? '') == 'Submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="Approved" {{ old('status', $review->status ?? '') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Rejected" {{ old('status', $review->status ?? '') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        {{-- Score --}}
        <div class="mb-3">
            <label for="score" class="form-label">Score (0-10)</label>
            <input type="number" name="score" id="score" class="form-control" min="0" max="10" 
                   value="{{ old('score', $review->score ?? '') }}">
        </div>

        {{-- Comments --}}
        <div class="mb-3">
            <label for="comments" class="form-label">Comments</label>
            <textarea name="comments" id="comments" rows="4" class="form-control">{{ old('comments', $review->comments ?? '') }}</textarea>
        </div>

        {{-- Recommendation --}}
        <div class="mb-3">
            <label for="recommendation" class="form-label">Recommendation</label>
            <select name="recommendation" id="recommendation" class="form-select">
                <option value="">Select</option>
                <option value="Accept" {{ old('recommendation', $review->recommendation ?? '') == 'Accept' ? 'selected' : '' }}>Accept</option>
                <option value="Minor Revision" {{ old('recommendation', $review->recommendation ?? '') == 'Minor Revision' ? 'selected' : '' }}>Minor Revision</option>
                <option value="Major Revision" {{ old('recommendation', $review->recommendation ?? '') == 'Major Revision' ? 'selected' : '' }}>Major Revision</option>
                <option value="Reject" {{ old('recommendation', $review->recommendation ?? '') == 'Reject' ? 'selected' : '' }}>Reject</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
</div>
@endsection

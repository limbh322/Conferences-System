@extends('layout.app')

@section('title', 'Assign Reviewers')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Assign Reviewers – {{ $conference->title }}</h3>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('conference.updateReviewers', $conference->conference_code) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Current reviewers --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Current Reviewers:</label>
                    <input type="text" class="form-control" 
                           value="{{ $conference->reviewers->pluck('name')->join(', ') ?: 'Not Assigned' }}" readonly>
                </div>

                {{-- Assign new reviewers --}}
                <div class="mb-3">
                    <label for="reviewers" class="form-label fw-bold">Select Reviewers:</label>
                    <select name="reviewers[]" id="reviewers" class="form-select" multiple required>
                        @foreach($reviewers as $reviewer)
                            <option value="{{ $reviewer->id }}"
                                @if($conference->reviewers->contains($reviewer->id)) selected @endif>
                                {{ $reviewer->name }} ({{ $reviewer->email }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl (Windows) / Cmd (Mac) to select multiple reviewers</small>
                </div>

                <button type="submit" class="btn btn-primary">Update Reviewers</button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection

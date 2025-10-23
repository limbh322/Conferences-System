@extends('layout.app')

@section('title', 'Add Conference')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4 text-primary">Add New Conference</h3>

        {{-- ✅ Back to Dashboard Button --}}
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    {{-- ✅ Success message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ❌ Validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems with your input:</strong>
            <ul class="mt-2 mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ✅ Add Conference Form --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('conference.store') }}" method="POST">
                @csrf

                {{-- Conference Code --}}
                <div class="mb-3">
                    <label for="conference_code" class="form-label fw-bold">Conference Code</label>
                    <input type="text" name="conference_code" id="conference_code" 
                           class="form-control @error('conference_code') is-invalid @enderror"
                           value="{{ old('conference_code') }}" placeholder="Optional, e.g., CONF2025">
                    @error('conference_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Leave empty to auto-generate a unique code.</div>
                </div>

                {{-- Title --}}
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Conference Title</label>
                    <input type="text" name="title" id="title" 
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" placeholder="Enter conference title" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Brief description of the conference">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Deadline --}}
                <div class="mb-3">
                    <label for="deadline" class="form-label fw-bold">Submission Deadline</label>
                    <input type="datetime-local" name="deadline" id="deadline" class="form-control @error('deadline') is-invalid @enderror"
                           value="{{ old('deadline') }}">
                    @error('deadline')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ✅ Reviewer assignment --}}
                <div class="mb-3">
                    <label for="reviewers" class="form-label fw-bold">Assign Reviewers</label>
                    <select name="reviewers[]" id="reviewers" class="form-select" multiple>
                        @foreach($reviewers as $reviewer)
                            <option value="{{ $reviewer->id }}"
                                {{ (collect(old('reviewers'))->contains($reviewer->id)) ? 'selected' : '' }}>
                                {{ $reviewer->name }} ({{ $reviewer->email }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl (Windows) / Command (Mac) to select multiple reviewers.</small>
                </div>

                <button type="submit" class="btn btn-success w-100">Add Conference</button>
            </form>
        </div>
    </div>

    {{-- ✅ Existing Conferences --}}
    @if(isset($conferences) && $conferences->count() > 0)
        <div class="mt-5">
            <h5 class="mb-3 text-secondary">Existing Conferences</h5>
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Deadline</th>
                        <th>Assigned Reviewers</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conferences as $conf)
                        <tr>
                            <td>{{ $conf->conference_code }}</td>
                            <td>{{ $conf->title }}</td>
                            <td>{{ $conf->deadline ?? '—' }}</td>
                            <td>
                                @if($conf->reviewers->count() > 0)
                                    {{ $conf->reviewers->pluck('name')->join(', ') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $conf->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

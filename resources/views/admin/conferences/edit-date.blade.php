@extends('layout.app')

@section('title', 'Edit Conference - ' . $conference->title)

@section('content')
<div class="container mt-4">

    <h3 class="mb-4">Edit Conference: {{ $conference->title }}</h3>

    {{-- ✅ Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ✅ Validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('conference.update', $conference->conference_code) }}" method="POST" class="card shadow-sm p-4">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label fw-bold">Conference Title</label>
            <input type="text" name="title" id="title" class="form-control" 
                   value="{{ old('title', $conference->title) }}" required>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description', $conference->description) }}</textarea>
        </div>

        {{-- Deadline (Date + Time) --}}
        <div class="mb-3">
            <label for="deadline" class="form-label fw-bold">Deadline (Date & Time)</label>
            <input type="datetime-local" name="deadline" id="deadline" class="form-control" 
                   value="{{ old('deadline', \Carbon\Carbon::parse($conference->deadline)->format('Y-m-d\TH:i')) }}" required>
            <small class="text-muted">Please set both date and time for the deadline.</small>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">Update Conference</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>
@endsection

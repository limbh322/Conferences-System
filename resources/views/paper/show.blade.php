@extends('layout.app')

@section('title', 'Paper Details')

@section('content')
<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">📄 Paper Details</h4>
        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">⬅ Back</a>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <strong>Title:</strong>
            <div class="ms-3">{{ $paper->title }}</div>
        </div>

        <div class="mb-3">
            <strong>Abstract:</strong>
            <div class="ms-3">{{ $paper->abstract ?? 'No abstract provided' }}</div>
        </div>

        <div class="mb-3">
            <strong>Keywords:</strong>
            <div class="ms-3">{{ $paper->keywords ?? '-' }}</div>
        </div>

        <div class="mb-3">
            <strong>Conference:</strong>
            <div class="ms-3">{{ $paper->conference->title ?? 'Not Assigned' }}</div>
        </div>

        <div class="mb-3">
            <strong>Author:</strong>
            <div class="ms-3">{{ $paper->author->name ?? 'Unknown Author' }}</div>
        </div>

        <div class="mb-3">
            <strong>Status:</strong>
            <div class="ms-3">{{ $paper->status ?? 'Submitted' }}</div>
        </div>

        <div class="mb-3">
            <strong>Date Submitted:</strong>
            <div class="ms-3">
                {{ $paper->created_at ? $paper->created_at->format('Y-m-d H:i') : '-' }}
            </div>
        </div>

        <div class="mb-3">
            <strong>File:</strong>
            <div class="ms-3">
                @if(!empty($paper->file_path))
                    <a href="{{ route('paper.viewFile', ['id' => $paper->paper_id]) }}" 
                        class="btn btn-outline-primary btn-sm" 
                        target="_blank">
                        View File ({{ basename($paper->file_path) }})
                        </a>

                @else
                    <span class="text-muted">No file uploaded</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

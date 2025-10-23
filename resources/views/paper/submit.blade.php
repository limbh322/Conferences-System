@extends('layout.app')

@section('title', 'Submit Paper')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span>Submit Paper</span>
                <a href="{{ route('home') }}" class="btn btn-light btn-sm">← Back to Dashboard</a>
            </div>
            <div class="card-body">

                {{-- ✅ Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ❌ Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ✅ Paper Submission Form --}}
                <form method="POST" action="{{ route('paper.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    {{-- Abstract --}}
                    <div class="mb-3">
                        <label class="form-label">Abstract</label>
                        <textarea name="abstract" class="form-control" rows="4"></textarea>
                    </div>

                    {{-- Keywords --}}
                    <div class="mb-3">
                        <label class="form-label">Keywords</label>
                        <input type="text" name="keywords" class="form-control">
                    </div>

                    {{-- ✅ Conference Info (Auto-filled, read-only) --}}
                    @if(isset($conference))
                        <div class="mb-3">
                            <label class="form-label">Conference</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $conference->title }} ({{ $conference->conference_code }})" 
                                   readonly>
                            {{-- Hidden field ensures correct submission --}}
                            <input type="hidden" name="conference_code" value="{{ $conference->conference_code }}">
                        </div>
                    @else
                        <div class="alert alert-warning">
                            ⚠️ No conference selected. Please access this page through a valid conference link.
                        </div>
                    @endif

                    {{-- File Upload --}}
                    <div class="mb-3">
                        <label class="form-label">Upload File (PDF)</label>
                        <input type="file" name="file_path" class="form-control" accept=".pdf" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Submit Paper</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layout.app')

@section('title', 'Edit Paper - ' . $paper->title)

@section('content')
<style>
body {
  background-color: #f9fafb;
  font-family: 'Arial', sans-serif;
  color: #1f2937;
}

.container {
  max-width: 800px;
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  margin-top: 40px;
}

h2 {
  font-weight: 600;
  margin-bottom: 25px;
}

.form-label {
  font-weight: 600;
  color: #374151;
}

.btn-primary {
  background-color: #2563eb;
  border: none;
  transition: 0.3s;
}
.btn-primary:hover {
  background-color: #1d4ed8;
}

.btn-secondary {
  background-color: #6b7280;
  border: none;
}
.btn-secondary:hover {
  background-color: #4b5563;
}

.alert {
  border-radius: 8px;
}
</style>

<div class="container">
    <h2>Edit Paper</h2>

    {{-- ✅ Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ⚠️ Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('paper.update', $paper->paper_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- 📄 Paper Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">Paper Title</label>
            <input type="text" name="title" id="title" class="form-control"
                   value="{{ old('title', $paper->title) }}" required>
        </div>

        {{-- 🧾 Abstract --}}
        <div class="mb-3">
            <label for="abstract" class="form-label">Abstract</label>
            <textarea name="abstract" id="abstract" class="form-control" rows="4"
                      placeholder="Write your abstract here...">{{ old('abstract', $paper->abstract) }}</textarea>
        </div>

        {{-- 🔑 Keywords --}}
        <div class="mb-3">
            <label for="keywords" class="form-label">Keywords</label>
            <input type="text" name="keywords" id="keywords" class="form-control"
                   value="{{ old('keywords', $paper->keywords) }}" placeholder="e.g. AI, Machine Learning, Conference">
        </div>

        {{-- 📂 Replace File --}}
        <div class="mb-3">
            <label for="file_path" class="form-label">Replace PDF File</label>
            <input type="file" name="file_path" id="file_path" class="form-control" accept="application/pdf">
            <small class="text-muted">Leave empty to keep current file.</small><br>
            @if($paper->file_path)
                <a href="{{ route('paper.viewFile', $paper->paper_id) }}" target="_blank">📑 View current file</a>
            @endif
        </div>

        {{-- 🔁 Auto Status Change Info --}}
        @if(in_array($paper->status, ['Rejected', 'Revision Needed']))
            <div class="alert alert-warning">
                <strong>Note:</strong> This paper was previously <strong>{{ $paper->status }}</strong>.
                Upon saving changes, its status will automatically change to <strong>Resubmitted</strong>.
            </div>
        @endif

        {{-- ✅ Action Buttons --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">💾 Save Changes</button>
            <a href="{{ route('author.myPapers') }}" class="btn btn-secondary">↩ Back to My Papers</a>
        </div>
    </form>
</div>
@endsection

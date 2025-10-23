@extends('layout.app')

@section('title', 'Submitted Papers - ' . $conference->title)

@section('content')
<div class="container mt-4">
    <h3>Submitted Papers for {{ $conference->title }} ({{ $conference->conference_code }})</h3>

    @if($papers->isEmpty())
        <div class="alert alert-info mt-3">No papers submitted yet for this conference.</div>
    @else
        <table class="table table-striped table-hover shadow-sm mt-3">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Submitted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($papers as $paper)
                    <tr>
                        <td>{{ $paper->paper_id }}</td>
                        <td>{{ $paper->title }}</td>
                        <td>{{ optional($paper->author)->name ?? 'Unknown' }}</td>
                        <td>
                            <span class="badge bg-{{ $paper->status == 'Accepted' ? 'success' : ($paper->status == 'Rejected' ? 'danger' : 'secondary') }}">
                                {{ $paper->status }}
                            </span>
                        </td>
                        <td>{{ $paper->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('paper.show', $paper->paper_id) }}" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</div>
@endsection

@extends('layout.app')

@section('title', 'Conferences')

@section('content')
<h3>Submitted Papers</h3>
<table class="table table-striped table-hover shadow-sm mb-4">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($papers ?? [] as $paper)
        <tr>
            <td>{{ $paper->paper_id ?? '-' }}</td>
            <td>{{ $paper->title ?? '-' }}</td>
            <td>{{ optional($paper->author)->name ?? 'N/A' }}</td>
            <td>
                <span class="badge bg-{{ $paper->status == 'Accepted' ? 'success' : ($paper->status == 'Rejected' ? 'danger' : 'secondary') }}">
                    {{ $paper->status ?? 'Pending' }}
                </span>
            </td>
            <td>
                @if(isset($paper->paper_id))
                <a href="{{ route('paper.show', $paper->paper_id) }}" class="btn btn-sm btn-primary">View</a>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No papers submitted yet.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<h3>All Conferences</h3>

<a href="{{ route('conference.create') }}" class="btn btn-success mb-3">Add New Conference</a>

<table class="table table-striped table-hover shadow-sm">
    <thead class="table-dark">
        <tr>
            <th>Code</th>
            <th>Title</th>
            <th>Description</th>
            <th>Deadline</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($conferences as $conf)
        <tr>
            <td>{{ $conf->conference_code }}</td>
            <td>{{ $conf->title }}</td>
            <td>{{ $conf->description ?? '-' }}</td>
            <td>{{ $conf->deadline ? \Carbon\Carbon::parse($conf->deadline)->format('Y-m-d') : '-' }}</td>
            <td>
                <!-- Remove button -->
                <form action="{{ route('conference.destroy', $conf->conference_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this conference?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                </form>
            </td>
            <td>
    <a href="{{ route('conference.editDate', $conf->conference_id) }}" class="btn btn-sm btn-warning">Change Date</a>
</td>

        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No conferences added yet.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection

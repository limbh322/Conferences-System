@extends('layout.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mt-4">

    {{-- ===== SUCCESS ALERT ===== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ===== PAGE HEADER ===== --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold">Admin Dashboard</h2>
        <p class="text-muted">Manage conferences, deadlines, and reviewers — with live search and fading effect.</p>
    </div>

    {{-- ✅ CREATE CONFERENCE BUTTON --}}
    <div class="mb-4 text-end">
        <a href="{{ route('conference.create') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle"></i> Create New Conference
        </a>
    </div>

    {{-- ===== AVAILABLE CONFERENCES ===== --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Available Conferences</h4>
            <input type="text" id="searchAvailable" class="form-control form-control-sm w-50"
                   placeholder="Search by code or title...">
        </div>

        <div class="card-body">
            <table class="table table-striped table-hover align-middle" id="availableTable">
                <thead class="table-success">
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Deadline</th>
                        <th>Reviewers</th>
                        <th>Submissions</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $availableConfs = $conferences->filter(fn($c) => $c->deadline >= now());
                    @endphp

                    @forelse($availableConfs as $conf)
                        <tr>
                            <td>{{ $conf->conference_code }}</td>
                            <td>{{ $conf->title }}</td>
                            <td>{{ $conf->description }}</td>
                            <td class="text-success fw-semibold">
                                {{ \Carbon\Carbon::parse($conf->deadline)->format('Y-m-d H:i') }}
                            </td>
                            <td>
                                @if($conf->reviewers->count())
                                    @foreach($conf->reviewers as $reviewer)
                                        <span class="badge bg-info text-dark mb-1">{{ $reviewer->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted fst-italic">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                @php $paperCount = $conf->papers->count(); @endphp
                                {{ $paperCount }}
                                @if($paperCount > 0)
                                    <a href="{{ route('conference.papers', $conf->conference_code) }}" 
                                       class="btn btn-sm btn-outline-primary ms-2">View</a>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('conference.edit', $conf->conference_code) }}" 
                                       class="btn btn-sm btn-warning">Edit</a>
                                    <a href="{{ route('conference.editReviewers', $conf->conference_code) }}" 
                                       class="btn btn-sm btn-primary">Assign</a>
                                    <form action="{{ route('conference.destroy', $conf->conference_code) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Remove this conference?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No available conferences found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== CLOSED CONFERENCES ===== --}}
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Closed Conferences</h4>
            <input type="text" id="searchClosed" class="form-control form-control-sm w-50"
                   placeholder="Search by code or title...">
        </div>

        <div class="card-body">
            <table class="table table-striped table-hover align-middle" id="closedTable">
                <thead class="table-dark">
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Deadline</th>
                        <th>Reviewers</th>
                        <th>Submissions</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $closedConfs = $conferences->filter(fn($c) => $c->deadline < now());
                    @endphp

                    @forelse($closedConfs as $conf)
                        <tr>
                            <td>{{ $conf->conference_code }}</td>
                            <td>{{ $conf->title }}</td>
                            <td>{{ $conf->description }}</td>
                            <td class="text-danger fw-bold">
                                {{ \Carbon\Carbon::parse($conf->deadline)->format('Y-m-d H:i') }}
                            </td>
                            <td>
                                @if($conf->reviewers->count())
                                    @foreach($conf->reviewers as $reviewer)
                                        <span class="badge bg-info text-dark mb-1">{{ $reviewer->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted fst-italic">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                @php $paperCount = $conf->papers->count(); @endphp
                                {{ $paperCount }}
                                @if($paperCount > 0)
                                    <a href="{{ route('conference.papers', $conf->conference_code) }}" 
                                       class="btn btn-sm btn-outline-primary ms-2">View</a>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('conference.edit', $conf->conference_code) }}" 
                                   class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('conference.destroy', $conf->conference_code) }}" 
                                      method="POST" 
                                      class="d-inline" 
                                      onsubmit="return confirm('Remove this conference?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No closed conferences found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

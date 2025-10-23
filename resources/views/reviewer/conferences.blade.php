@extends('layout.reviewer')

@section('title', 'Assigned Conferences')

@section('content')
<div class="container mt-4">
    <h3>Conferences Assigned to You</h3>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Conference Code</th>
                <th>Conference Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($conferences as $conference)
            <tr>
                <td>{{ $conference->conference_code }}</td>
                <td>{{ $conference->name }}</td>
                <td>
                    <a href="{{ route('reviewer.conference.papers', $conference->id) }}" class="btn btn-primary btn-sm">View Papers</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

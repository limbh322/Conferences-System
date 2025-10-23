@extends('layout.app')

@section('title', 'Manage Users')

@section('content')
<div class="container mt-4">
    <h2>Manage Registered Users</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Current Role</th>
                <th>Assign Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                    <form action="{{ route('admin.assignRole', $user) }}" method="POST">
                        @csrf
                        <select name="role" class="form-select" required>
                            <option value="">Select Role</option>
                            <option value="Admin">Admin</option>
                            <option value="Reviewer">Reviewer</option>
                            <option value="Author">Author</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm mt-1">Assign</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

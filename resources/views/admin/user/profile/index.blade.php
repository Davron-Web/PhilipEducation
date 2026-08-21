@extends('layouts.admin')

@section('title', 'Profiles')

@section('content')
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Profiles</h1>
        </div>
    </div>

    <div class="card">
        <form method="GET" style="margin-bottom: 16px; display: flex; gap: 8px;">
            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Avatar</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone</th>
                <th>Birth Date</th>
                <th>Gender</th>
                <th>Country</th>
                <th>City</th>
                <th>Timezone</th>
                <th>Native Language</th>
                <th>Learning Goal</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($profiles as $profile)
                <tr>
                    <td>{{ $profile->id }}</td>
                    <td>{{ $profile->user?->name ?? '-' }}</td>
                    <td>
                        @if($profile->avatar)
                            <img src="{{ asset('storage/' . $profile->avatar) }}" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $profile->first_name ?? '-' }}</td>
                    <td>{{ $profile->last_name ?? '-' }}</td>
                    <td>{{ $profile->phone ?? '-' }}</td>
                    <td>{{ $profile->birth_date?->format('d.m.Y') ?? '-' }}</td>
                    <td>{{ $profile->gender ?? '-' }}</td>
                    <td>{{ $profile->country ?? '-' }}</td>
                    <td>{{ $profile->city ?? '-' }}</td>
                    <td>{{ $profile->timezone }}</td>
                    <td>{{ $profile->native_language }}</td>
                    <td>{{ $profile->learning_goal }}</td>
                    <td class="actions">
                        <a href="{{ route('admin.user.profiles.show', $profile) }}" class="btn btn-primary">View</a>
                        <a href="{{ route('admin.user.profiles.edit', $profile) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('admin.user.profiles.destroy', $profile) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="14" style="text-align: center;">No profiles found</td></tr>
            @endforelse
            </tbody>
        </table>

        @if($profiles->hasPages())
            <div style="margin-top: 16px;">
                {{ $profiles->links() }}
            </div>
        @endif
    </div>
@endsection

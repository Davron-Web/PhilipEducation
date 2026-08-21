@extends('layouts.admin')

@section('title', 'Profile #' . $profile->id)

@section('content')
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Profile Details</h1>
            <div>
                <a href="{{ route('admin.user.profiles.edit', $profile) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('admin.user.profiles.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>

    <div class="card">
        <table>
            <tr>
                <th>ID</th>
                <td>{{ $profile->id }}</td>
            </tr>
            <tr>
                <th>User</th>
                <td>
                    @if($profile->user)
                        <a href="{{ route('admin.user.users.show', $profile->user) }}">{{ $profile->user->name }}</a>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Avatar</th>
                <td>
                    @if($profile->avatar)
                        <img src="{{ asset('storage/' . $profile->avatar) }}" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>First Name</th>
                <td>{{ $profile->first_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Last Name</th>
                <td>{{ $profile->last_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $profile->phone ?? '-' }}</td>
            </tr>
            <tr>
                <th>Birth Date</th>
                <td>{{ $profile->birth_date?->format('d.m.Y') ?? '-' }}</td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>{{ $profile->gender ?? '-' }}</td>
            </tr>
            <tr>
                <th>Bio</th>
                <td>{{ $profile->bio ?? '-' }}</td>
            </tr>
            <tr>
                <th>Country</th>
                <td>{{ $profile->country ?? '-' }}</td>
            </tr>
            <tr>
                <th>City</th>
                <td>{{ $profile->city ?? '-' }}</td>
            </tr>
            <tr>
                <th>Timezone</th>
                <td>{{ $profile->timezone }}</td>
            </tr>
            <tr>
                <th>Native Language</th>
                <td>{{ $profile->native_language }}</td>
            </tr>
            <tr>
                <th>Learning Goal</th>
                <td>{{ $profile->learning_goal }}</td>
            </tr>
            <tr>
                <th>Created</th>
                <td>{{ $profile->created_at->format('d.m.Y H:i') }}</td>
            </tr>
            <tr>
                <th>Updated</th>
                <td>{{ $profile->updated_at->format('d.m.Y H:i') }}</td>
            </tr>
        </table>
    </div>
@endsection

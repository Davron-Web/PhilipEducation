@extends('layouts.admin')

@section('title', 'Level: ' . $level->name)

@section('content')
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Level Details</h1>
            <div>
                <a href="{{ route('admin.system.levels.edit', $level) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('admin.system.levels.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #7C3AED, #A855F7); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <span style="font-size: 2.5rem; font-weight: bold; color: white;">{{ $level->code }}</span>
                    </div>
                    <h4 class="mt-3">{{ $level->name }}</h4>
                </div>
                <div class="col-md-9">
                    <table class="table">
                        <tr>
                            <th>ID</th>
                            <td>{{ $level->id }}</td>
                        </tr>
                        <tr>
                            <th>Code</th>
                            <td><span class="badge bg-primary" style="font-size: 1rem;">{{ $level->code }}</span></td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td><strong>{{ $level->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $level->description ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $level->created_at?->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $level->updated_at?->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($level->lessons && $level->lessons->count())
        <div class="card mt-4">
            <div class="card-header">
                <h3>Lessons ({{ $level->lessons->count() }})</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($level->lessons as $lesson)
                            <tr>
                                <td>{{ $lesson->id }}</td>
                                <td>{{ $lesson->title }}</td>
                                <td>
                                    <a href="{{ route('admin.system.levels.show', $lesson) }}" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-4">
        <form action="{{ route('admin.system.levels.destroy', $level) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this level?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete Level</button>
        </form>
    </div>
@endsection

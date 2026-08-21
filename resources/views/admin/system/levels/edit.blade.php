@extends('layouts.admin')

@section('title', 'Edit Level')

@section('content')
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Edit Level: {{ $level->name }}</h1>
            <a href="{{ route('admin.system.levels.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <form action="{{ route('admin.system.levels.update', $level) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $level->code) }}" maxlength="10" required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $level->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $level->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@endsection

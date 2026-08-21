@extends('layouts.admin')

@section('title', 'Create Level')

@section('content')
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Create Level</h1>
            <a href="{{ route('admin.system.levels.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <form action="{{ route('admin.system.levels.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" maxlength="10" placeholder="A1, A2, B1, B2..." required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">CEFR level code: A1, A2, B1, B2, C1, C2</div>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Beginner, Elementary..." required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@endsection

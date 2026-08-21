@extends('layouts.admin')

@section('title', 'Edit User Achievement')

@section('content')
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Edit Achievement Award #{{ $userAchievement->id }}</h1>
            <a href="{{ route('admin.user.userachievements.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <form action="{{ route('admin.user.userachievements.update', $userAchievement) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                        <option value="">Select User</option>
                        @foreach($users as $id => $name)
                            <option value="{{ $id }}" {{ old('user_id', $userAchievement->user_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="achievement_id" class="form-label">Achievement <span class="text-danger">*</span></label>
                    <select class="form-select @error('achievement_id') is-invalid @enderror" id="achievement_id" name="achievement_id" required>
                        <option value="">Select Achievement</option>
                        @foreach($achievements as $id => $title)
                            <option value="{{ $id }}" {{ old('achievement_id', $userAchievement->achievement_id) == $id ? 'selected' : '' }}>{{ $title }}</option>
                        @endforeach
                    </select>
                    @error('achievement_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="earned_at" class="form-label">Earned At <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('earned_at') is-invalid @enderror" id="earned_at" name="earned_at" value="{{ old('earned_at', $userAchievement->earned_at?->format('Y-m-d\TH:i')) }}" required>
                    @error('earned_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Create Profile')

@section('content')
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Create Profile</h1>
            <a href="{{ route('admin.user.profiles.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('admin.user.profiles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="user_id">User <span class="text-danger">*</span></label>
                <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                    <option value="">Select User</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="avatar">Avatar</label>
                <input type="file" name="avatar" id="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}">
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="birth_date">Birth Date</label>
                    <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}">
                    @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="gender">Gender</label>
                <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="bio">Bio</label>
                <textarea name="bio" id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror">{{ old('bio') }}</textarea>
                @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="country">Country</label>
                    <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country') }}">
                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}">
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="timezone">Timezone</label>
                    <input type="text" name="timezone" id="timezone" class="form-control @error('timezone') is-invalid @enderror" value="{{ old('timezone', 'UTC') }}">
                    @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="native_language">Native Language</label>
                    <input type="text" name="native_language" id="native_language" class="form-control @error('native_language') is-invalid @enderror" value="{{ old('native_language', 'en') }}">
                    @error('native_language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="learning_goal">Learning Goal</label>
                <select name="learning_goal" id="learning_goal" class="form-control @error('learning_goal') is-invalid @enderror">
                    <option value="beginner" {{ old('learning_goal', 'beginner') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ old('learning_goal') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="advanced" {{ old('learning_goal') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    <option value="fluent" {{ old('learning_goal') == 'fluent' ? 'selected' : '' }}>Fluent</option>
                </select>
                @error('learning_goal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-success">Create</button>
        </form>
    </div>
@endsection

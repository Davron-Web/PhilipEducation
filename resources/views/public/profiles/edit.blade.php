@extends('layouts.app')

@section('title', 'Settings')
@section('page_title', 'Settings')
@section('page_description', 'Manage your profile and security')

@section('content')
    @if(session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Password updated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-person me-2"></i>Profile Information</h2>

                    <form method="POST" action="{{ route('profiles.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <div id="password" class="card shadow-sm rounded-4 border-0 mt-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-shield-lock me-2"></i>Change Password</h2>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-shield-check me-1"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-4 text-center">
                    <span class="avatar-circle mx-auto mb-3" style="width: 4rem; height: 4rem; font-size: 1.4rem;">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </span>
                    <h3 class="h6 fw-bold mb-1">{{ $user->name }}</h3>
                    <p class="text-secondary small mb-0">{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

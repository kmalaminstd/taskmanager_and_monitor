@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="login-card">
    <div class="login-header">
        <h3 class="mb-1"><i class="bi bi-check2-square"></i> TASK MASTER</h3>
        <p class="mb-0 opacity-75" style="font-size:0.9rem;">Task Management System</p>
    </div>
    <div class="p-4 p-md-5">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" id="email"
                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus
                       placeholder="you@example.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password"
                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                       required placeholder="••••••••">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label for="remember" class="form-check-label">Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </button>
        </form>

        <div class="mt-4 text-center text-muted" style="font-size:0.8rem;">
            <p class="mb-1 fw-medium">Demo Accounts</p>
            <p class="mb-0">Admin: admin@taskmaster.test / password</p>
            <p class="mb-0">Supervisor: supervisor@taskmaster.test / password</p>
            <p class="mb-0">Assignee: rahim@taskmaster.test / password</p>
        </div>
    </div>
</div>
@endsection

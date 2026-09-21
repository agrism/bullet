@extends('admin.layouts.app')

@section('title', 'Change Admin Password')

@section('content')
<div style="max-width: 540px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-bottom: 4px;">
            Account Security
        </h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Update the administrator login password.
        </p>
    </div>

    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Change Password</h2>
        </div>
        <div class="card__body">
            <form action="{{ route('admin.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 20px;">
                    <label for="current_password" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; outline: none;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="password" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">New Password</label>
                    <input type="password" id="password" name="password" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; outline: none;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label for="password_confirmation" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; outline: none;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

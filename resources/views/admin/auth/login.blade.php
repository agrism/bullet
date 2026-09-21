@extends('admin.layouts.app')

@section('title', 'Admin Login')

@section('content')
<div style="min-height: calc(100vh - 120px); display: flex; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 420px; width: 100%;">
        <div class="card__header" style="text-align: center; justify-content: center; background: #242166; color: #ffffff; padding: 28px 24px;">
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800; letter-spacing: 0.05rem;">BULLET <span class="admin-nav__badge">CMS</span></h1>
                <p style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-top: 4px;">Sign in to edit website content</p>
            </div>
        </div>

        <div class="card__body" style="padding: 28px;">
            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf

                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px; color: var(--text-dark);">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@bullet.legal') }}" required autofocus
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor='#242166'" onblur="this.style.borderColor='var(--border-color)'">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="password" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px; color: var(--text-dark);">Password</label>
                    <input type="password" id="password" name="password" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor='#242166'" onblur="this.style.borderColor='var(--border-color)'">
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted); cursor: pointer;">
                        <input type="checkbox" name="remember" value="1" checked>
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px; font-size: 1rem;">
                    Sign In to Dashboard
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<h2 class="auth-title">Password Baru 🔑</h2>
<p class="auth-subtitle">Satu langkah lagi! Silakan buat password baru Anda.</p>

<form action="{{ route('password.update') }}" method="POST">
    @csrf
    <input type="hidden" name="phone" value="{{ $phone }}">
    <input type="hidden" name="token" value="{{ $token }}">

    {{-- Password --}}
    <div class="mb-3">
        <label class="form-label">Password Baru</label>
        <div class="input-group-icon">
            <i class="bi bi-lock icon"></i>
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Minimal 8 karakter" required autofocus>
            <i class="bi bi-eye toggle-pw"
               onclick="togglePw('password', this)"
               style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#9ca3af; font-size:18px; z-index:5;"></i>
        </div>
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Konfirmasi Password --}}
    <div class="mb-4">
        <label class="form-label">Konfirmasi Password</label>
        <div class="input-group-icon">
            <i class="bi bi-lock-fill icon"></i>
            <input type="password" name="password_confirmation" id="password_confirm"
                class="form-control"
                placeholder="Ulangi password baru" required>
            <i class="bi bi-eye toggle-pw"
               onclick="togglePw('password_confirm', this)"
               style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#9ca3af; font-size:18px; z-index:5;"></i>
        </div>
    </div>

    <button type="submit" class="btn-login">
        <i class="bi bi-check-circle me-2"></i> Perbarui Password
    </button>
</form>
@endsection

@section('scripts')
<script>
function togglePw(fieldId, icon) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endsection

@extends('layouts.auth')

@section('title', 'Daftar Akun Siswa')

@section('content')
{{-- Brand --}}
<a href="{{ route('login') }}" class="brand-logo">
    @if(setting('school_logo'))
        <img src="{{ school_logo() }}" alt="Logo" style="height: 40px; margin-right: 10px;">
    @else
        <div class="logo-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
    @endif
    <span>{{ setting('school_name', 'Sistem SPP') }}</span>
</a>

<h2 class="auth-title">Daftar Akun Siswa 📝</h2>
<p class="auth-subtitle">Isi data diri Anda untuk membuat akun login.</p>

{{-- Alert Error --}}
@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-3" style="border-radius:10px; font-size:14px;">
        <i class="bi bi-exclamation-circle-fill flex-shrink-0 mt-1"></i>
        <ul class="mb-0 ps-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('register') }}" method="POST">
    @csrf

    {{-- Nama --}}
    <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <div class="input-group-icon">
            <i class="bi bi-person icon"></i>
            <input type="text" name="name"
                class="form-control @error('name') is-invalid @enderror"
                placeholder="Nama sesuai rapor"
                value="{{ old('name') }}" autofocus>
        </div>
    </div>

    {{-- NIS & Kelas side by side --}}
    <div class="row g-2 mb-3">
        <div class="col-6">
            <label class="form-label">NIS</label>
            <div class="input-group-icon">
                <i class="bi bi-card-text icon"></i>
                <input type="text" name="nis"
                    class="form-control @error('nis') is-invalid @enderror"
                    placeholder="Nomor Induk Siswa"
                    value="{{ old('nis') }}">
            </div>
        </div>
        <div class="col-6">
            <label class="form-label">Kelas</label>
            <select name="class_room_id"
                class="form-select @error('class_room_id') is-invalid @enderror"
                style="height:48px; border-radius:10px; border:1.5px solid #e5e7eb; font-size:14px;">
                <option value="">-- Pilih --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ old('class_room_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- No HP --}}
    <div class="mb-3">
        <label class="form-label">No. WhatsApp Aktif</label>
        <div class="input-group-icon">
            <i class="bi bi-whatsapp icon"></i>
            <input type="text" name="parent_phone"
                class="form-control @error('parent_phone') is-invalid @enderror"
                placeholder="08xxxxxxxxxx"
                value="{{ old('parent_phone') }}" required>
        </div>
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="input-group-icon">
            <i class="bi bi-envelope icon"></i>
            <input type="email" name="email"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="contoh@email.com"
                value="{{ old('email') }}">
        </div>
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label class="form-label">Password <span class="text-muted fw-normal">(min. 8 karakter)</span></label>
        <div class="input-group-icon">
            <i class="bi bi-lock icon"></i>
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Buat password">
            <i class="bi bi-eye toggle-pw"
               onclick="togglePw('password', this)"
               style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#9ca3af; font-size:18px; z-index:5;"></i>
        </div>
    </div>

    {{-- Konfirmasi Password --}}
    <div class="mb-4">
        <label class="form-label">Konfirmasi Password</label>
        <div class="input-group-icon">
            <i class="bi bi-lock-fill icon"></i>
            <input type="password" name="password_confirmation" id="password_confirm"
                class="form-control"
                placeholder="Ulangi password">
            <i class="bi bi-eye toggle-pw"
               onclick="togglePw('password_confirm', this)"
               style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#9ca3af; font-size:18px; z-index:5;"></i>
        </div>
    </div>

    <button type="submit" class="btn-login">
        <i class="bi bi-person-plus me-2"></i> Buat Akun
    </button>
</form>

<p class="text-center mt-4 mb-0" style="font-size:14px; color:#6b7280;">
    Sudah punya akun?
    <a href="{{ route('login') }}" style="color:#6366f1; font-weight:600; text-decoration:none;">Masuk di sini</a>
</p>
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

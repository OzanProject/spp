@extends('backend.layouts.master')

@section('title', 'Edit Pengguna')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold">Edit Pengguna</h3>
            <p class="text-muted small">Perbarui informasi akun untuk {{ $user->name }}.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-pill px-4 border">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>
</div>

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4 text-center">
                            <div class="bg-light-info p-4 rounded-circle d-inline-flex mb-3">
                                <i class="bi bi-person-check text-info display-6"></i>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-pill @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control rounded-pill @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold">Role Akses</label>
                            <select name="role" class="form-select rounded-pill @error('role') is-invalid @enderror" required>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Siswa</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="alert alert-light-primary border-0 small mb-4">
                            <i class="bi bi-info-circle me-2"></i> Kosongkan password jika tidak ingin diubah.
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="form-control rounded-pill @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-pill" placeholder="Ulangi password baru">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-info py-2 rounded-pill fw-bold shadow-sm text-white">
                                <i class="bi bi-check2-circle me-2"></i> Perbarui Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('backend.layouts.master')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-primary">Manajemen Akun</h3>
            <p class="text-muted small">Kelola akses dan keamanan akun sistem secara terpusat.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-person-plus-fill me-2"></i> Tambah User
        </a>
    </div>
</div>

<div class="page-content">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            {{-- Filter & Search --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 mb-4 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill py-2" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="role" class="form-select bg-light border-0 rounded-pill py-2" onchange="this.form.submit()">
                        <option value="">Semua Hak Akses</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Siswa</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-light-primary rounded-pill w-100 fw-bold py-2">Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light-primary text-primary">
                        <tr class="text-uppercase small fw-extrabold">
                            <th class="ps-4 border-0">Profil Pengguna</th>
                            <th class="border-0">Role</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">Tgl Daftar</th>
                            <th class="pe-4 text-end border-0">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="transition-hover">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3">
                                        <div class="avatar-content bg-light-primary text-primary fw-bold" style="width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $user->name }}</h6>
                                        <span class="text-muted smaller">ID: #{{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-light-danger text-danger px-3 rounded-pill small fw-bold">ADMIN</span>
                                @else
                                    <span class="badge bg-light-primary text-primary px-3 rounded-pill small fw-bold">SISWA</span>
                                @endif
                            </td>
                            <td><span class="text-dark small">{{ $user->email }}</span></td>
                            <td><span class="text-muted small">{{ $user->created_at->translatedFormat('d M Y') }}</span></td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    {{-- Reset Password --}}
                                    <form action="{{ route('admin.users.resetPassword', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-light-warning rounded-pill px-3 fw-bold confirm-reset" title="Reset Password ke 12345">
                                            <i class="bi bi-shield-lock me-1"></i> <small>Reset</small>
                                        </button>
                                    </form>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-light-info rounded-pill px-3 fw-bold" title="Edit Data">
                                        <i class="bi bi-pencil-square me-1"></i> <small>Edit</small>
                                    </a>

                                    {{-- Delete --}}
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-light-danger rounded-pill px-3 fw-bold confirm-delete" title="Hapus User">
                                            <i class="bi bi-trash-fill me-1"></i> <small>Hapus</small>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="opacity-25">
                                    <i class="bi bi-person-x display-1"></i>
                                </div>
                                <p class="text-muted mt-3">Data pengguna tidak ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .transition-hover { transition: all 0.2s ease; }
    .transition-hover:hover { background-color: rgba(67, 94, 190, 0.02) !important; }
    .btn-sm { font-size: 11px; }
    .badge { font-weight: 700; letter-spacing: 0.5px; }
</style>
@endsection

@section('scripts')
<script>
    // Konfirmasi Reset Password
    document.querySelectorAll('.confirm-reset').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reset Password User?',
                text: "Password akan dikembalikan ke default: 12345",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                background: '#fff',
                borderRadius: '1rem'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            });
        });
    });

    // Konfirmasi Hapus
    document.querySelectorAll('.confirm-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Akun Ini?',
                text: "Tindakan ini tidak dapat dibatalkan!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Permanen!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                background: '#fff',
                borderRadius: '1rem'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            });
        });
    });
</script>
@endsection

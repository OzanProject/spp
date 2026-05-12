@extends('backend.layouts.master')

@section('title', 'Data Kelas')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Data Kelas</h3>
                <p class="text-subtitle text-muted">Kelola data kelas yang tersedia di sekolah.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Kelas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            {{-- Form Tambah Kelas (kiri) --}}
            <div class="col-12 col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Tambah Kelas Baru</h5>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.classes.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Nama Kelas</label>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Contoh: X RPL 1"
                                    value="{{ old('name') }}"
                                    autofocus
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Masukkan nama kelas, misal: X IPA 1, XI IPS 2, XII RPL 1, dst.</small>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Simpan Kelas
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tabel Daftar Kelas (kanan) --}}
            <div class="col-12 col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Daftar Kelas</h5>
                        <span class="badge bg-primary rounded-pill">{{ $classes->count() }} Kelas</span>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kelas</th>
                                        <th class="text-center">Jml. Siswa</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($classes as $index => $class)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $class->name }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light-primary rounded-pill">
                                                {{ $class->students_count }} siswa
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.classes.edit', $class->id) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.classes.destroy', $class->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus kelas {{ $class->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            Belum ada data kelas. Tambahkan kelas di form sebelah kiri.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

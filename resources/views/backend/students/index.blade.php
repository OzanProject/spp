@extends('backend.layouts.master')

@section('title', 'Data Siswa')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Data Siswa</h3>
                <p class="text-subtitle text-muted">Kelola data siswa, kelas, dan akun login.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Data Siswa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        {{-- Bulk Action Info (Static, not fixed) --}}
        <div id="bulkDeleteSection" class="alert alert-dark d-none animate__animated animate__fadeIn mb-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <span class="fw-bold"><span id="selectedCount">0</span> siswa dipilih untuk tindakan massal</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-danger btn-sm px-3" onclick="submitBulkDelete()">
                        <i class="bi bi-trash me-1"></i> Hapus Permanen
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-3" onclick="deselectAll()">Batal</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h5 class="card-title mb-0">Daftar Siswa</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-file-earmark-arrow-up"></i> Import
                    </button>
                    <a href="{{ route('admin.students.export') }}" class="btn btn-outline-info">
                        <i class="bi bi-file-earmark-arrow-down"></i> Export
                    </a>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Tambah Siswa
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Filter & Search Form --}}
                <form method="GET" action="{{ route('admin.students.index') }}" class="row g-3 mb-4 align-items-center">
                    <div class="col-md-2 col-4">
                        <label class="form-label text-muted small mb-1">Tampilkan</label>
                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Baris</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 Baris</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Baris</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Baris</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-8">
                        <label class="form-label text-muted small mb-1">Urutkan Berdasarkan</label>
                        <select name="sort" class="form-select" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                            <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>Nama (A - Z)</option>
                            <option value="za" {{ request('sort') == 'za' ? 'selected' : '' }}>Nama (Z - A)</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-12 ms-auto">
                        <label class="form-label text-muted small mb-1 d-none d-md-block">&nbsp;</label>
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari NIS, Nama, atau Email...">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </form>

                <form id="bulkDeleteForm" action="{{ route('admin.students.bulkDestroy') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase small tracking-wider">
                                    <th width="40" class="ps-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input border-secondary" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $student)
                                <tr>
                                    <td class="ps-4">
                                        <div class="form-check">
                                            <input type="checkbox" name="ids[]" value="{{ $student->id }}" class="form-check-input border-secondary checkItem">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md me-3">
                                                @if($student->user->photo && Storage::disk('public')->exists('photos/' . $student->user->photo))
                                                    <img src="{{ Storage::url('photos/' . $student->user->photo) }}" class="rounded-circle" style="object-fit: cover;">
                                                @else
                                                    <div class="avatar-content bg-light-primary text-primary fw-bold rounded-circle border border-primary border-opacity-10 d-flex align-items-center justify-content-center" style="width: 100%; height: 100%;">
                                                        {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-bold mb-0 text-dark">{{ $student->user->name }}</p>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <span class="text-muted small"><i class="bi bi-envelope-at me-1"></i>{{ $student->user->email }}</span>
                                                    <span class="badge bg-light-secondary text-secondary" style="font-size: 10px;">NIS: {{ $student->nis }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($student->classRoom)
                                            <span class="badge bg-light-primary text-primary fw-bold">{{ $student->classRoom->name }}</span>
                                        @else
                                            <span class="text-muted italic small">Belum ada kelas</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="confirmDelete('{{ route('admin.students.destroy', $student->id) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="opacity-50">
                                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                                            <p class="text-muted mt-3">Tidak ada data siswa ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                <form id="singleDeleteForm" method="POST" style="display:none">
                    @csrf
                    @method('DELETE')
                </form>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <p class="text-muted small mb-0">Menampilkan {{ $students->firstItem() ?? 0 }} sampai {{ $students->lastItem() ?? 0 }} dari total {{ $students->total() }} siswa</p>
                    <div>
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Modal Import --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Siswa (Excel)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light-info">
                        Pastikan format file Anda sesuai dengan template yang kami sediakan.
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('admin.students.template') }}" class="btn btn-sm btn-info w-100">
                            <i class="bi bi-download"></i> Download Template Excel
                        </a>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Upload File Excel</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Proses Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(url) {
        if (confirm('Yakin ingin menghapus data ini secara permanen?')) {
            const form = document.getElementById('singleDeleteForm');
            form.action = url;
            form.submit();
        }
    }

    const checkAll = document.getElementById('checkAll');
    const checkItems = document.querySelectorAll('.checkItem');
    const bulkSection = document.getElementById('bulkDeleteSection');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkUI() {
        const checked = document.querySelectorAll('.checkItem:checked');
        if (checked.length > 0) {
            bulkSection.classList.remove('d-none');
            selectedCount.textContent = checked.length;
        } else {
            bulkSection.classList.add('d-none');
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkItems.forEach(item => item.checked = this.checked);
            updateBulkUI();
        });
    }

    checkItems.forEach(item => {
        item.addEventListener('change', updateBulkUI);
    });

    function deselectAll() {
        if (checkAll) checkAll.checked = false;
        checkItems.forEach(item => item.checked = false);
        updateBulkUI();
    }

    function submitBulkDelete() {
        if (confirm('Hapus semua data siswa yang dipilih?')) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }
</script>
@endsection

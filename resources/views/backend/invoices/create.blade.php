@extends('backend.layouts.master')

@section('title', 'Tambah Tagihan')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Tagihan Manual</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Tagihan SPP</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-12 col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Form Tagihan</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.invoices.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Siswa</label>
                                    <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->user->name }} - {{ $student->classRoom->name }} (NIS: {{ $student->nis }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Bulan</label>
                                    <select name="month" class="form-select @error('month') is-invalid @enderror" required>
                                        @foreach($months as $num => $name)
                                            <option value="{{ $num }}" {{ old('month', date('n')) == $num ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Tahun</label>
                                    <select name="year" class="form-select @error('year') is-invalid @enderror" required>
                                        @foreach($years as $y)
                                            <option value="{{ $y }}" {{ old('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                    @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Nominal (Rp)</label>
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                        value="{{ old('amount', 350000) }}" required>
                                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Jatuh Tempo</label>
                                    <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                                        value="{{ old('due_date', date('Y-m-10')) }}" required>
                                    @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Catatan (Opsional)</label>
                                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                </div>

                                <div class="col-12 d-flex justify-content-between">
                                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Simpan Tagihan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

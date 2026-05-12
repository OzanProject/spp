@extends('backend.layouts.master')
@section('title', 'Edit Tagihan')
@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Tagihan</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Tagihan SPP</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                        <h5 class="card-title">
                            Edit Tagihan: {{ $invoice->student->user->name }} &mdash; {{ $invoice->month_name }} {{ $invoice->year }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Siswa</label>
                                    <input type="text" class="form-control" value="{{ $invoice->student->user->name }} ({{ $invoice->student->classRoom->name }})" disabled>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Periode</label>
                                    <input type="text" class="form-control" value="{{ $invoice->month_name }} {{ $invoice->year }}" disabled>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="unpaid"  {{ $invoice->status === 'unpaid'  ? 'selected' : '' }}>Belum Bayar</option>
                                        <option value="paid"    {{ $invoice->status === 'paid'    ? 'selected' : '' }}>Lunas</option>
                                        <option value="overdue" {{ $invoice->status === 'overdue' ? 'selected' : '' }}>Jatuh Tempo</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Nominal (Rp)</label>
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                        value="{{ old('amount', $invoice->amount) }}" required>
                                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Jatuh Tempo</label>
                                    <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                                        value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
                                    @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $invoice->notes) }}</textarea>
                                </div>
                                <div class="col-12 d-flex justify-content-between">
                                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Batal</a>
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
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

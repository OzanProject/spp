@extends('backend.layouts.master')

@section('title', 'Edit Siswa')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Data Siswa</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Data Siswa</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <form class="form form-horizontal" action="{{ route('admin.students.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>NIS</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" class="form-control @error('nis') is-invalid @enderror" name="nis" value="{{ old('nis', $student->nis) }}" required>
                                    @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label>Nama Lengkap</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $student->user->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label>Kelas</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select class="form-control @error('class_room_id') is-invalid @enderror" name="class_room_id" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ (old('class_room_id', $student->class_room_id) == $class->id) ? 'selected' : '' }}>{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('class_room_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label>No. HP Orang Tua</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" class="form-control @error('parent_phone') is-invalid @enderror" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}">
                                    @error('parent_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mt-4">
                                    <label>Email (Untuk Login)</label>
                                </div>
                                <div class="col-md-8 form-group mt-4">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $student->user->email) }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label>Password Baru</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Kosongkan jika tidak ingin ganti password">
                                    <small class="text-muted">Isi hanya jika ingin mengubah password lama.</small>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-sm-12 d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary me-1 mb-1">Update</button>
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

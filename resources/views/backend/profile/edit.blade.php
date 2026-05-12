@extends('backend.layouts.master')
@section('title', 'Edit Profil')
@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Profil</h3>
                <p class="text-subtitle text-muted">Perbarui data diri dan kata sandi akun Anda.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit Profil</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-center align-items-center flex-column">
                            <div class="avatar avatar-xl bg-primary text-white d-flex align-items-center justify-content-center fw-bold mb-3" style="font-size: 2.5rem; width: 120px; height: 120px; overflow: hidden;">
                                @if($user->photo)
                                    <img src="{{ Storage::url('photos/' . $user->photo) }}" id="avatarPreview" style="object-fit: cover; width: 100%; height: 100%;">
                                @else
                                    <span id="avatarInitials">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    <img src="" id="avatarPreview" style="object-fit: cover; width: 100%; height: 100%; display: none;">
                                @endif
                            </div>
                            <h4 class="mb-0">{{ $user->name }}</h4>
                            <p class="text-muted mt-1">{{ $user->email }}</p>
                            <span class="badge bg-light-primary text-capitalize">{{ $user->role }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Detail Akun</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="form-group mb-4">
                                <label for="photo" class="form-label">Foto Profil (Opsional)</label>
                                <input type="file" name="photo" id="photo" class="form-control" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(event)">
                                <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal: 2MB.</small>
                            </div>

                            <hr>
                            <h6 class="mt-4 mb-3">Ubah Kata Sandi <small class="text-muted fw-normal">(Kosongkan jika tidak ingin mengubah)</small></h6>
                            
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="password" class="form-label">Kata Sandi Baru</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter">
                                </div>
                                <div class="col-md-6 form-group mb-4">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ketik ulang sandi baru">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('avatarPreview');
            var initials = document.getElementById('avatarInitials');
            output.src = reader.result;
            output.style.display = 'block';
            if (initials) {
                initials.style.display = 'none';
            }
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endsection

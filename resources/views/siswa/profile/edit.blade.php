@extends('siswa.layouts.master')
@section('title', 'Profil Saya')
@section('content')
<div class="page-heading">
    <h3>Profil Saya</h3>
    <p class="text-subtitle text-muted">Perbarui data profil dan keamanan akun Anda.</p>
</div>

<div class="page-content">
    <section class="section">
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-center align-items-center flex-column">
                            <div class="avatar avatar-xl bg-primary text-white d-flex align-items-center justify-content-center fw-bold mb-3 shadow" style="width: 120px; height: 120px; overflow: hidden; font-size: 2.5rem;">
                                @if($user->photo)
                                    <img src="{{ Storage::url('photos/' . $user->photo) }}" id="avatarPreview" style="object-fit: cover; width: 100%; height: 100%;">
                                @else
                                    <span id="avatarInitials">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    <img src="" id="avatarPreview" style="object-fit: cover; width: 100%; height: 100%; display: none;">
                                @endif
                            </div>
                            <h4 class="mb-0">{{ $user->name }}</h4>
                            <p class="text-muted mt-1 mb-0">{{ $student->nis }}</p>
                            <span class="badge bg-light-primary mt-2">{{ $student->classRoom->name ?? '-' }}</span>
                        </div>
                        <hr>
                        <div class="mt-3">
                            <div class="mb-3">
                                <label class="small text-muted mb-1 d-block">Email</label>
                                <span class="fw-bold">{{ $user->email }}</span>
                            </div>
                            <div>
                                <label class="small text-muted mb-1 d-block">No. HP Orang Tua</label>
                                <span class="fw-bold">{{ $student->parent_phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ubah Profil & Password</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('siswa.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="phone_parent" class="form-label">Nomor HP Orang Tua (WA)</label>
                                <input type="text" name="phone_parent" id="phone_parent" class="form-control" value="{{ old('phone_parent', $student->parent_phone) }}" required>
                                <small class="text-muted">Digunakan untuk notifikasi tagihan.</small>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="photo" class="form-label">Ganti Foto Profil</label>
                                <input type="file" name="photo" id="photo" class="form-control" accept="image/*" onchange="previewImage(event)">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti.</small>
                            </div>

                            <hr>
                            <h6 class="mt-4 mb-3 text-primary">Ubah Password Akun</h6>
                            
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="password" class="form-label">Password Baru</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter">
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ketik ulang password">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                                </button>
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

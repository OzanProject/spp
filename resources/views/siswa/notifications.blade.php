@extends('siswa.layouts.master')
@section('title', 'Notifikasi')
@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0 text-primary fw-bold">Notifikasi</h3>
            <p class="text-muted small">Pesan dan informasi terbaru untuk Anda.</p>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->unreadNotifications->count() > 0)
                <form action="{{ route('siswa.notifications.readAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-check2-all me-1"></i> Tandai Semua Terbaca
                    </button>
                </form>
            @endif
            @if(Auth::user()->notifications->count() > 0)
                <form action="{{ route('siswa.notifications.clear') }}" method="POST" onsubmit="return confirm('Hapus semua notifikasi?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-light-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-trash me-1"></i> Hapus Semua
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<div class="page-content">
    <section class="section">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
        @endif
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                @forelse(Auth::user()->notifications()->paginate(10) as $notification)
                    <div class="card shadow-sm border-0 mb-3 transition-hover {{ $notification->unread() ? 'border-start border-4 border-primary' : '' }}">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-light-{{ $notification->data['color'] ?? 'primary' }} p-3 rounded-circle text-{{ $notification->data['color'] ?? 'primary' }} d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                                    <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }} fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-bold mb-0">{{ $notification->data['title'] }}</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            <form action="{{ route('siswa.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
                                                    <i class="bi bi-trash small"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-3">{{ $notification->data['message'] }}</p>
                                    
                                    <div class="d-flex gap-2">
                                        @if(isset($notification->data['link']))
                                            <a href="{{ $notification->data['link'] }}" class="btn btn-sm btn-primary rounded-pill px-3">Lihat Detail</a>
                                        @endif
                                        @if($notification->unread())
                                            <form action="{{ route('siswa.notifications.read', $notification->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-light-secondary rounded-pill px-3">Tandai Terbaca</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card shadow-sm border-0 py-5">
                        <div class="card-body text-center py-5">
                            <div class="bg-light p-4 rounded-circle d-inline-flex mb-4">
                                <i class="bi bi-bell-slash display-4 text-muted"></i>
                            </div>
                            <h5 class="fw-bold">Belum ada notifikasi</h5>
                            <p class="text-muted">Semua pemberitahuan penting akan muncul di sini.</p>
                        </div>
                    </div>
                @endforelse

                <div class="d-flex justify-content-center mt-4">
                    {{ Auth::user()->notifications()->paginate(10)->links() }}
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .bg-light-primary { background-color: rgba(67, 94, 190, 0.1); }
    .bg-light-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-light-warning { background-color: rgba(255, 193, 7, 0.1); }
    .bg-light-danger { background-color: rgba(220, 53, 69, 0.1); }
    .transition-hover { transition: transform 0.2s; }
    .transition-hover:hover { transform: translateY(-3px); }
</style>
@endsection

@extends('siswa.layouts.master')

@section('title', 'Siswa Dashboard')

@section('content')
<div class="page-heading">
    <h3>Dashboard Siswa</h3>
</div> 
<div class="page-content"> 
    <section class="row">
        <div class="col-12 col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h4>Selamat datang, {{ Auth::user()->name }}!</h4>
                </div>
                <div class="card-body">
                    <p>Ini adalah dashboard khusus siswa. Di sini Anda bisa melihat tagihan dan riwayat pembayaran SPP Anda nantinya.</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl">
                            <img src="https://zuramai.github.io/mazer/demo/assets/static/images/faces/1.jpg" alt="Face 1">
                        </div>
                        <div class="ms-3 name text-truncate">
                            <h5 class="font-bold">{{ Auth::user()->name }}</h5>
                            <h6 class="text-muted mb-0 text-truncate">{{ Auth::user()->email }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

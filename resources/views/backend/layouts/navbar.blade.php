<header>
    <nav class="navbar navbar-expand navbar-light navbar-top">
        <div class="container-fluid">
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3"></i>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-lg-0">
                </ul>
                <div class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-menu d-flex align-items-center">
                            <div class="user-name text-end me-3">
                                <h6 class="mb-0 text-gray-600">{{ Auth::user()->name ?? 'Administrator' }}</h6>
                                <p class="mb-0 text-sm text-gray-600 text-capitalize">{{ Auth::user()->role ?? 'Admin' }}</p>
                            </div>
                             <div class="user-img d-flex align-items-center">
                                <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center fw-bold rounded-circle" style="width: 40px; height: 40px; overflow: hidden; font-size: 1.2rem;">
                                    @if(Auth::user()->photo)
                                        <img src="{{ Storage::url('photos/' . Auth::user()->photo) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="dropdownMenuButton" style="min-width: 12rem;">
                        <li>
                            <h6 class="dropdown-header px-4 py-2">
                                Selamat datang,<br>
                                <span class="fw-bold">{{ Auth::user()->name ?? 'Administrator' }}</span>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item px-4 py-2" href="{{ route('admin.profile.edit') }}">
                                <i class="icon-mid bi bi-person me-2 text-muted"></i> Edit Profil
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item px-4 py-2 text-danger">
                                    <i class="icon-mid bi bi-box-arrow-left me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>

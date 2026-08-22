<x-app-layout :title="($title ?? 'Dashboard').' — Altura Admin'">
    <div class="d-flex">
        {{-- Sidebar --}}
        <aside class="altura-sidebar d-none d-lg-flex flex-column p-3" style="width: 260px; flex-shrink: 0;">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none mb-4 px-1">
                <img src="{{ asset('images/logo.png') }}" alt="Altura" style="height: 40px; border-radius: 8px;">
                <span class="text-white fw-semibold" style="font-family: 'Poppins', sans-serif;">Altura Admin</span>
            </a>

            <nav class="nav flex-column flex-grow-1">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-gauge"></i>Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('admin.onboarding.*') ? 'active' : '' }}" href="{{ route('admin.onboarding.index') }}">
                    <i class="fa-solid fa-user-plus"></i>Onboarding
                </a>
                <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
                    <i class="fa-solid fa-users"></i>Students
                </a>
                <a class="nav-link {{ request()->routeIs('admin.job-seekers.*') ? 'active' : '' }}" href="{{ route('admin.job-seekers.index') }}">
                    <i class="fa-solid fa-briefcase"></i>Job Seekers
                </a>
                <a class="nav-link {{ request()->routeIs('admin.job-postings.*') ? 'active' : '' }}" href="{{ route('admin.job-postings.index') }}">
                    <i class="fa-solid fa-list-check"></i>Job Postings
                </a>
                <a class="nav-link {{ request()->routeIs('admin.study-postings.*') ? 'active' : '' }}" href="{{ route('admin.study-postings.index') }}">
                    <i class="fa-solid fa-graduation-cap"></i>Study Postings
                </a>
                <a class="nav-link {{ request()->routeIs('admin.visa-management.*') ? 'active' : '' }}" href="{{ route('admin.visa-management.index') }}">
                    <i class="fa-solid fa-passport"></i>Visa Management
                </a>
                <a class="nav-link {{ request()->routeIs('admin.payments-management.*') ? 'active' : '' }}" href="{{ route('admin.payments-management.index') }}">
                    <i class="fa-solid fa-money-bill-wave"></i>Payments Management
                </a>
                <a class="nav-link {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}" href="{{ route('admin.resources.index') }}">
                    <i class="fa-solid fa-book-open"></i>Resources
                </a>
                <a class="nav-link {{ request()->routeIs('admin.worker-requests.*') ? 'active' : '' }}" href="{{ route('admin.worker-requests.index') }}">
                    <i class="fa-solid fa-people-arrows"></i>Worker Requests
                </a>
                <a class="nav-link {{ request()->routeIs('admin.employers.*') ? 'active' : '' }}" href="{{ route('admin.employers.index') }}">
                    <i class="fa-solid fa-building"></i>Employers
                </a>
                {{-- Remaining Admin sections (Tickets queue, Tasks, Resources management, etc.)
                     land here as later deliverables build them out. --}}
            </nav>

            <div class="text-white-50 small px-1 mb-2">
                Logged in as<br>
                <strong class="text-white">{{ auth()->user()->name }}</strong>
                <div class="text-white-50" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.04em;">
                    {{ auth()->user()->getRoleNames()->first() ? str_replace('_', ' ', auth()->user()->getRoleNames()->first()) : '' }}
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>Log Out
                </button>
            </form>
        </aside>

        {{-- Main content --}}
        <div class="flex-grow-1" style="background: #F7F9FC; min-height: 100vh;">
            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 class="h5 mb-0 fw-semibold" style="font-family: 'Poppins', sans-serif; color: #082159;">{{ $title ?? 'Dashboard' }}</h1>
                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:.8rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Log Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </header>

            <main class="p-4">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Mobile sidebar (offcanvas) --}}
    <div class="offcanvas offcanvas-start altura-sidebar" tabindex="-1" id="adminMobileSidebar">
        <div class="offcanvas-header">
            <img src="{{ asset('images/logo.png') }}" alt="Altura" style="height: 36px; border-radius: 8px;">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge"></i>Dashboard</a>
                <a class="nav-link {{ request()->routeIs('admin.onboarding.*') ? 'active' : '' }}" href="{{ route('admin.onboarding.index') }}"><i class="fa-solid fa-user-plus"></i>Onboarding</a>
                <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}"><i class="fa-solid fa-users"></i>Students</a>
                <a class="nav-link {{ request()->routeIs('admin.job-seekers.*') ? 'active' : '' }}" href="{{ route('admin.job-seekers.index') }}"><i class="fa-solid fa-briefcase"></i>Job Seekers</a>
                <a class="nav-link {{ request()->routeIs('admin.job-postings.*') ? 'active' : '' }}" href="{{ route('admin.job-postings.index') }}"><i class="fa-solid fa-list-check"></i>Job Postings</a>
                <a class="nav-link {{ request()->routeIs('admin.study-postings.*') ? 'active' : '' }}" href="{{ route('admin.study-postings.index') }}"><i class="fa-solid fa-graduation-cap"></i>Study Postings</a>
                <a class="nav-link {{ request()->routeIs('admin.visa-management.*') ? 'active' : '' }}" href="{{ route('admin.visa-management.index') }}"><i class="fa-solid fa-passport"></i>Visa Management</a>
                <a class="nav-link {{ request()->routeIs('admin.payments-management.*') ? 'active' : '' }}" href="{{ route('admin.payments-management.index') }}"><i class="fa-solid fa-money-bill-wave"></i>Payments Management</a>
                <a class="nav-link {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}" href="{{ route('admin.resources.index') }}"><i class="fa-solid fa-book-open"></i>Resources</a>
                <a class="nav-link {{ request()->routeIs('admin.worker-requests.*') ? 'active' : '' }}" href="{{ route('admin.worker-requests.index') }}"><i class="fa-solid fa-people-arrows"></i>Worker Requests</a>
                <a class="nav-link {{ request()->routeIs('admin.employers.*') ? 'active' : '' }}" href="{{ route('admin.employers.index') }}"><i class="fa-solid fa-building"></i>Employers</a>
            </nav>
        </div>
    </div>
</x-app-layout>

<x-app-layout :title="($title ?? 'Dashboard').' — Altura Job Seeker Portal'">
    <div class="d-flex">
        {{-- Sidebar --}}
        <aside class="altura-sidebar d-none d-lg-flex flex-column p-3" style="width: 260px; flex-shrink: 0;">
            <a href="{{ route('job-seeker.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none mb-4 px-1">
                <img src="{{ asset('images/logo.png') }}" alt="Altura" style="height: 40px; border-radius: 8px;">
                <span class="text-white fw-semibold" style="font-family: 'Poppins', sans-serif;">Altura</span>
            </a>

            <nav class="nav flex-column flex-grow-1">
                <a class="nav-link {{ request()->routeIs('job-seeker.dashboard') ? 'active' : '' }}" href="{{ route('job-seeker.dashboard') }}">
                    <i class="fa-solid fa-gauge"></i>Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('job-seeker.jobs.*') ? 'active' : '' }}" href="{{ route('job-seeker.jobs.index') }}">
                    <i class="fa-solid fa-magnifying-glass"></i>Browse Jobs
                </a>
                <a class="nav-link {{ request()->routeIs('job-seeker.applications.*') ? 'active' : '' }}" href="{{ route('job-seeker.applications.index') }}">
                    <i class="fa-solid fa-file-lines"></i>My Applications
                </a>
                <a class="nav-link {{ request()->routeIs('job-seeker.documents.*') ? 'active' : '' }}" href="{{ route('job-seeker.documents.index') }}"><i class="fa-solid fa-folder-open"></i>Documents</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.payments.*') ? 'active' : '' }}" href="{{ route('job-seeker.payments.index') }}"><i class="fa-solid fa-sack-dollar"></i>Payments</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.invoices.*') ? 'active' : '' }}" href="{{ route('job-seeker.invoices.index') }}"><i class="fa-solid fa-file-invoice"></i>Invoices</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.appointments.*') ? 'active' : '' }}" href="{{ route('job-seeker.appointments.index') }}"><i class="fa-solid fa-calendar-days"></i>Interviews</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.offers.*') ? 'active' : '' }}" href="{{ route('job-seeker.offers.index') }}"><i class="fa-solid fa-handshake"></i>Job Offers</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.support.*') ? 'active' : '' }}" href="{{ route('job-seeker.support.index') }}"><i class="fa-solid fa-headset"></i>Support</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.notifications.*') ? 'active' : '' }}" href="{{ route('job-seeker.notifications.index') }}"><i class="fa-solid fa-bell"></i>Notifications</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.profile.*') ? 'active' : '' }}" href="{{ route('job-seeker.profile.edit') }}"><i class="fa-solid fa-user"></i>Profile</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.resources.*') ? 'active' : '' }}" href="{{ route('job-seeker.resources.index') }}"><i class="fa-solid fa-book-open"></i>Resources</a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>Log Out
                </button>
            </form>
        </aside>

        {{-- Main content --}}
        <div class="flex-grow-1" style="background: #F7F9FC; min-height: 100vh;">
            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebarJobSeeker">
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
                        <li><a class="dropdown-item" href="{{ route('job-seeker.profile.edit') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
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
    <div class="offcanvas offcanvas-start altura-sidebar" tabindex="-1" id="mobileSidebarJobSeeker">
        <div class="offcanvas-header">
            <img src="{{ asset('images/logo.png') }}" alt="Altura" style="height: 36px; border-radius: 8px;">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('job-seeker.dashboard') ? 'active' : '' }}" href="{{ route('job-seeker.dashboard') }}"><i class="fa-solid fa-gauge"></i>Dashboard</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.jobs.*') ? 'active' : '' }}" href="{{ route('job-seeker.jobs.index') }}"><i class="fa-solid fa-magnifying-glass"></i>Browse Jobs</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.applications.*') ? 'active' : '' }}" href="{{ route('job-seeker.applications.index') }}"><i class="fa-solid fa-file-lines"></i>My Applications</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.documents.*') ? 'active' : '' }}" href="{{ route('job-seeker.documents.index') }}"><i class="fa-solid fa-folder-open"></i>Documents</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.payments.*') ? 'active' : '' }}" href="{{ route('job-seeker.payments.index') }}"><i class="fa-solid fa-sack-dollar"></i>Payments</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.invoices.*') ? 'active' : '' }}" href="{{ route('job-seeker.invoices.index') }}"><i class="fa-solid fa-file-invoice"></i>Invoices</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.appointments.*') ? 'active' : '' }}" href="{{ route('job-seeker.appointments.index') }}"><i class="fa-solid fa-calendar-days"></i>Interviews</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.offers.*') ? 'active' : '' }}" href="{{ route('job-seeker.offers.index') }}"><i class="fa-solid fa-handshake"></i>Job Offers</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.support.*') ? 'active' : '' }}" href="{{ route('job-seeker.support.index') }}"><i class="fa-solid fa-headset"></i>Support</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.notifications.*') ? 'active' : '' }}" href="{{ route('job-seeker.notifications.index') }}"><i class="fa-solid fa-bell"></i>Notifications</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.profile.*') ? 'active' : '' }}" href="{{ route('job-seeker.profile.edit') }}"><i class="fa-solid fa-user"></i>Profile</a>
                <a class="nav-link {{ request()->routeIs('job-seeker.resources.*') ? 'active' : '' }}" href="{{ route('job-seeker.resources.index') }}"><i class="fa-solid fa-book-open"></i>Resources</a>
            </nav>
        </div>
    </div>
</x-app-layout>

<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($title ?? 'Dashboard').' — Altura Admin')]); ?>
    <div class="d-flex">
        
        <aside class="altura-sidebar d-none d-lg-flex flex-column p-3" style="width: 260px; flex-shrink: 0;">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="d-flex align-items-center gap-2 text-decoration-none mb-4 px-1">
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Altura" style="height: 40px; border-radius: 8px;">
                <span class="text-white fw-semibold" style="font-family: 'Poppins', sans-serif;">Altura Admin</span>
            </a>

            <nav class="nav flex-column flex-grow-1">
                <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                    <i class="fa-solid fa-gauge"></i>Dashboard
                </a>
                                <a class="nav-link <?php echo e(request()->routeIs('admin.payments-management.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.payments-management.index')); ?>">
                    <i class="fa-solid fa-money-bill-wave"></i>Payments
                </a>

                <a class="nav-link <?php echo e(request()->routeIs('admin.visa-management.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.visa-management.index')); ?>">
                    <i class="fa-solid fa-passport"></i>VISA Management
                </a>

                <a class="nav-link <?php echo e(request()->routeIs('admin.onboarding.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.onboarding.index')); ?>">
                    <i class="fa-solid fa-user-plus"></i>Onboarding
                </a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.job-postings.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.job-postings.index')); ?>">
                    <i class="fa-solid fa-list-check"></i>Job Postings
                </a>                
                <a class="nav-link <?php echo e(request()->routeIs('admin.study-postings.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.study-postings.index')); ?>">
                    <i class="fa-solid fa-graduation-cap"></i>Study Postings
               </a>

                <a class="nav-link <?php echo e(request()->routeIs('admin.worker-requests.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.worker-requests.index')); ?>">
                    <i class="fa-solid fa-people-arrows"></i>Worker Requests
                </a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.job-seekers.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.job-seekers.index')); ?>">
                    <i class="fa-solid fa-briefcase"></i>Job Seekers
                </a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.employers.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.employers.index')); ?>">
                    <i class="fa-solid fa-building"></i>Employers
                </a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.students.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.students.index')); ?>">
                    <i class="fa-solid fa-users"></i>Students
                </a>

                <a class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.users.index')); ?>">
                    <i class="fa-solid fa-users-gear"></i>All Users
                </a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.support.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.support.index')); ?>">
                    <i class="fa-solid fa-headset"></i>Support Tickets
                </a>

                <a class="nav-link <?php echo e(request()->routeIs('admin.resources.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.resources.index')); ?>">
                    <i class="fa-solid fa-book-open"></i>Resources
                </a>



            </nav>

            <div class="text-white-50 small px-1 mb-2">
                Logged in as<br>
                <strong class="text-white"><?php echo e(auth()->user()->name); ?></strong>
                <div class="text-white-50" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.04em;">
                    <?php echo e(auth()->user()->getRoleNames()->first() ? str_replace('_', ' ', auth()->user()->getRoleNames()->first()) : ''); ?>

                </div>
            </div>

            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>Log Out
                </button>
            </form>
        </aside>

        
        <div class="flex-grow-1" style="background: #F7F9FC; min-height: 100vh;">
            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 class="h5 mb-0 fw-semibold" style="font-family: 'Poppins', sans-serif; color: #082159;"><?php echo e($title ?? 'Dashboard'); ?></h1>
                <div class="d-flex align-items-center gap-2">
                    <?php $unreadNotifications = auth()->user()->unreadNotifications; ?>
                    <div class="dropdown">
                        <button class="btn btn-light position-relative" data-bs-toggle="dropdown" aria-label="Notifications">
                            <i class="fa-solid fa-bell"></i>
                            <?php if($unreadNotifications->count() > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">
                                    <?php echo e($unreadNotifications->count() > 9 ? '9+' : $unreadNotifications->count()); ?>

                                </span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-0" style="width:340px;max-height:420px;overflow-y:auto;">
                            <div class="px-3 py-2 border-bottom fw-semibold small" style="font-family:'Poppins',sans-serif;">Notifications</div>
                            <?php $__empty_1 = true; $__currentLoopData = $unreadNotifications->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <form method="POST" action="<?php echo e(route('admin.notifications.read', $notification->id)); ?>" class="d-flex align-items-start gap-2 px-3 py-2 border-bottom bg-primary-subtle bg-opacity-10">
                                    <?php echo csrf_field(); ?>
                                    <i class="fa-solid fa-<?php echo e($notification->data['icon'] ?? 'bell'); ?> text-primary mt-1"></i>
                                    <button type="submit" class="btn btn-link text-start text-decoration-none p-0 flex-grow-1">
                                        <div class="fw-semibold small text-dark"><?php echo e($notification->data['title'] ?? 'Notification'); ?></div>
                                        <div class="text-secondary" style="font-size:.75rem;"><?php echo e($notification->created_at->diffForHumans()); ?></div>
                                    </button>
                                </form>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="px-3 py-4 text-center text-secondary small">You're all caught up.</div>
                            <?php endif; ?>
                            <a href="<?php echo e(route('admin.notifications.index')); ?>" class="d-block text-center py-2 small border-top">View All Notifications</a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:.8rem;">
                                <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                            </span>
                            <span class="d-none d-sm-inline"><?php echo e(auth()->user()->name); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item">Log Out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="p-4">
                <?php echo e($slot); ?>

            </main>
        </div>
    </div>

    
    <div class="offcanvas offcanvas-start altura-sidebar" tabindex="-1" id="adminMobileSidebar">
        <div class="offcanvas-header">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Altura" style="height: 36px; border-radius: 8px;">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav flex-column">
                <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>"><i class="fa-solid fa-gauge"></i>Dashboard</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.onboarding.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.onboarding.index')); ?>"><i class="fa-solid fa-user-plus"></i>Onboarding</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.students.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.students.index')); ?>"><i class="fa-solid fa-users"></i>Students</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.job-seekers.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.job-seekers.index')); ?>"><i class="fa-solid fa-briefcase"></i>Job Seekers</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.job-postings.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.job-postings.index')); ?>"><i class="fa-solid fa-list-check"></i>Job Postings</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.study-postings.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.study-postings.index')); ?>"><i class="fa-solid fa-graduation-cap"></i>Study Postings</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.visa-management.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.visa-management.index')); ?>"><i class="fa-solid fa-passport"></i>Visa Management</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.payments-management.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.payments-management.index')); ?>"><i class="fa-solid fa-money-bill-wave"></i>Payments Management</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.resources.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.resources.index')); ?>"><i class="fa-solid fa-book-open"></i>Resources</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.support.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.support.index')); ?>"><i class="fa-solid fa-headset"></i>Support Tickets</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.users.index')); ?>"><i class="fa-solid fa-users-gear"></i>All Users</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.worker-requests.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.worker-requests.index')); ?>"><i class="fa-solid fa-people-arrows"></i>Worker Requests</a>
                <a class="nav-link <?php echo e(request()->routeIs('admin.employers.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.employers.index')); ?>"><i class="fa-solid fa-building"></i>Employers</a>
            </nav>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\wamp64\www\altura-workforce\resources\views/layouts/admin.blade.php ENDPATH**/ ?>
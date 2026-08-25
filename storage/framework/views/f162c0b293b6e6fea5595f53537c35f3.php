<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Create Account']); ?>

    <?php
        $roleLabels = ['student' => 'Student', 'job_seeker' => 'Job Seeker', 'employer' => 'Employer'];
        $roleTaglines = [
            'student' => 'Begin your study abroad journey with Altura.',
            'job_seeker' => "Find your next career abroad — let's get started.",
            'employer' => 'Request workers and let Altura manage your recruitment.',
        ];
    ?>

    <div class="btn-group w-100 mb-4" role="group">
        <a href="<?php echo e(route('register')); ?>" class="btn <?php echo e($role === 'student' ? 'btn-primary' : 'btn-outline-secondary'); ?>">
            <i class="fa-solid fa-graduation-cap"></i> Student
        </a>
        <a href="<?php echo e(route('register', ['as' => 'job_seeker'])); ?>" class="btn <?php echo e($role === 'job_seeker' ? 'btn-primary' : 'btn-outline-secondary'); ?>">
            <i class="fa-solid fa-briefcase"></i> Job Seeker
        </a>
        <a href="<?php echo e(route('register', ['as' => 'employer'])); ?>" class="btn <?php echo e($role === 'employer' ? 'btn-primary' : 'btn-outline-secondary'); ?>">
            <i class="fa-solid fa-building"></i> Employer
        </a>
    </div>

    <h2 class="h4 fw-semibold text-center mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">
        Create Your <?php echo e($roleLabels[$role]); ?> Account
    </h2>
    <p class="text-secondary text-center small mb-4">
        <?php echo e($roleTaglines[$role]); ?>

    </p>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger small">
            <ul class="mb-0 ps-3">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('register')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="role" value="<?php echo e($role); ?>">

        <div class="mb-3">
            <label class="form-label fw-semibold small"><?php echo e($role === 'employer' ? 'Contact Full Name' : 'Full Name'); ?></label>
            <input id="name" type="text" name="name" value="<?php echo e(old('name')); ?>" class="form-control" required autofocus autocomplete="name">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Email</label>
            <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control" required autocomplete="username">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Password</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>

        <?php if($role === 'employer'): ?>
            <div class="alert alert-info small mb-4">
                You'll complete your Company Profile (company name, industry, country) right after creating this account.
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            Create <?php echo e($roleLabels[$role]); ?> Account
        </button>

        <p class="text-center small text-secondary mb-0">
            Already have an account? <a href="<?php echo e(route('login')); ?>">Log in</a>
        </p>
    </form>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php /**PATH C:\wamp64\www\altura-workforce\resources\views/auth/register.blade.php ENDPATH**/ ?>
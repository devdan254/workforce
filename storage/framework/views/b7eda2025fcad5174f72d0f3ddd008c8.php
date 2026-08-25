<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Log In']); ?>

    <h2 class="h4 fw-semibold text-center mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Welcome Back</h2>
    <p class="text-secondary text-center small mb-4">Log in to your Altura account</p>

    <?php if(session('status')): ?>
        <div class="alert alert-success small"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger small">
            <ul class="mb-0 ps-3">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Email</label>
            <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control" required autofocus autocomplete="username">
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label class="form-label fw-semibold small">Password</label>
                <?php if(Route::has('password.request')): ?>
                    <a href="<?php echo e(route('password.request')); ?>" class="small">Forgot password?</a>
                <?php endif; ?>
            </div>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
        </div>

        <div class="form-check mb-4">
            <input type="checkbox" name="remember" id="remember_me" class="form-check-input">
            <label for="remember_me" class="form-check-label small">Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">Log In</button>

        <p class="text-center small text-secondary mb-0">
            Don't have an account?
            <a href="<?php echo e(route('register')); ?>">Register as a Student</a>,
            <a href="<?php echo e(route('register', ['as' => 'job_seeker'])); ?>">Job Seeker</a>,
            or <a href="<?php echo e(route('register', ['as' => 'employer'])); ?>">Employer</a>
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
<?php /**PATH C:\wamp64\www\altura-workforce\resources\views/auth/login.blade.php ENDPATH**/ ?>
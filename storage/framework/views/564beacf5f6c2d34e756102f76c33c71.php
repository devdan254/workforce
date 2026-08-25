<?php if (isset($component)) { $__componentOriginal42b37f006f8ebbe12b66cfa27a5def06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal42b37f006f8ebbe12b66cfa27a5def06 = $attributes; } ?>
<?php $component = App\View\Components\PublicLayout::resolve(['title' => 'Study Abroad Programs | Altura Workforce Solutions','metaDescription' => 'Get expert guidance on university admissions, student visas, accommodation and travel preparation for studying abroad.'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PublicLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="--hero-img:url('https://images.pexels.com/photos/7683693/pexels-photo-7683693.jpeg?auto=compress&cs=tinysrgb&w=1920');">
  <div class="container">
    <div class="breadcrumb"><a href="<?php echo e(route('public.home')); ?>">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>Study Abroad</span></div>
    <span class="hero-mini">Study Abroad with Altura Workforce Solutions</span>
    <h1>Your Journey to an International Education &amp; Career Starts Here</h1>
    <p>We make your journey to study abroad simple yet successful.</p>
    <div class="hero-actions">
      <a href="#consultation" class="btn btn-primary">Request Free Consultation <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#universities" class="btn btn-outline">Browse Universities</a>
    </div>
  </div>
</section>

<!-- ============ SERVICES + CONSULTATION ============ -->
<section class="section bg-white" id="consultation">
  <div class="container">
    <div class="about-split" style="align-items:flex-start;">
      <div>
        <span class="eyebrow">Our Services</span>
        <h2 class="mb-6">We Make It Look Simple, Though</h2>
        <div class="why-grid reveal-stagger">
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-university"></i></div><div><h4>University &amp; College Application</h4><p>We assist with your university/college application and placements, including follow-ups.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-list-check"></i></div><div><h4>University/College Selection</h4><p>We guide you on which university will best fit your needs.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-comments"></i></div><div><h4>Career Counselling</h4><p>We guide you on what course will best suit your passion.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-passport"></i></div><div><h4>Visa Processing</h4><p>We give you expert visa assistance including completion and submissions.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-house"></i></div><div><h4>Accommodation Booking</h4><p>We assist you to secure comfortable accommodation.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-plane"></i></div><div><h4>Flight Booking</h4><p>We assist you to make your flight arrangements.</p></div></div>
        </div>
      </div>

      <div class="form-card reveal">
        <h3>Not Sure Where to Begin?</h3>
        <p style="font-size:14.5px;color:var(--color-ink-600);margin:12px 0 24px;line-height:1.7;">Speak with one of our education advisors for personalized guidance based on your academic background, career goals, and preferred destination.</p>
        <form data-ajax-form action="<?php echo e(route('public.inquiries.consultation')); ?>">
          <?php echo csrf_field(); ?>
          <div class="form-row-2">
            <div class="form-field"><label>Full Name <span class="req">*</span></label><input type="text" name="name" required><span class="field-error">Please enter your full name.</span></div>
            <div class="form-field"><label>Email Address <span class="req">*</span></label><input type="email" name="email" required><span class="field-error">Please enter a valid email.</span></div>
          </div>
          <div class="form-row-2">
          <div class="form-field"><label>Phone Number <span class="req">*</span></label><input type="tel" name="phone" required><span class="field-error">Please enter your phone number.</span></div>
          <div class="form-field"><label>Preferred Study Destination</label><select name="destination"><option value="">Select destination</option><option>Germany</option><option>Australia</option><option>England</option><option>South Africa</option><option>USIU – Kenya</option><option>China</option><option>Other</option></select></div>
          </div>
          <div class="form-field"><label>Intended Course</label><input type="text" name="course" placeholder="e.g. Business Administration"></div>
          <button type="submit" class="btn btn-primary btn-block">Request Free Consultation</button>
        </form>
        <div class="form-submit-error" style="display:none;background:var(--color-danger-tint);color:var(--color-danger);padding:12px 16px;border-radius:var(--radius-sm);margin-top:16px;font-size:13.5px;">Something went wrong sending your request. Please try again or contact us directly.</div>
        <div class="form-success">
          <div class="icon-circle"><i class="fa-solid fa-check"></i></div>
          <h3>Request Received!</h3>
          <p style="color:var(--color-ink-600);margin-top:10px;">One of our education advisors will reach out within 1–2 business days.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ ADMISSION REQUIREMENTS ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="about-split">
      <div>
        <span class="eyebrow">Get Ready</span>
        <h2 class="mb-4">Admission Requirements</h2>
        <p style="font-size:15.5px;color:var(--color-ink-600);line-height:1.75;margin-bottom:24px;">Documents you may need. Requirements may vary depending on the university, country, and course — our advisors will guide you through the exact requirements for your application.</p>
        <div class="checklist-grid reveal-stagger">
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Academic Certificates</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Academic Transcripts</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Passport</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Passport-Sized Photographs</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>English Proficiency Results</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Personal Statement</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Recommendation Letters</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Curriculum Vitae</span></div>
        </div>
      </div>
      <div class="about-media reveal">
        <img src="https://images.pexels.com/photos/8093004/pexels-photo-8093004.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="Africans graduating abroad" loading="lazy" style="height:480px;">
      </div>
    </div>
  </div>
</section>

<!-- ============ UNIVERSITIES / COLLEGES (DYNAMIC — replaces "Your Path Forward") ============ -->
<section class="section bg-white" id="universities">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Where You Could Study</span>
      <h2>Universities &amp; Colleges</h2>
      <p>Browse current opportunities and apply directly to the one that fits you.</p>
    </div>

    <div class="d-flex justify-content-center mb-6" style="gap:12px;flex-wrap:wrap;">
      <form method="GET" action="<?php echo e(route('public.study-abroad.index')); ?>" style="display:flex;gap:10px;flex-wrap:wrap;">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="University name" style="min-width:200px;">
        <select name="country" onchange="this.form.submit()">
          <option value="">All Countries</option>
          <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($country); ?>" <?php if(request('country') === $country): echo 'selected'; endif; ?>><?php echo e($country); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="scholarship_type" onchange="this.form.submit()">
          <option value="">All Scholarships</option>
          <option value="full" <?php if(request('scholarship_type') === 'full'): echo 'selected'; endif; ?>>Full Scholarship</option>
          <option value="partial" <?php if(request('scholarship_type') === 'partial'): echo 'selected'; endif; ?>>Partial Scholarship</option>
          <option value="none" <?php if(request('scholarship_type') === 'none'): echo 'selected'; endif; ?>>No Scholarship</option>
        </select>
        <button type="submit" class="btn btn-outline-navy btn-sm">Search</button>
        <?php if(request()->hasAny(['search', 'country', 'scholarship_type'])): ?>
          <a href="<?php echo e(route('public.study-abroad.index')); ?>" class="btn btn-ghost btn-sm">Clear</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="jobs-grid reveal-stagger">
      <?php $__empty_1 = true; $__currentLoopData = $postings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $posting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card job-card reveal">
          <div class="job-card-img">
            <?php if($posting->image_url): ?>
              <img src="<?php echo e($posting->image_url); ?>" alt="<?php echo e($posting->university_name); ?>" loading="lazy">
            <?php else: ?>
              <img src="https://images.pexels.com/photos/5875778/pexels-photo-5875778.jpeg?auto=compress&cs=tinysrgb&w=800" alt="<?php echo e($posting->university_name); ?>" loading="lazy">
            <?php endif; ?>
            <?php if($posting->scholarship_type !== 'none'): ?>
              <span class="job-card-tag"><?php echo e(ucfirst($posting->scholarship_type)); ?> Scholarship</span>
            <?php endif; ?>
            <span class="job-card-country"><i class="fa-solid fa-location-dot"></i> <?php echo e($posting->country); ?></span>
          </div>
          <div class="job-card-body">
            <h3><?php echo e($posting->university_name); ?></h3>
            <p class="job-desc"><?php echo e(\Illuminate\Support\Str::limit($posting->description, 100)); ?></p>
            <?php if($posting->school_fees_per_semester): ?>
              <div class="job-card-salary"><?php echo e($posting->fees_currency); ?> <?php echo e(number_format($posting->school_fees_per_semester, 0)); ?> / Semester</div>
            <?php endif; ?>
            <a href="<?php echo e(route('public.study-abroad.show', $posting)); ?>" class="btn btn-navy btn-block">View Details &amp; Apply <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div style="grid-column:1/-1;text-align:center;padding:48px 0;color:var(--color-ink-500);">
          <p style="font-size:16px;">No universities match your search right now.</p>
          <?php if(request()->hasAny(['search', 'country', 'scholarship_type'])): ?>
            <a href="<?php echo e(route('public.study-abroad.index')); ?>" class="btn btn-outline-navy">Clear Filters</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if($postings->hasPages()): ?>
      <div class="pagination-wrap"><?php echo e($postings->onEachSide(1)->links('public.pagination')); ?></div>
    <?php endif; ?>
  </div>
</section>

<!-- ============ COUNTRIES ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Top Study Destinations</span>
      <h2>Countries You Can Study In</h2>
    </div>
    <div class="sticky-bg-section reveal" style="background-image:url('https://images.pexels.com/photos/34066027/pexels-photo-34066027.jpeg?auto=compress&cs=tinysrgb&w=1920');">
      <div class="country-grid">
        <div class="country-card"><span class="flag-emoji">🇩🇪</span><h4>Germany</h4></div>
        <div class="country-card"><span class="flag-emoji">🇦🇺</span><h4>Australia</h4></div>
        <div class="country-card"><span class="flag-emoji">🇬🇧</span><h4>England</h4></div>
        <div class="country-card"><span class="flag-emoji">🇿🇦</span><h4>South Africa</h4></div>
        <div class="country-card"><span class="flag-emoji">🇰🇪</span><h4>USIU – Kenya</h4></div>
        <div class="country-card"><span class="flag-emoji">🇨🇳</span><h4>China</h4></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Success Stories</span>
      <h2>Success Stories from Our Students</h2>
    </div>
    <div class="testimonial-grid reveal-stagger">
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/19039168/pexels-photo-19039168.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Brian O." loading="lazy"><div><div class="review-name">Brian O.</div><div class="review-sub">Canada</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">Altura made my university application process simple and stress-free. Today I'm studying in Canada, and I couldn't be happier.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/29086752/pexels-photo-29086752.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Faith A." loading="lazy"><div><div class="review-name">Faith A.</div><div class="review-sub">Australia</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">From choosing the right course to securing my student visa, the team supported me every step of the way.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/6348688/pexels-photo-6348688.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Kevin M." loading="lazy"><div><div class="review-name">Kevin M.</div><div class="review-sub">United Kingdom</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">Professional, responsive, and genuinely committed to helping students succeed abroad.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
    </div>
    <div class="text-center mt-7"><a href="<?php echo e(route('public.contact')); ?>" class="btn btn-outline-navy">View More Reviews <i class="fa-solid fa-arrow-right"></i></a></div>
  </div>
</section>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal42b37f006f8ebbe12b66cfa27a5def06)): ?>
<?php $attributes = $__attributesOriginal42b37f006f8ebbe12b66cfa27a5def06; ?>
<?php unset($__attributesOriginal42b37f006f8ebbe12b66cfa27a5def06); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal42b37f006f8ebbe12b66cfa27a5def06)): ?>
<?php $component = $__componentOriginal42b37f006f8ebbe12b66cfa27a5def06; ?>
<?php unset($__componentOriginal42b37f006f8ebbe12b66cfa27a5def06); ?>
<?php endif; ?>
<?php /**PATH C:\wamp64\www\altura-workforce\resources\views/public/study-abroad/index.blade.php ENDPATH**/ ?>
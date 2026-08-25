<?php if (isset($component)) { $__componentOriginal42b37f006f8ebbe12b66cfa27a5def06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal42b37f006f8ebbe12b66cfa27a5def06 = $attributes; } ?>
<?php $component = App\View\Components\PublicLayout::resolve(['title' => 'Altura Workforce Solutions | Work Abroad, Study Abroad, Visa Support & Hire Talent','metaDescription' => 'Altura Workforce Solutions connects African talent, students and employers with international job placements, study abroad programs, visa support and skilled workforce recruitment.'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PublicLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

<!-- ============ HERO SLIDER ============ -->
<section class="hero-slider">
  <div class="slide active" style="background-image:url('<?php echo e(asset('assets/images/bg-2.jpg')); ?>');">
    <div class="slide-content"><div class="container"><div class="slide-inner">
      <span class="hero-mini">Altura Workforce Solutions</span>
      <h1 class="hero-title"><span class="word-cycle"><span class="word-cycle-inner" data-words="Study,Work">Study</span></span> Abroad,<br>Hire Skilled African Talent, VISA Support</h1>
      <p class="hero-sub">Connecting African Talent, Students, and Employers with life-changing global opportunities.</p>
      <div class="hero-buttons">
        <a href="<?php echo e(route('public.jobs.index')); ?>" class="btn btn-primary">Start your Journey <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="hero-buttons-secondary">
        <a href="<?php echo e(route('public.study-abroad.index')); ?>">Study Abroad</a>
        <a href="<?php echo e(route('public.hire')); ?>">Hire Talent</a>
        <a href="<?php echo e(route('public.visa')); ?>">Visa Support</a>
      </div>
    </div></div></div>
  </div>
  <div class="slide" style="background-image:url('<?php echo e(asset('assets/images/bg-4.jpg')); ?>');">
    <div class="slide-content"><div class="container"><div class="slide-inner">
      <span class="hero-mini">Altura Workforce Solutions</span>
      <h1 class="hero-title"><span class="word-cycle"><span class="word-cycle-inner" data-words="Work,Study">Work</span></span> Abroad,<br>Hire Skilled African Talent, VISA Support</h1>
      <p class="hero-sub">Connecting African Talent, Students, and Employers with life-changing global opportunities.</p>
      <div class="hero-buttons">
        <a href="<?php echo e(route('public.jobs.index')); ?>" class="btn btn-primary">Start your Journey <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="hero-buttons-secondary">
        <a href="<?php echo e(route('public.study-abroad.index')); ?>">Study Abroad</a>
        <a href="<?php echo e(route('public.hire')); ?>">Hire Talent</a>
        <a href="<?php echo e(route('public.visa')); ?>">Visa Support</a>
      </div>
    </div></div></div>
  </div>

  <div class="slider-nav"></div>
  <div class="slider-arrows"><div class="container">
    <button class="slide-arrow prev" aria-label="Previous slide"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="slide-arrow next" aria-label="Next slide"><i class="fa-solid fa-chevron-right"></i></button>
  </div></div>
</section>

<!-- ============ I AM LOOKING FOR ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Where do you want to start?</span>
      <h2>I Am Looking For</h2>
      <p>Choose the path that fits your goals — we'll guide you from your first click to your first day.</p>
    </div>
    <div class="lookingfor-grid reveal-stagger">
      <a href="<?php echo e(route('public.jobs.index')); ?>" class="lf-card reveal">
        <img src="https://images.pexels.com/photos/6129496/pexels-photo-6129496.jpeg?auto=compress&cs=tinysrgb&w=800" alt="African healthcare professional working abroad" loading="lazy">
        <div class="lf-card-body">
          <h3>Work Abroad</h3>
          <p>Discover international job opportunities across multiple industries.</p>
          <span class="lf-btn">Explore Jobs <i class="fa-solid fa-arrow-right"></i></span>
        </div>
      </a>
      <a href="<?php echo e(route('public.study-abroad.index')); ?>" class="lf-card reveal">
        <img src="https://images.pexels.com/photos/31437216/pexels-photo-31437216.jpeg?auto=compress&cs=tinysrgb&w=800" alt="African graduate ready to study abroad" loading="lazy">
        <div class="lf-card-body">
          <h3>Study Abroad</h3>
          <p>Begin your journey to internationally recognized universities and colleges.</p>
          <span class="lf-btn">Learn More <i class="fa-solid fa-arrow-right"></i></span>
        </div>
      </a>
      <a href="<?php echo e(route('public.visa')); ?>" class="lf-card reveal">
        <img src="https://images.pexels.com/photos/4173219/pexels-photo-4173219.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Traveler with passport ready for visa approved trip" loading="lazy">
        <div class="lf-card-body">
          <h3>VISA Support</h3>
          <p>Receive professional assistance with work and student visas.</p>
          <span class="lf-btn">Request Now <i class="fa-solid fa-arrow-right"></i></span>
        </div>
      </a>
      <a href="<?php echo e(route('public.hire')); ?>" class="lf-card reveal">
        <img src="https://images.pexels.com/photos/19982408/pexels-photo-19982408.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Skilled African worker ready to be recruited" loading="lazy">
        <div class="lf-card-body">
          <h3>Hire Skilled Workers</h3>
          <p>Recruit pre-screened, qualified African professionals across multiple industries.</p>
          <span class="lf-btn">Recruit Talent <i class="fa-solid fa-arrow-right"></i></span>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- ============ ABOUT ALTURA ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="about-split">
      <div class="about-media reveal">
        <img src="https://images.pexels.com/photos/9879938/pexels-photo-9879938.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="African construction workers on-site abroad" loading="lazy">
        <div class="about-media-badge"><img src="<?php echo e(asset('assets/images/logo.png')); ?>" alt="Altura Workforce Solutions"></div>
      </div>
      <div class="about-copy reveal">
        <span class="eyebrow">About Us</span>
        <h2>Opening Doors to Global Opportunities</h2>
        <p>Altura Workforce Solutions is a professional workforce management and staffing company providing reliable, skilled, and job-ready talent to organizations across diverse sectors. The company delivers flexible workforce solutions designed to enhance productivity, reduce operational risk, and support sustainable organizational growth.</p>
        <p>Altura Workforce Solutions combines industry knowledge, efficient recruitment processes, and a genuine commitment to the people we place — because every placement is a life changed.</p>
        <a href="<?php echo e(route('public.about')); ?>" class="btn btn-outline-navy">Discover Our Story <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
    <div class="trust-ticker reveal">
      <div class="stat-item"><div class="stat-num" data-count="1200" data-suffix="+">0</div><div class="stat-label">Successful Placements</div></div>
      <div class="stat-item"><div class="stat-num" data-count="250" data-suffix="+">0</div><div class="stat-label">Partner Employers</div></div>
      <div class="stat-item"><div class="stat-num" data-count="15" data-suffix="+">0</div><div class="stat-label">Destination Countries</div></div>
      <div class="stat-item"><div class="stat-num" data-count="10" data-suffix=" Yrs">0</div><div class="stat-label">Years of Experience</div></div>
    </div>
  </div>
</section>

<!-- ============ OUR SERVICES ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">What We Do</span>
      <h2>Our Services</h2>
      <p>End-to-end workforce solutions for job seekers, students and employers — built around one promise: real opportunity, professionally delivered.</p>
    </div>
    <div class="services-grid reveal-stagger">
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-earth-africa"></i></div>
        <h3>International Job Placements</h3>
        <p>Altura Workforce Solutions provides connection for African skilled talents to work abroad. We hire in various industries including healthcare, hospitality, construction, engineering, agriculture, logistics and transport, manufacturing, domestic and care services, information technology, education, and more.</p>
        <a href="<?php echo e(route('public.jobs.index')); ?>" class="service-link">Apply Now <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-graduation-cap"></i></div>
        <h3>International Studies Placements</h3>
        <p>We assist in international placements of students to colleges and universities abroad, including Germany, Australia, England, South Africa and USIU in Kenya. We provide expert support with university selection, admissions, student visas, accommodation guidance, and travel preparation.</p>
        <a href="<?php echo e(route('public.study-abroad.index')); ?>" class="service-link">Apply Now <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-stamp"></i></div>
        <h3>VISA Application</h3>
        <p>Altura Workforce has perfected the application of visas for different countries. For those who want to study and work abroad we facilitate successful visa applications. Apart from work and study visas we also facilitate tourist and business visas.</p>
        <a href="<?php echo e(route('public.visa')); ?>" class="service-link">Apply for VISA <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-building"></i></div>
        <h3>Outsourced Workforce Recruitment</h3>
        <p>Are you a company looking to hire African talent? Altura has a pool of qualified African skilled workers for various industries. We recruit on your behalf and facilitate the transition to your organization. Our talents are highly skilled, professional and ready to work.</p>
        <a href="<?php echo e(route('public.hire')); ?>" class="service-link">Request for Workers <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- ============ INDUSTRIES + DESTINATIONS ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Where We Place Talent</span>
      <h2>Industries We Recruit For</h2>
      <p>A broad recruitment network spanning the sectors that keep economies moving.</p>
    </div>
    <div class="industry-grid reveal-stagger mb-6">
      <div class="industry-card card reveal"><div class="icon-badge"><i class="fa-solid fa-heart-pulse"></i></div><span>Healthcare</span></div>
      <div class="industry-card card reveal"><div class="icon-badge"><i class="fa-solid fa-helmet-safety"></i></div><span>Construction</span></div>
      <div class="industry-card card reveal"><div class="icon-badge"><i class="fa-solid fa-concierge-bell"></i></div><span>Hospitality</span></div>
      <div class="industry-card card reveal"><div class="icon-badge"><i class="fa-solid fa-tractor"></i></div><span>Agriculture</span></div>
      <div class="industry-card card reveal"><div class="icon-badge"><i class="fa-solid fa-gears"></i></div><span>Engineering</span></div>
      <div class="industry-card card reveal"><div class="icon-badge"><i class="fa-solid fa-truck-fast"></i></div><span>Logistics</span></div>
      <div class="industry-card card reveal"><div class="icon-badge"><i class="fa-solid fa-industry"></i></div><span>Manufacturing</span></div>
      <div class="industry-card card reveal"><div class="icon-badge"><i class="fa-solid fa-laptop-code"></i></div><span>Information Technology</span></div>
      <div class="industry-card card reveal"><div class="icon-badge"><i class="fa-solid fa-chalkboard-user"></i></div><span>Education</span></div>
      <div class="industry-card card reveal"><div class="icon-badge"><i class="fa-solid fa-house-chimney-medical"></i></div><span>Domestic &amp; Care Services</span></div>
    </div>

    <div class="section-head center mt-7">
      <span class="eyebrow">Where You Could Be Working</span>
      <h2>Recruitment Destinations</h2>
    </div>
    <div class="destination-strip reveal">
      <div class="destination-grid">
        <div class="destination-item"><span class="destination-flag">🇦🇪</span><span>UAE</span></div>
        <div class="destination-item"><span class="destination-flag">🇸🇦</span><span>Saudi Arabia</span></div>
        <div class="destination-item"><span class="destination-flag">🇶🇦</span><span>Qatar</span></div>
        <div class="destination-item"><span class="destination-flag">🇰🇼</span><span>Kuwait</span></div>
        <div class="destination-item"><span class="destination-flag">🇧🇭</span><span>Bahrain</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ FEATURED OPPORTUNITIES (DYNAMIC) ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Live Openings</span>
      <h2>Featured Opportunities</h2>
      <p>A snapshot of current openings across our jobs and study abroad programs.</p>
    </div>
    <div data-tabs>
      <div class="job-tabs" style="justify-content:center;">
        <button class="job-tab active" data-tab-btn="jobs">Jobs Abroad</button>
        <button class="job-tab" data-tab-btn="study">Study Abroad</button>
      </div>

      <div data-tab-panel="jobs" class="active">
        <div class="jobs-grid reveal-stagger">
          <?php $__empty_1 = true; $__currentLoopData = $featuredJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="card job-card reveal">
              <div class="job-card-img">
                <?php if($job->image_url): ?>
                  <img src="<?php echo e($job->image_url); ?>" alt="<?php echo e($job->title); ?>" loading="lazy">
                <?php else: ?>
                  <img src="https://images.pexels.com/photos/8961345/pexels-photo-8961345.jpeg?auto=compress&cs=tinysrgb&w=800" alt="<?php echo e($job->title); ?>" loading="lazy">
                <?php endif; ?>
                <span class="job-card-country"><i class="fa-solid fa-location-dot"></i> <?php echo e($job->country); ?></span>
              </div>
              <div class="job-card-body">
                <h3><?php echo e($job->title); ?></h3>
                <p class="job-desc"><?php echo e(\Illuminate\Support\Str::limit($job->description, 110)); ?></p>
                <div class="job-card-salary">
                  <?php echo e($job->currency); ?> <?php echo e(number_format($job->salary_min, 0)); ?><?php if($job->salary_max): ?> – <?php echo e(number_format($job->salary_max, 0)); ?><?php endif; ?> / Month
                </div>
                <a href="<?php echo e(route('public.jobs.show', $job)); ?>" class="btn btn-navy btn-block">View Job &amp; Apply <i class="fa-solid fa-arrow-right"></i></a>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center" style="grid-column:1/-1;color:var(--color-ink-500);">No featured jobs right now — check back soon.</p>
          <?php endif; ?>
        </div>
      </div>

      <div data-tab-panel="study">
        <div class="jobs-grid reveal-stagger">
          <?php $__empty_1 = true; $__currentLoopData = $featuredStudyPostings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $posting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="card job-card reveal">
              <div class="job-card-img">
                <?php if($posting->image_url): ?>
                  <img src="<?php echo e($posting->image_url); ?>" alt="<?php echo e($posting->university_name); ?>" loading="lazy">
                <?php else: ?>
                  <img src="https://images.pexels.com/photos/5875778/pexels-photo-5875778.jpeg?auto=compress&cs=tinysrgb&w=800" alt="<?php echo e($posting->university_name); ?>" loading="lazy">
                <?php endif; ?>
                <span class="job-card-country"><i class="fa-solid fa-location-dot"></i> <?php echo e($posting->country); ?></span>
              </div>
              <div class="job-card-body">
                <h3><?php echo e($posting->university_name); ?></h3>
                <p class="job-desc"><?php echo e(\Illuminate\Support\Str::limit($posting->description, 110)); ?></p>
                <a href="<?php echo e(route('public.study-abroad.show', $posting)); ?>" class="btn btn-navy btn-block">Learn More</a>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center" style="grid-column:1/-1;color:var(--color-ink-500);">No featured study opportunities right now — check back soon.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="text-center mt-7"><a href="<?php echo e(route('public.jobs.index')); ?>" class="btn btn-outline-navy">View All Opportunities <i class="fa-solid fa-arrow-right"></i></a></div>
  </div>
</section>

<!-- ============ HOW IT WORKS ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">The Process</span>
      <h2>How It Works</h2>
      <p>A clear, guided path from your first application to your first day abroad.</p>
    </div>
    <div class="steps-row reveal-stagger">
      <div class="step-item card reveal"><div class="step-num-circle">1</div><h4>Choose Your Path</h4></div>
      <div class="step-item card reveal"><div class="step-num-circle">2</div><h4>Complete Your Application</h4></div>
      <div class="step-item card reveal"><div class="step-num-circle">3</div><h4>Our Experts Review Everything</h4></div>
      <div class="step-item card reveal"><div class="step-num-circle">4</div><h4>Placement &amp; Processing</h4></div>
      <div class="step-item card reveal"><div class="step-num-circle">5</div><h4>Begin Your Journey Abroad</h4></div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Real Stories</span>
      <h2>What Our Clients Say</h2>
      <p>Verified feedback from the people we've placed abroad.</p>
    </div>
    <div class="testimonial-grid reveal-stagger">
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/19379640/pexels-photo-19379640.jpeg?auto=compress&cs=tinysrgb&w=200" alt="James Otieno" loading="lazy"><div><div class="review-name">James Otieno</div><div class="review-sub">Healthcare Professional — Qatar</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">Altura made my dream of working abroad a reality. Their guidance throughout the recruitment process gave me confidence every step of the way.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/29086752/pexels-photo-29086752.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Faith Achieng" loading="lazy"><div><div class="review-name">Faith Achieng</div><div class="review-sub">Hospitality Professional — UAE</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">From my first interview to landing in Dubai, the Altura team was with me every step. Professional, honest, and truly caring.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/19039168/pexels-photo-19039168.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Brian Omondi" loading="lazy"><div><div class="review-name">Brian Omondi</div><div class="review-sub">International Student — Germany</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">Altura made my university application process simple and stress-free. Today I'm studying abroad, and I couldn't be happier.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
    </div>
  </div>
</section>

<!-- ============ MEET THE TEAM ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Our People</span>
      <h2>The Team Behind Your Success</h2>
      <p>Experienced professionals dedicated to helping you reach your international goals.</p>
    </div>
    <div class="team-grid reveal-stagger">
      <div class="team-card card reveal"><div class="team-avatar"><img src="<?php echo e(asset('assets/images/teams/gabriel.jpg')); ?>" alt="Gabriel Okumu Oloo" class="team-avatar-img"></div><h3>Gabriel Okumu Oloo</h3><div class="team-role">Managing Director</div></div>
      <div class="team-card card reveal"><div class="team-avatar"><img src="<?php echo e(asset('assets/images/teams/sauda.jpg')); ?>" alt="Sauda Asmin Rashid" class="team-avatar-img"></div><h3>Sauda Asmin Rashid</h3><div class="team-role">Finance Officer</div></div>
      <div class="team-card card reveal"><div class="team-avatar"><img src="<?php echo e(asset('assets/images/teams/caroline.jpg')); ?>" alt="Caroline Achieng Oloo" class="team-avatar-img"></div><h3>Caroline Achieng Oloo</h3><div class="team-role">HR &amp; Administration Manager</div></div>
    </div>
  </div>
</section>

<!-- ============ FINAL CTA ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="final-cta reveal">
      <h2>Ready to Begin Your Journey?</h2>
      <p>Whether you're looking for international employment, higher education, professional visa assistance, or skilled workforce recruitment, our team is ready to help.</p>
      <div class="hero-actions">
        <a href="<?php echo e(route('public.jobs.index')); ?>" class="btn btn-primary">Work Abroad</a>
        <a href="<?php echo e(route('public.study-abroad.index')); ?>" class="btn btn-outline">Study Abroad</a>
        <a href="<?php echo e(route('public.hire')); ?>" class="btn btn-outline">Hire Talent</a>
      </div>
    </div>
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
<?php /**PATH C:\wamp64\www\altura-workforce\resources\views/public/home.blade.php ENDPATH**/ ?>
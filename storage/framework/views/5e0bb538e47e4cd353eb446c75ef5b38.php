<?php if (isset($component)) { $__componentOriginal42b37f006f8ebbe12b66cfa27a5def06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal42b37f006f8ebbe12b66cfa27a5def06 = $attributes; } ?>
<?php $component = App\View\Components\PublicLayout::resolve(['title' => 'Visa Support Services | Altura Workforce Solutions','metaDescription' => 'Professional visa guidance for work, student, tourist and business visas. Altura Workforce Solutions helps you prepare a complete and accurate application.'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PublicLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="--hero-img:url('https://images.pexels.com/photos/4173219/pexels-photo-4173219.jpeg?auto=compress&cs=tinysrgb&w=1920');">
  <div class="container">
    <div class="breadcrumb"><a href="<?php echo e(route('public.home')); ?>">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>Visa Support</span></div>
    <span class="hero-mini">Professional Visa Support Services</span>
    <h1>Expert Visa Guidance for Your International Journey</h1>
    <p>Whether you're planning to work, study, travel, or conduct business abroad, our experienced consultants provide professional guidance to help you prepare a complete and accurate visa application.</p>
    <div class="hero-actions">
      <a href="#consultation" class="btn btn-primary">Book a Visa Consultation <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?php echo e(route('public.visa-application-form')); ?>" class="btn btn-outline">Start Your Visa Application</a>
    </div>
  </div>
</section>

<!-- ============ VISA TYPES ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Choose Your Path</span>
      <h2>Visa Types We Support</h2>
      <p>Whatever your purpose for travel, our team prepares a complete, accurate application built around your destination's requirements.</p>
    </div>
    <div class="services-grid reveal-stagger">
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-briefcase"></i></div>
        <h3>Work Visa</h3>
        <p>Planning to build your career overseas? We assist with documentation, application preparation, and guidance throughout your work visa process.</p>
        <a href="<?php echo e(route('public.visa-application-form')); ?>" class="service-link">Apply for a Work Visa <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-graduation-cap"></i></div>
        <h3>Student Visa</h3>
        <p>Starting your education abroad? We help students prepare complete visa applications after receiving their admission offers.</p>
        <a href="<?php echo e(route('public.visa-application-form')); ?>" class="service-link">Apply for a Student Visa <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-plane-departure"></i></div>
        <h3>Tourist Visa</h3>
        <p>Planning your next international trip? Receive guidance for tourist visa applications, travel documentation, and supporting requirements.</p>
        <a href="<?php echo e(route('public.visa-application-form')); ?>" class="service-link">Apply for a Tourist Visa <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-handshake"></i></div>
        <h3>Business Visa</h3>
        <p>Travelling for meetings, conferences, or business opportunities? We'll help you prepare your application professionally.</p>
        <a href="<?php echo e(route('public.visa-application-form')); ?>" class="service-link">Apply for a Business Visa <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- ============ CONSULTATION FORM ============ -->
<section class="section bg-tint" id="consultation">
  <div class="container">
    <div class="about-split" style="align-items:center;">
      <div class="about-media reveal">
        <img src="https://images.pexels.com/photos/4173219/pexels-photo-4173219.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="Traveler with passport ready for departure" loading="lazy" style="height:520px;">
      </div>
      <div class="form-card reveal">
        <span class="eyebrow">Let's Talk</span>
        <h2 class="mb-3">Request a Visa Consultation</h2>
        <p style="font-size:14.5px;color:var(--color-ink-600);margin-bottom:24px;line-height:1.7;">Have questions about the process? Book a consultation with one of our experienced advisors for personalized guidance.</p>
        <form data-ajax-form action="<?php echo e(route('public.inquiries.visa-consultation')); ?>">
          <?php echo csrf_field(); ?>
          <div class="form-row-2">
            <div class="form-field"><label>Full Name <span class="req">*</span></label><input type="text" name="name" required><span class="field-error">Required field.</span></div>
            <div class="form-field"><label>Phone Number <span class="req">*</span></label><input type="tel" name="phone" required><span class="field-error">Required field.</span></div>
          </div>
          <div class="form-field"><label>Email Address <span class="req">*</span></label><input type="email" name="email" required><span class="field-error">Enter a valid email.</span></div>
          <div class="form-row-2">
            <div class="form-field"><label>Country of Residence</label><input type="text" name="country_of_residence"></div>
            <div class="form-field"><label>Destination Country</label><input type="text" name="destination_country"></div>
          </div>
          <div class="form-row-2">
            <div class="form-field"><label>Visa Type</label><select name="visa_type"><option value="">Select visa type</option><option>Work Visa</option><option>Student Visa</option><option>Tourist Visa</option><option>Business Visa</option></select></div>
            <div class="form-field"><label>Preferred Consultation Date</label><input type="date" name="preferred_date"></div>
          </div>
          <div class="form-field"><label>Message</label><textarea name="message" placeholder="Tell us about your travel plans..."></textarea></div>
          <button type="submit" class="btn btn-primary btn-block">Book My Consultation</button>
        </form>
        <div class="form-submit-error" style="display:none;background:var(--color-danger-tint);color:var(--color-danger);padding:12px 16px;border-radius:var(--radius-sm);margin-top:16px;font-size:13.5px;">Something went wrong sending your request. Please try again or contact us directly.</div>
        <div class="form-success">
          <div class="icon-circle"><i class="fa-solid fa-check"></i></div>
          <h3>Consultation Requested!</h3>
          <p style="color:var(--color-ink-600);margin-top:10px;">One of our visa specialists will reach out to confirm your appointment.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ VISA REQUIREMENTS ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="about-split">
      <div>
        <span class="eyebrow">Be Prepared</span>
        <h2 class="mb-4">Prepare Your Application with Confidence</h2>
        <p style="font-size:15px;color:var(--color-ink-600);line-height:1.75;margin-bottom:24px;">Requirements vary by country and visa type, but applicants commonly need the following.</p>
        <div class="checklist-grid reveal-stagger">
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Valid Passport</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Passport-Sized Photographs</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Completed Application Forms</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Proof of Financial Support</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Travel Itinerary</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Invitation or Admission Letter</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Employment or Academic Documents</span></div>
          <div class="checklist-item reveal"><i class="fa-solid fa-circle-check"></i><span>Additional Supporting Documents</span></div>
        </div>
        <div class="mt-6" style="display:flex;gap:14px;background:var(--color-accent-tint);padding:20px;border-radius:var(--radius-md);align-items:flex-start;">
          <i class="fa-solid fa-thumbtack" style="color:var(--color-accent);margin-top:2px;"></i>
          <p style="font-size:14px;color:var(--color-ink-600);line-height:1.65;">Our consultants will provide a personalized checklist based on your destination and visa category.</p>
        </div>
      </div>
      <div class="about-media reveal">
        <img src="https://images.pexels.com/photos/16940578/pexels-photo-16940578.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="Advisor preparing visa documents with client" loading="lazy" style="height:480px;">
      </div>
    </div>
  </div>
</section>

<!-- ============ VISA PROCESS ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Guided Every Step</span>
      <h2>Our Visa Support Process</h2>
      <p>A simple, guided process from start to finish.</p>
    </div>
    <div class="timeline reveal">
      <div class="timeline-step"><div class="timeline-num"><i class="fa-solid fa-calendar-check"></i></div><h3>Step 1 — Book Your Consultation</h3><p>Meet with one of our visa specialists (online or in-person) to discuss your travel plans and determine the appropriate visa category.</p></div>
      <div class="timeline-step"><div class="timeline-num"><i class="fa-solid fa-file-lines"></i></div><h3>Step 2 — Document Assessment</h3><p>We review your documents and advise you on any additional requirements before submission.</p></div>
      <div class="timeline-step"><div class="timeline-num"><i class="fa-solid fa-pen-to-square"></i></div><h3>Step 3 — Application Preparation</h3><p>Receive professional guidance while completing your visa application accurately.</p></div>
      <div class="timeline-step"><div class="timeline-num"><i class="fa-solid fa-paper-plane"></i></div><h3>Step 4 — Submission Guidance</h3><p>We guide you through the submission process and any required appointments.</p></div>
      <div class="timeline-step"><div class="timeline-num"><i class="fa-solid fa-plane"></i></div><h3>Step 5 — Follow-Up Support</h3><p>We continue supporting you throughout the application process until a decision is received.</p></div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Trusted Guidance</span>
      <h2>What Our Clients Say</h2>
    </div>
    <div class="testimonial-grid reveal-stagger">
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/20209020/pexels-photo-20209020.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Mercy Atieno" loading="lazy"><div><div class="review-name">Mercy Atieno</div><div class="review-sub">Student Visa — Canada</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">From university application to my student visa, the team provided exceptional support. I highly recommend them.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/14630664/pexels-photo-14630664.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Daniel Kimani" loading="lazy"><div><div class="review-name">Daniel Kimani</div><div class="review-sub">Work Visa — UAE</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">My visa application felt overwhelming until Altura stepped in. They handled everything with clarity and care.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/4045708/pexels-photo-4045708.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Linet Achieng" loading="lazy"><div><div class="review-name">Linet Achieng</div><div class="review-sub">Business Visa — Qatar</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">Quick, professional, and always available to answer my questions. I got my visa without any hassle.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
    </div>
  </div>
</section>

<!-- ============ FINAL CTA ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="final-cta reveal">
      <h2>Take the Next Step Toward Your International Journey</h2>
      <p>Whether you're travelling for work, study, business, or leisure, our experienced consultants are ready to guide you through every stage of your visa application.</p>
      <div class="hero-actions">
        <a href="<?php echo e(route('public.visa-application-form')); ?>" class="btn btn-primary">Start Your Visa Application</a>
        <a href="<?php echo e(route('public.contact')); ?>" class="btn btn-outline">Chat with Our Visa Consultant</a>
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
<?php /**PATH C:\wamp64\www\altura-workforce\resources\views/public/visa/index.blade.php ENDPATH**/ ?>
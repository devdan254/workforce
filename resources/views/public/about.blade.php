<x-public-layout title="About Us | Altura Workforce Solutions" :meta-description="'Learn the story, mission, vision and team behind Altura Workforce Solutions — connecting African talent, students and employers with global opportunities.'">

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="--hero-img:url('https://images.pexels.com/photos/6129496/pexels-photo-6129496.jpeg?auto=compress&cs=tinysrgb&w=1920');">
  <div class="container">
    <div class="breadcrumb"><a href="{{ route('public.home') }}">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>About</span></div>
    <span class="hero-mini">About Altura Workforce Solutions</span>
    <h1>Connecting People. Creating Opportunities. Transforming Futures.</h1>
    <p>At Altura Workforce Solutions, we believe every opportunity has the power to change a life. Whether you're pursuing a global career, studying abroad, or building a stronger workforce, we're committed to helping you succeed.</p>
    <div class="hero-actions">
      <a href="{{ route('public.jobs.index') }}" class="btn btn-primary">Explore Opportunities <i class="fa-solid fa-arrow-right"></i></a>
      <a href="{{ route('public.contact') }}" class="btn btn-outline">Request a Free Consultation</a>
    </div>
  </div>
</section>

<!-- ============ OUR STORY ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="about-split">
      <div class="about-copy reveal">
        <span class="eyebrow">Our Story</span>
        <h2>Building Global Opportunities Through Trust and Excellence</h2>
        <p>Altura Workforce Solutions was founded with a simple but powerful mission — to bridge the gap between African talent and global opportunities.</p>
        <p>Over the years, we've grown into a trusted workforce solutions provider, helping professionals secure meaningful careers abroad, supporting students in accessing international education, and enabling organizations to recruit skilled and reliable talent.</p>
        <p>We believe recruitment is more than filling positions — it's about changing lives, empowering businesses, and creating lasting partnerships built on professionalism, integrity, and results.</p>
        <p>Today, our growing network of employers, institutions, and industry partners allows us to connect the right people with the right opportunities across multiple countries and sectors.</p>
      </div>
      <div class="about-media reveal">
        <img src="https://images.pexels.com/photos/9301291/pexels-photo-9301291.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="Altura Workforce Solutions team at work" loading="lazy" style="height:520px;">
        <div class="about-media-badge"><img src="{{ asset('assets/images/logo.png') }}" alt="Altura Workforce Solutions"></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ VISION MISSION VALUES ============ -->
<section class="section">
  <div class="container">
    <div class="sticky-bg-section reveal" style="background-image:url('https://images.pexels.com/photos/19982408/pexels-photo-19982408.jpeg?auto=compress&cs=tinysrgb&w=1920');">
      <div class="section-head center" style="max-width:700px;">
        <span class="eyebrow">Our Purpose</span>
        <h2 style="color:#fff;">Vision, Mission &amp; Core Values</h2>
      </div>
      <div class="value-cards">
        <div class="value-card reveal">
          <h3>Our Vision</h3>
          <p>To be a trusted workforce solutions partner recognized for empowering organizations and transforming lives through meaningful employment.</p>
        </div>
        <div class="value-card reveal">
          <h3>Our Mission</h3>
          <p>To provide dependable, ethical, and innovative workforce solutions that meet client needs while creating sustainable opportunities for job seekers.</p>
        </div>
        <div class="value-card reveal">
          <h3>Our Core Values</h3>
          <ul>
            <li><i class="fa-solid fa-check"></i> Integrity</li>
            <li><i class="fa-solid fa-check"></i> Excellence</li>
            <li><i class="fa-solid fa-check"></i> People First</li>
            <li><i class="fa-solid fa-check"></i> Reliability</li>
            <li><i class="fa-solid fa-check"></i> Innovation</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ OUR SERVICES ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">What We Do</span>
      <h2>Helping Individuals and Businesses Achieve More</h2>
    </div>
    <div class="services-grid reveal-stagger">
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-earth-africa"></i></div>
        <h3>International Recruitment</h3>
        <p>Connect with verified international employers and discover rewarding career opportunities across multiple industries.</p>
        <a href="{{ route('public.jobs.index') }}" class="service-link">Apply for Jobs Abroad <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-graduation-cap"></i></div>
        <h3>Study Abroad</h3>
        <p>Receive expert guidance on university admissions, student visas, accommodation, and travel preparation.</p>
        <a href="{{ route('public.study-abroad.index') }}" class="service-link">Study Abroad <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-stamp"></i></div>
        <h3>Visa Support</h3>
        <p>Professional assistance for work, student, tourist, and business visa applications.</p>
        <a href="{{ route('public.visa') }}" class="service-link">Visa Support <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="service-card card reveal">
        <div class="icon-badge"><i class="fa-solid fa-building"></i></div>
        <h3>Employer Recruitment</h3>
        <p>Recruit qualified, pre-screened African professionals through our trusted recruitment network.</p>
        <a href="{{ route('public.hire') }}" class="service-link">Hire Talent <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- ============ WHY CHOOSE US ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="about-split">
      <div>
        <span class="eyebrow">Why Choose Us</span>
        <h2 class="mb-6">Why Thousands Choose Altura Workforce Solutions</h2>
        <div class="why-grid reveal-stagger">
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-earth-africa"></i></div><div><h4>Global Opportunities</h4><p>Access international employment, education, and workforce solutions through one trusted partner.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-check"></i></div><div><h4>Professional Guidance</h4><p>Our experienced consultants provide personalized support throughout every stage of your journey.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-handshake"></i></div><div><h4>Ethical Recruitment</h4><p>We believe in transparency, integrity, and responsible recruitment practices.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-file-lines"></i></div><div><h4>End-to-End Support</h4><p>From consultation to placement, we guide you every step of the way.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-globe"></i></div><div><h4>International Network</h4><p>Partnering with employers and institutions across multiple countries to create meaningful opportunities.</p></div></div>
          <div class="why-item reveal"><div class="icon-badge"><i class="fa-solid fa-star"></i></div><div><h4>Client-Focused Service</h4><p>Every client receives dedicated support tailored to their unique goals.</p></div></div>
        </div>
      </div>
      <div class="about-media reveal">
        <img src="https://images.pexels.com/photos/3869642/pexels-photo-3869642.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="Confident African professional at Altura Workforce Solutions" loading="lazy" style="height:520px;">
      </div>
    </div>
  </div>
</section>

<!-- ============ ACHIEVEMENTS ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Our Impact</span>
      <h2>Making a Difference, One Opportunity at a Time</h2>
    </div>
    <div class="counter-row reveal-stagger">
      <div class="counter-item reveal"><div class="counter-num" data-count="10" data-suffix="+">0</div><div class="counter-label">Years of Professional Experience</div></div>
      <div class="counter-item reveal"><div class="counter-num" data-count="1200" data-suffix="+">0</div><div class="counter-label">Successful Placements</div></div>
      <div class="counter-item reveal"><div class="counter-num" data-count="250" data-suffix="+">0</div><div class="counter-label">Partner Employers</div></div>
      <div class="counter-item reveal"><div class="counter-num" data-count="15" data-suffix="+">0</div><div class="counter-label">Destination Countries</div></div>
      <div class="counter-item reveal"><div class="counter-num" data-count="5000" data-suffix="+">0</div><div class="counter-label">Lives Impacted</div></div>
    </div>
  </div>
</section>

<!-- ============ MEET THE TEAM ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Our People</span>
      <h2>Meet Our Team</h2>
      <p>Meet the professionals committed to helping you achieve your international goals.</p>
    </div>
    <div class="team-grid reveal-stagger">
      <div class="team-card card reveal">
        <div class="team-avatar"><img src="{{ asset('assets/images/teams/gabriel.jpg') }}" alt="Gabriel Okumu Oloo" class="team-avatar-img"></div>
        <h3>Gabriel Okumu Oloo</h3>
        <div class="team-role">Managing Director &amp; CEO</div>
        <p class="bio">Provides strategic leadership while ensuring the delivery of ethical and client-focused workforce solutions.</p>
      </div>
      <div class="team-card card reveal">
        <div class="team-avatar"><img src="{{ asset('assets/images/teams/sauda.jpg') }}" alt="Sauda Asmin Rashid" class="team-avatar-img"></div>
        <h3>Sauda Asmin Rashid</h3>
        <div class="team-role">Finance Officer</div>
        <p class="bio">Oversees financial planning, budgeting, reporting, and organizational compliance.</p>
      </div>
      <div class="team-card card reveal">
        <div class="team-avatar"><img src="{{ asset('assets/images/teams/caroline.jpg') }}" alt="Caroline Achieng Oloo" class="team-avatar-img"></div>
        <h3>Caroline Achieng Oloo</h3>
        <div class="team-role">HR &amp; Administration Manager</div>
        <p class="bio">Leads human resource and administrative operations, supporting effective workforce management.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ CLIENT SUCCESS STORIES ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Client Success Stories</span>
      <h2>Trusted by Professionals, Students &amp; Employers</h2>
    </div>
    <div class="testimonial-grid reveal-stagger">
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/19379640/pexels-photo-19379640.jpeg?auto=compress&cs=tinysrgb&w=200" alt="James O." loading="lazy"><div><div class="review-name">James O.</div><div class="review-sub">Healthcare Professional</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">Altura made my dream of working abroad a reality. Their guidance throughout the recruitment process gave me confidence every step of the way.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/20209020/pexels-photo-20209020.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Mercy A." loading="lazy"><div><div class="review-name">Mercy A.</div><div class="review-sub">International Student</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">From university application to my student visa, the team provided exceptional support. I highly recommend them.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
      <div class="review-card reveal">
        <div class="review-top"><img class="review-avatar" src="https://images.pexels.com/photos/3869642/pexels-photo-3869642.jpeg?auto=compress&cs=tinysrgb&w=200" alt="HR Manager" loading="lazy"><div><div class="review-name">HR Manager</div><div class="review-sub">Employer</div></div></div>
        <div class="review-stars">★★★★★</div>
        <p class="review-text">Finding qualified employees became much easier with Altura. Their recruitment process saved us valuable time.</p>
        <div class="review-google"><i class="fa-brands fa-google" style="color:#4285F4;"></i> Posted on Google</div>
      </div>
    </div>
    <div class="text-center mt-7"><a href="{{ route('public.contact') }}" class="btn btn-outline-navy">View More Reviews <i class="fa-solid fa-arrow-right"></i></a></div>
  </div>
</section>

<!-- ============ FINAL CTA + ACTION CARDS ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="final-cta reveal">
      <h2>Your Next Opportunity Starts Here</h2>
      <p>Whether you're looking to build an international career, pursue higher education abroad, obtain professional visa support, or recruit exceptional African talent, Altura Workforce Solutions is ready to help you take the next step.</p>
      <div class="action-cards-grid mt-7">
        <div class="action-card"><h4>Work Abroad</h4><p>Explore verified international career opportunities.</p><a href="{{ route('public.jobs.index') }}">Find Jobs →</a></div>
        <div class="action-card"><h4>Study Abroad</h4><p>Begin your journey to world-class education.</p><a href="{{ route('public.study-abroad.index') }}">Apply Now →</a></div>
        <div class="action-card"><h4>Visa Support</h4><p>Receive professional guidance for your visa application.</p><a href="{{ route('public.visa') }}">Get Started →</a></div>
        <div class="action-card"><h4>Hire Talent</h4><p>Build your workforce with qualified African professionals.</p><a href="{{ route('public.hire') }}">Request Talent →</a></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ CREATE ACCOUNT ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="form-card reveal text-center" style="max-width:700px;margin:0 auto;">
      <span class="eyebrow">Create an Account</span>
      <h2 class="mb-4">Join Altura Workforce Solutions Today</h2>
      <p style="color:var(--color-ink-600);font-size:15px;line-height:1.75;margin-bottom:20px;">Create your free account to:</p>
      <ul style="display:flex;flex-direction:column;gap:12px;text-align:left;max-width:420px;margin:0 auto 28px;">
        <li style="display:flex;gap:10px;font-size:14.5px;color:var(--color-ink-600);"><i class="fa-solid fa-circle-check" style="color:var(--color-success);margin-top:4px;"></i>Apply for international jobs</li>
        <li style="display:flex;gap:10px;font-size:14.5px;color:var(--color-ink-600);"><i class="fa-solid fa-circle-check" style="color:var(--color-success);margin-top:4px;"></i>Start your study abroad application</li>
        <li style="display:flex;gap:10px;font-size:14.5px;color:var(--color-ink-600);"><i class="fa-solid fa-circle-check" style="color:var(--color-success);margin-top:4px;"></i>Track your application progress</li>
        <li style="display:flex;gap:10px;font-size:14.5px;color:var(--color-ink-600);"><i class="fa-solid fa-circle-check" style="color:var(--color-success);margin-top:4px;"></i>Upload your documents securely</li>
        <li style="display:flex;gap:10px;font-size:14.5px;color:var(--color-ink-600);"><i class="fa-solid fa-circle-check" style="color:var(--color-success);margin-top:4px;"></i>Receive personalized opportunity alerts</li>
      </ul>
      <a href="{{ route('register') }}" class="btn btn-primary">Create Your Free Account <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>
</section>

</x-public-layout>

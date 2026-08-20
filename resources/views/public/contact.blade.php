<x-public-layout title="Contact Us | Altura Workforce Solutions" :meta-description="'Get in touch with Altura Workforce Solutions for job applications, study abroad guidance, visa support or workforce recruitment inquiries.'">

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="padding: var(--space-8) 0 var(--space-7); --hero-img:url('https://images.pexels.com/photos/9301291/pexels-photo-9301291.jpeg?auto=compress&cs=tinysrgb&w=1920');">
  <div class="container">
    <div class="breadcrumb"><a href="{{ route('public.home') }}">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>Contact</span></div>
    <span class="hero-mini">Altura Workforce Solutions</span>
    <h1>Contact Us</h1>
    <p>Have a question about a job, your study abroad application, or your visa? Our team is ready to help.</p>
  </div>
</section>

<!-- ============ CONTACT FORM + INFO ============ -->
<section class="section bg-white">
  <div class="container">
    <div class="contact-layout">
      <div class="form-card reveal">
        <h3 class="mb-5">Send Us a Message</h3>
        <form data-ajax-form action="{{ route('public.inquiries.contact') }}">
          @csrf
          <div class="form-row-2">
            <div class="form-field"><label>Full Name <span class="req">*</span></label><input type="text" name="name" required><span class="field-error">Please enter your name.</span></div>
            <div class="form-field"><label>Email Address <span class="req">*</span></label><input type="email" name="email" required><span class="field-error">Please enter a valid email.</span></div>
          </div>
          <div class="form-row-2">
            <div class="form-field"><label>Phone Number</label><input type="tel" name="phone"></div>
            <div class="form-field"><label>Subject</label><select name="subject"><option value="">Select a topic</option><option>Job Application</option><option>Study Abroad</option><option>Visa Support</option><option>Hire Talent</option><option>General Inquiry</option></select></div>
          </div>
          <div class="form-field"><label>Message <span class="req">*</span></label><textarea name="message" required placeholder="How can we help you?"></textarea><span class="field-error">Please enter a message.</span></div>
          <button type="submit" class="btn btn-primary btn-block">Send Message</button>
        </form>
        <div class="form-submit-error" style="display:none;background:var(--color-danger-tint);color:var(--color-danger);padding:12px 16px;border-radius:var(--radius-sm);margin-top:16px;font-size:13.5px;">Something went wrong sending your message. Please try again or contact us directly.</div>
        <div class="form-success">
          <div class="icon-circle"><i class="fa-solid fa-check"></i></div>
          <h3>Message Sent!</h3>
          <p style="color:var(--color-ink-600);margin-top:10px;">Thank you for reaching out. Our team will respond within 1–2 business days.</p>
        </div>
      </div>

      <div class="contact-info-card reveal">
        <h3>Get in Touch</h3>
        <div class="contact-info-item">
          <div class="icon-badge"><i class="fa-solid fa-phone"></i></div>
          <div><h4>Phone</h4><a href="tel:+254758434825">+254 758 434 825</a><br><a href="tel:+254140132281">+254 140 132 281</a></div>
        </div>
        <div class="contact-info-item">
          <div class="icon-badge"><i class="fa-brands fa-whatsapp"></i></div>
          <div><h4>WhatsApp</h4><a href="https://wa.me/254758434825" target="_blank" rel="noopener">Chat with our team</a></div>
        </div>
        <div class="contact-info-item">
          <div class="icon-badge"><i class="fa-solid fa-envelope"></i></div>
          <!-- Email obfuscated client-side via JS to prevent bot harvesting -->
          <div><h4>Email</h4><a href="#" data-obfuscate-email data-user="alturaworkforcesolutions" data-domain="gmail.com">Loading…</a></div>
        </div>
        <div class="contact-info-item">
          <div class="icon-badge"><i class="fa-solid fa-location-dot"></i></div>
          <div><h4>Office Address</h4><p>Utalii House, 3rd Floor, Rm No. 321, Nairobi, Kenya</p></div>
        </div>
        <div class="contact-info-item">
          <div class="icon-badge"><i class="fa-solid fa-clock"></i></div>
          <div><h4>Working Hours</h4><p>Mon–Fri: 8:00 AM – 5:00 PM</p></div>
        </div>
        <div class="contact-social">
          <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ MAP ============ -->
<section class="section bg-tint">
  <div class="container">
    <div class="section-head center">
      <span class="eyebrow">Visit Us</span>
      <h2>Our Head Office</h2>
    </div>
    <div class="map-embed reveal">
      <iframe src="https://www.google.com/maps?q=Utalii%20House%2C%20Nairobi%2C%20Kenya&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Altura Workforce Solutions Head Office Map"></iframe>
    </div>
  </div>
</section>

</x-public-layout>

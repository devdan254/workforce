/* ==========================================================================
   ALTURA WORKFORCE SOLUTIONS — MAIN JS
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function () {

  /* ---------- Header scroll state ---------- */
  var header = document.querySelector('.site-header');
  function onScroll() {
    if (!header) return;
    if (window.scrollY > 40) header.classList.add('scrolled');
    else header.classList.remove('scrolled');

    var btt = document.querySelector('.back-to-top');
    if (btt) {
      if (window.scrollY > 500) btt.classList.add('show');
      else btt.classList.remove('show');
    }
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Mobile menu ---------- */
  var toggle = document.querySelector('.mobile-toggle');
  var mobileMenu = document.querySelector('.mobile-menu');
  var mobileMenuClose = document.querySelector('.mobile-menu-close');
  var mobileMenuBackdrop = document.querySelector('.mobile-menu-backdrop');

  function openMobileMenu() {
    if (!toggle || !mobileMenu) return;
    toggle.classList.add('open');
    toggle.setAttribute('aria-expanded', 'true');
    mobileMenu.classList.add('open');
    if (mobileMenuBackdrop) mobileMenuBackdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeMobileMenu() {
    if (!toggle || !mobileMenu) return;
    toggle.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
    mobileMenu.classList.remove('open');
    if (mobileMenuBackdrop) mobileMenuBackdrop.classList.remove('open');
    document.body.style.overflow = '';
  }
  if (toggle && mobileMenu) {
    toggle.addEventListener('click', function () {
      if (mobileMenu.classList.contains('open')) closeMobileMenu();
      else openMobileMenu();
    });
    if (mobileMenuClose) mobileMenuClose.addEventListener('click', closeMobileMenu);
    if (mobileMenuBackdrop) mobileMenuBackdrop.addEventListener('click', closeMobileMenu);
    mobileMenu.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', closeMobileMenu);
    });
    window.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeMobileMenu();
    });
    // Close menu if viewport is resized up to desktop width
    window.addEventListener('resize', function () {
      if (window.innerWidth > 960) closeMobileMenu();
    });
  }

  /* ---------- Back to top ---------- */
  var btt = document.querySelector('.back-to-top');
  if (btt) {
    btt.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ---------- Scroll reveal ---------- */
  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('in-view'); });
  }

  /* ---------- Animated counters ---------- */
  var counters = document.querySelectorAll('[data-count]');
  if (counters.length) {
    var counted = new WeakSet();
    var countIo = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting && !counted.has(entry.target)) {
          counted.add(entry.target);
          animateCount(entry.target);
        }
      });
    }, { threshold: 0.4 });
    counters.forEach(function (c) { countIo.observe(c); });
  }
  function animateCount(el) {
    var target = parseInt(el.getAttribute('data-count'), 10) || 0;
    var suffix = el.getAttribute('data-suffix') || '';
    var duration = 1600;
    var start = null;
    function step(ts) {
      if (!start) start = ts;
      var progress = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target).toLocaleString() + suffix;
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target.toLocaleString() + suffix;
    }
    requestAnimationFrame(step);
  }

  /* ---------- Hero Slider ---------- */
  var slides = document.querySelectorAll('.hero-slider .slide');
  if (slides.length) {
    var current = 0;
    var dotsWrap = document.querySelector('.slider-nav');
    var dots = [];
    if (dotsWrap) {
      slides.forEach(function (s, i) {
        var b = document.createElement('button');
        if (i === 0) b.classList.add('active');
        b.addEventListener('click', function () { goTo(i); });
        dotsWrap.appendChild(b);
        dots.push(b);
      });
    }
    function goTo(i) {
      slides[current].classList.remove('active');
      dots[current] && dots[current].classList.remove('active');
      current = (i + slides.length) % slides.length;
      slides[current].classList.add('active');
      dots[current] && dots[current].classList.add('active');
    }
    document.querySelectorAll('.slide-arrow.next').forEach(function (b) { b.addEventListener('click', function(){ goTo(current+1); }); });
    document.querySelectorAll('.slide-arrow.prev').forEach(function (b) { b.addEventListener('click', function(){ goTo(current-1); }); });
    setInterval(function () { goTo(current + 1); }, 6000);
  }

  /* ---------- Tabs (Featured Opportunities Jobs/Study, Job category tabs) ---------- */
  document.querySelectorAll('[data-tabs]').forEach(function (group) {
    var buttons = group.querySelectorAll('[data-tab-btn]');
    var panels = group.querySelectorAll('[data-tab-panel]');
    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        buttons.forEach(function (b) { b.classList.remove('active'); });
        panels.forEach(function (p) { p.classList.remove('active'); });
        btn.classList.add('active');
        var target = group.querySelector('[data-tab-panel="' + btn.getAttribute('data-tab-btn') + '"]');
        if (target) target.classList.add('active');
      });
    });
  });

  document.querySelectorAll('.word-cycle-inner[data-words]').forEach(function (el) {
  var words = el.getAttribute('data-words').split(',');
  var i = 0;
  setInterval(function () {
    i = (i + 1) % words.length;
    el.classList.add('fade-out');
    setTimeout(function () {
      el.textContent = words[i];
      el.classList.remove('fade-out');
    }, 300);
  }, 1800);
});
	
  /* ---------- Mobile filter toggle (Jobs page) ---------- */
  var filterToggle = document.querySelector('.mobile-filter-toggle');
  var filterCard = document.querySelector('.filter-card');
  if (filterToggle && filterCard) {
    filterToggle.addEventListener('click', function () {
      filterCard.classList.toggle('open-mobile');
      filterToggle.querySelector('span').textContent = filterCard.classList.contains('open-mobile') ? 'Hide Filters' : 'Filter Jobs';
    });
  }

  /* ---------- Generic form handling with premium states ---------- */
  document.querySelectorAll('form[data-ajax-form]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var requiredFields = form.querySelectorAll('[required]');
      var valid = true;
      requiredFields.forEach(function (field) {
        var wrap = field.closest('.form-field') || field.closest('.checkbox-field');
        if (!field.value || (field.type === 'checkbox' && !field.checked)) {
          valid = false;
          if (wrap) wrap.classList.add('error');
        } else if (wrap) {
          wrap.classList.remove('error');
        }
      });
      if (!valid) {
        var firstError = form.querySelector('.error');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }
      var btn = form.querySelector('[type="submit"]');
      if (btn) {
        btn.classList.add('btn-loading');
        btn.disabled = true;
      }
      setTimeout(function () {
        if (btn) { btn.classList.remove('btn-loading'); btn.disabled = false; }
        form.style.display = 'none';
        var success = form.parentElement.querySelector('.form-success');
        if (success) success.classList.add('show');
        else alert('Thank you! Your submission has been received.');
      }, 1400);
    });

    form.querySelectorAll('input, select, textarea').forEach(function (field) {
      field.addEventListener('input', function () {
        var wrap = field.closest('.form-field');
        if (wrap && field.value) wrap.classList.remove('error');
      });
    });
  });

  /* ---------- Multi-step wizard (application forms) ---------- */
  var wizard = document.querySelector('[data-wizard]');
  if (wizard) {
    var wizPanels = wizard.querySelectorAll('.wizard-panel');
    var indicators = wizard.querySelectorAll('.wizard-step-indicator');
    var fill = wizard.querySelector('.wizard-progress-fill');
    var stepIdx = 0;

    function renderWizard() {
      wizPanels.forEach(function (p, i) { p.classList.toggle('active', i === stepIdx); });
      indicators.forEach(function (ind, i) {
        ind.classList.toggle('active', i === stepIdx);
        ind.classList.toggle('done', i < stepIdx);
      });
      if (fill) fill.style.width = (stepIdx / (wizPanels.length - 1)) * 100 + '%';
      wizard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    wizard.querySelectorAll('.wizard-next').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var panel = wizPanels[stepIdx];
        var requiredFields = panel.querySelectorAll('[required]');
        var valid = true;
        requiredFields.forEach(function (field) {
          var wrap = field.closest('.form-field') || field.closest('.checkbox-field');
          if (!field.value || (field.type === 'checkbox' && !field.checked)) {
            valid = false;
            if (wrap) wrap.classList.add('error');
          } else if (wrap) { wrap.classList.remove('error'); }
        });
        if (!valid) return;
        if (stepIdx < wizPanels.length - 1) { stepIdx++; renderWizard(); }
      });
    });
    wizard.querySelectorAll('.wizard-prev').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (stepIdx > 0) { stepIdx--; renderWizard(); }
      });
    });
    wizard.querySelectorAll('.wizard-submit').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var panel = wizPanels[stepIdx];
        var requiredFields = panel.querySelectorAll('[required]');
        var valid = true;
        requiredFields.forEach(function (field) {
          var wrap = field.closest('.form-field') || field.closest('.checkbox-field');
          if (!field.value || (field.type === 'checkbox' && !field.checked)) {
            valid = false;
            if (wrap) wrap.classList.add('error');
          } else if (wrap) { wrap.classList.remove('error'); }
        });
        if (!valid) return;
        btn.classList.add('btn-loading'); btn.disabled = true;
        setTimeout(function () {
          wizard.style.display = 'none';
          var success = document.querySelector('.form-success');
          if (success) { success.classList.add('show'); success.scrollIntoView({behavior:'smooth'}); }
        }, 1500);
      });
    });
    renderWizard();

    /* add another experience block */
    var addExpBtn = document.querySelector('[data-add-experience]');
    var expContainer = document.querySelector('[data-experience-container]');
    if (addExpBtn && expContainer) {
      addExpBtn.addEventListener('click', function () {
        var block = expContainer.querySelector('.exp-block').cloneNode(true);
        block.querySelectorAll('input').forEach(function (i) { i.value = ''; });
        var rm = document.createElement('button');
        rm.type = 'button'; rm.className = 'exp-remove'; rm.textContent = 'Remove';
        rm.addEventListener('click', function () { block.remove(); });
        block.appendChild(rm);
        expContainer.appendChild(block);
      });
    }
  }

  /* ---------- Upload field labels ---------- */
  document.querySelectorAll('.upload-field input[type="file"]').forEach(function (input) {
    input.addEventListener('change', function () {
      var wrap = input.closest('.upload-field');
      var label = wrap.querySelector('span');
      if (input.files && input.files.length) {
        wrap.classList.add('has-file');
        label.textContent = input.files[0].name;
      }
    });
  });

  /* ---------- Anti-bot obfuscated email/phone (contact page) ---------- */
  document.querySelectorAll('[data-obfuscate-email]').forEach(function (el) {
    var user = el.getAttribute('data-user');
    var domain = el.getAttribute('data-domain');
    if (user && domain) {
      var addr = user + '@' + domain;
      el.setAttribute('href', 'mailto:' + addr);
      el.textContent = addr;
    }
  });

  /* ---------- Lazy image fade-in ---------- */
  document.querySelectorAll('img.img-fade').forEach(function (img) {
    if (img.complete) img.classList.add('loaded');
    else img.addEventListener('load', function () { img.classList.add('loaded'); });
  });

  /* ---------- Job filter demo (client-side show/hide) ---------- */
  var jobSearch = document.querySelector('[data-job-search]');
  var jobCategory = document.querySelector('[data-job-category]');
  var jobCountry = document.querySelector('[data-job-country]');
  var jobCards = document.querySelectorAll('[data-job-card]');
  function filterJobs() {
    if (!jobCards.length) return;
    var q = jobSearch ? jobSearch.value.toLowerCase() : '';
    var cat = jobCategory ? jobCategory.value : '';
    var country = jobCountry ? jobCountry.value : '';
    jobCards.forEach(function (card) {
      var matchQ = !q || card.getAttribute('data-title').toLowerCase().indexOf(q) !== -1;
      var matchCat = !cat || card.getAttribute('data-category') === cat;
      var matchCountry = !country || card.getAttribute('data-country') === country;
      card.style.display = (matchQ && matchCat && matchCountry) ? '' : 'none';
    });
  }
  [jobSearch, jobCategory, jobCountry].forEach(function (el) {
    if (el) el.addEventListener('input', filterJobs);
  });

});

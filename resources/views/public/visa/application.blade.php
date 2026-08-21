<x-public-layout title="Visa Application Form | Altura Workforce Solutions" :meta-description="'Submit your visa application to Altura Workforce Solutions in a few simple, guided steps.'">

<!-- ============ HERO BANNER ============ -->
<section class="hero-banner" style="padding: var(--space-7) 0 var(--space-6);">
  <div class="container">
    <div class="breadcrumb"><a href="{{ route('public.home') }}">Home</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><a href="{{ route('public.visa') }}">Visa Support</a><i class="fa-solid fa-chevron-right" style="font-size:10px;"></i><span>Application</span></div>
    <span class="hero-mini">Visa Application</span>
    <h1>Submit Your Visa Application</h1>
    <p>Provide your personal, travel and visa details exactly as they appear on your passport. Our visa support team reviews every submission personally.</p>
  </div>
</section>

<!-- ============ WIZARD ============ -->
<section class="section bg-white">
  <div class="container">

    @unless(session('success'))
      @if($errors->any())
        <div class="alert" style="background:var(--color-danger-tint);color:var(--color-danger);padding:14px 16px;border-radius:var(--radius-sm);margin-bottom:20px;font-size:13.5px;max-width:900px;margin-left:auto;margin-right:auto;">
          <p style="margin:0 0 6px;font-weight:600;">Please fix the following:</p>
          @foreach($errors->all() as $error)
            <p style="margin:0;">{{ $error }}</p>
          @endforeach
        </div>
      @endif

      <div class="form-card reveal" style="max-width:900px;margin:0 auto;" data-wizard>
        <div class="wizard-progress">
          <div class="wizard-progress-fill"></div>
        </div>
        <div class="wizard-step-indicator active"><div class="dot">1</div><span class="label">Personal</span></div>
        <div class="wizard-step-indicator"><div class="dot">2</div><span class="label">Travel</span></div>
        <div class="wizard-step-indicator"><div class="dot">3</div><span class="label">Visa</span></div>
        <div class="wizard-step-indicator"><div class="dot">4</div><span class="label">Documents</span></div>
        <div class="wizard-step-indicator"><div class="dot">5</div><span class="label">Submit</span></div>

        <form method="POST" action="{{ route('public.visa-application-form.store') }}" enctype="multipart/form-data">
          @csrf

          <!-- STEP 1 — Personal Details -->
          <div class="wizard-panel active">
            <h3 class="mb-3">Step 1 — Personal Details</h3>
            <p style="font-size:13.5px;color:var(--color-ink-500);margin-bottom:20px;">Please provide your personal information exactly as it appears on your passport.</p>
            <div class="form-row-2">
              <div class="form-field"><label>First Name <span class="req">*</span></label><input type="text" name="first_name" value="{{ old('first_name') }}" required><span class="field-error">Required field.</span></div>
              <div class="form-field"><label>Middle Name (Optional)</label><input type="text" name="middle_name" value="{{ old('middle_name') }}"></div>
            </div>
            <div class="form-row-2">
              <div class="form-field"><label>Last Name <span class="req">*</span></label><input type="text" name="last_name" value="{{ old('last_name') }}" required><span class="field-error">Required field.</span></div>
              <div class="form-field"><label>Date of Birth <span class="req">*</span></label><input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required><span class="field-error">Required field.</span></div>
            </div>
            <div class="form-row-2">
              <div class="form-field"><label>Gender <span class="req">*</span></label><select name="gender" required><option value="">Select gender</option><option @selected(old('gender')==='Male')>Male</option><option @selected(old('gender')==='Female')>Female</option></select><span class="field-error">Required field.</span></div>
              <div class="form-field"><label>Nationality <span class="req">*</span></label><input type="text" name="nationality" value="{{ old('nationality') }}" required><span class="field-error">Required field.</span></div>
            </div>
            <div class="form-row-2">
              <div class="form-field"><label>Country of Residence <span class="req">*</span></label><input type="text" name="country_of_residence" value="{{ old('country_of_residence') }}" required><span class="field-error">Required field.</span></div>
              <div class="form-field"><label>Passport Number <span class="req">*</span></label><input type="text" name="passport_number" value="{{ old('passport_number') }}" required><span class="field-error">Required field.</span></div>
            </div>
            <div class="form-row-2">
              <div class="form-field"><label>Passport Expiry Date <span class="req">*</span></label><input type="date" name="passport_expiry" value="{{ old('passport_expiry') }}" required><span class="field-error">Required field.</span></div>
              <div class="form-field"><label>Phone Number <span class="req">*</span></label><input type="tel" name="phone" value="{{ old('phone') }}" required><span class="field-error">Required field.</span></div>
            </div>
            <div class="form-field"><label>Email Address <span class="req">*</span></label><input type="email" name="email" value="{{ old('email') }}" required><span class="field-error">Enter a valid email.</span></div>
            <div class="wizard-nav"><span></span><button type="button" class="btn btn-navy wizard-next">Next Step <i class="fa-solid fa-arrow-right"></i></button></div>
          </div>

          <!-- STEP 2 — Travel Information -->
          <div class="wizard-panel">
            <h3 class="mb-3">Step 2 — Travel Information</h3>
            <p style="font-size:13.5px;color:var(--color-ink-500);margin-bottom:20px;">This information helps us determine the most suitable visa application process.</p>
            <div class="form-row-2">
              <div class="form-field"><label>Destination Country <span class="req">*</span></label><input type="text" name="destination_country" value="{{ old('destination_country') }}" required><span class="field-error">Required field.</span></div>
              <div class="form-field"><label>Purpose of Travel <span class="req">*</span></label><select name="purpose_of_travel" required><option value="">Select purpose</option><option>Work</option><option>Study</option><option>Tourism</option><option>Business</option><option>Family Visit</option><option>Other</option></select><span class="field-error">Required field.</span></div>
            </div>
            <div class="form-row-2">
              <div class="form-field"><label>Expected Travel Date</label><input type="date" name="expected_travel_date" value="{{ old('expected_travel_date') }}"></div>
              <div class="form-field"><label>Duration of Stay</label><input type="text" name="duration_of_stay" placeholder="e.g. 2 years" value="{{ old('duration_of_stay') }}"></div>
            </div>
            <div class="form-row-2">
              <div class="form-field"><label>Do you have an Admission Letter?</label><select name="has_admission_letter"><option value="">Select</option><option>Yes</option><option>No</option></select></div>
              <div class="form-field"><label>Do you have an Employment Contract?</label><select name="has_employment_contract"><option value="">Select</option><option>Yes</option><option>No</option></select></div>
            </div>
            <div class="form-field"><label>Do you have an Invitation Letter?</label><select name="has_invitation_letter"><option value="">Select</option><option>Yes</option><option>No</option></select></div>
            <div class="wizard-nav"><button type="button" class="btn btn-outline-navy wizard-prev"><i class="fa-solid fa-arrow-left"></i> Back</button><button type="button" class="btn btn-navy wizard-next">Next Step <i class="fa-solid fa-arrow-right"></i></button></div>
          </div>

          <!-- STEP 3 — Visa Information -->
          <div class="wizard-panel">
            <h3 class="mb-5">Step 3 — Your Visa Application</h3>
            <div class="form-field"><label>Visa Type <span class="req">*</span></label><select name="visa_type" required><option value="">Select visa type</option><option>Work Visa</option><option>Student Visa</option><option>Tourist Visa</option><option>Business Visa</option></select><span class="field-error">Required field.</span></div>
            <div class="form-row-2">
              <div class="form-field"><label>Previously applied for this country's visa?</label><select name="previously_applied"><option value="">Select</option><option>Yes</option><option>No</option></select></div>
              <div class="form-field"><label>Have you ever been refused a visa?</label><select name="previously_refused"><option value="">Select</option><option>Yes</option><option>No</option></select></div>
            </div>
            <div class="form-field"><label>If yes, please explain</label><textarea name="refusal_explanation" placeholder="Explain any prior visa refusal (if applicable)">{{ old('refusal_explanation') }}</textarea></div>
            <div class="form-row-2">
              <div class="form-field"><label>Travelled internationally before?</label><select name="travelled_internationally"><option value="">Select</option><option>Yes</option><option>No</option></select></div>
              <div class="form-field"><label>Countries Visited</label><input type="text" name="countries_visited" placeholder="e.g. Uganda, Tanzania" value="{{ old('countries_visited') }}"></div>
            </div>
            <div class="wizard-nav"><button type="button" class="btn btn-outline-navy wizard-prev"><i class="fa-solid fa-arrow-left"></i> Back</button><button type="button" class="btn btn-navy wizard-next">Next Step <i class="fa-solid fa-arrow-right"></i></button></div>
          </div>

          <!-- STEP 4 — Documents -->
          <div class="wizard-panel">
            <h3 class="mb-5">Step 4 — Required Documents</h3>
            <p style="font-size:13.5px;color:var(--color-ink-500);margin-bottom:20px;">Upload the following where applicable. Files are emailed directly to our visa support team — nothing is stored on our servers.</p>
            <div class="form-row-2">
              <div class="form-field"><label>Passport <span class="req">*</span></label><label class="upload-field"><i class="fa-solid fa-cloud-arrow-up"></i><span>Click to upload</span><input type="file" name="passport" required style="display:none;"></label><span class="field-error">Please upload your passport.</span></div>
              <div class="form-field"><label>Passport Photo <span class="req">*</span></label><label class="upload-field"><i class="fa-solid fa-cloud-arrow-up"></i><span>Click to upload</span><input type="file" name="passport_photo" required style="display:none;"></label><span class="field-error">Please upload a passport photo.</span></div>
            </div>
            <div class="form-row-2">
              <div class="form-field"><label>Admission Letter</label><label class="upload-field"><i class="fa-solid fa-cloud-arrow-up"></i><span>Upload if applicable</span><input type="file" name="admission_letter" style="display:none;"></label></div>
              <div class="form-field"><label>Employment Contract</label><label class="upload-field"><i class="fa-solid fa-cloud-arrow-up"></i><span>Upload if applicable</span><input type="file" name="employment_contract" style="display:none;"></label></div>
            </div>
            <div class="form-row-2">
              <div class="form-field"><label>Invitation Letter</label><label class="upload-field"><i class="fa-solid fa-cloud-arrow-up"></i><span>Upload if applicable</span><input type="file" name="invitation_letter" style="display:none;"></label></div>
              <div class="form-field"><label>Bank Statement</label><label class="upload-field"><i class="fa-solid fa-cloud-arrow-up"></i><span>Upload if applicable</span><input type="file" name="bank_statement" style="display:none;"></label></div>
            </div>
            <div class="form-row-2">
              <div class="form-field"><label>Academic Certificates</label><label class="upload-field"><i class="fa-solid fa-cloud-arrow-up"></i><span>Upload if applicable</span><input type="file" name="academic_certificates" style="display:none;"></label></div>
              <div class="form-field"><label>Additional Supporting Documents</label><label class="upload-field"><i class="fa-solid fa-cloud-arrow-up"></i><span>Upload if applicable</span><input type="file" name="additional_documents" style="display:none;"></label></div>
            </div>
            <div class="wizard-nav"><button type="button" class="btn btn-outline-navy wizard-prev"><i class="fa-solid fa-arrow-left"></i> Back</button><button type="button" class="btn btn-navy wizard-next">Next Step <i class="fa-solid fa-arrow-right"></i></button></div>
          </div>

          <!-- STEP 5 — Additional Info + Declaration -->
          <div class="wizard-panel">
            <h3 class="mb-5">Step 5 — Additional Information &amp; Declaration</h3>
            <div class="form-field"><label>Do you have any special circumstances or questions regarding your application?</label><textarea name="additional_info" placeholder="Tell us more...">{{ old('additional_info') }}</textarea></div>
            <div class="checkbox-field"><input type="checkbox" id="visa-agree1" name="declaration_accurate" value="1" required><label for="visa-agree1">I confirm that the information provided is accurate and complete to the best of my knowledge.</label></div>
            <div class="checkbox-field"><input type="checkbox" id="visa-agree2" name="declaration_consent" value="1" required><label for="visa-agree2">I consent to Altura Workforce Solutions contacting me regarding my visa application.</label></div>
            <div class="wizard-nav"><button type="button" class="btn btn-outline-navy wizard-prev"><i class="fa-solid fa-arrow-left"></i> Back</button><button type="submit" class="btn btn-primary wizard-submit">Submit Visa Application <i class="fa-solid fa-arrow-right"></i></button></div>
          </div>
        </form>
      </div>
    @endunless

    @if(session('success'))
      <div class="form-success show" style="max-width:700px;margin:0 auto;">
        <div class="icon-circle"><i class="fa-solid fa-champagne-glasses"></i></div>
        <h2 style="margin-bottom:10px;">🎉 Application Received Successfully</h2>
        <p style="color:var(--color-ink-600);max-width:480px;margin:0 auto;line-height:1.75;">Thank you for submitting your visa application request. Our visa support team will review your information and contact you within 1–2 business days with the next steps.</p>
        <a href="{{ route('public.visa') }}" class="btn btn-outline-navy mt-6">Back to Visa Support</a>
      </div>
    @endif
  </div>
</section>

</x-public-layout>

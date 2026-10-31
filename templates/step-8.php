<div
  class="mf-step mf-step-7"
  :class="{ 'mf-active': currentStepName === 'Preview' }"
>
  <h2 class="mf-title">Good news. You are one step away from your booking.</h2>
  <p class="mf-subtitle" style="margin-bottom: var(--space-2xl)">
    Your Selection:
  </p>

  <!-- Appointment Summary -->
  <div class="mf-appointment-summary-card">
    <div class="mf-appointment-summary-main">
      <img
        :src="getThumbnail(selectedClinicianId)"
        class="mf-counselor-image-final-preview mf-appointment-summary-image"
      />
      <div class="mf-therapist-details-summary">
        <h3 class="mf-therapist-name-summary">
          {{ getClinicianName() }}
          <a
            :href="getClinicianProfileUrl()"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="View therapist profile"
            class="mf-therapist-profile-link"
          >
            <i class="fas fa-external-link-alt"></i>
          </a>
        </h3>

        <p class="mf-therapist-address">
          <span class="mf-slot-tag">
            <template v-if="this.selectedSlotObject?.teletherapyOnlyFlag">
              Online
            </template>
            <template v-else-if="this.selectedSlotObject?.location?.address">
              {{ formatLocation(this.selectedSlotObject?.location?.address) }}
            </template>
          </span>
        </p>
      </div>
    </div>

    <div class="mf-appointment-cost-time">
      <p class="mf-appointment-datetime">
        {{ formatSlotDate(this.selectedSlotObject.start1, 'long') }},
        {{ formatSlotTime(this.selectedSlotObject.start, this.selectedSlotObject.endDt) }}
      </p>
      <p class="mf-appointment-duration">
        <i class="fas fa-clock"></i>
        Get Duration comes here
      </p>
    </div>
  </div>

  <!-- Additional Information Field -->
  <div class="mf-contact-preference-section">
    <div
      class="mf-input-field"
      style="margin-top: var(--space-xl); margin-bottom: var(--space-xl);"
    >
      <label for="mf-description" class="mf-input-label">
        Describe the reason for seeking therapy</span class="required">*</span>
      </label>
      <div class="mf-input-wrapper">
        <textarea
          placeholder="Type here..."
          rows="3"
          v-model="userDescription"
          class="mf-textarea"
        ></textarea>
      </div>
    </div>
  </div>

  <!-- Minor Clients Section -->
  <div class="mf-contact-preference-section" v-if="age < 18">
    <h3 class="mf-contact-preference-title">
      Important Information for Minor Clients (Under 18):
    </h3>
    <p>
      A parent or legal guardian is required to attend the initial appointment
      with the minor.
    </p>
    <p>Please do not drop off your child without staying for this first session.</p>

    <div class="mf-contact-options">
      <div class="mf-sms-opt-out-section">
        <div class="mf-preference-option">
          <input
            type="checkbox"
            id="mf-confirmation"
            value="true"
            v-model="minorStayConfirmation"
          />
          <label for="mf-confirmation">
            I understand that a parent or legal guardian must attend the first
            appointment.<span class="required">*</span>
          </label>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact Preferences -->
  <div class="mf-contact-preference-section">
    <h3 class="mf-contact-preference-title">
      How would you like to be contacted? Fill in a preferred choice:
    </h3>

    <div class="mf-contact-options mf-grid">
      <div class="mf-input-field">
        <div class="mf-input-wrapper">
          <i class="fas fa-phone mf-input-icon"></i>
          <input
            type="tel"
            placeholder="Enter 10-digit phone number *"
            title="Please enter a 10-digit phone number"
            autocomplete="tel"
            maxlength="10"
            v-model="phoneNumber"
            @blur="validatePhoneNumber"
            @input="filterDigits"
          />
        </div>
        <small v-if="phoneError" class="error-message">{{ phoneError }}</small>
      </div>

      <div class="mf-input-field">
        <div class="mf-input-wrapper">
          <i class="fas fa-envelope mf-input-icon"></i>
          <input
            type="email"
            placeholder="Enter your email address *"
            autocomplete="email"
            v-model="email"
            @blur="validateEmail"
          />
        </div>
        <small v-if="emailError" class="error-message">{{ emailError }}</small>
      </div>
    </div>
  </div>

  <p class="mf-subtitle">
    We may send you appointment reminders, requests for information, or other
    important details related to your care. You can opt out of these messages,
    but doing so may make it difficult for us to keep in touch with you.
  </p>

  <!-- SMS Opt-In -->
  <div class="mf-sms-opt-out-section">
    <div class="mf-preference-option">
      <input
        type="checkbox"
        id="mf-opt-out-sms"
        value="true"
        v-model="smsOptIn"
      />
      <label for="mf-opt-out-sms">
        Send me SMS alerts about my appointment
      </label>
    </div>
  </div>

  <!-- Email Opt-In -->
  <div class="mf-email-opt-out-section">
    <div class="mf-preference-option">
      <input
        type="checkbox"
        id="mf-opt-out-email"
        value="true"
        v-model="emailOptIn"
      />
      <label for="mf-opt-out-email">
        Send me email alerts about my appointment
      </label>
    </div>
  </div>
</div>

<?php

/**
 * Template Name: VueJS Form
 */

get_header();
?>

<div id="loader" class="mf-container" style="text-align: center;" >
    <i class="fas fa-regular fa-bahai fa-spin fa-6x gradient-icon"></i>
</div>

<div id="app" class="mf-container" style="display: none;">

  <form class="mf-form" >

    <div class="warning-box" v-if="showWarning">
        <ul v-if="Array.isArray(warningMsg)">
            <span>Please fix all the errors below.</span>
            <li v-for="(msg, index) in warningMsg" :key="index">{{ msg }}</li>
        </ul>
        <span v-else>{{ warningMsg }}</span>
    </div>


    <!-- Step 1: Service Type -->
    <div class="mf-step mf-step-1" :class="{ 'mf-active': currentStepName === 'Service' }">
      <h2 class="mf-title">
        Let's find the right provider for your needs
      </h2>
      <p class="mf-subtitle prominent-text"><b>This form is for new client scheduling only.</b> To reschedule, cancel, or book additional sessions, please contact your provider directly or call <a href="tel:+18887188148">888-718-8148</a>.</p>
      <p class="mf-subtitle">What service are you looking for?<span class="required">*</span></p>
      <div class="mf-counseling-options mf-grid">
        <div
          v-for="(service, index) in serviceData"
          :key="index"
          class="mf-option-box"
          :class="{ 'mf-selected': selectedService === service.id }"
          @click="selectOption(service.id, 'Service')"
          :data-value="service.id"
        >
          {{ service.name }}
        </div>
      </div>
      <input type="hidden" name="service_type" id="mf-service_type" />
    </div>

    <!-- Step 2: Initial Concerns -->
      <?php include locate_template('templates/step-2-issues.php'); ?>

    <!-- Step 3: About You (Age & Location) -->
      <?php include locate_template('templates/step-3.php'); ?>

     <!-- Step 4: Booking Type -->
    <div class="mf-step mf-step-1" :class="{ 'mf-active': currentStepName === 'Therapy Type' }">
      <h2 class="mf-title">
        Select type of therapy
      </h2>
      <p class="mf-subtitle">What format of therapy are you looking for?<span class="required">*</span></p>
      <div class="mf-counseling-options mf-grid">
        <div
          v-for="(therapy, index) in appointmentTypeData"
          :key="index"
          class="mf-option-box"
          :class="{ 'mf-selected': selectedAppointmentType === therapy.id }"
          @click="selectOption(therapy.id, 'Therapy Type')"
          :data-value="therapy.id"
        >
          {{ therapy.name }}
        </div>
      </div>
    </div>

      <?php include locate_template('templates/step-5.php'); ?>

    <!-- Step 6: Insurance Details (Formerly Step 5) -->
    <div class="mf-step mf-step-5" :class="{ 'mf-active': currentStepName === 'Client Info' }">
      <h2 class="mf-title">Client Details</h2>
      <p class="mf-subtitle">Please provide your contact information with your accurate phone and email details.</p>

      <div class="mf-grid">
        <!-- Row 1: First Name, Last Name -->
        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-user mf-input-icon"></i>
            <input
              type="text"
              placeholder="First Name *"
              required
              v-model="clientInfo.firstName"
            />
          </div>
        </div>
        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-user mf-input-icon"></i>
            <input
              type="text"
              placeholder="Last Name *"
              required
              v-model="clientInfo.lastName"
            />
          </div>
        </div>

                <!-- Row 2: Email, Phone -->
        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-envelope mf-input-icon"></i>
            <input
              type="email"
              placeholder="Email *"
              required
              autocomplete="email"
              v-model="email"
              @blur="validateEmail"
            />
          </div>
        </div>
        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-phone mf-input-icon"></i>
            <input
              type="tel"
              inputmode="numeric"
              placeholder="Phone Number *"
              required
              maxlength="10"
              autocomplete="tel-national"
              v-model="phoneNumber"
              @input="filterDigits"
            />
          </div>
        </div>

      </div>
      <hr class="mf-form-divider" />
      <p class="mf-subtitle">On the next screen, you'll be asked to confirm your appointment selection and contact information. To complete your appointment, we also ask that you keep a credit card on file.</p>

    </div>
    <!-- End of Step 6 -->

    <div class="mf-step mf-step-6" :class="{ 'mf-active': currentStepName === 'Insurer Info' }">
      <h2 class="mf-title">Insurer Details</h2>
      <p class="mf-subtitle">
        Please provide your insurance information to verify eligibility and
        potential co-pay. If you selected "No Insurance" previously, you can skip this step or provide details if you change your mind.
      </p>

      <div class="mf-grid" v-if="clientInfo.insuredRelationship != 'Self'">
        <!-- Row 1: First Name, Last Name -->
        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-user mf-input-icon"></i>
            <input
              type="text"
              placeholder="First Name *"
              required
              v-model="insurerInfo.firstName"
            />
          </div>
        </div>
        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-user mf-input-icon"></i>
            <input
              type="text"
              placeholder="Last Name *"
              required
              v-model="insurerInfo.lastName"
            />
          </div>
        </div>

        <!-- Row 2: Gender, DOB -->
        <div class="mf-input-field">
          <div class="mf-input-wrapper mf-select-wrapper">
            <i class="fas fa-venus-mars mf-input-icon"></i>
            <select id="mf-gender_step6" name="gender_step6" required v-model="insurerInfo.gender">
              <option value="" disabled selected>Insurer's Gender</option>
                <option value="female">Female</option>
                <option value="male">Male</option>
            </select>
            <i class="fas fa-chevron-down mf-dropdown-arrow"></i>
          </div>
        </div>
        <div class="mf-input-field">
          <div class="mf-input-wrapper" @click="openDatePicker('dob_insurer')">
            <i class="fas fa-calendar-alt mf-input-icon"></i>
              <input
                  type="date"
                  v-model="insurerInfo.birthDate"
                  :max="maxAllowedBirthDate"
                  @input="ValidateDate"
                  title="Insurer's DOB"
              />
          </div>
        </div>

        <!-- Insurance Provider -->
        <div class="mf-input-field">
          <div class="mf-input-wrapper mf-select-wrapper">
            <i class="fas fa-shield-alt mf-input-icon"></i>
            <input type="text" id="mf-insurance_provider_step6" required disabled v-model="selectedInsuranceName" title="You can go to step 3 to change insurance provider"/>

          </div>
        </div>
      </div>

      <hr class="mf-form-divider" v-if="clientInfo.insuredRelationship != 'Self'" />

      <div class="mf-grid">
        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-id-card mf-input-icon"></i>
            <input
              type="text"
              placeholder="Insurance Policy Number"
              required
              v-model="insurerInfo.policyNumber"
            />
          </div>
        </div>

        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-hashtag mf-input-icon"></i>
            <input
              type="text"
              placeholder="Insurance Group Number"
              v-model="insurerInfo.groupNumber"
            />
          </div>
        </div>
      </div>

      <hr class="mf-form-divider" v-if="clientInfo.insuredRelationship != 'Self'" />
      <div class="mf-grid" v-if="clientInfo.insuredRelationship != 'Self'">
        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-home mf-input-icon"></i>
            <input
              type="text"
              placeholder="Address 1 *"
              required
              v-model="insurerInfo.address1"
            />
          </div>
        </div>

        <!-- Row 6: Address 2, City -->
        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-home mf-input-icon"></i>
            <input
              type="text"
              placeholder="Address 2 (Optional)"
              v-model="insurerInfo.address2"
            />
          </div>
        </div>

        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-city mf-input-icon"></i>
            <input
              type="text"
              placeholder="City *"
              required
              v-model="insurerInfo.city"
            />
          </div>
        </div>

        <!-- Row 7: State -->
        <div class="mf-input-field">
          <div class="mf-input-wrapper mf-select-wrapper">
            <i class="fas fa-map mf-input-icon"></i>
            <select v-model="insurerInfo.state">
              <option value="" readonly selected>Select State *</option>
              <option v-for="state in allStatesData" :key="state.code" :value="state.name">
                {{ state.name }}
              </option>
            </select>
            <i class="fas fa-chevron-down mf-dropdown-arrow"></i>
          </div>
        </div>

                <!-- Row 5: ZIP-->
        <div class="mf-input-field">
          <div class="mf-input-wrapper">
            <i class="fas fa-map-marker-alt mf-input-icon"></i>
            <input
              type="tel"
              pattern="[0-9]*"
              placeholder="Zip code* e.g. 00000"
              max="99999"
              title="Please enter a 5-digit ZIP code"
              v-model="insurerInfo.zip"
              @input="handleZipChangeForInsurer"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation & Booking (Formerly Step 6) -->
    <!-- End of Confirmation & Booking -->

    <!-- End of Step 8 -->
  </form>

  <footer class="mf-navigation" :class="{ 'd-none': currentStepName === 'Print' }">
    <button
      type="button"
      class="mf-navigation-button mf-navigation-back"
      :class="{ 'd-none': currentStepName === 'Service' }"
      :disabled="prevBtnDisabled"
      @click="prevStep"
    >
      <i class="fas fa-arrow-left"></i>
      <span class="mf-button-text">Previous</span>
    </button>

    <div class="mf-progress-tracker">
      <span class="mf-progress-step">STEP {{ currentStepIndex + 1 }} OF {{ selectedSteps.length }}</span>
      <div class="mf-progress-container">
        <div class="mf-progress-fill" :style="{ width: progressPercentage  + '%' }"></div>
      </div>
    </div>

    <button
      type="button"
      class="mf-navigation-button mf-navigation-next"
      @click="nextStep"
      :disabled="isSubmitting"
      :class="{ 'd-none': cliniciansLoaded && groupedSlots.length === 0 && currentStepName === 'Clinicians' }"
    >
      <span v-if="isSubmitting">
        Submitting
      </span>

      <span class="mf-button-text" v-else>
        {{ buttonText }}
      </span>

      <i class="fas fa-arrow-right"></i>
    </button>

  </footer>
</div>

<?php include locate_template('templates/popup.php'); ?>

<?php get_footer(); ?>

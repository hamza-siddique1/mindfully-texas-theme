<div class="mf-step mf-step-3" :class="{ 'mf-active': currentStepName === 'Basic Info' }">
    <h2 class="mf-title">Tell us a bit about yourself</h2>
    <p class="mf-subtitle">
    Client's age and location help us find licensed clinicians in your area.
    </p>
    <div class="mf-grid-2">
    <div class="mf-input-field" @click="openDatePicker('dob_basic_info')">
        <label class="mf-input-label">Client's DOB<span class="required">* </span> </label>
        <div class="mf-input-wrapper">
        <i class="fas fa-calendar-alt mf-input-icon"></i>

            <input
                type="date"
                v-model="clientInfo.birthDate"
                :max="maxAllowedBirthDate"
                @input="ValidateDate"
            />
        </div>
        <span style="color: var(--text-light);" v-if="age > 0 && age < 200"> Client's Age: {{ age }} year(s)</span>
        <div v-if="dateError" style="
            color: var(--error);
            margin-top: var(--space-sm);
            font-size: 0.9em;
            ">{{ dateError }}</div>
    </div>
    <div class="mf-input-field" >
        <label for="mf-gender" class="mf-input-label">Client’s gender on file with insurance company<span class="required">*</span></label>
        <div class="mf-input-wrapper">
            <i class="fas fa-venus-mars mf-input-icon"></i> <!-- gender icon -->
            <select id="mf-gender"  required v-model="clientInfo.gender">
            <option value="" selected>Select client gender</option>
                <option value="female">Female</option>
                <option value="male">Male</option>
            </select>
        </div>
    </div>
    </div>
    <div class="mf-grid-2" style="margin-top: 30px;">
    <div class="mf-input-field">
        <label for="mf-zip" class="mf-input-label">Zip Code<span class="required">*</span></label>
        <div class="mf-input-wrapper">
        <i class="fas fa-map-marker-alt mf-input-icon"></i>
        <input
            type="text"
            id="mf-zip"
            name="zip"
            v-model="zip"
            @input="handleZipChange"
            placeholder="00000"
            required
            pattern="^\d{5}$"
            title="Please enter a 5-digit ZIP code"
        />
        </div>

        <span
            class="mf-step5-validation-message"
            style="
            color: var(--error);
            margin-top: var(--space-sm);
            font-size: 0.9em;
            ">
            {{ zip_code_msg }}
        </span>
    </div>

    <div class="mf-input-field" v-if="zip.length == 5 && insuranceData.length > 0 && zip_code_msg === ''">
    <label for="mf-insurance_step4" class="mf-input-label">Insurance Provider<span class="required">*</span></label>


        <p-search
                v-model="selectedInsuranceName"
                :options="insuranceDataSimple"
                placeholder="Select Insurance Provider"
                filter="true"
                showClear="true"
        >
            <template #value="slotProps">
                <div v-if="slotProps.value" class="flex items-center">
                    {{ slotProps.value }}
                </div>
                <span v-else>{{ slotProps.placeholder }}</span>
            </template>

            <template #option="slotProps">
                {{ slotProps.option }}
            </template>
        </p-search>


    </div>


    </div>
</div>

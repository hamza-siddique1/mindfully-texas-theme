
<div class="mf-step mf-step-4" :class="{ 'mf-active': currentStepName === 'Clinicians' }">

    <h2 class="mf-title">Select clinician and time slot</h2>

    <div class="mf-therapist-accordion">

        <div style="text-align: center;" v-if="loading">
            <i class="fas fa-regular fa-bahai fa-spin fa-6x gradient-icon"></i>
        </div>

        <div v-if="cliniciansLoaded && groupedSlots.length === 0">
            <div v-if="selectedAppointmentType === 'inOffice'">
                <p class="mf-subtitle grey-error">
                    Sorry, we don’t have in-person options that match your criteria currently, but we may have <b><u>Teletherapy options</u></b> that fit your needs.
                </p>

                <div class="mf-therapist-actions">
                    <button type="button" class="mf-see-all-availability-btn" @click="showTeletherapyOptions">Show Teletherapy Options</button>
                </div>
            </div>
            <div v-else>
                <p class="mf-subtitle grey-error">
                    Sorry, we don’t have any therapists who match your insurance and criteria right now. Please adjust your search, or call our Intake team at <a href="tel:+18888300347">(888) 830-0347</a> for help.
                </p>
            </div>
        </div>

        <div class="mf-therapist-card" v-if="cliniciansLoaded && groupedSlots.length > 0"
             v-for="{ clinician, slots, distance } in groupedSlots"
             :key="clinician.id"
             :class="{ 'mf-open': isExpanded(clinician.id) }">

            <div class="mf-card-content">
                <a :href="getProfileUrl(clinician.profile_url)" target="_blank">
                    <div class="mf-therapist-image-wrapper">
                        <img :src="getThumbnail(clinician.thumbnail_url)"
                            :alt="clinician.name"
                            class="mf-counselor-image">
                    </div>
                </a>

                <div class="mf-therapist-info-wrapper">

                    <div class="mf-therapist-details">
                        <h3 class="mf-therapist-name">{{ clinician.name }}<span class="mf-therapist-credentials"></span></h3>

                        <div class="mf-therapist-bio">
                            <p v-html="clinician.description"></p>
                        </div>

                        <div class="mf-therapist-meta">
                            <div class="mf-therapist-specialties" v-if="getSpecialities(clinician.id).length > 0">
                                <strong>Specialties</strong>
                                <div class="mf-therapist-tags">
                                    <span
                                            v-for="(spec, index) in getSpecialities(clinician.id)"
                                            :key="index"
                                            class="mf-tag"
                                    >
                                        {{ spec }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="selectedAppointmentType === 'in_office'">
                                <strong>Office Location</strong>
                                <div class="mf-therapist-tags">
                                    <span class="mf-tag">{{ slots[0].telehealthAvailable ? 'Telehealth' : (slots[0].offices?.[0]?.location || 'In Person') }}</span>
                                    <span v-if="distance !== null">
                                    {{ distance < 5
                                        ? 'Less than 5 miles away'
                                        : `${Math.round(distance)} miles away` }}
                                    </span>
                                </div>
                            </div>
                                    </br>
                            <div v-if="selectedInsurance == 9999">
                                <strong>Private Pay Rate</strong>
                                <div class="mf-therapist-tags">
                                    <p :href="clinician.locationUrl"
                                       class="mf-tag mf-location-link">${{getPayRate(clinician.id)}}/session</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mf-therapist-actions">
                         <div class="mf-next-availability">
                            Next Available: <strong>{{ formatSlotDate(slots[0]) }}</strong>
                        </div>
                        <button type="button"
                                class="mf-see-all-availability-btn"
                                @click="toggleAvailability(clinician.id)"
                                :aria-expanded="isExpanded(clinician.id)">
                            {{ isExpanded(clinician.id) ? 'Hide Availability' : 'See All Availability' }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="mf-availability-slots-wrapper">
                <?php include __DIR__ . '/step-5-slots.php'; ?>
            </div>

        </div>
    </div>
</div>

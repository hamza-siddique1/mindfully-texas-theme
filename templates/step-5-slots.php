<div class="mf-availability-container">
  <h4 class="mf-availability-title">Available Slots</h4>

  <div class="mf-date-slots">
    <div
      v-for="slot in slots.slice(0, visibleSlotCounts[clinician.id] || defaultVisibleCount)"
      :key="slot.id"
      class="mf-date-slot mf-slot-insurance"
      :class="{ 'mf-selected': selectedSlotId === slot.id }"
      @click="selectedSlotId = slot.id"
    >
      <div class="mf-slot-main">
        <div class="mf-slot-day-date">{{ formatSlotDate(slot) }}</div>
        <div class="mf-slot-time">{{ formatSlotTime(slot) }}</div>
      </div>

      <div class="mf-slot-details">
        <span class="mf-slot-tag">
          <i class="fa-solid fa-clock"></i>{{ slot.lengthMinutes }} min
        </span>

        <span class="mf-slot-tag">
          <i class="fa-solid" :class="slot.telehealth ? 'fa-video' : 'fa-location-dot'"></i>
          {{ slot.telehealthAvailable ? 'Telehealth' : (slot.offices?.[0]?.location || 'In Person') }}
        </span>
      </div>

      <button type="button" class="mf-select-slot-btn" @click="handleSlotSelection(slot)">
        Select Slot
      </button>
    </div>
  </div>

    <button
            v-if="(visibleSlotCounts[clinician.id] || defaultVisibleCount) < slots.length"
            @click="showMoreSlots(clinician.id)"
            type="button" class="mf-view-more-btn">
        <i class="fas fa-calendar-plus"></i> View More Slots
    </button>

    <button
            type="button"
            class="mf-toggle-availability-btn"
            @click="toggleAvailability(clinician.id)"
            :aria-expanded="isExpanded(clinician.id)"
    >
                <span class="mf-btn-text">
                  Hide This Clinician
                </span>
    </button>

</div>

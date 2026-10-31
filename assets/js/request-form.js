const { createApp } = Vue;

const app = createApp({
  data() {
    return {
      isLocalEnv: false,
      allSteps: [
        {
          id: 1,
          name: "Service",
        },
        {
          id: 2,
          name: "Issues",
        },
        {
          id: 3,
          name: "Basic Info",
        },
        {
          id: 4,
          name: "Clinicians",
        },
        {
          id: 5,
          name: "Client Info",
        },
        {
          id: 6,
          name: "Insurer Info",
        },
        {
          id: 7,
          name: "Preview",
        },
        {
          id: 8,
          name: "Therapy Type",
        },
        {
          id: 9,
          name: "Print",
        },
      ],

      stepConditions: {
        defaultCondition: [
          "Service",
          "Issues",
          "Basic Info",
          "Therapy Type",
          "Clinicians",
          "Client Info"
        ],
      },

      zipLocations: {
        "43017": { lat: 40.0898086, lon: -83.1310477 },               // Dublin
        "43302": { lat: 40.5884928, lon: -83.1308035 },               // Marion
        "43623": { lat: 41.70395240304676, lon: -83.64611231801332 }, // Toledo
        "44136": { lat: 41.3278087, lon: -81.8369622 },               // Strongsville
        "44202": { lat: 41.312073, lon: -81.345406 },                 // Aurora
        "45014": { lat: 39.3373958, lon: -84.5388773 },               // Fairfield
        "45056": { lat: 39.50925789996194, lon: -84.74604091697807 }, // Oxford
        "45236": { lat: 39.203635, lon: -84.370476 },                 // Kenwood
        "45242": { lat: 39.1894491, lon: -84.4116975 },               // Montgomery (check: old point was 3.7 km from Kenwood, 6.6 km from Montgomery)
        "45244": { lat: 39.0710114, lon: -84.310021 },                // Anderson (same point as 45255)
        "45248": { lat: 39.158819, lon: -84.634467 },                 // Western Hills
        "45255": { lat: 39.0710114, lon: -84.310021 },                // Anderson (same point as 45244)
        "45458": { lat: 39.5908076, lon: -84.2008227 },               // Centerville
        "78759": { lat: 30.374774430114645, lon: -97.76048217650641 }, // Spicewood
        "78752": { lat: 30.331088415630024, lon: -97.70904351534247 },
        "78756": { lat: 30.324584924933117, lon: -97.73914333478297 },
        "78757": { lat: 30.37158463767897,  lon: -97.7383496288356 },
        "78748": { lat: 30.17379810522861,  lon: -97.81365769630133 },
      },

      selectedSteps: [],
      currentStepIndex: 0, //change this number to open particular step directly.

      issuesData: [],
      showAll: false,
      insuranceData: [],
      locationsData: [],
      nearestLocationId: '',
      insuranceDataSimple: [],
      availableSlots: [],
      groupedSlots: [],
      sortedSlots: [],
      cliniciansLoaded: false,
      demographicID: 0,
      phoneNumber: "",
      phoneError: "",
      email: "",
      emailError: "",
      zip: "", //45236 use this for testing
      zip_code_msg: '',
      lat: null,
      lng: null,
      stateCode: null,
      allStatesData: [],
      stateName: "",
      dateError: "",
      stateID: null,
      serviceData: [
        { id: "INDIVIDUAL", name: "Individual Counseling" },
        { id: "COUPLE", name: "Couples Counseling" },
        { id: "FAMILY", name: "Family Counseling" },
        // { id: "GROUP", name: "Group Counseling" },
        { id: "EMBEDDED", name: "Psychiatry (Medication Management)" },
      ],
      appointmentTypeData: [
        { id: "in_office", name: "In-Person Therapy" },
        { id: "telehealth", name: "Online Therapy" },
      ],

      isToggledAppointmentType: false,

      loading: true,
      showWarning: false,
      warningMsg: "Please select at least one option.",
      invalidZipCode: false,

      selectedService: null,
      selectedIssues: [],
      selectedInsurance: '',
      selectedAppointmentType: "",
      selectedInsuranceName: "",
      selectedSlotId: 0,
      selectedClinicianId: "",
      selectedSlotObject: {},
      visibleSlotCounts: {},
      defaultVisibleCount: 6,
      cares_slug: [],
      cares_zip_codes: [],

      expandedClinicians: {},

      nextBtnDisabled: true,
      submitBtnDisabled: false,
      submittingForm: false,
      formSubmitted: false,
      isSubmitting: false,

      filtersChanged: true,
      clientInfo: {
        firstName: "",
        lastName: "",
        fullName: "",
        preferredName: "",
        insuredRelationship: "",
        address1: "",
        address2: "",
        city: "",
        gender: "",
        birthDate: '',
      },
      age: 0,
      fullNameManuallyEdited: false,
      clientInfoErrors: {},
      insurerInfo: {
        firstName: "",
        lastName: "",
        address1: "",
        address2: "",
        city: "",
        state: "",
        zip: "",
        policyNumber: "",
        groupNumber: "",
        gender: "",
        birthDate: "",
      },
      insurerInfoErrors: {},
      previewErrors: {},

      emailOptIn: true,
      smsOptIn: true,
      minorStayConfirmation: false,
      latestZipRequestToken: null,
      nearestOfficeLocation: {},
      printing: false,
      userDescription: '',
      expandedDescriptionList: [],

      clientObject: {
        id: 39229,
        firstName: "Deserunt aliqua Ipsmwro",
        lastName: "Est id illo eius q",
        preferredName: "Test test1",
        preferredPronouns: null,
        email: "pawofijaty@mailinator.com",
        dob: "1997-06-09",
        clientFlag: false,
        activeFlag: true,
        accountNumber: 1750017932420,
        note: "",
        billingNote: "",
        hasAttachment: false,
        onboardingStatus: "NOT_STARTED",
        formProvider: "FORMIO",
      },

      mustShowNames: [
        "I need someone to talk to",
        "Abuse/Trauma/PTSD",
        "Addiction (behavioral or substance)",
        "Anxiety/depression",
        "ADHD",
        "DBT - Informed",
        "DBT - Team",
        "EMDR",
        "Grief",
        "LGBTQIA (Gender and Sexual Identity)",
        "Life Changes",
        "Self Harming/Suicidal Thoughts"
      ],

      issuesForChildTherapy: [
        "I need someone to talk to",
        "Abuse/Trauma/PTSD",
        "ADHD",
        "Behavior Modification/Changes",
        "Bullying",
        "LGBTQIA (Gender and Sexual Identity)",
        "Life Changes",
        "Parenting Concerns",
        "Play Therapy",
        "School Problems",
        "Self Harming/Suicidal Thoughts",
        "Stress Management"
      ],

      maxDistanceMiles: 20,
      maxLocationIds: 3,
      noNearbyOffices: false,
      searchRangeStart: null,

      sessionId: 'sess-' + Math.random().toString(36).substr(2, 9),
    };
  },
  computed: {
    progressPercentage() {
      return ((this.currentStepIndex + 1) / this.selectedSteps.length) * 100;
    },

    currentStepName() {
      return this.selectedSteps[this.currentStepIndex]?.name || "";
    },

    prevBtnDisabled() {
      return this.currentStepIndex === 0;
    },

    isLastStep() {
      return this.currentStepIndex === this.selectedSteps.length - 1;
    },

    filteredIssues() {

      let baseList = this.issuesData.filter(
        (issue) => issue.id !== this.selectedService
      );

      if (this.showAll) {
        return baseList;
      }

      if(this.selectedService == 'ADOLESCENT'){
        return baseList.filter((issue) =>
        this.issuesForChildTherapy.some((keyword) =>
          issue.name.toLowerCase().includes(keyword.toLowerCase())
        )
      );
      }

      return baseList.filter((issue) =>
        this.mustShowNames.some((keyword) =>
          issue.name.toLowerCase().includes(keyword.toLowerCase())
        )
      );
    },

    selectedIssueNames() {
      return this.issuesData
        .filter(issue => this.selectedIssues.includes(issue.tag))
        .map(issue => issue.name)
        .join(', ');
    },

    maxAllowedBirthDate() {
      const today = new Date();
      today.setFullYear(today.getFullYear() - 3);
      return today.toISOString().split('T')[0];
    },

    buttonText() {
      if (this.currentStepIndex === this.selectedSteps.length - 1) {
        return 'CONFIRM';
      }

      if (this.currentStepName === 'Therapy Type') {
        return 'Search';
      }

      return 'Next';
    },

    combinedDescription() {
      return `Most Important Issue: ${this.selectedIssueNames} \nReason for Seeking Therapy: ${this.userDescription}`;
    },

    locationsByDistance() {
        if (this.lat == null || this.lng == null) return [];
        return this.locationsData
          .filter((l) => l.coordinates?.latitude != null && l.coordinates?.longitude != null)
          .map((l) => ({
            ...l,
            distance: this.haversineDistance(
              this.lat, this.lng,
              l.coordinates.latitude, l.coordinates.longitude
            ),
          }))
          .sort((a, b) => a.distance - b.distance);
    },

    nearestLocation() {
        return this.locationsByDistance[0] || null;
    },

      // { "36681": 4.2, "36682": 11.8, ... } in miles
    distanceByLocationId() {
        return Object.fromEntries(
          this.locationsByDistance.map((l) => [String(l.healthieLocationId), l.distance])
        );
    },

    distanceByZip() {
      return Object.fromEntries(
        this.locationsByDistance
          .filter((l) => l.address?.postalCode)
          .map((l) => [String(l.address.postalCode).slice(0, 5), l.distance])
      );
    },

    // computed
    nearbyLocationIds() {
      const offices = new Map();
      for (const l of this.locationsByDistance) {
        if (l.distance > this.maxDistanceMiles) break; // list is sorted, so stop early
        const key = `${l.coordinates.latitude},${l.coordinates.longitude}`;
        if (!offices.has(key)) offices.set(key, []);
        offices.get(key).push(String(l.healthieLocationId));
      }

      // Add whole offices until we reach the API's limit of 5 IDs
      const ids = [];
      for (const officeIds of offices.values()) {
        if (ids.length + officeIds.length > this.maxLocationIds) break;
        ids.push(...officeIds);
      }
      return ids;
    },
  },

  watch: {
    nearestLocation(loc) {
      this.nearestLocationId = loc ? String(loc.healthieLocationId) : null;
    },

    birthDate() {
      this.updateNextButtonState();
    },

    zip() {
      this.updateNextButtonState();
    },

    selectedSlotId() {
      this.updateNextButtonState();
    },

    currentStepIndex(newIndex, oldindex) {
      if (this.currentStepName === "Clinicians" && newIndex > oldindex) {
        this.fetchAvailableSlots();
      }

      if (this.currentStepName === "Insurer Info") {
        if (this.clientInfo.insuredRelationship === 'Self') {
          this.insurerInfo.firstName = this.clientInfo.firstName;
          this.insurerInfo.lastName = this.clientInfo.lastName;
          this.insurerInfo.birthDate = this.clientInfo.birthDate;
          this.insurerInfo.gender = this.clientInfo.gender;
          this.insurerInfo.address1 = this.clientInfo.address1;
          this.insurerInfo.address2 = this.clientInfo.address2;
          this.insurerInfo.city = this.clientInfo.city;
          this.insurerInfo.state = this.stateName;
          this.insurerInfo.zip = this.zip;
        }
        else {
          this.insurerInfo.firstName = '';
          this.insurerInfo.lastName = '';
          this.insurerInfo.birthDate = '';
          this.insurerInfo.gender = '';
          this.insurerInfo.address1 = '';
          this.insurerInfo.address2 = '';
          this.insurerInfo.city = '';
          this.insurerInfo.state = '';
          this.insurerInfo.zip = '';
        }
      }

      if (this.currentStepName === "Preview") {
        this.showWarning = false;
      }

      if (this.currentStepName === "Issues") {
        const parent = document.getElementById('app');
        if (parent) {
          parent.classList.add('mf-container-issues');
        }
      }
      else {
        const parent = document.getElementById('app');
        if (parent) {
          parent.classList.remove('mf-container-issues');
        }
      }

      if (this.currentStepName === "Client Info") {
        this.getClosestOfficeLocationZipCode();
      }
    },

    selectedInsuranceName(newName) {
      const found = this.insuranceData.find((ins) => ins.payerName === newName);
      this.selectedInsurance = found.id;
    },

    clientInfo: {
      handler(newVal) {
        this.updateNextButtonState();
      },
      deep: true,
    },

    insurerInfo: {
      handler() {
        this.updateNextButtonState();
      },
      deep: true,
    },

    phoneNumber() {
      this.updateNextButtonState();
    },

    email() {
      this.updateNextButtonState();
    },

    selectedService(newService) {
      if (newService === 'EMBEDDED') {
        window.location.href = '/psych-services-request-form/';
      }
    },
  },

  methods: {

    isClinicianBoosted(clinician) {
      return String(clinician?.boosted).trim().toLowerCase() === 'true';
    },

    sortByBoosted(groups) {
      const rank = (g) => (String(g.clinician?.boosted).trim().toLowerCase() === 'true' ? 0 : 1);
      return [...groups].sort((a, b) => rank(a) - rank(b));
    },

    sortByEarliestOpening(groups) {
      const cmp = (x, y) => (x === y ? 0 : x < y ? -1 : 1);
      const t = (s) => (s?.start ? new Date(s.start).getTime() : Infinity);

      return groups
        .map((g) => ({
          ...g,
          slots: [...(g.slots || [])].sort((a, b) => cmp(t(a), t(b))), // slots[0] = first opening
        }))
        .sort((a, b) => cmp(t(a.slots[0]), t(b.slots[0])));
    },

    addDistances(groups) {
      const canMeasure =
        this.selectedAppointmentType === 'in_office' && this.locationsByDistance.length > 0;

      return groups.map((group) => {
        const slots = (group.slots || []).map((slot) => {
          const d = canMeasure ? this.getSlotDistance(slot) : Infinity;
          return { ...slot, distance: Number.isFinite(d) ? Math.round(d * 10) / 10 : null };
        });

        const known = slots.map((s) => s.distance).filter((d) => d !== null);
        const distance = known.length ? Math.min(...known) : null;

        return { ...group, slots, distance };
      });
    },

    getSlotDistance(slot) {
      const distances = (slot?.offices || [])
        .map((o) => this.distanceByLocationId[String(o.locationId)])
        .filter((d) => d != null);

      return distances.length ? Math.min(...distances) : Infinity; // unknown goes last
    },

    sortByClosestOffice(groups) {
      // Only meaningful for in-office searches once the user's lat/lng is known
      if (this.selectedAppointmentType !== 'in_office' || !this.locationsByDistance.length) {
        return groups;
      }

      const nearestMiles = (g) =>
        Math.min(...(g.slots || []).map((s) => this.getSlotDistance(s)), Infinity);

      return groups
        .map((g) => ({ g, d: nearestMiles(g) }))
        .sort((a, b) => (a.d === b.d ? 0 : a.d < b.d ? -1 : 1)) // safe with Infinity
        .map((x) => x.g);
    },
    // 1) Boosted clinicians first
    compareBoosted(a, b) {
      const rank = (g) => (this.isClinicianBoosted(g.clinician) ? 0 : 1);
      return rank(a) - rank(b);
    },

    // 2) Closest office first
    compareClosestOffice(a, b) {
      const da = a.distance ?? Infinity;
      const db = b.distance ?? Infinity;
      if (da === db) return 0; // avoids Infinity - Infinity = NaN
      return da < db ? -1 : 1;
    },

    // 3) Earliest first opening
    compareEarliestOpening(a, b) {
      const t = (g) => (g.slots?.[0]?.start ? new Date(g.slots[0].start).getTime() : Infinity);
      const ta = t(a);
      const tb = t(b);
      if (ta === tb) return 0;
      return ta < tb ? -1 : 1;
    },

    toggleShowAll() {
      this.showAll = !this.showAll;
    },

    haversineDistance(lat1, lon1, lat2, lon2) {
      const R = 6371;
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;

      const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);

      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c * 0.621371; // converting km to miles
    },


    ValidateDate(event) {
      const value = event.target.value;
      this.dateError = '';

      if (value.length !== 10) return;

      this.age = this.calculateAge();
      if (this.age < 0) {
        this.dateError = 'Future dates are not allowed';
      } else if (this.age < 3) {
        this.dateError = 'Client must be at least 3 years old';
      } else {
        this.dateError = null;
      }
    },

    sendDetailsToMyTherapyFlow(extraParams = {}) {
      //Send form submit success status
      (async () => {
        try {
          await fetch('/wp-json/api/v1/form-submitted', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              session_id: this.sessionId,
              status: 'success'
            })
          });
        } catch (error) {
          console.error("Progress tracking error:", error);
        }
      })();

      const slot = this.selectedSlotObject;
      const locationName = slot.offices?.[0]?.location || '-';

      const clinician = this.getSelectedClinician();

      const payload = {
        ...Object.fromEntries(new URLSearchParams(window.location.search)), // utm_*, gclid, etc.
        first_name: this.clientInfo.firstName,
        last_name: this.clientInfo.lastName,
        phone: this.phoneNumber,
        email: this.email,
        dob: this.clientInfo.birthDate,
        insurance_name: this.selectedInsuranceName,
        form: 'schedule',
        page_url: window.location.origin + window.location.pathname,
        selectedService: this.selectedService,
        selectedIssue: this.selectedIssueNames,
        gender: this.clientInfo.gender,
        zipcode: this.zip,
        location: locationName,
        date: this.getSlotDateTime(slot).date,
        time: this.getSlotDateTime(slot).time,
        clinician: (clinician?.name || '').split(',')[0].trim()
      };

      return fetch(`${mf_data.site_url}/wp-json/api/v1/crm-lead`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        keepalive: true,
      }).catch((e) => console.error('CRM1 send failed:', e));
    },

    getSelectedClinician() {
      const providerId = this.selectedSlotObject?.providerId;
      if (!providerId) return null;
      return this.groupedSlots.find((g) => g.clinician?.id === providerId)?.clinician || null;
    },

    getSlotDateTime(slot) {
      const d = slot?.start ? new Date(slot.start) : null;
      if (!d || isNaN(d)) return { date: '', isoDate: '', time: '', time12: '' };

      const parts = new Intl.DateTimeFormat('en-GB', {
        timeZone: slot.timezone || 'America/New_York',
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit', hourCycle: 'h23',
      }).formatToParts(d);
      const p = Object.fromEntries(parts.map((x) => [x.type, x.value]));

      const h = Number(p.hour);
      return {
        date: `${p.day}.${p.month}.${p.year}`,
        time: `${h % 12 || 12}:${p.minute} ${h >= 12 ? 'PM' : 'AM'}`,
      };
    },

    getClientFullName() {
      return this.clientInfo.fullName ? this.clientInfo.fullName : this.clientInfo.firstName;
    },

    printSummary() {
      this.printing = true;
      fetch('/wp-json/api/v1/generate-pdf', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          client: this.getClientFullName(),
          appointment_date: this.formatSlotDate(this.selectedSlotObject.start, 'long'),
          appointment_time: this.formatSlotTime(this.selectedSlotObject.start, this.selectedSlotObject.endDt),
          therapist: this.getClinicianName(),
          location: this.formatLocation(this.selectedSlotObject?.location?.address),
          type: this.selectedAppointmentType,
          reminder: this.age < 18,
        })
      })
        .then(res => res.blob())
        .then(blob => {
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'appointment-summary.pdf';
          a.click();
        });

      this.printing = false;

    },

    openDatePicker(refName) {
      const dobInput = this.$refs[refName];
      if (dobInput) {
        dobInput.showPicker?.();
        dobInput.focus();
      }
    },

    selectSingleIssue(issueId) {
      if (this.selectedIssues.includes(issueId)) {
        this.selectedIssues = [];
      } else {
        this.selectedIssues = [issueId];
      }
    },
    validateClientInfo() {

      const errors = {};

      if (!this.clientInfo.firstName) errors.firstName = "First name is required.";
      if (!this.clientInfo.lastName) errors.lastName = "Last name is required.";
      if (!this.email) errors.email = "Email is required.";
      if (!this.phoneNumber) errors.phone = "Phone number is required.";
      // if (!this.clientInfo.insuredRelationship) errors.insuredRelationship = "Relationship to insured is required.";
      // if (!this.clientInfo.address1) errors.address1 = "Address 1 is required.";
      // if (!this.clientInfo.city) errors.city = "City is required.";
      if (!this.stateName) errors.state = "State is required.";
      if (!this.clientInfo.birthDate || this.age < 3) errors.birthDate = "Birth date is required.";
      if (!this.clientInfo.gender) errors.gender = "Gender is required.";

      this.clientInfoErrors = errors;
      return Object.keys(this.clientInfoErrors).length === 0;
    },

    validateInsurerInfo() {

      const errors = {};

      if (!this.insurerInfo.firstName) errors.firstName = "First name is required.";
      if (!this.insurerInfo.lastName) errors.lastName = "Last name is required.";
      if (!this.insurerInfo.gender) errors.gender = "Gender is required.";
      if (!this.insurerInfo.birthDate) errors.birthDate = "Birth date is required.";

      if (!this.insurerInfo.address1) errors.address1 = "Address 1 is required.";
      if (!this.insurerInfo.city) errors.city = "City is required.";
      if (!this.insurerInfo.state) errors.state = "State is required.";
      if (!this.insurerInfo.zip) errors.zip = "Zip code is required.";

      this.insurerInfoErrors = errors;
      return Object.keys(this.insurerInfoErrors).length === 0;
    },

    validatePreview() {

      const errors = {};

      if (!this.minorStayConfirmation && (this.age < 18)) {
        errors.minorStayConfirmation = "Please select the checkbox for minor clients.";
      }

      this.validateEmail();
      if (this.emailError) {
        errors.email = this.emailError;
      }

      if (!this.validatePhoneNumber()) {
        errors.phoneNumber = "Phone number is required.";
      } else if (!this.validatePhoneNumber()) {
        errors.phoneNumber = this.phoneError;
      }

      if (this.userDescription === '') {
        errors.userDescription = "Please provide a reason for seeking therapy.";
      }

      this.previewErrors = errors;
      return Object.keys(this.previewErrors).length === 0;
    },

    setSteps() {
      const selectedCondition = this.stepConditions.defaultCondition;

      this.selectedSteps = selectedCondition
        .map((stepName) => this.allSteps.find((step) => step.name === stepName))
        .filter((step) => step !== undefined);
    },

    selectOption(value, stepName) {
      if (stepName === "Service") {
        this.selectedService = value;
      } else if (stepName === "Therapy Type") {
        this.selectedAppointmentType = value;
      }
      this.updateNextButtonState();
    },

    onIssuesSelect() {
      //allow only single issue
      const selectedId = event.target.value;
      this.selectedIssues = [selectedId];
    },

    async fetchDropDownValues() {
      try {
        const response = await fetch(`${mf_data.assets_url}ehr-dropdown.json`);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const responseBody = await response.json();

        const responseData = responseBody.data;

        this.servicesData = responseData.services;
        this.issuesData = responseData.specialties;
        this.insuranceData = responseData.insurancePlans;

        let parsed;
        try {
          parsed = JSON.parse(responseBody);
        } catch (e) {
          parsed = responseBody;
        }
        if (typeof parsed !== "object" || !parsed.data) {
          throw new Error("Invalid response structure");
        }
      } catch (error) {
        this.showRequestError();
      }
    },

    async fetchLocationValues() {
      try {
        const response = await fetch(`${mf_data.site_url}/wp-json/api/v1/locations`);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const body = await response.json();
        const locations = body?.data?.locations;
        if (!Array.isArray(locations)) throw new Error("Invalid response structure");

        this.locationsData = locations;
      } catch (error) {
        console.error("fetchLocationValues failed:", error);
        this.showRequestError();
      }
    },

    getActiveSortVersion() {
      const hour = new Date().getHours();
      return hour % 2 === 0 ? 'v2' : 'v1';
    },

    getFirstAvailability(group) {
      return group.slots?.[0]?.startDt || group.slots?.[0]?.start;
    },

    sortCliniciansByBoostedAvailability(clinicians) {
      return [...clinicians].sort((a, b) => {
        const aBoosted = this.isBoosted(a.clinician.id) ? 1 : 0;
        const bBoosted = this.isBoosted(b.clinician.id) ? 1 : 0;

        if (aBoosted !== bBoosted) return bBoosted - aBoosted; // boosted first

        return new Date(this.getFirstAvailability(a)) - new Date(this.getFirstAvailability(b));
      });
    },

    async logSearchEntry() {
      //if (this.groupedSlots.length !== 0) return;

      const logData = {
          service_requested: (this.serviceData.find(s => s.id === this.selectedService) || {}).name,
          issue: this.selectedIssueNames,
          client_age: this.age,
          gender: this.clientInfo.gender,
          zip_code: this.zip,
          insurance_provider: this.selectedInsuranceName,
          appointment_type: this.selectedAppointmentType,
          providers_count: this.groupedSlots.length,
          slots_exposed_count: this.groupedSlots.reduce((sum, item) => sum + item.slots.length, 0)
        };

        this.trackFormProgress(logData);
      try {
      } catch (error) {
        console.error("Failed to store log:", error);
        this.showRequestError();
      }
    },

    trackFormProgress(extraData = {}) {
      const payload = {
        session_id: this.sessionId,
        max_step: this.currentStepIndex + 1,
        ...extraData
      };

      (async () => {
        try {
          await fetch('/wp-json/api/v1/form-progress', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
          });
        } catch (error) {
          console.error("Progress tracking error:", error);
        }
      })();
    },

    extractClinicianSlugsById(sortedClinicians) {
      const slugIdMap = {};

      sortedClinicians.forEach((group) => {
        const clinician = group.clinician;
        const bioLink = clinician.bioLink;

        try {
          const url = new URL(bioLink);
          const pathParts = url.pathname.split("/").filter(Boolean);
          const slug = pathParts[pathParts.length - 1];

          if (slug && clinician.id) {
            slugIdMap[clinician.id] = slug;
          }
        } catch (e) {
          console.warn(`Invalid bioLink: ${bioLink}`, e);
        }
      });

      return slugIdMap;
    },

    getProviderIds(slots) {
      return [...new Set(slots.map((slot) => slot.healthieProviderId))];
    },

    extractClinicianZipCodesById(sortedClinicians) {
      const zipCodeMap = {};

      sortedClinicians.forEach((group) => {
        const clinician = group.clinician;
        const slot = group.slots?.[0];
        let zipCode = slot?.location?.address?.zipCode;

        if (zipCode && typeof zipCode === 'string') {
          zipCode = zipCode.split("-")[0];
        }

        if (clinician?.id) {
          zipCodeMap[clinician.id] = zipCode || "default";
        }
      });

      return zipCodeMap;
    },

    sortCliniciansByDistance(clinicians, zipCodeMap, zipLocations) {

      const siteUrl = mf_data.site_url;

      const result = clinicians
        .map((group) => {
          const clinicianId = group.clinician?.id;
          const zip = zipCodeMap[clinicianId];
          const loc = zipLocations[zip];


          if (!zip || !loc) {
            return { group, distance: Infinity };
          }

          const distance = this.haversineDistance(this.lat, this.lng, loc.lat, loc.lon);

          const locationName = this.getClinicianLocation(group.slots);
          const slug = locationName
            ? locationName.toLowerCase().replace(/\s+/g, '-')
            : '';

          return {
            ...group,
            clinician: {
              ...group.clinician,
              distance,
              locationUrl: slug ? `${siteUrl}/locations/${slug}/` : null
            }
          };
        })
        .sort((a, b) => a.clinician.distance - b.clinician.distance);

      return result;
    },

    getClosestOfficeLocationZipCode() {
      let closestZip = null;
      let shortestDistance = Infinity;

      for (const [zip, coords] of Object.entries(this.zipLocations)) {
        const distance = this.haversineDistance(this.lat, this.lng, coords.lat, coords.lon);

        if (distance < shortestDistance) {
          shortestDistance = distance;
          closestZip = zip;

        }
      }
    },

    getThumbnail(thumbnail_url) {
      return thumbnail_url || `${mf_data.site_url}/wp-content/uploads/2024/08/mindfully-lotus-logo-1.webp`;
    },

    getDescription(id) {
      const text = this.clinicianThumbnails[id]?.description || "";
      const profileUrl = this.getProfileUrl(id);

      if (!text || text.length === 0) return text;

      return (
        text +
        ` <a href="${profileUrl}" target="_blank"> Read more</a>`
      );
    },

    getProfileUrl(url) {
      return url || `#`;
    },

    async handleZipChange() {
      this.zip = this.zip.replace(/\D/g, "").slice(0, 5);
      await this.getStateFromZip(this.zip);
      await this.fetchInsurance();
    },

    getSpecialities(id) {
      return '';
      const thumb = this.clinicianThumbnails[id];
      const specialities = thumb?.specialities || [];

      return specialities.map(spec => {
        let clean = spec.replace(/&amp;/g, "&").replace(/^"|"$/g, "");

        if (clean.length > 33) {
          clean = clean.substring(0, 31) + "...";
        }

        return clean;
      });
    },

    getPayRate(id) {
      const thumb = this.clinicianThumbnails[id];

      return thumb?.private_pay_rate || 0;
    },

    async handleZipChangeForInsurer() {
      this.insurerInfo.zip = this.insurerInfo.zip.replace(/\D/g, "").slice(0, 5);
    },

    async getStateFromZip(zip) {
      if (!zip || zip.length < 5) return null;

      this.zip_code_msg = 'Checking zip code...';

      if (!window.google || !window.google.maps) {
        console.error("Google Maps JS SDK not loaded");
        return null;
      }

      const requestToken = Date.now();
      this.latestZipRequestToken = requestToken;

      const geocoder = new window.google.maps.Geocoder();
      this.stateCode = null;
      return new Promise((resolve, reject) => {
        geocoder.geocode(
          {
            componentRestrictions: {
              country: "US",
              postalCode: zip,
            },
          },
          (results, status) => {
            if (this.latestZipRequestToken !== requestToken) {
              console.log("Stale response ignored for ZIP:", zip);
              return;
            }

            console.log("Geocode results:", results);
            this.invalidZipCode = false;
            if (status === "OK") {
              const addressComponents = results[0].address_components;
              const stateComponent = addressComponents.find((component) =>
                component.types.includes("administrative_area_level_1")
              );
              const stateCode = stateComponent.short_name;
              const stateName = stateComponent.long_name;
              this.stateCode = stateCode;
              this.stateName = stateName;

              const location = results[0].geometry.location;
              this.lat = location.lat();
              this.lng = location.lng();

              resolve(stateCode);

            } else {
              console.error("Geocode failed: " + status);
              this.invalidZipCode = true;
              this.stateCode = null;
              this.stateID = null;
              this.lat = null;
              this.lng = null;
              //this.insuranceData = [];
              resolve(null);
              this.zip_code_msg = 'Sorry, we currently do not provide services in your area.';
            }
          }
        );
      });
    },

    async fetchInsurance() {
      if (this.zip.length != 5) {
        return;
      }

      try {
        this.zip_code_msg = 'Fetching insurances...';

        const excludedIds = [26, 27, 29, 113];

        const filtered = this.insuranceData.filter(
            item => !excludedIds.includes(item.id)
        );

        const customInsurance = {
            id: 9999,
            name: "Uninsured/No Insurance/Private Pay",
        };

        this.insuranceData = [...filtered, customInsurance];

        this.insuranceDataSimple = this.insuranceData.map(item => item.payerName);
        this.zip_code_msg = '';

      } catch (error) {
        console.error("Error fetching insurances:", error);
        this.showRequestError?.();
      }
    },

    getDemographicTag(age) {
      const gender = (this.clientInfo.gender || '').toLowerCase();
      if (!['male', 'female'].includes(gender)) return null;

      const bands = [
        { min: 0,  max: 3,   label: '0-3' },
        { min: 4,  max: 8,   label: '4-8' },
        { min: 9,  max: 12,  label: '9-12' },
        { min: 13, max: 17,  label: '13-17' },
        { min: 18, max: 25,  label: '18-25' },
        { min: 26, max: 64,  label: '26-64' },
        { min: 65, max: 200, label: '65-older' },
      ];

      const band = bands.find((b) => age >= b.min && age <= b.max);
      return band ? `${gender}-${band.label}` : null;
    },

    calculateAge() {
      const birth = new Date(this.clientInfo.birthDate);
      const today = new Date();
      let age = today.getFullYear() - birth.getFullYear();
      const m = today.getMonth() - birth.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
        age--;
      }
      return age;
    },

    async fetchAvailableSlots() {
      this.groupedSlots = [];
      this.cliniciansLoaded = false
      this.loading = true;

      const isInOffice = this.selectedAppointmentType === 'in_office';

      if (isInOffice && !this.nearbyLocationIds.length) {
        this.noNearbyOffices = true;
        this.cliniciansLoaded = true;
        this.loading = false;
        return; // don't call the API at all
      }
      this.noNearbyOffices = false;

      try {
        const addBusinessDays = (date, days) => {
          const result = new Date(date);
          let added = 0;
          while (added < days) {
            result.setDate(result.getDate() + 1);
            const day = result.getDay(); // 0 = Sunday, 6 = Saturday
            if (day !== 0 && day !== 6) added++;
          }
          return result;
        };

        const BUFFER_BUSINESS_DAYS = 2; // days reserved for validating the request with providers

        // Skip the buffer, then start on the next business day
        const startDate = addBusinessDays(new Date(), BUFFER_BUSINESS_DAYS + 1);

        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + 13);
        this.searchRangeStart = this.getFormattedDate(startDate, "00:00:00");

        const demographicTag = this.getDemographicTag(this.age);
        const payload = {
          organization: window.mf_data.orgId,
          insurance: [this.selectedInsuranceName],
          // insurance: ["Self-Pay"],
          visit_type: this.selectedAppointmentType,
          // telehealth: true,
          service_type: (this.selectedService === "ADOLESCENT"
            ? "INDIVIDUAL"
            : this.selectedService
          ).toLowerCase(),
          // service_type: 'individual',
          range_start: this.searchRangeStart,
          range_end: this.getFormattedDate(endDate, "23:59:59"),
          specialties: this.selectedIssues,
          // specialties: ["specialty-i-need-to-talk"],
          ...(isInOffice && { locations: this.nearbyLocationIds }),
          ...(demographicTag && { demographics: [demographicTag] }),
        };

        const response = await fetch(
          `${mf_data.site_url}/wp-json/api/v1/proxy`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              key: "fetch_slots",
              payload: payload,
            }),
          }
        );
        this.groupedSlots = await response.json();

        this.groupedSlots = this.addDistances(this.groupedSlots);
        this.groupedSlots = this.sortByEarliestOpening(this.groupedSlots);
        this.groupedSlots = this.sortByClosestOffice(this.groupedSlots);
        this.groupedSlots = this.sortByBoosted(this.groupedSlots);
        this.cliniciansLoaded = true;
        this.loading = false;

        setTimeout(() => {
          document.dispatchEvent(new Event('api-data-loaded'));
        }, 100);

        await this.logSearchEntry();

      } catch (error) {
        console.error("Error fetching slots:", error);
        this.showRequestError?.();
        this.loading = false;
      }
    },
    // Sort slots by start time (earliest first). Does not mutate the input.
    sortSlotsByTime(slots) {
      return [...slots].sort(
        (a, b) => new Date(a.start) - new Date(b.start)
      );
    },

    // Group slots by providerId. Works with sorted or unsorted input.
    groupSlotsByProvider(slots) {
      const providerMap = new Map();

      slots.forEach((slot) => {
        if (!providerMap.has(slot.providerId)) {
          providerMap.set(slot.providerId, {
            clinician: {
              id: slot.providerId,
              healthieProviderId: slot.healthieProviderId,
              organizationId: slot.organizationId,
            },
            slots: [],
          });
        }

        providerMap.get(slot.providerId).slots.push(slot);
      });

      return Array.from(providerMap.values());
    },

    showTeletherapyOptions() {
      this.selectedAppointmentType = 'telehealth';

      this.fetchAvailableSlots();

      this.isToggledAppointmentType = true;

    },

    handleSlotSelection(slot) {
      this.selectedSlotObject = slot;

      this.currentStepIndex++;
      this.showWarning = false;
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });

      this.trackFormProgress();
    },
    getFormattedDate(date, time = "00:00:00") {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },

    sortSlotsByClinician(slots) {
      const clinicianSlotsMap = new Map();

      slots.forEach((slot) => {
        const clinicianId = slot.clinicians[0].id;

        if (!clinicianSlotsMap.has(clinicianId)) {
          clinicianSlotsMap.set(clinicianId, {
            clinician: slot.clinicians[0],
            slots: [],
          });
        }

        clinicianSlotsMap.get(clinicianId).slots.push(slot);
      });

      const sortedClinicians = Array.from(clinicianSlotsMap.values()).sort(
        (a, b) => a.clinician.id - b.clinician.id
      );

      sortedClinicians.forEach((clinicianGroup) => {
        clinicianGroup.slots.sort((a, b) => a.start - b.start);
      });

      return sortedClinicians;
    },

    removeNotAcceptingSlots(allSlots) {
      return allSlots.filter(slot => slot.acceptingNewPatientFlag === true);
    },

    showMoreSlots(clinicianId) {
      this.visibleSlotCounts[clinicianId] += 6;
    },

    toggleAvailability(clinicianId) {
      document.dispatchEvent(new Event('user-interacted-on-therapists-page'));
      if (!this.visibleSlotCounts[clinicianId]) {
        this.visibleSlotCounts[clinicianId] = 6;
      }

      this.expandedClinicians = {
        ...this.expandedClinicians,
        [clinicianId]: !this.expandedClinicians[clinicianId],
      };
    },

    isExpanded(clinicianId) {
      return !!this.expandedClinicians[clinicianId];
    },

    formatSlotDate(slot) {
      if (!slot?.start) return '';
      return new Intl.DateTimeFormat('en-US', {
        weekday: 'short', month: 'short', day: 'numeric',
        timeZone: slot.timezone || 'America/New_York',
      }).format(new Date(slot.start));
    },

    formatSlotTime(slot) {
      if (!slot?.start) return '';
      const start = new Date(slot.start);
      const end = new Date(start.getTime() + (slot.lengthMinutes || 60) * 60000);
      const fmt = new Intl.DateTimeFormat('en-US', {
        hour: 'numeric', minute: '2-digit',
        timeZone: slot.timezone || 'America/New_York',
      });
      return `${fmt.format(start)} - ${fmt.format(end)}`;
    },

    formatLocation(address) {
      if (!address || typeof address !== 'object') {
        return '---';
      }

      let parts = [];
      // if (address.address1) parts.push(address.address1);
      // if (address.address2) parts.push(address.address2);
      if (address.city) parts.push(address.city);
      if (address.state) parts.push(address.state);

      return parts.join(", ");
    },

    getClinicianName() {
      if (this.selectedSlotObject?.clinicians?.length > 0) {
        const clinician = this.selectedSlotObject.clinicians[0];
        return `${clinician.firstName} ${clinician.lastName}`;
      }

      return "";
    },

    getClinicianLocation(slots) {
      return slots[0].location?.name;
    },

    getClinicianProfileUrl() {
      if (this.selectedSlotObject?.clinicians?.length > 0) {
        const clinician = this.selectedSlotObject.clinicians[0];
        return `${clinician.bioLink}`;
      }

      return "";
    },

    validateStep() {
      this.showWarning = false;
      this.warningMsg = "";

      const validations = {
        Service: {
          valid: () => this.selectedService !== null,
          message: "Please select a service before continuing.",
        },
        Issues: {
          valid: () => this.selectedIssues.length > 0,
          message: "Please select at least one issue you're facing.",
        },
        "Basic Info": {
          valid: () => this.clientInfo.birthDate != '' && this.age >= 3 && this.clientInfo.gender != '' && this.zip != '' && this.zip_code_msg == '' && this.selectedInsuranceName != '',
          message: "Please fill in all the fields."
        },
        "Therapy Type": {
          valid: () => this.selectedAppointmentType != '',
          message: "Please select one option."
        },
        "Client Info": {
          valid: () => this.validateClientInfo(),
          message: () => Object.values(this.clientInfoErrors)
        },
        "Insurer Info": {
          valid: () => this.validateInsurerInfo(),
          message: () => Object.values(this.insurerInfoErrors)
        },
        Clinicians: {
          valid: () => this.selectedSlotId !== 0,
          message: "Please select an appointment slot with a clinician.",
        },
        Preview: {
          valid: () => this.validatePreview(),
          message: () => Object.values(this.previewErrors)
        },
      };

      const validation = validations[this.currentStepName];
      const valid = validation.valid();

      if (!valid) {
        this.warningMsg = typeof validation.message === 'function'
          ? validation.message()
          : validation.message;
        this.showWarning = true;
        return false;
      }

      return true;
    },

    validatePhoneNumber() {
      const cleaned = this.phoneNumber.replace(/\D/g, "");
      if (cleaned.length !== 10) {
        this.phoneError = "Phone number must be exactly 10 digits.";
        return false;
      }
      this.phoneError = "";
      return true;
    },

    filterDigits(e) {
      this.phoneNumber = this.phoneNumber.replace(/\D/g, "").slice(0, 10);
    },

    validateEmail() {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!this.email) {
        this.emailError = "Email is required.";
      } else if (!emailPattern.test(this.email)) {
        this.emailError = "Please enter a valid email address.";
      } else {
        this.emailError = "";
      }
    },

    updateNextButtonState() {
      // this.nextBtnDisabled = !this.validateStep();
    },

    async nextStep() {
      const isLastStep = this.currentStepIndex >= this.selectedSteps.length - 2;
      if (this.validateStep()) {
        if (this.currentStepIndex < this.selectedSteps.length - 2) {
          this.currentStepIndex++;

          if (this.currentStepName == 'Issues' && (this.selectedService === 'COUPLE' || this.selectedService === 'FAMILY' ) ) {
            this.selectedIssues = ['specialty-i-need-to-talk'];
            this.currentStepIndex++;
          }

          if (this.currentStepName == 'Basic Info') {
            await this.fetchLocationValues();
          }

          this.prevBtnDisabled = false;
          this.updateNextButtonState();

          this.trackFormProgress();
        } else {
          //Submit Form
          this.sendDetailsToMyTherapyFlow()
          this.trackFormProgress();
          this.redirectToCrm();
        }
      }

      if (!isLastStep || this.showWarning) {
        setTimeout(() => {
          try {
            window.scrollTo({ top: 0, behavior: 'smooth' });
          } catch (e) {
            window.scrollTo(0, 0);
          }
        }, 10);
      }
    },

    prevStep() {
      if (!this.prevBtnDisabled) {
        this.currentStepIndex--;
        this.updateNextButtonState();

        this.warningMsg = '';
        this.showWarning = false;
      }
    },

    markFiltersAsChanged() {
      this.filtersChanged = true;
    },

    showRequestError() {
      this.showWarning = true;

      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    },

    removeLoader() {
      const formDiv = document.getElementById('app');
      if (formDiv) {
        formDiv.style.display = null;
      }

      const loader = document.getElementById('loader');
      if (loader) {
        loader.style.display = 'none';
      }
    },

    logApiActivity(endpoint, requestPayload, responsePayload) {
      const logPayload = {
        endpoint,
        payload: requestPayload,
        response: responsePayload
      };

      var url = mf_data.site_url + "/wp-json/api/v1/log";

      try {
        const res = fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(logPayload)
        });

      } catch (err) {
        console.error('Error logging API activity:', err);
      }
    },

    redirectToCrm() {
      const slot = this.selectedSlotObject;

      const offices = slot?.offices || [];
      const nearestOffice = [...offices].sort(
        (a, b) =>
          (this.distanceByLocationId[String(a.locationId)] ?? Infinity) -
          (this.distanceByLocationId[String(b.locationId)] ?? Infinity)
      )[0];

      const isTelehealth = this.selectedAppointmentType === 'telehealth';
      const apptLocId = isTelehealth ? '' : nearestOffice?.locationId;

      const startRange = slot?.start
        ? new Intl.DateTimeFormat('en-CA', {
            timeZone: slot.timezone || 'America/New_York',
            year: 'numeric', month: '2-digit', day: '2-digit',
          }).format(new Date(slot.start)).replace(/-/g, '')
        : '';

      const contactType =
        this.selectedAppointmentType === 'telehealth'
          ? slot.telehealthContactType
          : slot.inOfficeContactType;
      const data = {
        first_name: this.clientInfo.firstName,
        last_name: this.clientInfo.lastName,
        email: this.email,
        phone_number: this.phoneNumber,
        provider_id: slot.healthieProviderId,
        appt_type_id: slot.appointmentTypeId,
        contact_type: contactType,
        organizatino_id: window.mf_data.orgId,
        appt_loc_id: apptLocId,
        start_range: startRange,
        primary_color: '6c37ff',
        read_only: true,
      };

      const params = new URLSearchParams();
      Object.entries(data).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== "") {
          params.set(key, value);
        }
      });

      const url = `https://secure.gethealthie.com/appointments/embed_appt?${params.toString()}`;
      window.open(url, "_blank", "noopener");
    },
  },

  mounted() {
    this.fetchDropDownValues();
    this.setSteps();
    this.removeLoader();
    this.trackFormProgress();
  },
});

app.use(PrimeVue.Config, {
  theme: {
    preset: PrimeUIX.Themes.Aura
  }
});

app.component('p-search', PrimeVue.Select);

app.mount('#app');


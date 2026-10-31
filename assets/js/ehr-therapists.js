const APPT_TYPE_MAP = {
  '880': 530675, // Adolescent & Teen Therapy
  '892': 531075, // Child Therapy         → same appt type as above?
  '624': 530674, // Counseling
  '879': 540746, // Couples Counseling    → same as Counseling?
  '894': 540747, // Family Counseling
  '872': 540747, // Group Therapy         → same as Family?
};

function getSelectedApptTypeId() {
  const select = document.querySelector('select[name="mf-cares-services"]');
  return select && select.value ? APPT_TYPE_MAP[select.value] || null : null;
}

function updateBookingButtons() {
  const apptTypeId = getSelectedApptTypeId();

  document.querySelectorAll('.book-btn').forEach(wrapper => {
    const link = wrapper.querySelector('a.elementor-button');
    if (!link) return;

    const href = link.getAttribute('href');

    if (!href) {
      wrapper.style.display = 'none';
      return;
    }

    wrapper.style.display = '';

    const providerId = href.replace(/\D/g, '');
    if (!providerId) return;

    let url = `https://secure.mindfullyworks.com/appointments/embed_appt?provider_id=${providerId}`;
    if (apptTypeId) url += `&appt_type_ids=[${apptTypeId}]`;
    url += `&primary_color=411AA8&insurance_step=true&organization_id=128872`;

    link.setAttribute('href', url);
  });
}

document.addEventListener('DOMContentLoaded', updateBookingButtons);
jQuery(document).on('jet-filter-content-rendered', updateBookingButtons);

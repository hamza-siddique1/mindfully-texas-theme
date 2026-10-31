<script id="step6-popup-trigger">
(function() {
  'use strict';

  const step6Trigger = {
    step6StartTime: null,
    hasTriggered: false,
    popupId: 71816,
    userHasInteracted: false,
    popupTimeout: null,
    popupDelay: 35000,

    init() {
      document.addEventListener('api-data-loaded', () => {
        this.startPopupTimer();
      });

      document.addEventListener('user-interacted-on-therapists-page', () => {
        console.log('User interacted - stopping popup timer');
        this.stopPopupTimer();
      });

      document.addEventListener('vue-popup-step-deactivated', () => {
        this.stopPopupTimer();
      });

      this.attachExitIntent();
    },

    startPopupTimer() {
      console.log('Starting 20 second popup timer after API response');
      this.popupStartTime = Date.now();
      this.userHasInteracted = false;

      if (this.popupTimeout) {
        clearTimeout(this.popupTimeout);
      }

      this.popupTimeout = setTimeout(() => {
        if (!this.userHasInteracted && !this.hasTriggered) {
          console.log('20 seconds passed with no interaction - opening popup');
          this.openPopup('inactivity-timeout');
        }
      }, this.popupDelay);
    },

    stopPopupTimer() {
      if (this.popupTimeout) {
        clearTimeout(this.popupTimeout);
        this.popupTimeout = null;
      }
      this.popupStartTime = null;
    },

    attachExitIntent() {
      document.addEventListener('mousemove', (e) => {
        if (!this.popupStartTime || this.hasTriggered) return;

        if (e.clientY < window.innerHeight * 0.05) {
          console.log('Exit intent - opening popup');
          this.openPopup('exit-intent');
        }
      });

      window.addEventListener('beforeunload', (e) => {
        if (this.popupStartTime && !this.hasTriggered) {
          this.openPopup('tab-close');
          e.preventDefault();
        }
      });
    },

    openPopup(trigger) {
      this.hasTriggered = true;
      this.stopPopupTimer();

      if (window.elementorProFrontend?.modules?.popup) {
        window.elementorProFrontend.modules.popup.showPopup({ id: this.popupId });
        console.log('Popup opened by:', trigger);
      }
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => step6Trigger.init());
  } else {
    step6Trigger.init();
  }
})();
</script>

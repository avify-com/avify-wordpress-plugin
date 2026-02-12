(function () {
  let avfBillingPhone = document.querySelector('#avf_billing_tel');
  let wooBillingPhone = document.querySelector('#billing_phone');
  if (!avfBillingPhone) return;

  let initialCountry = (window.avfPhoneIntl && window.avfPhoneIntl.country)
    ? window.avfPhoneIntl.country.toLowerCase()
    : 'cr';

  let iti = window.intlTelInput(avfBillingPhone, {
    initialCountry: initialCountry,
    nationalMode: true,
    autoPlaceholder: 'aggressive',
    formatAsYouType: true,
    countrySearch: true,
    showFlags: true,
    useFullscreenPopup: false
  });

  function setFullNumber() {
    if (avfBillingPhone.value) {
      wooBillingPhone.value = `+${iti.getSelectedCountryData().dialCode}${avfBillingPhone.value}`;
    }
  }

  let secondStepBtn = document.querySelector('#avf_to_second_step_button');
  let checkoutBtn = document.querySelector('#avf_checkout_button');
  if (secondStepBtn) {
    secondStepBtn.addEventListener('click', setFullNumber, true);
  }
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', setFullNumber, true);
  }
  // Also intercept form submit as a safety net
  let form = avfBillingPhone.closest('form');
  if (form) {
    form.addEventListener('submit', setFullNumber, true);
  }
})();

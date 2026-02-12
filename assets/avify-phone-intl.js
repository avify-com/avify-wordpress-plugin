(function () {
  var input = document.querySelector('#avf_billing_tel');
  if (!input) return;

  var initialCountry = (window.avfPhoneIntl && window.avfPhoneIntl.country)
    ? window.avfPhoneIntl.country.toLowerCase()
    : 'cr';

  var iti = window.intlTelInput(input, {
    initialCountry: initialCountry,
    nationalMode: true,
    autoPlaceholder: 'aggressive',
    formatAsYouType: true,
    countrySearch: true,
    showFlags: true,
    useFullscreenPopup: false
  });

  // Before the minified JS reads the value, we set the full international number.
  // Using capturing phase (3rd arg = true) so our handler runs BEFORE the minified
  // JS event listeners that are on the bubbling phase.
  function setFullNumber() {
    var fullNumber = iti.getNumber();
    if (fullNumber) {
      input.value = fullNumber;
    }
  }

  var secondStepBtn = document.querySelector('#avf_to_second_step_button');
  var checkoutBtn = document.querySelector('#avf_checkout_button');

  if (secondStepBtn) {
    secondStepBtn.addEventListener('click', setFullNumber, true);
  }
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', setFullNumber, true);
  }

  // Also intercept form submit as a safety net
  var form = input.closest('form');
  if (form) {
    form.addEventListener('submit', setFullNumber, true);
  }
})();

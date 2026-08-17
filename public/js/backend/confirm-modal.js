document.addEventListener('DOMContentLoaded', function () {
  var modalEl = document.getElementById('lmsConfirmModal');
  if (!modalEl || !window.bootstrap) {
    return;
  }

  var titleEl = document.getElementById('lmsConfirmTitle');
  var bodyEl = document.getElementById('lmsConfirmBody');
  var submitEl = document.getElementById('lmsConfirmSubmit');
  var modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
  var pendingForm = null;

  function confirmSource(form, submitter) {
    if (submitter && submitter.hasAttribute && submitter.hasAttribute('data-confirm-title')) {
      return submitter;
    }

    if (form && form.hasAttribute && form.hasAttribute('data-confirm-title')) {
      return form;
    }

    return null;
  }

  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement) || form.getAttribute('data-confirm-skip') === '1') {
      return;
    }

    var source = confirmSource(form, event.submitter);
    if (!source) {
      return;
    }

    event.preventDefault();
    pendingForm = form;
    titleEl.textContent = source.getAttribute('data-confirm-title') || 'Please confirm';
    bodyEl.textContent = source.getAttribute('data-confirm-body') || 'Are you sure?';
    submitEl.textContent = source.getAttribute('data-confirm-submit') || 'Confirm';
    modal.show();
  });

  submitEl.addEventListener('click', function () {
    var form = pendingForm;
    if (!form) {
      return;
    }

    form.setAttribute('data-confirm-skip', '1');
    pendingForm = null;
    modal.hide();
    form.submit();
  });

  modalEl.addEventListener('hidden.bs.modal', function () {
    pendingForm = null;
  });
});

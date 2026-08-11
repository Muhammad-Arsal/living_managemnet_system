document.addEventListener('DOMContentLoaded', function () {
  var alerts = document.querySelectorAll('.alert.alert-dismissible');
  var delay = 4000;

  alerts.forEach(function (alertEl) {
    window.setTimeout(function () {
      if (!alertEl || !alertEl.isConnected) {
        return;
      }

      if (window.bootstrap && window.bootstrap.Alert) {
        window.bootstrap.Alert.getOrCreateInstance(alertEl).close();
        return;
      }

      alertEl.classList.remove('show');
      window.setTimeout(function () {
        alertEl.remove();
      }, 150);
    }, delay);
  });
});

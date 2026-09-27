import 'bootstrap';
import * as bootstrap from 'bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const toastElement = document.getElementById('storeToast');
    if (toastElement) {
      const toast = new bootstrap.Toast(toastElement);

      if (toast) {
        toast.show();
      }
    }
});
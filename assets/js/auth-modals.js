(function () {
  'use strict';

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove('in');
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    var backdrop = document.querySelector('.auth-modal-backdrop');
    if (backdrop) backdrop.parentNode.removeChild(backdrop);
  }

  function openModal(id) {
    var modal = document.getElementById(id);
    if (!modal) return;

    var current = document.querySelector('.modal.in');
    if (current && current !== modal) closeModal(current);

    modal.style.display = 'block';
    modal.classList.add('in');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');

    if (!document.querySelector('.auth-modal-backdrop')) {
      var backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade in auth-modal-backdrop';
      backdrop.addEventListener('click', function () { closeModal(modal); });
      document.body.appendChild(backdrop);
    }

    var firstField = modal.querySelector('input:not([type="hidden"])');
    if (firstField) firstField.focus();
  }

  document.addEventListener('click', function (event) {
    var opener = event.target.closest('[data-auth-open]');
    if (opener) {
      event.preventDefault();
      openModal(opener.getAttribute('data-auth-open'));
      return;
    }

    if (event.target.closest('[data-auth-close]')) {
      event.preventDefault();
      closeModal(event.target.closest('.modal'));
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeModal(document.querySelector('.modal.in'));
  });

  window.openAuthModal = openModal;
}());

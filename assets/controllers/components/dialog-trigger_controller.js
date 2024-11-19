import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    this.initModalTriggerListener();
  }

  /**
   * Dispatch 'dialog.trigger' event when the trigger element is clicked
   */
  initModalTriggerListener = () => {
    this.element.addEventListener('click', () => {
      this.element.dispatchEvent(new Event('dialog.trigger', { bubbles: true }));
      document.getElementById(this.element.getAttribute('data-dialog-id')).showModal();
    });
  }
}

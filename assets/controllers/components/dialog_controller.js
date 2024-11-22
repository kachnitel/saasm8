import { Controller } from '@hotwired/stimulus';

/**
 * Use mutation observer to listen for changes to the open attribute
 * and dispatch 'dialog.open' or 'dialog.close' event accordingly
 */
export default class extends Controller {
  connect() {
    this.initDialogListener();
  }

  /**
   * use mutation observer to listen for changes to the open attribute
   * https://developer.mozilla.org/en-US/docs/Web/API/MutationObserver
   */
  initDialogListener = () => {
    const observer = new MutationObserver((mutationsList, observer) => {
      for (const mutation of mutationsList) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'open') {
          const isOpen = mutation.target.hasAttribute('open');
          const eventName = isOpen ? 'dialog.open' : 'dialog.close';
          const event = new CustomEvent(eventName, { detail: { target: mutation.target.id }, bubbles: true });

          this.element.dispatchEvent(event);
        }
      }
    });

    observer.observe(this.element, { attributes: true });
  }
}

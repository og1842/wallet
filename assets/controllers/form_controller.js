import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        // this.element.addEventListener('click', function (ev) {
        //     ev.currentTarget.value = 'Processing...';
        //     ev.currentTarget.setAttribute('disabled', 'disabled');
        //
        //     ev.currentTarget.closest('form').submit();
        // })

        this.element.closest('form').addEventListener('submit', function (ev) {
            let btn = ev.currentTarget.querySelector('input[type=submit]');
            btn.value = 'Processing...';
            btn.setAttribute('disabled', 'disabled');

            // ev.currentTarget.find('input[type=submit]').submit();
        })
    }
}
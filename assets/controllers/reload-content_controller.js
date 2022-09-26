import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content'];

    static values = {
        url: String,
    }

    async refreshContent(event) {
        const target = this.hasContentTarget ? this.contentTarget : this.element;

        const response = await fetch(this.urlValue);
        target.innerHTML = (await response.text()).replaceAll('?reload=1', '?');
    }
}

import {Controller} from '@hotwired/stimulus';
import TomSelect from 'tom-select';

export default class extends Controller {

    connect() {
        new TomSelect(this.element, {
            plugins: {
                remove_button: {
                    title: 'Remove this item',
                    label: '&times;'
                }
            },
            create: true,
            maxItems: 100,
        });
    }

}
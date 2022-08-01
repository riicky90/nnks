import {Controller} from '@hotwired/stimulus';
import {default as axios} from "axios";

export default class extends Controller {
    connect() {

    }

    toggle(event) {
        axios.post(event.params.url, {
            setting: event.params.setting,
            value: event.target.checked
        }).then((result) => {
        })
    }

    updateValue(event) {
        axios.post(event.params.url, {
            setting: event.params.setting,
            value: event.target.value
        }).then((result) => {
        })
    }
}
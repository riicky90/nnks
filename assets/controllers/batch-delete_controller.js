import {Controller} from "@hotwired/stimulus";
import axios from "axios";

export default class extends Controller {
    connect() {

    }

    delete(e) {
        const checked = e.params.checkbox;
        const url = e.params.url;
        forEach(check in checked)
        {
            axios.post(url + '/'+check.value)
                .then((result) => {
                    consol.log(result);
                });
        }
    }
}
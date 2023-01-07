import {Controller} from '@hotwired/stimulus';
import Swal from "sweetalert2";
import {useDispatch} from "stimulus-use";

export default class extends Controller {

    static values = {
        title: String,
        text: String,
        icon: String,
        confirmationButtonText: String,
        submitAsync: Boolean,
        refreshAfterSubmit: Boolean,
    }

    connect() {
        useDispatch(this);
    }

    onSubmit(event) {
        event.preventDefault();
        Swal.fire({
            title: this.titleValue || null,
            text: this.textValue || null,
            icon: this.iconValue || "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: this.confirmationButtonTextValue || 'Ja',
            showLoaderOnConfirm: true,
            cancelButtonText: 'Nee',
            preConfirm: () => {
                return this.submitForm();
            }
        })
    }

    async submitForm() {
        if (!this.submitAsyncValue) {
            this.element.submit();
            return;
        }

        const response = await fetch(this.element.action, {
            method: this.element.method,
            body: new URLSearchParams(new FormData(this.element))
        }).then((result) => {
            if (!result.ok) {
                Swal.fire({
                    title: this.titleValue + " mislukt",
                    text: 'verwijderen niet mogelijk',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
            } else {
                if (this.refreshAfterSubmitValue) {
                    window.location.reload();
                }
            }
        });

        this.dispatch('async:submitted', {
            response
        });
    }
}
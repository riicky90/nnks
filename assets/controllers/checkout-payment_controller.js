import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

export default class CheckoutPayment_controller extends Controller {

    static values = {
        regfee: Number,
    }

    connect() {
        console.log('CheckoutPayment_controller connected');
        this.teamField = document.getElementById('registrations_Team');
        this.costOverview = document.getElementById('cost-overview');
        this.dancersField = document.getElementById('registrations_number_dancers');
        this.dancersAutocomplete = document.getElementById('registrations_Dancers_autocomplete');
        this.payButton = document.getElementById('pay-button');
        this.saveButton = document.getElementById('save-button');
        this.saveButtonLast = document.getElementById('save-button-last');
        this.totalDancers = document.getElementById('total_dancers');
        this.grandTotal = document.getElementById('grand_total');
        this.leftTotal = document.getElementById('left_total');
        this.paidTotal = document.getElementById('paid_total');
        this.registrationForm = document.getElementsByName('registrations')[0];
        this.dancersFieldCalc = document.getElementById('registrations_Dancers');

        //make dancersfieldcalc select and label invisible
        this.dancersFieldCalc.style.display = 'none';
        this.dancersFieldCalc.previousElementSibling.style.display = 'none';

        //hide cost overview
        this.costOverview.style.display = 'none';

        this.initEventListeners();

    }

    initEventListeners() {
        this.teamField.addEventListener('change', () => this.onTeamFieldChange());
        this.saveButton.addEventListener('click', (e) => this.onSaveButtonClick(e));
        this.saveButtonLast.addEventListener('click', (e) => this.onSaveButtonLastClick(e));
        this.payButton.addEventListener('click', (e) => this.onPayButtonClick(e));
    }

    onTeamFieldChange() {
        const teamId = this.teamField.value;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/frontend/registration/getdancers/' + teamId);
        xhr.onload = () => {
            if (xhr.status === 200) {

                const dancers = JSON.parse(xhr.responseText);

                //clear dancersFieldCalc options before adding new ones
                this.dancersFieldCalc.innerHTML = '';

                //foreach over dancers.dancers add options to dancersFieldCalc select field make them all selected
                dancers.dancers.forEach(dancer => {
                    const option = document.createElement('option');
                    option.value = dancer.id;
                    option.text = dancer.AllDetails;
                    option.selected = true;
                    this.dancersFieldCalc.appendChild(option);
                });

                this.calculateTotalPrice(dancers.count);
            }
        };
        xhr.send();
    }

    calculateTotalPrice(dancers) {
        const total = dancers * this.regfeeValue;
        console.log(this.regfeeValue);
        const paidTotal = this.paidTotal.innerText;

        if (paidTotal >= total) {
            this.payButton.style.display = 'none';
            this.costOverview.style.display = 'none';
            this.saveButton.style.display = 'block';
        } else {
            this.payButton.style.display = 'block';
            this.costOverview.style.display = 'block';
            this.saveButton.style.display = 'none';
            this.leftTotal.innerHTML = (total - paidTotal).toFixed(2);
        }

        this.totalDancers.innerHTML = dancers;
        this.grandTotal.innerHTML = total.toFixed(2);
    }

    onSaveButtonClick(event) {
        if (this.registrationForm.checkValidity()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'save';
            input.value = 'save';
            this.registrationForm.appendChild(input);

            this.registrationForm.submit();
        } else {
            this.registrationForm.reportValidity();
        }
    }

    onSaveButtonLastClick(event) {
        if (this.registrationForm.checkValidity()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'save';
            input.value = 'save';
            this.registrationForm.appendChild(input);

            this.registrationForm.submit();
        } else {
            this.registrationForm.reportValidity();
        }
    }

    onPayButtonClick(event) {
        if (this.registrationForm.checkValidity()) {
                this.registrationForm.submit();
            } else {
                this.registrationForm.reportValidity();
            }
    }
}
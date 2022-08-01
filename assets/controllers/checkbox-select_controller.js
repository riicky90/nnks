import CheckboxSelectAll from "stimulus-checkbox-select-all";

export default class extends CheckboxSelectAll {
    connect() {
        super.connect();
        console.log("Do what you want here.");
    }

    refresh () {
        super.refresh();
        this.checked.forEach(checkbox => console.log(checkbox.value));
    }
}
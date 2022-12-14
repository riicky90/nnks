import { startStimulusApp } from '@symfony/stimulus-bridge';
import { Alert, Modal } from "tailwindcss-stimulus-components";
import LiveController from '@symfony/ux-live-component';
import TomSelect from "tom-select";
import '@symfony/ux-live-component/styles/live.css';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));

app.debug = false;

// register any custom, 3rd party controllers here
app.register('alert', Alert);
app.register('modal', Modal);
app.register("TomSelect", TomSelect);
app.register('live', LiveController);
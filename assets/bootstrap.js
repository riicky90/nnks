import { startStimulusApp } from '@symfony/stimulus-bridge';
import { Alert, Modal, Slideover } from "tailwindcss-stimulus-components";
import Sortable from "stimulus-sortable";
import LiveController from '@symfony/ux-live-component';
import '@symfony/ux-live-component/styles/live.css';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));

// register any custom, 3rd party controllers here
app.register('alert', Alert);
app.register('modal', Modal);
app.register('slideover', Slideover);
app.register("sortable", Sortable);
app.register('live', LiveController);
require('./bootstrap');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import VueSweetalert2 from 'vue-sweetalert2';
import VueLuxon from "vue-luxon";

import 'sweetalert2/dist/sweetalert2.min.css';
Vue.mixin({ methods: { route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(VueSweetalert2);
Vue.use(VueLuxon, {
    serverZone: 'Asia/Jakarta',
    serverFormat: 'iso',
    clientZone: 'locale',
    clientFormat: 'locale',
});

const app = document.getElementById('app');

new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);

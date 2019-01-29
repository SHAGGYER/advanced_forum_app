
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.moment = require('moment');

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

/**
 * Router
 */

import VueRouter from 'vue-router';
import routes from './routes';

const router = new VueRouter({
    routes
});
Vue.use(VueRouter);

/**
 * Components
 *
 */

Vue.component('spinner', require('vue-simple-spinner'));


/*
Plugins
 */

import Pagination from './plugins/pagination';
Vue.use(Pagination);




import App from './App.vue';

new Vue({
    router,
    render: h => h(App)
}).$mount('#app');

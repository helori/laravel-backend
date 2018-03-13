require('lodash');
window.Popper = require('popper.js').default;
window.$ = window.jQuery = require('jquery');
require('bootstrap');

import fontawesome from '@fortawesome/fontawesome'

import freeSolid from '@fortawesome/fontawesome-free-solid'
fontawesome.icon(freeSolid);

import proLight from '@fortawesome/fontawesome-pro-light'
fontawesome.icon(proLight);

window.moment = require('moment');
window.moment.locale('fr');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

require('tinymce');
require('tinymce/themes/modern/theme.min');
require('tinymce/plugins/textcolor/plugin.min');
require('tinymce/plugins/advlist/plugin.min');
require('tinymce/plugins/autolink/plugin.min');
require('tinymce/plugins/lists/plugin.min');
require('tinymce/plugins/link/plugin.min');
require('tinymce/plugins/image/plugin.min');
require('tinymce/plugins/charmap/plugin.min');
require('tinymce/plugins/print/plugin.min');
require('tinymce/plugins/preview/plugin.min');
require('tinymce/plugins/anchor/plugin.min');
require('tinymce/plugins/emoticons/plugin.min');
require('tinymce/plugins/searchreplace/plugin.min');
require('tinymce/plugins/visualblocks/plugin.min');
require('tinymce/plugins/code/plugin.min');
require('tinymce/plugins/fullscreen/plugin.min');
require('tinymce/plugins/charmap/plugin.min');
require('tinymce/plugins/insertdatetime/plugin.min');
require('tinymce/plugins/table/plugin.min');
require('tinymce/plugins/contextmenu/plugin.min');
require('tinymce/plugins/paste/plugin.min');

import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter);

Vue.component(
    'editor',
    require('./backend/Widgets/Editor.vue')
);

const Media = Vue.component('media',require('./backend/Media.vue'));
const Users = Vue.component('users',require('./backend/Users.vue'));

var router = new VueRouter({
    base: '/admin',
    linkActiveClass: 'router-link-active',
    routes: [
        {            
            path: '/',
            redirect: '/media'
        },
        {
            name: 'media',
            path: '/media',
            component: Media
        },
        {
            name: 'users',
            path: '/users',
            component: Users
        },
    ]
});


const app = new Vue({

    //store: store,

    router: router,

    el: '#vue-app',

    components: { 
        Media,
        Users,
    },

    data() {
        return {
            
        };
    },

    mounted(){

    	
        
    },
});

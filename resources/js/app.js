
import Vue from 'vue';

// axios
import axios from 'axios';
import VueAxios from 'vue-axios';

import vuetify from './vuetify';

// imports
//import VueFeatherIcon from 'vue-feather-icon';
//import VueSession from 'vue-session';
import VueSweetalert2 from 'vue-sweetalert2';
import Croppa from 'vue-croppa';
import 'vue-croppa/dist/vue-croppa.css';
import VueFileAgent from 'vue-file-agent';
import VueFileAgentStyles from 'vue-file-agent/dist/vue-file-agent.css';
import LoadScript from 'vue-plugin-load-script';
//import ElementUI from 'element-ui';
//import 'element-ui/lib/theme-chalk/index.css';
import store from './store/store';
import { EmojiPickerPlugin } from 'vue-emoji-picker';
import 'material-icons/iconfont/material-icons.css';

window.axios = require('axios');
Vue.prototype.$http = window.axios;

window.Vue = Vue;
window.Vue.config.productionTip = false;

//Layouts
Vue.component('inc-header', require('./inc/Header.vue').default);
Vue.component('inc-leftnav', require('./inc/LeftNavMobile.vue').default);
Vue.component('inc-floatybar', require('./inc/FloatyBar.vue').default);

//Modules
Vue.component('landing', require('./modules/landing/Landing.vue').default);
Vue.component('newsfeed', require('./modules/newsfeed/Newsfeed.vue').default);
Vue.component('profile', require('./modules/profile/Profile.vue').default);
Vue.component('marketplace', require('./modules/marketplace/Marketplace.vue').default);
Vue.component('autologin', require('./modules/autologin/Autologin.vue').default);

Vue.component('testerpage', require('./modules/testerpage/Testerpage.vue').default);

//Modules>Shared
Vue.component('newsfeedupload', require('./modules/newsfeed/shared/NewsfeedUpload.vue').default);

//Reusable Components
Vue.component('audio-controls', require('./modules/newsfeed/shared/AudioControls.vue').default);

//Shared Components
Vue.component('post-dialog', require('./modules/newsfeed/shared/PostDialog.vue').default);
Vue.component('post-dialog-v3', require('./modules/newsfeed/shared/PostDialogV3.vue').default);
//Vue.component('post-dialogv2', require('./modules/newsfeed/shared/PostDialogV2.vue').default);
Vue.component('share-dialog', require('./modules/newsfeed/shared/ShareDialog.vue').default);
Vue.component('profile-settings', require('./modules/profile/shared/ProfileSettings.vue').default);

Vue.component('notfound', require('./modules/404/404NotFound.vue').default);

//Callback Redirects
Vue.component('googlecallbackredirect', require('./callback-redirects/GoogleCallbackRedirect.vue').default);

//App Meta
Vue.component('apptitle', require('./appmeta/AppTitle.vue').default);


/**
 * Libraries
 */
//use
Vue.use(VueAxios, axios);
//Vue.use(VueFeatherIcon);
//Vue.use(VueSession);
Vue.use(VueSweetalert2);
Vue.use(Croppa);
Vue.use(VueFileAgent);
Vue.use(LoadScript);
//Vue.use(ElementUI);
Vue.use(EmojiPickerPlugin);

//Feather Icon
const feather = require('feather-icons')
feather.replace();


var app = new Vue({
    el: '#vue-app',
    store,
    vuetify
});

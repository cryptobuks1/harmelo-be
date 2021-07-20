import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import Utils from '../utilities/Utils';
import constant from '../utilities/Constant.js';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        app_user: [],
        app_ready: false,
        app_theme: {},
        current_app_theme: ''
    },
    getters: {
        getAppTheme(state) {
            return state.app_theme;
        },
        getAppUser(state) {
            return state.app_user;
        },
        isAppReady(state) {
            return state.app_ready;
        },
        getCurrentAppTheme(state) {
            return state.current_app_theme;
        }
    },
    mutations: {
        mutate_app_theme(state, request) {
            state.app_theme = request;
        },
        mutate_app_user(state, user) {
            state.app_user = user;
        },
        mutate_app_ready(state, request) {
            state.app_ready = request
        },
        mutate_current_app_theme(state, request) {
            state.current_app_theme = request
        },
    },
    actions: {
        _setAppTheme({commit}, value) {
            if (value === 'light') {
                document.body.style.backgroundColor = '#f8f8fb';
                commit('mutate_app_theme', {body: '#f8f8fb', 'panel':'#fff', 'component-color': '#615dfa', 'text-color': '#1d2333'});
            } else {
                document.body.style.backgroundColor = '#161b28';
                commit('mutate_app_theme', {body: '#161b28', 'panel':'#1d2333', 'component-color': '#3f249c', 'text-color': '#fff'});
            }
        },
        _setUserInfo({commit}) {
            const f = new FormData();
            f.append('user_id',  localStorage.getItem('id'));
            return new Promise((resolve, reject) => {
                axios.post(Utils._getWeb(constant.usergetuserbyid), f, {
                    headers:  ''
                }).then(response => {
                    console.log('vuex res', response);
                    const status = response.data.status;
                    if (status == 1) {
                        commit('mutate_app_user', response.data.data[0]);
                    }
                    resolve(response)
                }).catch(err => {
                    console.log('vuex err', err);
                    reject(err)
                });
            });
        },
        _setAppReady({commit}, value) {
            commit('mutate_app_ready', value);
        },
        _setCurrentAppTheme({commit}, value) {
            if (value === 'light') {
                document.body.style.backgroundColor = '#f8f8fb';
                commit('mutate_app_theme', {body: '#f8f8fb', 'panel':'#fff', 'component-color': '#615dfa', 'text-color': '#1d2333'});
            } else {
                document.body.style.backgroundColor = '#161b28';
                commit('mutate_app_theme', {body: '#161b28', 'panel':'#1d2333', 'component-color': '#3f249c', 'text-color': '#fff'});
            }
            commit('mutate_current_app_theme', value);
        },
    }
})

<script>
import moment from 'moment';
import Utils from '../utilities/Utils';
import constant from '../utilities/Constant';
import Toast from '../utilities/Toast';


export default {
    data() {
        return {
            s3_site: constant.s3_site,
            moment: moment,
            const: constant,

            friend_request_list: [],
            user_search_result: [],
            user_search_query: '',
            is_search_querying: false,
            user_details: []
        }
    },
    created() {
    },
    mounted: function() {
        /*this.$store.dispatch('_setUserInfo').then(res => {
            self.$store.dispatch('_setAppReady', true).then(res => {

            });
        }).catch(err => { }); */
        this.$nextTick(function() {
            if (localStorage.getItem('id')) {
                this.$store.dispatch('_setAppTheme', localStorage.getItem('app_theme'));
                this.user_details = {
                    id: localStorage.getItem('id'),
                    name: localStorage.getItem('name'),
                    avatar: localStorage.getItem('avatar'),
                    slug: localStorage.getItem('slug'),
                    access_code: localStorage.getItem('access_code'),
                    app_theme: localStorage.getItem('app_theme')
                }
            }
            this.userfollowergetfriendrequestlist();
        });


    },
    watch: {
        user_search_query: function(n, o) {
            if (this.user_search_query !== '' || !this.user_search_query) {
                this.usersearchuser();
            }
            if (this.user_search_result == '') {
                this.user_search_result = [];
            }
        },
    },
    computed: {
        access_code() {
            return localStorage.getItem('access_code')
        },
        app_component_theme() {
            return 'harmelo-dark-component-color'; //this.user_details.app_theme === 'dark' ? 'harmelo-dark-component-color' : 'harmelo-light-component-color';
        },
        app_subpanel_theme() {
            return localStorage.getItem('app_theme') === 'dark' ? 'harmelo-dark-subpanel' : 'harmelo-light-subpanel';
        },
        app_body_theme() {
            return localStorage.getItem('app_theme') ? 'harmelo-dark-body' : 'harmelo-light-body';
        },
        app_panel_theme() {
            return localStorage.getItem('app_theme') ? 'harmelo-dark-card' : 'harmelo-light-card';
        },
        app_text_theme() {
            return localStorage.getItem('app_theme') ? 'text-white' : 'text-dark';
        },
    },
    methods: {
        changeapptheme(theme) {
            const f = new FormData();
            f.append('user_id', this.user_details.id);
            f.append('theme', theme);
            this.axios.post(Utils._getWeb(this.const.changeapptheme), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    this.user_details.app_theme = theme;
                    localStorage['app_theme'] = theme;
                    this.$store.dispatch('_setCurrentAppTheme', theme);
                    //this.$store.dispatch('_setAppTheme', localStorage.getItem('app_theme'));
                }
            })
        },
        _redirectToUser(slug) {
            window.location.href = '/user/' + slug;
        },
        usersearchuser() {
            this.is_search_querying = true;
            this.user_search_result = [];
            const f = new FormData();
            f.append('term', this.user_search_query);
            this.axios.post(Utils._getWeb(this.const.usersearchuser), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    this.user_search_result = data;
                    this.is_search_querying = false;
                }
            })
        },
        userfollowergetfriendrequestlist() {
            const self = this;
            const f = new FormData();
            f.append('user_id', localStorage.getItem('id'));
            this.axios.post(Utils._getWeb(this.const.userfollowergetfriendrequestlist), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    this.friend_request_list = data;
                }
            })
        },
        _goToProfile() {
            return '{{ route("profile", ["slug" =>' + localStorage.getItem('slug') + ' ]) }}';
        },
        _logOut() {
            const f = new FormData();
            f.append('user_id', localStorage.getItem('id'));
            this.axios.post(Utils._getWeb(this.const.invalidateaccesscode), f, {
                headers: ''
            });

            localStorage.removeItem('id');
            localStorage.removeItem('name');
            localStorage.removeItem('avatar');
            localStorage.removeItem('slug');
        }
    },

};
</script>

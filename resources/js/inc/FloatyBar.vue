<script>
import constant from '../utilities/Constant';
import moment from 'moment';
import Utils from '../utilities/Utils';
import Toast from '../utilities/Toast';

export default {
    data() {
        return {
            const: constant,
            s3_site: constant.s3_site,
            moment: moment,

            user_details: {}
        }
    },
    watch: {
    },
    mounted: function() {
        const self = this;
        this.user_details = {
            id: localStorage.getItem('id'),
            name: localStorage.getItem('name'),
            avatar: localStorage.getItem('avatar'),
            slug: localStorage.getItem('slug'),
        }
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
    },
    computed: {
        current_app_theme: {
            get() {
                return this.$store.getters.getCurrentAppTheme;
            },
            set(val) {
                return val;
            }
        },
    }

};
</script>

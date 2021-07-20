<script>

import Utils from '../../utilities/Utils';
import constant from '../../utilities/Constant';
import Toast from '../../utilities/Toast';

export default {
    name: 'auto-login',
    data() {
        return {
            const: constant,
        }
    },
    mounted() {
        this.webautologin();
    },
    methods: {
        webautologin() {
            const f = new URLSearchParams();
            f.append('access_code', window.location.href.split('autologin/')[1]);
            this.axios.post(Utils._getWeb(this.const.webautologin), f, {
                headers: ''
            }).then(response => {

            })
        }
    }
}

</script>
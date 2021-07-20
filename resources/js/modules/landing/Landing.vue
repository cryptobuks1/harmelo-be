<script>

import { ValidationProvider, extend, ValidationObserver  } from 'vee-validate';
import { required, min, max, email, min_value } from 'vee-validate/dist/rules';
import Utils from '../../utilities/Utils';
import constant from '../../utilities/Constant';
import Toast from '../../utilities/Toast';
import AppTitle from '../../appmeta/AppTitle';

extend('required', {
  ...required,
  message: 'This field is required'
});

extend("min_value", min_value);

extend("min", min);
extend('min', {
  validate(value, { min}) {
    return value.length >= min;
  },
  params: ['min'],
  message:  'The password field must be a minimum of {min} characters.'
});
extend('email', {
  ...email,
  message: 'Please enter a valid email format.'
})

extend('password', {
  params: ['target'],
  validate(value, { target }) {
    return value === target;
  },
  message: 'Password confirmation does not match'
});

export default {
    name: 'landing',
    metaInfo: {
        title: 'Sign in',
        titleTemplate: ' %s | Harmelo Community'
    },
    components: { ValidationProvider, 'validationobserver' : ValidationObserver, AppTitle },
    data() {
        return {
            data: 1,
            const: constant,
            media_site: constant.api_full_url,

            register_email: '',
            register_firstname: '',
            register_lastname: '',
            register_password: '',
            register_password_repeat: '',

            login_email: '',
            login_password: '',

            is_agree: false,
            show_checkbox_msg: false,

            is_verification_show: false,
            unverified_registrant_details: [],
            verification_code: '',
            tab_mode: 'login',
            is_logging_in: false,
            snackbar_message: '',
            is_verifying: false,
            is_sending_new_verification: false,
            is_registering: false
        }
    },
    created() {
    },
    mounted: function() {

    },
    methods: {
        _showSnackbar(type, message) {
            var x = document.getElementById(type == 'success' ? 'snackbar_success' : 'snackbar_error');
            this.snackbar_message = message;
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
        },
        _successPrompt() { //postID, sourceUserID, sourcePostID
            const self = this;
            this.$swal(Utils._swalV1("Account verified!", "Welcome to Harmelo! Your musical journey begins here!", false, 'success')).then((result) => {
                if (result.value) self.loginnative();
            });
        },
        _validateOTPViaEmail() {
            this.is_verifying = true;
            const f = new FormData();
            f.append('user_id', this.unverified_registrant_details.id);
            f.append('email', this.unverified_registrant_details.email);
            f.append('otp', this.verification_code);
            this.axios.post(Utils._getWeb(this.const.validateverificationcode), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    let tempLoginPassword = this.tab_mode == 'login' ? this.login_password : this.register_password;
                    this.login_email = this.unverified_registrant_details.email;
                    this.login_password = tempLoginPassword;
                    this._successPrompt();
                } else {
                    if (status == -2) {
                        this._showSnackbar('error', "You've entered an expired code. Click 'Resend verification' to get a new one.");
                        //Toast._error(this, 'Invalid code', 'You entered an expired code. Please click "Resend verification" so that we can send you a new verification code.');
                    } else {
                        this._showSnackbar('error', "You've entered an invalid code.");
                        //Toast._error(this, 'Invalid code', "You have entered an invalid verification code.");
                    }
                }
                this.is_verifying = false;
            })
        },
        insertnewverificationcode() {
            this.is_sending_new_verification = true;
            const f = new FormData();
            f.append('user_id', this.unverified_registrant_details.id);
            f.append('email', this.unverified_registrant_details.email);
            f.append('receiver_name', this.unverified_registrant_details.name);
            this.axios.post(Utils._getWeb(this.const.insertnewverificationcode), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    this.is_verification_show = true;
                    this._showSnackbar('success', "Your new OTP have been sent!");
                }
                this.is_sending_new_verification = false;
            })
        },
        loginnative() {
            this.is_logging_in = true;
            const f = new FormData();
            f.append('email', this.login_email);
            f.append('password', this.login_password);
            this.axios.post(Utils._getWeb(this.const.loginnative), f, {
                headers: ''
            }).then(response => {

                const status = response.data.status;
                const api_msg = response.data.msg;

                if ( status == 1 ) {

                    const data = response.data.data;
                    this.unverified_registrant_details = data;
                    if (data.is_verified == 0) {
                        this.is_verification_show = true;
                        //this.insertnewverificationcode();
                    } else {
                        window.location.href = '/feed';
                    }

                } else {
                    if ( status == 0 ) {
                        this._showSnackbar('error', api_msg);
                    }
                }

                this.is_logging_in = false;

            }).catch(err => {
                Toast._error(this, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
                console.log('err ', err);
            })
        },

        registernative() {
            if (this.is_agree) {

                this.show_checkbox_msg = false;

                this._registerFunc();

            } else {
                this.show_checkbox_msg = true;
            }
        },

        _registerFunc() {
            this.is_registering = true;
            const self = this;
            const f = new URLSearchParams();
            f.append('email', this.register_email);
            f.append('firstname', this.register_firstname);
            f.append('lastname', this.register_lastname);
            f.append('password', this.register_password);
            f.append('password_confirmation', this.register_password_repeat);
            this.axios.post(Utils._getApi(this.const.registernative), f, {
                headers: ''
            }).then(response => {

                const status = response.data.status;
                const msg = response.data.msg;

                if ( status == 1 ) {

                    const data = response.data.data;
                    this.unverified_registrant_details = data;
                    this.is_verification_show = true;
                    this.is_registering = false;
                } else {

                    if ( status == 0 ) {
                        Toast._error(this, 'Invalid request', msg[0]);
                        console.log('result  = ', msg);
                    }
                    if ( status == -1 ) {
                        Toast._error(this, 'Oops!', 'Something went wrong. This might be on the server side, please stand by...');
                        console.log('result = ', msg);
                    }
                }
            }).catch(err => {

            })
        }
    },

};
</script>

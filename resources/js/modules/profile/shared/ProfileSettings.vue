<script>
import moment from 'moment';
import Utils from '../../../utilities/Utils';
import constant from '../../../utilities/Constant';
import Toast from '../../../utilities/Toast';
import defaultAlbumArt from '../../../../assets/img/harmelo_album_art.png';

import { VEmojiPicker } from 'v-emoji-picker';
import { CircleStencil, Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';

export default {
    name: 'profile-setting',
    props: [],
    components: {
        'vemojipicker': VEmojiPicker, Cropper, CircleStencil
    },
    data: () => ({
        img: 'https://images.pexels.com/photos/1642574/pexels-photo-1642574.jpeg?h=1500&w=2520',
        s3_site: constant.s3_site,
        moment: moment,
        const: constant,
        windowWidth: window.innerWidth,
        windowHeight: Window.innerHeight,
        defaultThumbnail: defaultAlbumArt,
        user_details: null,
        panel_type: false,

        //Avatar VueCroppa
        avatar_croppa: null,
        avatar_sliderVal: 0,
        avatar_sliderMin: 0,
        avatar_sliderMax: 0,
        avatarDataUrl: '',
        uploaded_avatar: null,
        avatar_image_fileArray: [],

        //Cover VueCroppa
        cover_croppa: null,
        cover_sliderVal: 0,
        cover_sliderMin: 0,
        cover_sliderMax: 0,
        coverDataUrl: '',
        uploaded_cover: null,
        cover_image_fileArray: [],

        categoried_instrument_list: [],
        selected_instrument_list: [],
        selected_instrument_id_list: [],
        instrument_raw_list: [],

        uploaded_avatar_fileName: null,
        uploaded_cover_fileName: null,
        avatar_uploadProgress: 0,
        cover_uploadProgress: 0,

        new_name: '',
        new_slug: '',
        new_bio: '',
        new_website: '',
        new_country: '',
        new_city: '',
        new_occupation: '',
        editProfile_uploadProgress: 0,
        is_updating_profile: false,
        is_uploading_avatar: false,
        is_uploading_cover: false,

        avatar_uploadProgress: 0,
        avatar_edit_mode: false,
        avatar_img_rc: '',
        avatar_image_fileArray: []
    }),
    created() {
        this.$loadScript("/assets/vikinger/js/content/content.js");
        this.$loadScript("/assets/vikinger/js/global/global.popups.js");
        this.$loadScript("/assets/vikinger/js/vendor/tiny-slider.min.js");
        this.$loadScript("/assets/vikinger/js/utils/liquidify.js");
    },
    mounted() {
        this.$nextTick(function() {
            this.$store.dispatch('_setCurrentAppTheme', localStorage.getItem('app_theme'));
            this.usergetuserdetailsbyslug();
            //this.instrumentcategorygetallinstruments();
        });
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
        app_bordertop_theme() {
            return this.current_app_theme === 'dark' ? 'harmelo-dark-border-top' : 'harmelo-light-border-top';
        },
        app_borderbot_theme() {
            return this.current_app_theme === 'dark' ? 'harmelo-dark-border-bottom' : 'harmelo-light-border-bottom';
        },
        app_subpanel_theme() {
            return this.current_app_theme === 'dark' ? 'harmelo-dark-subpanel' : 'harmelo-light-subpanel';
        },
        app_body_theme() {
            return this.current_app_theme === 'dark' ? 'harmelo-dark-body' : 'harmelo-light-body';
        },
        app_panel_theme() {
            return this.current_app_theme === 'dark' ? 'harmelo-dark-card' : 'harmelo-light-card';
        },
        app_text_theme() {
            return this.current_app_theme === 'dark' ? 'text-white' : 'text-dark';
        },
    },
    methods: {
        resetAvatarInputValue(e) {
            //This resets the input's value for every file select
            //Thus, allowing user to select the same file as the previous selection
            e.target.value = '';
        },
        _getBackgroundImage(img_src) {
            /**
             * Check if avatar is from provider(google,fb) by checking if it consist https
             */
            return !img_src.includes('https') ?
                this.s3_site+'public/user/'+this.user_details.id+'/avatar/'+this.user_details.avatar
                : this.user_details.avatar;
        },
        _triggerFileInput(elementId) {
            let fileInput = document.getElementById(elementId);
            fileInput.click();
        },
        useruploadavatar_v2() {
            const f = new FormData();
            f.append('user_id', this.user_details.id);
            f.append('avatar_fileArray', this.avatar_image_fileArray.length > 0 ? JSON.stringify(this.avatar_image_fileArray) : 'none');
            this.axios.post(Utils._getWeb(this.const.useruploadavatar_v2), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    this.is_updating_profile = false;
                    this.is_uploading_avatar = false;
                    this.avatar_uploadProgress = this.avatar_uploadProgress + 1;
                    const data = response.data.data;
                    //this.uploaded_cover_fileName = data;
                    this.user_details['avatar'] = data;
                    this.avatar_edit_mode = false;
                    this.editProfile_uploadProgress = this.editProfile_uploadProgress + 1;
                    Toast._success(this, 'Successfull', "You've successfully edited your profile!");
                }
            })
        },
        _cropAvatar() {
            if (!this.is_uploading_avatar) { //Disable when upload is in progress
                if (this.avatar_edit_mode) {
                    const { coordinates, canvas, } = this.$refs.avatarcropper.getResult();
                    this.is_uploading_avatar = true;
                    this.coordinates = coordinates;
                    const ddate = new Date();
                    // You able to do different manipulations at a canvas
                    // but there we just get a cropped image, that can be used
                    // as src for <img/> to preview result
                    this.avatar_img_rc = canvas.toDataURL();
                    this.avatar_image_fileArray.push({
                        file: canvas.toDataURL().split('base64,')[1],
                        file_name: ddate.getTime()+Utils._getMD5(ddate.getTime() + '' + this.user_details.id)+'.png',
                        file_ext: '.png'
                    })
                    this.useruploadavatar_v2();
                } else {
                    this.editProfile_uploadProgress = this.editProfile_uploadProgress + 1;
                    this.is_uploading_avatar = false;
                    this.is_uploading_cover = false;
                    this.is_updating_profile = false;
                    Toast._success(this, 'Successfull', "You've successfully edited your profile!");
                }
            }
		},
        loadImage(event) {
            const self = this;
            this.avatar_uploadProgress = 0;
            this.avatar_edit_mode = true;
            this.avatar_img_rc = '';
            this.avatar_image_fileArray = [];

            const _URL = window.URL || window.webkitURL;

			// Reference to the DOM input element
			var input = event.target;
			// Ensure that you have a file before attempting to read it
			if (input.files && input.files[0]) {
				// create a new FileReader to read this image and convert to base64 format
				var reader = new FileReader();
				// Define a callback function to run, when FileReader finishes its job
				reader.onload = (e) => {
					// Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
					// Read image as base64 and set to imageData
					this.avatar_img_rc = e.target.result;

                    let img = new Image();
                    img.src = e.target.result;

                    img.onload = function() {
                        if (this.width < 397 && this.height < 397) {
                          //  self._coverImageDimensionPrompt('Quality Warning!', 'Providing an image with a dimension lower than 397x397 pixels might be displayed with a bad quality. Do you wish to continue using this image?');
                            //Toast._warning(self, 'Quality Warning!', 'Providing an image with a dimension lower than 1200x316 Pixels might be displayed with a bad quality. Do you wish to continue using this image?')
                        }
                    }
				};
				// Start the reader job - read file as a data url (base64 format)
				reader.readAsDataURL(input.files[0]);
			}
		},
        _coverImageDimensionPrompt(title, msg) {
            const self = this;
            this.$swal.fire(Utils._swalV2(title,msg, true)).then((result) => {
                if (result.value) {

                } else if (result.dismiss === 'cancel') {
                    self._discardCoverChange();
                }
            })
        },
        _discardCoverChange() {
            this.avatar_edit_mode = false;
            this.avatar_img_rc = '';
            this.is_uploading_avatar = false;
            this.avatar_image_fileArray = [];
            this.uploaded_cover_fileName = null;
        },
        percentsRestriction({ minWidth, minHeight, maxWidth, maxHeight, imageSize }) {
			return {
				maxWidth: (imageSize.width * (maxWidth || 100)) / 100,
				maxHeight: (imageSize.height * (maxHeight || 100)) / 100,
				minWidth: (imageSize.width * (minWidth || 0)) / 100,
				minHeight: (imageSize.height * (minHeight || 0)) / 100,
			};
		},
        _dropdownMenuClicked(e) {
            e.stopPropagation();
        },
        selectEmoji(emoji) {
            this.new_bio += emoji.data;
        },
        useruploadavatar() {
            this.is_uploading_avatar = true;
            let config = {
                onUploadProgress: progressEvent => {
                    let percentCompleted = Math.floor((progressEvent.loaded * 100) / progressEvent.total);
                    this.avatar_uploadProgress = percentCompleted-1; //stop at 99 to avoid hiding progress bar even if request is not actually done yet
                    //console.log('percent = ', percentCompleted);
                }
            }
            const f = new FormData();
            f.append('user_id', this.user_details.id);
            f.append('avatar_fileArray', this.avatar_image_fileArray.length > 0 ? JSON.stringify(this.avatar_image_fileArray) : 'none');
            this.axios.post(Utils._getWeb(this.const.useruploadavatar), f, config, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    this.is_uploading_avatar = false;
                    this.avatar_uploadProgress = this.avatar_uploadProgress + 1;
                    const data = response.data.data;
                    this.uploaded_avatar_fileName = data;
                }
            })
        },
        useruploadcover() {
            this.is_uploading_cover = true;
            let config = {
                onUploadProgress: progressEvent => {
                    let percentCompleted = Math.floor((progressEvent.loaded * 100) / progressEvent.total);
                    this.cover_uploadProgress = percentCompleted-1; //stop at 99 to avoid hiding progress bar even if request is not actually done yet
                    //console.log('percent = ', percentCompleted);
                }
            }
            const f = new FormData();
            f.append('user_id', this.user_details.id);
            f.append('cover_fileArray', this.cover_image_fileArray.length > 0 ? JSON.stringify(this.cover_image_fileArray) : 'none');
            this.axios.post(Utils._getWeb(this.const.useruploadcover), f, config, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    this.is_uploading_cover = false;
                    this.cover_uploadProgress = this.cover_uploadProgress + 1;
                    const data = response.data.data;
                    this.uploaded_cover_fileName = data;
                }
            })
        },
        userprofileeditprofile() {
            const self = this;
            this.is_updating_profile = true;
            let config = {
                onUploadProgress: progressEvent => {
                    let percentCompleted = Math.floor((progressEvent.loaded * 100) / progressEvent.total);
                    this.editProfile_uploadProgress = percentCompleted-1; //stop at 99 to avoid hiding progress bar even if request is not actually done yet
                    //console.log('percent = ', percentCompleted);
                }
            }
            const f = new FormData();
            f.append('user_id', this.user_details.id);
            f.append('name', this.new_name);
            f.append('slug', this.new_slug);
            f.append('website', this.new_website);
            f.append('country', this.new_country);
            f.append('city', this.new_city);
            f.append('occupation', this.new_occupation);
            f.append('biography', this.new_bio);
            f.append('instrument_list', this.selected_instrument_id_list.length > 0 ? JSON.stringify(this.selected_instrument_id_list) : 'none');
            f.append('avatar_fileName', this.uploaded_avatar_fileName != null ? this.uploaded_avatar_fileName : 'none');
            f.append('cover_fileName', this.uploaded_cover_fileName != null ? this.uploaded_cover_fileName : 'none');
            this.axios.post(Utils._getWeb(this.const.userprofileeditprofile), f, config, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    this._cropAvatar();
                }
            })
        },
        _selectInstrument(instrument) {
            this.selected_instrument_list.push(instrument);
            this.selected_instrument_id_list.push(instrument.id);
        },
        _deselectInstrument(instrument) {
            let instrumentIndex = this.selected_instrument_list.findIndex(obj => obj.id == instrument.id);
            if (instrumentIndex != -1) {
                this.selected_instrument_list.splice(instrumentIndex, 1);
            }
        },
        _isInstrumentPicked(instrumentID) {
            let ret = false;

            let instrumentIndex = this.selected_instrument_list.findIndex(obj => obj.id == instrumentID);
            if (instrumentIndex != -1) {
                ret = true;
            }

            return ret;
        },
        instrumentcategorygetallinstruments() {
            const self = this;
            const f = new FormData();
            f.append('a', 'a');
            this.axios.post(Utils._getWeb(this.const.instrumentcategorygetallinstruments), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    this.categoried_instrument_list = data;
                    if (self.user_details.instrument != null) {
                        data.forEach(function (cat) {
                            cat.instruments.forEach(function (ins) {
                                if (JSON.parse(self.user_details.instrument).includes(ins.id)) {
                                    self.selected_instrument_list = self.selected_instrument_list.concat(ins);
                                    self.selected_instrument_id_list.push(ins.id);
                                }
                            })
                        });
                    }
                }
            })
        },
        //Common VueCroppa
        onFileTypeMismatch (file) {
            Toast._error(this, 'Invalid file type', 'Please choose a jpeg or png file.')
        },
        onFileSizeExceed (file) {
            Toast._error(this, 'File size exceeds', 'Please choose a file smaller than 100kb.')
        },

        //Cover VueCroppa
        cover_onSliderChange(evt) {
            var increment = evt.target.value;
            this.cover_croppa.scaleRatio = +increment;
            this.coverDataUrl = this.cover_croppa.generateDataUrl();
        },
        cover_handleCroppaFileChoose(file) {
            this.cover_uploadProgress = 1;
            this.uploaded_cover = file;
            this._cover_readAsURL_Image(file, file.name);
        },
        cover_imageChanged() {
            this.coverDataUrl = this.cover_croppa.generateDataUrl();
        },
        cover_onZoom() {
            // To prevent zooming out of range when using scrolling to zoom
            //if (this.sliderMax && this.album_art.scaleRatio >= this.sliderMax) {
            //   this.album_art.scaleRatio = this.sliderMax
            //} else if (this.sliderMin && this.album_art.scaleRatio <= this.sliderMin) {
            //   this.album_art.scaleRatio = this.sliderMin
            //}

            this.cover_sliderVal = this.cover_croppa.scaleRatio
            this.cover_croppa.scaleRatio = this.cover_sliderVal;
            this.coverDataUrl = this.cover_croppa.generateDataUrl();
        },
        cover_onNewImage() {
            this.cover_sliderVal = this.cover_croppa.scaleRatio;
            this.cover_sliderMin = this.cover_croppa.scaleRatio / 2;
            this.cover_sliderMax = this.cover_croppa.scaleRatio * 2;
        },
        _cover_readAsURL_Image(file, file_name) {
            const path = require('path-parse');
            const self = this;
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                console.log('img path = ', path(file_name).ext);
                self.cover_image_fileArray.push({
                    file: reader.result.split('base64,')[1],
                    file_name: path(file_name).name,
                    file_ext: path(file_name).ext
                });
                self.useruploadcover();
            };
            reader.onerror = function (error) {
                console.log('Error: ', error);
            };
        },

        //Avatar VueCroppa
        avatar_onSliderChange(evt) {
            var increment = evt.target.value;
            this.avatar_croppa.scaleRatio = +increment;
            this.avatarDataUrl = this.avatar_croppa.generateDataUrl();
        },
        avatar_handleCroppaFileChoose(file) {
            this.avatar_uploadProgress = 1;
            this.uploaded_avatar = file;
            this._avatar_readAsURL_Image(file, file.name);
        },
        avatar_imageChanged() {
            this.avatarDataUrl = this.avatar_croppa.generateDataUrl();
        },
        avatar_onZoom() {
            // To prevent zooming out of range when using scrolling to zoom
            //if (this.sliderMax && this.album_art.scaleRatio >= this.sliderMax) {
            //   this.album_art.scaleRatio = this.sliderMax
            //} else if (this.sliderMin && this.album_art.scaleRatio <= this.sliderMin) {
            //   this.album_art.scaleRatio = this.sliderMin
            //}

            this.avatar_sliderVal = this.avatar_croppa.scaleRatio
            this.avatar_croppa.scaleRatio = this.avatar_sliderVal;
            this.avatarDataUrl = this.avatar_croppa.generateDataUrl();
        },
        avatar_onNewImage() {
            this.avatar_sliderVal = this.avatar_croppa.scaleRatio;
            this.avatar_sliderMin = this.avatar_croppa.scaleRatio / 2;
            this.avatar_sliderMax = this.avatar_croppa.scaleRatio * 2;
        },
        _avatar_readAsURL_Image(file, file_name) {
            const path = require('path-parse');
            const self = this;
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                console.log('img path = ', path(file_name).ext);
                self.avatar_image_fileArray.push({
                    file: reader.result.split('base64,')[1],
                    file_name: path(file_name).name,
                    file_ext: path(file_name).ext
                });
                self.useruploadavatar();
            };
            reader.onerror = function (error) {
                console.log('Error: ', error);
            };
        },

        usergetuserdetailsbyslug() {
            const user_slug = window.location.href.split('user/settings/')[1];

            const f = new FormData();
            f.append('slug', user_slug);
            this.axios.post(Utils._getWeb(this.const.usergetuserdetailsbyslug), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data[0]
                    this.user_details = data;
                    this.new_name = data.name;
                    this.new_slug = data.slug;
                    this.new_bio = data.bio != null ? data.bio : '';
                    this.new_website = data.website != null ? data.website : '';
                    this.new_country = data.country != null ? data.country : '';
                    this.new_city = data.city != null ? data.city : '';
                    this.new_occupation = data.profession != null ? data.profession : '';
                    this.uploaded_avatar_fileName = data.avatar;
                    this.uploaded_cover_fileName = data.header_cover;
                    this.instrumentcategorygetallinstruments();
                } else {
                    Toast._error(this, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
                }
            })
        }
    }
}
</script>

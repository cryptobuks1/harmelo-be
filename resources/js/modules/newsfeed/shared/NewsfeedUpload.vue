<script>
import moment from 'moment';
import { ValidationProvider, extend, ValidationObserver  } from 'vee-validate';
import { required, min, max, email, min_value } from 'vee-validate/dist/rules';
import Utils from '../../../utilities/Utils';
import constant from '../../../utilities/Constant';
import Toast from '../../../utilities/Toast';
import defaultAlbumArt from '../../../../assets/img/harmelo_album_art.png';

import { VEmojiPicker } from 'v-emoji-picker';
import { CircleStencil, Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';

export default {
    props: [],
    components: {
        'vemojipicker': VEmojiPicker, Cropper, CircleStencil
    },
    data() {
        return {
            s3_site: constant.s3_site,
            moment: moment,
            const: constant,
            defaultThumbnail: defaultAlbumArt,

            //BASIC DETAILS FIEND
            track_title: '',
            track_slug: '',
            profile_description: '',
            track_privacy: 3,
            track_producer: '',

            //USER DETAILS OBJECT
            user_details: {},

            //Vue-Croppa
            album_art: null,
            sliderVal: 0,
            sliderMin: 0,
            sliderMax: 0,
            albumArtDataUrl: '',
            uploaded_album_art: null,
            image_fileArray: [],


            //VueFileAgent
            individual_song_title: '',
            selected_musics: [],
            fileRecords: [],
            fileRecordsForUpload: [],
            track_fileArray: [],

            uploadProgress: 0,
            search_emoji: '',

            album_art_edit_mode: false,
            album_art_img_rc: '',
            albumArt_uploadProgress: 0,
            albumArt_image_fileArray: [],
            is_uploading_album_art: false,
            uploaded_albumart_fileName: null
        }
    },
    watch: {
    },
    mounted: function() {
        this.$loadScript("/assets/vikinger/js/content/content.js");
        this.$loadScript("/assets/vikinger/js/global/global.popups.js");
        this.$loadScript("/assets/vikinger/js/vendor/tiny-slider.min.js");
        this.$loadScript("/assets/vikinger/js/utils/liquidify.js");

        this.$store.dispatch('_setCurrentAppTheme', localStorage.getItem('app_theme'));

        this.user_details = {
            id: localStorage.getItem('id'),
            name: localStorage.getItem('name'),
            slug: localStorage.getItem('slug'),
            avatar: localStorage.getItem('avatar'),
            app_theme: localStorage.getItem('app_theme'),
        }
    },
    directives: {
        focus: {
            inserted(el) {
                el.focus()
            },
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
        percentsRestriction({ minWidth, minHeight, maxWidth, maxHeight, imageSize }) {
			return {
				maxWidth: (imageSize.width * (maxWidth || 100)) / 100,
				maxHeight: (imageSize.height * (maxHeight || 100)) / 100,
				minWidth: (imageSize.width * (minWidth || 0)) / 100,
				minHeight: (imageSize.height * (minHeight || 0)) / 100,
			};
		},
        _cropAlbumArt() {
            if (this.$refs.albumartcropper != undefined) {
                const { coordinates, canvas, } = this.$refs.albumartcropper.getResult();
                this.is_uploading_album_art = true;
                this.coordinates = coordinates;
                const ddate = new Date();
                // You able to do different manipulations at a canvas
                // but there we just get a cropped image, that can be used
                // as src for <img/> to preview result
                this.album_art_img_rc = canvas.toDataURL();
                this.albumArt_image_fileArray.push({
                    file: canvas.toDataURL().split('base64,')[1],
                    file_name: ddate.getTime()+Utils._getMD5(ddate.getTime() + '' + this.user_details.id)+'.png',
                    file_ext: '.png'
                })
            }
            this.musicfeedinsertpost();
		},
        _discardAllChanges() {
            this.track_title = '';
            this.track_producer = '';
            this.profile_description = '';
            this.track_privacy = 3;
            this._discardAlbumArtChange();
        },
        _discardAlbumArtChange() {
            this.album_art_edit_mode = false;
            this.album_art_img_rc = '';
            this.is_uploading_album_art = false;
            this.albumArt_image_fileArray = [];
            this.uploaded_albumart_fileName = null;
        },
        resetAlbumArtInputValue(e) {
            //This resets the input's value for every file select
            //Thus, allowing user to select the same file as the previous selection
            e.target.value = '';
        },
        loadImage(event) {
            const self = this;
            this.albumArt_uploadProgress = 0;
            this.album_art_edit_mode = true;
            this.album_art_img_rc = '';
            this.albumArt_image_fileArray = [];

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
					this.album_art_img_rc = e.target.result;

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
        _triggerFileInput(elementId) {
            let fileInput = document.getElementById(elementId);
            fileInput.click();
        },
        _getBackgroundImage(img_src) {
            /**
             * Check if avatar is from provider(google,fb) by checking if it consist https
             */
            return !img_src.includes('https') ?
                this.s3_site+'public/user/'+this.user_details.id+'/avatar/'+this.user_details.avatar
                : this.user_details.avatar;
        },
        _dropdownMenuClicked(e) {
            e.stopPropagation();
        },
        append(emoji) {
            this.profile_description += emoji
        },
        selectEmoji(emoji, event) {
            this.profile_description += emoji.data;
        },
        musicfeedinsertpost() {
            const file_ext = require('path-parse');
            //Request progress config
            let config = {
                onUploadProgress: progressEvent => {
                    let percentCompleted = Math.floor((progressEvent.loaded * 100) / progressEvent.total);
                    this.uploadProgress = percentCompleted-1; //stop at 99 to avoid hiding progress bar even if request is not actually done yet
                    //console.log('percent = ', percentCompleted);
                }
            }
            const f = new FormData();
            const d = new Date();
            f.append('image_fileArray', this.albumArt_image_fileArray.length > 0 ? JSON.stringify(this.albumArt_image_fileArray) : 'none');
            f.append('track_fileArray', this.selected_musics.length > 0 ? JSON.stringify(this.selected_musics) : 'none');
            f.append('user_id', this.user_details.id);
            f.append('title', this.track_title)
            f.append('track_path' , 'public/musicfeed/'+this.user_details.id+'/music/'+this.track_title);
            f.append('image_path', this.albumArt_image_fileArray.length > 0 ? 'public/musicfeed/'+this.user_details.id+'/image/'+this.albumArt_image_fileArray[0].file_name : 'none');
            f.append('user_slug', this.user_details.slug);
            f.append('caption', this.profile_description);
            f.append('producer', this.track_producer);
            f.append('is_album', 0);
            f.append('privacy', this.track_privacy);
            this.axios.post(Utils._getWeb(this.const.musicfeedinsertpost), f, config, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    this.uploadProgress = this.uploadProgress + 1; //set to 100% since request is successfull.
                    this.selected_musics, this.image_fileArray = [];
                    this.track_title, this.profile_description, this.track_privacy = '';
                    this._discardAlbumArtChange();
                    Toast._successRedirect(this, 'Upload success', 'Your music have been uploaded.', 'feed');

                }
            }).catch(err => {
                console.log(err);
                Toast._error(this, 'Something went wrong', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
            })
        },
        _userPrompt(head, body, type) {
            const self = this;
            if (this.track_title == '' && this.selected_musics.length == 0) {
                Toast._error(this, 'Invalid Request', "Please fill up all the necessary fields.");
            } else {
                this.$swal(Utils._swalV1(head,body, true, type)).then((result) => {
                    if (result.value) self._cropAlbumArt();
                    else if (result.dismiss === 'cancel');
                });
            }
        },
        makeid(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        },
        // Vue File Agent
        filesSelected(fileRecordsNewlySelected) {
            console.log('ha? ', fileRecordsNewlySelected);
            if (fileRecordsNewlySelected.length > 1) {
                this.filesSelected_Album(fileRecordsNewlySelected);
            } else {
                this.filesSelected_Single(fileRecordsNewlySelected);
            }
        },

        filesSelected_Album(fileRecordsNewlySelected) {
            console.log('agaghagha ', fileRecordsNewlySelected);
            this.individual_song_title = '';
            this.selected_musics = [];
            const self = this;

            let x = 0;
            for (var i = 0; i < fileRecordsNewlySelected.length; i++) {
                this._readAsURL(fileRecordsNewlySelected[i].file, fileRecordsNewlySelected[i].file.name);
                x++;
            }
        },

        filesSelected_Single(fileRecordsNewlySelected) {
            const path = require('path-parse');
            this.individual_song_title = path(fileRecordsNewlySelected[0].file.name).name;
            this.selected_musics = [];
            const self = this;

            this._readAsURL(fileRecordsNewlySelected[0].file, fileRecordsNewlySelected[0].file.name);
        },

        _readAsURL(file, file_name) {
            const path = require('path-parse');
            const self = this;
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                console.log('music path = ', file_name);
                self.selected_musics.push({
                    file: reader.result.split('base64,')[1],
                    file_name: path(file_name).name,
                    file_ext: path(file_name).ext
                });
            };
            reader.onerror = function (error) {
                console.log('Error: ', error);
            };
        },
        _readAsURL_Image(file, file_name) {
            const path = require('path-parse');
            const self = this;
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                console.log('img path = ', path(file_name).ext);
                self.image_fileArray.push({
                    file: reader.result.split('base64,')[1],
                    file_name: path(file_name).name,
                    file_ext: path(file_name).ext
                });
            };
            reader.onerror = function (error) {
                console.log('Error: ', error);
            };
        },

        onBeforeDelete(fileRecord) {
            var i = this.fileRecordsForUpload.indexOf(fileRecord);
            if (i !== -1) {
            // queued file, not yet uploaded. Just remove from the arrays
            this.fileRecordsForUpload.splice(i, 1);
            var k = this.fileRecords.indexOf(fileRecord);
            if (k !== -1) this.fileRecords.splice(k, 1);
            } else {
                if (confirm('Are you sure you want to delete?')) {
                    this.$refs.vueFileAgent.deleteFileRecord(fileRecord); // will trigger 'delete' event
                }
            }
        },



        // Vue Croppa
        handleCroppaFileChoose(file) {
            console.log('afaf ', file);
            this.uploaded_album_art = file;
            this._readAsURL_Image(file, file.name);
        },
        onNewImage() {
            this.sliderVal = this.album_art.scaleRatio;
            this.sliderMin = this.album_art.scaleRatio / 2;
            this.sliderMax = this.album_art.scaleRatio * 2;
        },
        onZoom() {
            // To prevent zooming out of range when using scrolling to zoom
            //if (this.sliderMax && this.album_art.scaleRatio >= this.sliderMax) {
            //   this.album_art.scaleRatio = this.sliderMax
            //} else if (this.sliderMin && this.album_art.scaleRatio <= this.sliderMin) {
            //   this.album_art.scaleRatio = this.sliderMin
            //}

            this.sliderVal = this.album_art.scaleRatio
            this.album_art.scaleRatio = this.sliderVal;
            this.albumArtDataUrl = this.album_art.generateDataUrl();
        },
        imageChanged() {
            this.albumArtDataUrl = this.album_art.generateDataUrl();
        },
        onSliderChange(evt) {
            var increment = evt.target.value;
            this.album_art.scaleRatio = +increment;
            this.albumArtDataUrl = this.album_art.generateDataUrl();
        },
        onFileTypeMismatch (file) {
            Toast._error(this, 'Invalid file type', 'Please choose a jpeg or png file.')
        },
        onFileSizeExceed (file) {
            Toast._error(this, 'File size exceeds', 'Please choose a file smaller than 100kb.')
        }
    },

};
</script>
<style scoped></style>

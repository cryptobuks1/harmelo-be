<script>
import moment from 'moment';
import Utils from '../../utilities/Utils';
import constant from '../../utilities/Constant';
import Toast from '../../utilities/Toast';
import defaultAlbumArt from '../../../assets/img/harmelo_album_art.png';
import { CircleStencil, Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';

export default {
    components: { Cropper, CircleStencil },
    data() {
        return {
            s3_site: constant.s3_site,
            moment: moment,
            const: constant,
            windowWidth: window.innerWidth,
            windowHeight: Window.innerHeight,
            defaultThumbnail: defaultAlbumArt,

            user_details: null,

            app_user_details: null,
            app_user: null,
            is_app_ready: false,
            user_profile_post_list: [],
            toPass_postObject: null,
            user_profile_followers: [],
            user_profile_followings: [],
            user_profile_visitors: [],
            section_type: 'posts',

            cover_img_rc: '',
            is_uploading_cover: false,
            cover_uploadProgress: 0,
            cover_image_fileArray: [],
            uploaded_cover_fileName: null,
            cover_edit_mode: false,
            test_dialog: false,
            post_preview_watcher: 1
        }
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
    created() {
        this.$loadScript("/assets/vikinger/js/content/content.js");
        this.$loadScript("/assets/vikinger/js/global/global.popups.js");
        this.$loadScript("/assets/vikinger/js/vendor/tiny-slider.min.js");
        this.$loadScript("/assets/vikinger/js/utils/liquidify.js");
    },
    mounted: function() {
        this.$nextTick(function() {
            this.$store.dispatch('_setCurrentAppTheme', localStorage.getItem('app_theme'));
            this.usergetuserdetailsbyslug();
            this.usergetuserbyid();
            this.app_user_details = {
                id: localStorage.getItem('id'),
                name: localStorage.getItem('name'),
                avatar: localStorage.getItem('avatar'),
                slug: localStorage.getItem('slug')
            }
        });
    },
    methods: {
        _test() {
            this.test_dialog = true;
        },
        resetCoverInputValue(e) {
            //This resets the input's value for every file select
            //Thus, allowing user to select the same file as the previous selection
            e.target.value = '';
        },
        _discardCoverChange() {
            this.cover_edit_mode = false;
            this.cover_img_rc = '';
            this.is_uploading_cover = false;
            this.cover_image_fileArray = [];
            this.uploaded_cover_fileName = null;
        },
        _cropCover() {
            if (!this.is_uploading_cover) { //Disable when upload is in progress
                const { coordinates, canvas, } = this.$refs.covercropper.getResult();
                this.is_uploading_cover = true;
                this.cover_uploadProgress = 1;
                this.coordinates = coordinates;
                const ddate = new Date();
                // You able to do different manipulations at a canvas
                // but there we just get a cropped image, that can be used
                // as src for <img/> to preview result
                this.cover_img_rc = canvas.toDataURL();
                this.cover_image_fileArray.push({
                    file: canvas.toDataURL().split('base64,')[1],
                    file_name: ddate.getTime()+Utils._getMD5(ddate.getTime() + '' + this.app_user_details.id)+'.png',
                    file_ext: '.png'
                })
                this.useruploadcover_v2();
            }
		},
        useruploadcover_v2() {
            let config = {
                onUploadProgress: progressEvent => {
                    let percentCompleted = Math.floor((progressEvent.loaded * 100) / progressEvent.total);
                    this.cover_uploadProgress = percentCompleted-1; //stop at 99 to avoid hiding progress bar even if request is not actually done yet
                    //console.log('percent = ', percentCompleted);
                }
            }
            const f = new FormData();
            f.append('user_id', this.app_user_details.id);
            f.append('cover_fileArray', this.cover_image_fileArray.length > 0 ? JSON.stringify(this.cover_image_fileArray) : 'none');
            this.axios.post(Utils._getWeb(this.const.useruploadcover_v2), f, config, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    this.is_uploading_cover = false;
                    this.cover_uploadProgress = this.cover_uploadProgress + 1;
                    const data = response.data.data;
                    //this.uploaded_cover_fileName = data;
                    this.user_details['header_cover'] = data;
                    this.cover_edit_mode = false;
                }
            })
        },
        percentsRestriction({ minWidth, minHeight, maxWidth, maxHeight, imageSize }) {
			return {
				maxWidth: (imageSize.width * (maxWidth || 100)) / 100,
				maxHeight: (imageSize.height * (maxHeight || 100)) / 100,
				minWidth: (imageSize.width * (minWidth || 0)) / 100,
				minHeight: (imageSize.height * (minHeight || 0)) / 100,
			};
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
        loadImage(event) {
            const self = this;
            this.cover_uploadProgress = 0;
            this.cover_edit_mode = true;
            this.cover_img_rc = '';
            this.cover_image_fileArray = [];

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
					this.cover_img_rc = e.target.result;

                    let img = new Image();
                    img.src = e.target.result;

                    img.onload = function() {
                        if (this.width < 1200 && this.height < 316) {
                            self._coverImageDimensionPrompt('Quality Warning!', 'Providing an image with a dimension lower than 1200x316 pixels might be displayed with a bad quality. Do you wish to continue using this image?');
                            //Toast._warning(self, 'Quality Warning!', 'Providing an image with a dimension lower than 1200x316 Pixels might be displayed with a bad quality. Do you wish to continue using this image?')
                        }
                    }
				};
				// Start the reader job - read file as a data url (base64 format)
				reader.readAsDataURL(input.files[0]);
			}
		},
        change({coordinates, canvas}) {
            console.log(coordinates, canvas)
        },
        _triggerFileInput(elementId) {
            let fileInput = document.getElementById(elementId);
            fileInput.click();
        },
        userfollowercancelrequest(target_user_id) {
            const f = new FormData();
            f.append('target_user_id', target_user_id);
            f.append('follower_id', this.app_user_details.id);
            this.axios.post(Utils._getWeb(this.const.userfollowercancelrequest), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;
                if (status == 1) {
                    let followedUserIndex = this.app_user['pending_followings'].findIndex(obj => obj.id == target_user_id);
                    if (followedUserIndex != -1) {
                        this.app_user['pending_followings'].splice(followedUserIndex, 1);
                    }
                }
            })
        },
        userfollowerdeclinerequest(target_user_id) {
            const f = new FormData();
            f.append('target_user_id', this.app_user_details.id);
            f.append('follower_id', target_user_id);
            this.axios.post(Utils._getWeb(this.const.userfollowerdeclinerequest), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;
                if (status == 1) {
                    let followerUserIndex = this.app_user['pending_followers'].findIndex(obj => obj.id == target_user_id);
                    if (followerUserIndex != -1) {
                        this.app_user['pending_followers'].splice(followerUserIndex, 1);
                    }
                }
            })
        },
        _getBackgroundImage(img_src) {
            return !img_src.includes('https') ?
                this.s3_site+'public/user/'+this.user_details.id+'/avatar/'+this.user_details.avatar
                : this.user_details.avatar;
        },
        _getProfileOwnerBackgroundImage(user_object, img_src) {
            return !img_src.includes('https') ?
                this.s3_site+'public/user/'+user_object.id+'/avatar/'+user_object.avatar
                : user_object.avatar;
        },
        _redirectTo(location) {
            window.location.href = location;
        },
        userprofilegetvisitcount() {
            const f = new FormData();
            f.append('profile_owner_id', this.user_details.id);
            this.axios.post(Utils._getWeb(this.const.userprofilegetvisitcount), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    this.user_profile_visitors = data;
                }
            })
        },
        userprofileinsertvisitcount() {
            const f = new FormData();
            f.append('visitor_id', this.app_user_details.id);
            f.append('profile_owner_id', this.user_details.id);
            this.axios.post(Utils._getWeb(this.const.userprofileinsertvisitcount), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    this.user_profile_visitors.unshift(data);
                }
            })
        },
        userfollowergetuserfollowercountbyslug() {
            const user_slug = window.location.href.split('user/')[1];
            const f = new FormData();
            f.append('slug', user_slug);
            this.axios.post(Utils._getWeb(this.const.userfollowergetuserfollowercountbyslug), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    this.user_profile_followers = data;
                }
            })
        },
        userfollowergetuserfollowingcountbyslug() {
            const user_slug = window.location.href.split('user/')[1];
            const f = new FormData();
            f.append('slug', user_slug);
            this.axios.post(Utils._getWeb(this.const.userfollowergetuserfollowingcountbyslug), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    this.user_profile_followings = data;
                }
            })
        },
        _loadPostPreview(postObject) {
            this.toPass_postObject = null;
            this.toPass_postObject = postObject;
            this.post_preview_watcher++;
        },
        _closePostPreview() {
            this.toPass_postObject = null;
        },
        musicfeedgetpostbyuserid() {

            const f = new FormData();
            const d = new Date();
            f.append('user_id', this.user_details.id);
            this.axios.post(Utils._getWeb(this.const.musicfeedgetpostbyuserid), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    this.user_profile_post_list = data;
                } else {
                    Toast._error(this, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
                }
            }).catch(err => {
                Toast._error(this, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
            })
        },
        usergetuserbyid() {
            const f = new FormData();
            f.append('user_id', localStorage.getItem('id'));
            this.axios.post(Utils._getWeb(this.const.usergetuserbyid), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;
                if (status == 1) {
                    const data = response.data.data;
                    this.app_user = data[0];
                }
            })
        },

        userfolloweracceptrequest(target_user_id) {
            const f = new FormData();
            f.append('target_user_id', this.app_user_details.id);
            f.append('follower_id', target_user_id);
            this.axios.post(Utils._getWeb(this.const.userfolloweracceptrequest), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;
                if (status == 1) {
                    //this.app_user['followings'].unshift(this.user_details);
                    this.app_user['followers'].unshift(this.user_details);
                    let toRemovePendingFollowerIndex = this.app_user['pending_followers'].findIndex(obj => obj.id == target_user_id);
                    if (toRemovePendingFollowerIndex != -1)
                        this.app_user['pending_followers'].splice(toRemovePendingFollowerIndex, 1);
                }
            })
        },
        _isSelf(userID) {
            return this.app_user[0].id == userID ? true : false;
        },
        _isFollowingUser(userID) {
            let following = false;
            let userIndex = this.app_user['followings'].findIndex(obj => obj.id == userID);
            if (userIndex != -1) {
                following = true;
            }
            return following;
        },
        _isFollowerUser(userID) {
            let follower = false;
            let userIndex = this.app_user['followers'].findIndex(obj => obj.id == userID);
            if (userIndex != -1) {
                follower = true;
            }
            return follower;
        },
        _isFriendWithUser(userID) {
            let friend = false;
            let followingUserIndex = this.app_user['followings'].findIndex(obj => obj.id == userID);
            let followerUserIndex = this.app_user['followers'].findIndex(obj => obj.id == userID);
            if (followingUserIndex != -1 && followerUserIndex != -1) {
                friend = true;
            }
            return friend;
        },
        _isPendingFollowingUser(userID) {
            let pending_following = false;
            let userIndex = this.app_user['pending_followings'].findIndex(obj => obj.id == userID);
            if (userIndex != -1) {
                pending_following = true;
            }
            return pending_following;
        },
        _isPendingFollowerUser(userID) {
            let pending_follower = false;
            let userIndex = this.app_user['pending_followers'].findIndex(obj => obj.id == userID);
            if (userIndex != -1) {
                pending_follower = true;
            }
            return pending_follower;
        },
        userfollowerfollowuser(target_user_id) {
            const f = new FormData();
            f.append('target_user_id', target_user_id);
            f.append('follower_id', this.app_user_details.id);
            this.axios.post(Utils._getWeb(this.const.userfollowerfollowuser), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    this.app_user['pending_followings'].unshift(this.user_details);
                }
            })
        },
        usergetuserdetailsbyslug() {
            const self = this;
            const user_slug = window.location.href.split('user/')[1];

            const f = new FormData();
            f.append('slug', user_slug);
            this.axios.post(Utils._getWeb(this.const.usergetuserdetailsbyslug), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    this.user_details = response.data.data[0];
                    this.is_app_ready = true;
                    this.musicfeedgetpostbyuserid();
                    this.userfollowergetuserfollowercountbyslug();
                    this.userfollowergetuserfollowingcountbyslug();
                    this.userprofileinsertvisitcount();
                    this.userprofilegetvisitcount();
                } else {
                    window.location.href = '/404notfound';
                }
            })
        }
    },

};
</script>

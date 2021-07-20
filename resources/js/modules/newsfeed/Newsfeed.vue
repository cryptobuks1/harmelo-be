<script>
import moment from 'moment';
import Utils from '../../utilities/Utils';
import constant from '../../utilities/Constant';
import Toast from '../../utilities/Toast';
import defaultAlbumArt from '../../../assets/img/harmelo_album_art.png';
//import defaultCover from '../../../assets/img/default-cover.jpg';

export default {
    props: ['user_session_details'],
    components: {  },
    data() {
        return {
            s3_site: constant.s3_site,
            moment: moment,
            const: constant,
            windowWidth: window.innerWidth,
            windowHeight: Window.innerHeight,
            defaultThumbnail: defaultAlbumArt,
            //defaultCoverPhoto: defaultCover,

            postsUrl: 'https://harmelo.com/wp-json/wp/v2/posts',
            blog_posts: [],
            queryOptions: {
                per_page: 1, // Only retrieve the 3 most recent blog posts.
                page: 1, // Current page of the collection.
                _embed: true, //Response should include embedded resources.
            },
            data: 1,
            profile_data: [],
            is_track_clickplayed: false,
            post_dialog_watcher: 1,
            postDetails: [],
            track_postList: [],
            toPass_postObject: null,
            postToShareData: null,
            post_share_privacy: 3,
            post_share_description: '',
            random_suggested_users: null,
            current_showed_random_users: [],
            is_dom_loaded: false,
            is_nothing_to_show: true,
            is_fetching_older_post: false,
            app_user_reactions_received: [],
            post_preview_watcher: 1
        }
    },
    created() {
        /**
         * Local scripts
         * Load some of newsfeed page scripts here
         */
        this.$loadScript("/assets/vikinger/js/content/content.js");
        this.$loadScript("/assets/vikinger/js/global/global.popups.js");
        this.$loadScript("/assets/vikinger/js/vendor/tiny-slider.min.js");
        this.$loadScript("/assets/vikinger/js/utils/liquidify.js");

        window.addEventListener('resize', this.handleWindowResize);
        this.handleWindowResize();
    },
    watch: {
        /* isAppReady: function(n, o) {
            if (this.user_session_details.is_verified == 1) {
                localStorage.setItem('id', this.user_session_details.id);
                localStorage.setItem('name', this.user_session_details.name);
                localStorage.setItem('avatar', this.user_session_details.avatar);
                localStorage.setItem('slug', this.user_session_details.slug);
                this.musicfeedgetrandomstrangerusers();
                this.getRecentBlogPosts();
                this.musicfeedgetpost();
            } else {
                window.location.href = '/';
            }
        } */
        is_dom_loaded: function(n, o) {
            if (this.is_dom_loaded) {
               // alert(true)
            } else {
              //  alert(false);
            }
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
        trackList() {
            return this.track_postList;
        },
        isAppReady() {
            return this.$store.getters.isAppReady;
        },
        app_user() {
            return this.$store.getters.getAppUser;
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
        app_theme: {
            get() {
                return this.$store.getters.getAppTheme;
            },
            set(val) {
                return val;
            }
        }
    },
    mounted() {
        this.$nextTick(function() {
            if (this.user_session_details.is_verified == 1) {
                this.$store.dispatch('_setCurrentAppTheme', this.user_session_details.app_theme);
                localStorage.setItem('id', this.user_session_details.id);
                localStorage.setItem('name', this.user_session_details.name);
                localStorage.setItem('avatar', this.user_session_details.avatar);
                localStorage.setItem('slug', this.user_session_details.slug);
                localStorage.setItem('access_code', this.user_session_details.access_code);
                localStorage.setItem('app_theme', this.user_session_details.app_theme);
                this.musicfeedgetrandomstrangerusers();
                this.getRecentBlogPosts();
                this.musicfeedgetpost();
                this.usergetreactionscount();
            } else {
                window.location.href = '/';
            }

            //document.body.style.backgroundColor = this.user_session_details.app_theme === 'dark' ? '#1d2333' : '#f8f8fb';
        });

        const self = this;
    },
    methods: {
        usergetreactionscount() {
            const f = new FormData();
            f.append('user_id', this.user_session_details.id);
            this.axios.post(Utils._getWeb(this.const.usergetreactionscount), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;
                if (status == 1) {
                    const data = response.data.data;
                    this.app_user_reactions_received = data.reduce((c, {reaction_type:key}) =>
                        (c[key] = (c[key] || 0) + 1, c), {}
                    );
                }
            })
        },
        _hidePost(postID) {
            let postIndex = this.track_postList.findIndex(obj => obj.id == postID);
            if (postIndex != -1) {
                this.track_postList.splice(postIndex, 1);
            }
        },
        _getBackgroundImage(img_src) {
            /**
             * Check if avatar is from provider(google,fb) by checking if it consist https
             */
            return !img_src.includes('https') ?
                this.s3_site+'public/user/'+this.user_session_details.id+'/avatar/'+this.user_session_details.avatar
                : this.user_session_details.avatar;
        },
        isADayAgo(input){
            let yesterday = moment().subtract(1, 'd');
            return input.isBefore(yesterday);
        },
        _refreshPostList(data) {
            this.track_postList.unshift(data);
        },
        _closePostPreview() {
            this.toPass_postObject = null;
        },
        _getPostLastID() {
            let len = this.track_postList.length;
            return this.track_postList[len-1].id;
        },
        musicfeedgetolderpost() {
            const self = this;
            this.is_fetching_older_post = true;
            const f = new FormData();
            f.append('last_rendered_id', this._getPostLastID());
            f.append('user_id', this.user_session_details.id);
            this.axios.post(Utils._getWeb(this.const.musicfeedgetolderpost), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;
                if (status == 1) {
                    const data = response.data.data;
                    self.track_postList = [...self.track_postList, data];
                    /*if (data.length > 0) {
                        data.forEach(function (post) {
                            self.track_postList.push(post);
                        });

                        this.is_nothing_to_show = false;
                    } else {
                        this.is_nothing_to_show = true;
                    }*/
                }
                setTimeout(() => {
                    self.is_fetching_older_post = false;
                }, 500);
            })
        },
        userfollowergeneratenewsuggesteruser() {
            const f = new FormData();
            f.append('user_id', this.user_session_details.id);
            f.append('current_ids', JSON.stringify(this.current_showed_random_users));
            this.axios.post(Utils._getWeb(this.const.userfollowergeneratenewsuggesteruser), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;
                if (status == 1) {
                    const data = response.data.data;
                    this.random_suggested_users.unshift(data[0]);
                }
            })
        },
        userfollowerfollowuser(target_user_id) {
            const self = this;
            const f = new FormData();
            f.append('target_user_id', target_user_id);
            f.append('follower_id', this.user_session_details.id);
            this.axios.post(Utils._getWeb(this.const.userfollowerfollowuser), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    let suggestedUserIndex = this.random_suggested_users.findIndex(obj => obj.id == target_user_id);
                    if (suggestedUserIndex != -1) {
                        this.random_suggested_users.splice(suggestedUserIndex, 1);
                        this.current_showed_random_users = this.random_suggested_users.map(random => random.id); //get id of all currently showing random users
                        setTimeout(() => {
                            self.userfollowergeneratenewsuggesteruser();
                        }, 1000);
                    }
                }
            })
        },
        musicfeedgetrandomstrangerusers() {
            const self = this;
            const f = new FormData();
            f.append('user_id', this.user_session_details.id);
            this.axios.post(Utils._getWeb(this.const.musicfeedgetrandomstrangerusers), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if ( status == 1 ) {
                    const data = response.data.data;
                    self.random_suggested_users = data;
                }
            })
        },
        _getImageSource(post_reaction, current_userId) {
            const curuser_reactionIndex = post_reaction.findIndex( obj => obj.reacted_by == current_userId );
            let filename;
            if ( curuser_reactionIndex != -1 ) {
                filename = post_reaction[curuser_reactionIndex].reaction_type;
            }
            return '../../../../assets/vikinger/img/reaction/' + filename + '.png';
        },
        _sharePostDialog_Close() {
            this.postToShareData = null;
        },
        _sharePostDialog_Open(post) {
            this.postToShareData = null;
            this.postToShareData = post;
        },
        hoverPlay(soundobj) {
            document.getElementById('fake-btn').click();
            const curAud = document.getElementById(soundobj);
            curAud.play();
            setTimeout(() => {
                curAud.pause();
                curAud.currentTime = 0;
            }, 15000);
        },
        hoverStop(soundobj) {
            const curAud = document.getElementById(soundobj);
            curAud.pause();
            curAud.currentTime = 0;
        },
        _sharePrompt(head, body, type, postObject) { //postID, sourceUserID, sourcePostID
            const self = this;
            this.$swal(Utils._swalV1(head,body, true, type)).then((result) => {
                if (result.value) self.musicfeedsharepost(postObject); //postID, sourceUserID, sourcePostID
                else if (result.dismiss === 'cancel');
            });
        },
        musicfeedsharepost(postObject) { //postID, sourceUserID, sourcePostID
            const self = this;
            const f = new FormData();
            const d = new Date();
            f.append('post_id', postObject.id);
            f.append('shared_by', this.user_session_details.id);
            f.append('description', this.post_share_description);
            f.append('privacy', this.post_share_privacy);
            f.append('source_user_id', postObject.source_user_id);
            f.append('source_post_id',  postObject.source_post_id);
            f.append('track_id', postObject.track_id);
            f.append('is_album', postObject.is_album);
            this.axios.post(Utils._getWeb(this.const.musicfeedsharepost), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;

                    self.track_postList.unshift(data[0]);

                    Toast._success(self, 'Post shared', 'You have successfully shared a post!');
                } else {
                    Toast._error(self, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
                }
            }).catch(err => {
                Toast._error(self, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
            })

        },
        musicfeedreacttopost(postID, reactionType) {
            const self = this;
            const f = new FormData();
            const d = new Date();
            f.append('post_id', postID);
            f.append('reacted_by', this.user_session_details.id);
            f.append('reaction_type', reactionType);
            this.axios.post(Utils._getWeb(this.const.musicfeedreacttopost), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    const postIndex = self.track_postList.findIndex(obj => obj.id == postID);
                    if (postIndex != -1) {
                        self.track_postList[postIndex].reactions.unshift(data);
                    }
                } else {
                    Toast._error(this, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
                }
            }).catch(err => {
                Toast._error(this, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
            })

        },
        musicfeedupdatereacttopost(post_reaction, current_userId, new_reaction, postID) {
            const self = this;
            const curuser_reactionIndex = post_reaction.findIndex( obj => obj.reacted_by == current_userId ); //Reaction index of the current user
            let reactionId;
            if ( curuser_reactionIndex != -1 ) {

                reactionId = post_reaction[curuser_reactionIndex].id;
                let currentReaction = post_reaction[curuser_reactionIndex].reaction_type;

                //Check if the chosen reaction is the same as the current, if true, then unreact, else update
                let updatedReaction = currentReaction === new_reaction ? 'unreact' : new_reaction;

                const f = new FormData();
                f.append('reaction_id', reactionId);
                f.append('new_reaction', updatedReaction);
                this.axios.post(Utils._getWeb(this.const.musicfeedupdatereacttopost), f, {
                    headers: ''
                }).then(response => {
                    const status = response.data.status;

                    if (status == 1) {
                        const postIndex = self.track_postList.findIndex(obj => obj.id == postID);
                        if (postIndex != -1) {
                            if (updatedReaction === 'unreact') {
                                self.track_postList[postIndex].reactions.splice(curuser_reactionIndex, 1);
                            } else {
                                self.track_postList[postIndex].reactions[curuser_reactionIndex].reaction_type = updatedReaction;
                            }
                        }
                    } else {
                        Toast._error(this, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
                    }
                })
            }
        },
        musicfeedgetpost() {
            const f = new FormData();
            const d = new Date();
            f.append('user_id', this.user_session_details.id);
            this.axios.post(Utils._getWeb(this.const.musicfeedgetpost), f, {
                headers: ''
            }).then(response => {
                const status = response.data.status;

                if (status == 1) {
                    const data = response.data.data;
                    this.track_postList = data;
                    this.is_dom_loaded = true;
                } else {
                    Toast._error(this, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
                }
            }).catch(err => {
                Toast._error(this, 'Cannot connect to the server', "Please check your network connectivity. If error still persist, contact support@harmelo.com.");
            })

        },
        _closePostPreview() {
            this.toPass_postObject = null;
        },
        _loadPostPreview(postObject) {
            this.toPass_postObject = null;
            this.toPass_postObject = postObject;
            this.post_preview_watcher++;
            //console.log('modal ', document.getElementById('post-preview').modal);
        },
        _playTrack() {

            this.player.play();

        },
        handleWindowResize() {
            this.windowWidth = window.innerWidth;
            this.windowHeight = window.innerHeight;
        },
        _getqoutedpath(path) {
            return "'"+path+"'";
        },
        // Get Recent Posts From WordPress Site
        getRecentBlogPosts() {
            this.axios
                .get(this.postsUrl, { params: this.queryOptions})
                .then(response => {
                    this.blog_posts= response.data;
                })
                .catch(error => {
                    console.log(error);
                });
        },
    },

};
</script>

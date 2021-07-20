<template>
  <div>
    <div
      class="modal fade"
      id="post-preview"
      role="dialog"
      style="z-index: 5000000"
    >
      <div class="modal-dialog modal-dialog-centered" >
        <div class="modal-content" v-if="post_details != null">
          <audio style="display: none" ref="player">
            <source
              :src="`${s3_site + post_details.track_details[0].track_path}`"
              type="audio/mpeg"
            />
            <!--  -->
          </audio>
          <div
            class="uk-modal-container uk-modal uk-open transition-animated"
            id="story-modal"
            style="display: block; z-index: 1000000 !important"
            key="dynamic_preview"
          >
            <div
              class="uk-modal-dialog story-modal"
              style="
                border-radius: 12px;
                overflow: hidden;
                height: 100%;
                width: 100%;
              "
            >
              <button
                class="uk-modal-close-default lg:-mt-9 lg:-mr-9 -mt-5 -mr-5 shadow-lg bg-white rounded-full p-4 transition dark:bg-gray-600 dark:text-white uk-icon uk-close"
                type="button"
                :class="app_panel_theme"
                style="border-radius: 12px !important; padding: 12px !important"
                @click="_closePostPreview()"
              >
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 14 14"
                  xmlns="http://www.w3.org/2000/svg"
                  data-svg="close-icon"
                >
                  <template v-if="app_user.app_theme === 'dark'">
                    <line
                      fill="none"
                      stroke="#fff"
                      stroke-width="1.1"
                      x1="1"
                      y1="1"
                      x2="13"
                      y2="13"
                    ></line>
                    <line
                      fill="none"
                      stroke="#fff"
                      stroke-width="1.1"
                      x1="13"
                      y1="1"
                      x2="1"
                      y2="13"
                    ></line>
                  </template>
                  <template v-else>
                    <line
                      fill="none"
                      stroke="#000"
                      stroke-width="1.1"
                      x1="1"
                      y1="1"
                      x2="13"
                      y2="13"
                    ></line>
                    <line
                      fill="none"
                      stroke="#000"
                      stroke-width="1.1"
                      x1="13"
                      y1="1"
                      x2="1"
                      y2="13"
                    ></line>
                  </template>
                </svg>
              </button>

              <!-- THUMBNAIL BLOCK -->
              <!-- THUMBNAIL OVERLAY ON HOVER -->
              <div
                class="popup-picture-image-wrap video-box small"
                @click="toggleAudio()"
                v-if="post_details != null"
                :style="[
                  view_mode == 'share_mode'
                    ? { height: '400px !important' }
                    : {},
                ]"
                style="flex-grow: 1"
              >
                <div
                  class="video-box-cover popup-video-trigger"
                  style="height: 100%;width: 100%;"
                >
                  <div class="story-modal-media" style="background-color: #15151f !important; border-top-left-radius: 9px; border-bottom-left-radius: 9px; height: 100%; min-width: 100%;">
                    <img :src="
                      post_details.track_details[0].image_path == '' ||
                      post_details.track_details[0].image_path == undefined ||
                      albumart == post_details.track_details[0].image_path ? defaultThumbnail : s3_site+post_details.track_details[0].image_path"
                      class="inset-0 h-full w-full object-cover story-modal-media-image"
                      style="object-fit: contain; height: 100% !important; width: 100% !important;"
                    >
                  </div>

                  <div
                    class="play-button track__playBtn"
                    :class="{ toggleBtns: isPlaying }"
                  >
                    <!-- heart animated css  -->
                    <img
                      id="rotating-play-btn"
                      class="pausedBtn"
                      :class="{ 'heart animated css': isPlaying }"
                      :src="harmeloPlayer"
                      :style="[
                        view_mode == 'preview_mode'
                          ? { 'max-width': '167px', height: '167px' }
                          : {
                              'max-width': '74px !important',
                              height: '74px !important',
                            },
                      ]"
                    />

                    <svg
                      class="play-button-icon icon-play playedBtn"
                      style="margin-left: 30px; position: absolute; top: 0"
                      :style="[
                        view_mode == 'preview_mode'
                          ? {
                              width: '153px !important',
                              height: '153px !important',
                            }
                          : {
                              width: '74px !important',
                              height: '74px !important',
                            },
                      ]"
                      @click="toggleAudio()"
                    >
                      <use xlink:href="#svg-play"></use>
                    </svg>
                  </div>

                  <div class="video-box-info">
                    <input
                      v-model="playbackTime"
                      type="range"
                      min="0"
                      :max="audioDuration"
                      class="slider w-full h-full video-box-text"
                      id="position"
                      name="position"
                    />

                    <p
                      class="video-box-title"
                      style="position: absolute; bottom: 55px; font-size: 1rem"
                    >
                      {{ post_details.user_details[0].name }} -
                      {{ post_details.track_details[0].title }}
                    </p>

                    <p
                      class="video-box-text"
                      style="position: absolute; bottom: 35px"
                      v-show="audioLoaded"
                    >
                      {{ elapsedTime() }} - {{ totalTime() }}
                    </p>
                    <p
                      class="video-box-text"
                      v-show="!audioLoaded"
                      v-html="totalTime()"
                    >
                      Loading please wait...
                    </p>
                  </div>
                </div>
              </div>
              <!-- /THUMBNAIL OVERLAY ON HOVER END -->
              <!-- THUMBNAIL BLOCK END -->

              <div
                class="flex-1 bg-white dark:bg-gray-900 dark:text-gray-100 comment-section"
                :class="app_panel_theme"
                style="
                  border-top-right-radius: 9px;
                  border-bottom-right-radius: 9px;
                  flex: 1 !important;
                "
              >
                <!-- post header-->
                <div
                  class="border-b flex items-center justify-between px-4 py-3 dark:border-gray-600"
                >
                  <div class="flex flex-1 items-center space-x-4">
                    <a href="#">
                      <div
                        class="bg-gradient-to-tr from-yellow-600 to-pink-600 p-0.5 rounded-full"
                      >
                        <img
                          :src="defaultThumbnail"
                          class="bg-gray-200 border border-white rounded-full w-8 h-8"
                        />
                      </div>
                    </a>
                    <div class="user-status-c">
                      <p class="user-status-title medium">
                        <a
                          class="bold"
                          :class="app_text_theme"
                          href="profile-timeline.html"
                          v-if="post_details != null"
                          >{{ post_details.original_poster_details[0].name }}</a
                        >
                      </p>
                      <p
                        class="user-status-text small"
                        v-if="post_details != null"
                      >
                        {{ moment(post_details.created_at).fromNow() }}
                      </p>
                    </div>
                  </div>
                  <a href="#">
                    <i
                      class="icon-feather-more-horizontal text-2xl rounded-full p-2 transition -mr-1"
                    ></i>
                  </a>
                </div>

                <div class="story-content p-4" data-simplebar>
                    <p
                        style="
                        font-size: 0.835rem;
                        line-height: 1.7142857143em;
                        font-weight: 600;
                        padding: 0 15px !important;
                        "
                        :class="app_text_theme"
                    >
                        {{ post_details.caption }}
                    </p>
                    <hr
                        class="-mx-4 my-3"
                        style="border-top: #ffffff1a !important"
                    />
                    <div class="py-4">
                        <div class="flex justify-around">
                        <div class="dropdown">
                            <div
                            class="post-actions"
                            data-toggle="dropdown"
                            >
                            <img
                                class="reaction-option-image"
                                height="20px"
                                style="
                                margin-right: 10px !important;
                                height: 26px !important;
                                "
                                data-toggle="dropdown"
                                v-if="
                                post_details.reactions.filter(
                                    (e) => e.reacted_by == app_user.id
                                ).length > 0
                                "
                                :src="
                                _getImageSource(
                                    post_details.reactions,
                                    app_user.id
                                )
                                "
                                alt="reaction-like"
                            />
                            <svg
                                class="post-option-icon icon-thumbs-up"
                                data-toggle="dropdown"
                                v-else
                            >
                                <use xlink:href="#svg-thumbs-up"></use>
                            </svg>
                            </div>
                            <ul
                            class="dropdown-menu"
                            :class="app_panel_theme"
                            style="
                                border-radius: 22px !important;
                                text-align: center !important;
                            "
                            >
                            <li
                                class="reaction_list"
                                style="
                                margin: 5px 13px !important;
                                cursor: pointer;
                                "
                                v-for="(react, rr_react) in [
                                'like',
                                'love',
                                'funny',
                                'wow',
                                'sad',
                                ]"
                                :key="rr_react"
                            >
                                <img
                                class="reaction-option-image"
                                :src="_getRawImageSource(react)"
                                :alt="'reaction-' + react"
                                />
                            </li>
                            </ul>
                        </div>
                        <div class="post-actions">
                            <svg class="post-option-icon icon-comment">
                            <use xlink:href="#svg-comment"></use>
                            </svg>
                        </div>
                        <div class="post-actions">
                            <svg class="post-option-icon icon-share">
                            <use xlink:href="#svg-share"></use>
                            </svg>
                        </div>
                        </div>
                        <hr
                        class="-mx-4 my-3"
                        style="border-top: #ffffff1a !important"
                        />
                        <div
                        class="flex items-center space-x-3"
                        style="padding: 10px; color: #fff !important"
                        v-if="post_details.reactions.length > 0"
                        >
                        <div class="flex items-center">
                            <img
                            :src="
                                _getRawImageSource(
                                post_details.reactions[0].reaction_type
                                )
                            "
                            alt=""
                            class="w-6 h-6 rounded-full border-2 border-white"
                            style="border: 2px solid #1d2333 !important"
                            />
                            <img
                            v-if="
                                post_details.reactions[1] != undefined
                            "
                            :src="
                                _getRawImageSource(
                                post_details.reactions[1].reaction_type
                                )
                            "
                            alt=""
                            class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                            style="border: 2px solid #1d2333 !important"
                            />
                            <img
                            v-if="
                                post_details.reactions[2] != undefined
                            "
                            :src="
                                _getRawImageSource(
                                post_details.reactions[2].reaction_type
                                )
                            "
                            alt=""
                            class="w-6 h-6 rounded-full border-2 border-white -ml-2"
                            style="border: 2px solid #1d2333 !important"
                            />
                        </div>
                        <div
                            style="
                            font-family: Roboto Mono, monospace !important;
                            margin: 0 15px !important;
                            font-size: 0.8rem !important;
                            "
                            class="mx-4"
                        >
                            Liked <strong> Johnson</strong> and
                            <strong> 209 Others </strong>
                        </div>
                        </div>
                    </div>

                <div class="post-comment-list">
                    <div
                        class="post-comment"
                        v-bind:class="[
                        app_panel_theme,
                        app_bordertop_theme,
                        ]"
                        style="padding: 26px 9px 28px 67px !important"
                    >
                        <a
                        class="user-avatar small no-outline"
                        href="profile-timeline.html"
                        style="left: 14px !important"
                        >
                        <img
                            :src="defaultThumbnail"
                            class="rounded-full w-8 h-8"
                        />
                        </a>

                        <p
                        class="post-comment-text"
                        :class="app_text_theme"
                        style="font-weight: 300; font-size: 0.845rem"
                        >
                        <a
                            class="post-comment-text-author"
                            :class="app_text_theme"
                            href="profile-timeline.html"
                            >Neko Bebop</a
                        >
                        - It's always a pleasure to do this streams
                        with you! If we have at least half the fun
                        than last time it will be an incredible
                        success!
                        </p>

                        <!-- CONTENT ACTIONS -->
                        <div class="content-actions">
                        <div class="content-action">
                            <div class="meta-line">
                            <div
                                class="meta-line-list reaction-item-list small"
                            >
                                <div class="reaction-item">
                                <img
                                    class="reaction-image"
                                    :src="_getRawImageSource('love')"
                                    alt="reaction-happy"
                                />
                                </div>
                            </div>
                            <p class="meta-line-text">4</p>
                            </div>
                            <div class="meta-line">
                            <p class="meta-line-link light">Reply</p>
                            </div>
                            <div class="meta-line">
                            <p class="meta-line-timestamp">
                                15 min ago
                            </p>
                            </div>

                            <div class="meta-line settings">
                            <div class="post-settings-wrap">
                                <div class="post-settings">
                                <svg
                                    class="post-settings-icon icon-more-dots"
                                >
                                    <use
                                    xlink:href="#svg-more-dots"
                                    ></use>
                                </svg>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                        <!-- /CONTENT ACTIONS -->
                    </div>
                    <div
                        class="post-comment"
                        v-bind:class="[
                        app_panel_theme,
                        app_bordertop_theme,
                        ]"
                        style="padding: 26px 9px 28px 67px !important"
                    >
                        <a
                        class="user-avatar small no-outline"
                        href="profile-timeline.html"
                        style="left: 14px !important"
                        >
                        <img
                            :src="defaultThumbnail"
                            class="rounded-full w-8 h-8"
                        />
                        </a>

                        <p
                        class="post-comment-text"
                        :class="app_text_theme"
                        style="font-weight: 300; font-size: 0.845rem"
                        >
                        <a
                            class="post-comment-text-author"
                            :class="app_text_theme"
                            href="profile-timeline.html"
                            >Neko Bebop</a
                        >
                        - It's always a pleasure to do this streams
                        with you! If we have at least half the fun
                        than last time it will be an incredible
                        success!
                        </p>

                        <!-- CONTENT ACTIONS -->
                        <div class="content-actions">
                        <div class="content-action">
                            <div class="meta-line">
                            <div
                                class="meta-line-list reaction-item-list small"
                            >
                                <div class="reaction-item">
                                <img
                                    class="reaction-image"
                                    :src="_getRawImageSource('love')"
                                    alt="reaction-happy"
                                />
                                </div>
                            </div>
                            <p class="meta-line-text">4</p>
                            </div>
                            <div class="meta-line">
                            <p class="meta-line-link light">Reply</p>
                            </div>
                            <div class="meta-line">
                            <p class="meta-line-timestamp">
                                15 min ago
                            </p>
                            </div>

                            <div class="meta-line settings">
                            <div class="post-settings-wrap">
                                <div class="post-settings">
                                <svg
                                    class="post-settings-icon icon-more-dots"
                                >
                                    <use
                                    xlink:href="#svg-more-dots"
                                    ></use>
                                </svg>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                        <!-- /CONTENT ACTIONS -->
                    </div>

                    <!-- POST COMMENT HEADING -->
                    <p
                        class="post-comment-heading"
                        v-bind:class="[
                        app_panel_theme,
                        app_bordertop_theme,
                        app_text_theme,
                        ]"
                    >
                        Load More Comments
                        <span class="highlighted">9+</span>
                    </p>
                    <!-- /POST COMMENT HEADING -->
                    </div>
                </div>
                <div class="p-3 border-t dark:border-gray-600">
                  <div
                    class="bg-gray-200 dark:bg-gray-700 rounded-full rounded-md relative"
                    :class="app_panel_theme"
                    style="border: 1px solid #2c3344 !important"
                  >
                    <input
                      type="text"
                      placeholder="Add your Comment.."
                      class="bg-transparent max-h-8 shadow-none"
                    />
                    <div
                      class="absolute bottom-0 flex h-full items-center right-0 right-3 text-xl space-x-2"
                    >
                      <a href="#"> <i class="uil-image"></i></a>
                      <a href="#"> <i class="uil-video"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import constant from "../../../utilities/Constant";
import playerLogo from "../../../../assets/logo/harmelo-logo-no-word-white.png";
import defaultAlbumArt from "../../../../assets/img/harmelo_album_art.png";

export default {
  name: "post-preview",
  props: [
    "post_preview_watcher",
    "albumart",
    "prop_post_details",
    "view_mode",
    "app_user",
  ],
  data() {
    return {
      s3_site: constant.s3_site,
      moment: moment,
      const: constant,
      defaultThumbnail: defaultAlbumArt,
      harmeloPlayer: playerLogo,
      playbackTime: 0,
      audioDuration: 100,
      audioLoaded: false,
      isPlaying: false,
      dialog: false,
      post_details: null
    };
  },
  watch: {
    post_preview_watcher: function (n, o) {
      $("#post-preview").modal("toggle");
      this.post_details = this.prop_post_details;
      this.dialog = !this.dialog;
    },
    dialog: function(n, o) {
        this.$nextTick(function () {
            var audio=this.$refs.player;

                //Wait for audio to load, then run initSlider() to get audio duration and set the max value of our slider
                // "loademetadata" Event https://www.w3schools.com/tags/av_event_loadedmetadata.asp
                audio.addEventListener(
                "loadedmetadata",
                function(e) {
                    this.initSlider();
                }.bind(this)
                );
                // "canplay" HTML Event lets us know audio is ready for play https://www.w3schools.com/tags/av_event_canplay.asp
                audio.addEventListener(
                "canplay",
                function(e) {
                    this.audioLoaded=true;
                }.bind(this)
                );
                //Wait for audio to begin play, then start playback listener function
                this.$watch("isPlaying",function() {
                if(this.isPlaying) {
                    var audio=this.$refs.player;
                    this.initSlider();
                    //console.log("Audio playback started.");
                    //prevent starting multiple listeners at the same time
                    if(!this.listenerActive) {
                    this.listenerActive=true;
                    //for a more consistent timeupdate, include freqtimeupdate.js and replace both instances of 'timeupdate' with 'freqtimeupdate'
                    audio.addEventListener("timeupdate",this.playbackListener);
                    }
                }
                });
                //Update current audio position when user drags progress slider
                this.$watch("playbackTime",function() {
                    var audio=this.$refs.player;
                var diff=Math.abs(this.playbackTime-this.$refs.player.currentTime);

                //Throttle synchronization to prevent infinite loop between playback listener and this watcher
                if(diff>0.01) {
                    this.$refs.player.currentTime=this.playbackTime;
                }
                });
        });
    }
  },
  mounted() {

  },
  computed: {
    app_bordertop_theme() {
      return this.app_user.app_theme === "dark"
        ? "harmelo-dark-border-top"
        : "harmelo-light-border-top";
    },
    app_text_theme() {
      return this.app_user.app_theme === "dark" ? "text-white" : "text-dark";
    },
    app_body_theme() {
      return this.app_user.app_theme === "dark"
        ? "harmelo-dark-body"
        : "harmelo-light-body";
    },
    app_panel_theme() {
      return this.app_user.app_theme === "dark"
        ? "harmelo-dark-card"
        : "harmelo-light-card";
    },
  },
  methods: {
    _closePostPreview() {
        $("#post-preview").modal("toggle");
        this.dialog = false;
        this.post_details = null;
    },
    _getFirstThreeReactionByCount() {
      let reactions = ["like", "love", "funny", "wow", "sad"];
      let likeCounts = this.post_details.reactions.filter(
        (r) => r.reaction_type === "like"
      ).length;
      let loveCounts = this.post_details.reactions.filter(
        (r) => r.reaction_type === "love"
      ).length;
      let funnyCounts = this.post_details.reactions.filter(
        (r) => r.reaction_type === "funny"
      ).length;
      let wowCounts = this.post_details.reactions.filter(
        (r) => r.reaction_type === "wow"
      ).length;
      let sadCounts = this.post_details.reactions.filter(
        (r) => r.reaction_type === "sad"
      ).length;
      let count_array = [
        likeCounts,
        loveCounts,
        funnyCounts,
        wowCounts,
        sadCounts,
      ].sort(function (a, b) {
        return b - a;
      });
    },
    _getRawImageSource(reaction) {
      return "../../../../../assets/vikinger/img/reaction/" + reaction + ".png";
    },
    _getImageSource(post_reaction, current_userId) {
      const curuser_reactionIndex = post_reaction.findIndex(
        (obj) => obj.reacted_by == current_userId
      );
      let filename;
      if (curuser_reactionIndex != -1) {
        filename = post_reaction[curuser_reactionIndex].reaction_type;
      }
      return "../../../../../assets/vikinger/img/reaction/" + filename + ".png";
    },
    //Set the range slider max value equal to audio duration
    initSlider() {
      var audio = this.$refs.player;
      if (audio) {
        this.audioDuration = Math.round(audio.duration);
      }
    },
    //Convert audio current time from seconds to min:sec display
    convertTime(seconds) {
      const format = (val) => `0${Math.floor(val)}`.slice(-2);
      var hours = seconds / 3600;
      var minutes = (seconds % 3600) / 60;
      return [minutes, seconds % 60].map(format).join(":");
    },
    //Show the total duration of audio file
    totalTime() {
      var audio = this.$refs.player;
      if (audio) {
        var seconds = audio.duration;
        return this.convertTime(seconds);
      } else {
        return "00:00";
      }
    },
    //Display the audio time elapsed so far
    elapsedTime() {
      var audio = this.$refs.player;
      if (audio) {
        var seconds = audio.currentTime;
        return this.convertTime(seconds);
      } else {
        return "00:00";
      }
    },
    //Playback listener function runs every 100ms while audio is playing
    playbackListener(e) {
      var audio = this.$refs.player;
      //Sync local 'playbackTime' var to audio.currentTime and update global state
      this.playbackTime = audio.currentTime;

      //console.log("update: " + audio.currentTime);
      //Add listeners for audio pause and audio end events
      audio.addEventListener("ended", this.endListener);
      audio.addEventListener("pause", this.pauseListener);
    },
    //Function to run when audio is paused by user
    pauseListener() {
      this.isPlaying = false;
      this.listenerActive = false;
      this.cleanupListeners();
    },
    //Function to run when audio play reaches the end of file
    endListener() {
      this.isPlaying = false;
      this.listenerActive = false;
      this.cleanupListeners();
    },
    //Remove listeners after audio play stops
    cleanupListeners() {
      var audio = this.$refs.player;
      audio.removeEventListener("timeupdate", this.playbackListener);
      audio.removeEventListener("ended", this.endListener);
      audio.removeEventListener("pause", this.pauseListener);
      //console.log("All cleaned up!");
    },
    toggleAudio() {
      var audio = this.$refs.player;
      //var audio = document.getElementById("audio-player");
      if (audio.paused) {
        audio.play();
        this.isPlaying = true;
      } else {
        audio.pause();
        this.isPlaying = false;
      }
    },
  },
};
</script>

<style scoped>
.pausedBtn {
  opacity: 0;
  transition: opacity 0.6s;
}
.toggleBtns .pausedBtn {
  opacity: 1;
  transition: opacity 0.6s;
}
.toggleBtns .playedBtn {
  opacity: 0;
  transition: opacity 0.6s;
}
.playedBtn {
  opacity: 1;
  transition: opacity 0.6s;
}
.preview_fade-enter,
.preview_fade-leave-active {
  opacity: 0;
  transform: translateY(-10px);
}
.preview_fade-leave-active {
  position: absolute;
}
.transition-animated {
  transition: all 0.5s;
  display: flex;
  width: 100%;
}

.uk-modal.uk-open {
  opacity: 1;
}
@media (min-width: 960px) {
  .uk-modal {
    padding-left: 40px;
    padding-right: 40px;
  }
}

@media (min-width: 640px) {
  .uk-modal {
    padding: 50px 30px;
  }
}

.uk-modal {
  display: none;
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 1010;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
  padding: 15px 15px;
  background: #15151ff5;
  opacity: 0;
  transition: opacity 0.15s linear;
}
.uk-modal-container .uk-modal-dialog {
  width: 1200px;
}
.uk-open > .uk-modal-dialog {
  opacity: 1;
  transform: translateY(0);
}
.story-modal {
  max-width: 990px !important;
  min-height: 87vh;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
}
.uk-modal-dialog {
  position: relative;
  box-sizing: border-box;
  margin: 0 auto;
  width: 600px;
  max-width: calc(100% - 0.01px) !important;
  background: #fff;
  opacity: 0;
  transform: translateY(-100px);
  transition: 0.3s linear;
  transition-property: opacity, transform;
}
button.uk-icon:not(:disabled) {
  cursor: pointer;
}
@media (min-width: 1024px) {
  .lg\:-mr-9 {
    margin-right: -2.25rem;
  }
}

@media (min-width: 1024px) {
  .lg\:-mt-9 {
    margin-top: -2.25rem;
  }
}

.transition {
  transition-property: background-color, border-color, color, fill, stroke,
    opacity, box-shadow, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
.shadow-lg {
  --tw-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -2px rgba(0, 0, 0, 0.05);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),
    var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
.p-4 {
  padding: 1rem;
}
.-mr-5 {
  margin-right: -1.25rem;
}
.-mt-5 {
  margin-top: -1.25rem;
}
.rounded-full {
  border-radius: 9999px;
}
.bg-white {
  --tw-bg-opacity: 1;
  background-color: rgba(255, 255, 255, var(--tw-bg-opacity));
}
button,
[type="button"],
[type="submit"] {
  -webkit-appearance: button;
}
[class*="uk-modal-close-"] {
  position: absolute;
  z-index: 1010;
  top: 10px;
  right: 10px;
  padding: 5px;
}
.uk-close {
  color: #999;
  transition: 0.1s ease-in-out;
  transition-property: color, opacity;
}
.uk-icon {
  margin: 0;
  border: none;
  border-radius: 0;
  overflow: visible;
  font: inherit;
  color: inherit;
  text-transform: none;
  padding: 0;
  background-color: transparent;
  display: inline-block;
  fill: currentcolor;
  line-height: 0;
}
button,
input,
optgroup,
select,
textarea {
  padding: 0;
  line-height: inherit;
  color: inherit;
}
button,
[role="button"] {
  cursor: pointer;
}
button {
  background-color: transparent;
  background-image: none;
}
button,
select {
  text-transform: none;
}
button,
input,
optgroup,
select,
textarea {
  font-size: 100%;
  line-height: 1.15;
  margin: 0;
}
button,
body .pac-container {
  text-transform: none;
}
button {
  outline: none !important;
}
button {
  border: none;
  margin: 0;
  padding: 0;
  width: auto;
  overflow: visible;
  background: transparent;
  color: inherit;
  font: inherit;
  line-height: normal;
  cursor: pointer;
}
button {
  vertical-align: middle;
}
.uk-icon > * {
  transform: translate(0, 0);
}
img,
svg,
video,
canvas,
audio,
iframe,
embed,
object {
  display: block;
  vertical-align: middle;
}
.story-modal .story-modal-media {
  max-width: 660px;
}
[class*="uk-modal-close-"]:first-child + * {
  margin-top: 0;
}
.flex-1 {
  flex: 1 1 0%;
}
.bg-white {
  --tw-bg-opacity: 1;
  background-color: rgba(255, 255, 255, var(--tw-bg-opacity));
}
.justify-between {
  justify-content: space-between;
}
.items-center {
  align-items: center;
}
.flex {
  display: flex;
}
.border-b {
  border-bottom-width: 1px;
}
.flex-1 {
  flex: 1 1 0%;
}
.items-center {
  align-items: center;
}

.flex {
  display: flex;
}
.space-x-4 > :not([hidden]) ~ :not([hidden]) {
  --tw-space-x-reverse: 0;
  margin-right: calc(1rem * var(--tw-space-x-reverse));
  margin-left: calc(1rem * calc(1 - var(--tw-space-x-reverse)));
}
.text-lg {
  font-size: 1.125rem;
  line-height: 1.75rem;
}
.font-semibold {
  font-weight: 600;
}
.block {
  display: block;
}
.p-0\.5 {
  padding: 0.125rem;
}
.rounded-full {
  border-radius: 9999px;
}
.to-pink-600 {
  --tw-gradient-to: #db2777;
}
.from-yellow-600 {
  --tw-gradient-from: #5b1f94;
  --tw-gradient-stops: var(--tw-gradient-from),
    var(--tw-gradient-to, rgba(217, 119, 6, 0));
}
.bg-gradient-to-tr {
  background-image: linear-gradient(to top right, var(--tw-gradient-stops));
}
.w-8 {
  width: 2rem;
}
.h-8 {
  height: 2rem;
}
.border {
  border-width: 1px;
}
.rounded-full {
  border-radius: 9999px;
}
.border-white {
  --tw-border-opacity: 1;
  border-color: rgba(255, 255, 255, var(--tw-border-opacity));
}
.bg-gray-200 {
  --tw-bg-opacity: 1;
  background-color: rgba(229, 231, 235, var(--tw-bg-opacity));
}
img,
video {
  max-width: 100%;
  height: auto;
}
img,
svg,
video,
canvas,
audio,
iframe,
embed,
object {
  display: block;
  vertical-align: middle;
}
img {
  max-width: 100%;
}
.transition {
  transition-property: background-color, border-color, color, fill, stroke,
    opacity, box-shadow, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
.p-2 {
  padding: 0.5rem;
}
.-mr-1 {
  margin-right: -0.25rem;
}
.text-2xl {
  font-size: 1.5rem;
  line-height: 2rem;
}
.rounded-full {
  border-radius: 9999px;
}
[class^="icon-feather-"],
[class*=" icon-feather-"] {
  font-family: "Feather-Icons" !important;
  speak: none;
  font-style: normal;
  font-weight: normal;
  font-variant: normal;
  text-transform: none;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
.story-modal .story-content {
  height: 80vh;
}
.p-4 {
  padding: 1rem;
}
.justify-around {
  justify-content: space-around;
}
.font-bold {
  font-weight: 700;
}
.items-baseline {
  align-items: baseline;
}
[class^="uil-"],
[class*=" uil-"] {
  font-family: "unicons";
  font-style: normal;
  font-weight: normal;
  speak: none;
  display: inline-block;
  text-decoration: inherit;
  text-align: center;
  font-variant: normal;
  text-transform: none;
  line-height: 1em;
  font-size: 120%;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  /* text-shadow: 1px 1px 1px rgb(127 127 127 / 30%); */
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
.w-6 {
  width: 1.5rem;
}
.h-6 {
  height: 1.5rem;
}
.border-2 {
  border-width: 2px;
}
.rounded-full {
  border-radius: 9999px;
}
.-mt-1 {
  margin-top: -0.25rem;
}
.relative {
  position: relative;
}
.rounded-full {
  border-radius: 9999px;
}
.rounded-md {
  border-radius: 0.375rem;
}
input[type="text"],
input[type="password"],
input[type="email"] {
  text-transform: none;
}
input[type="text"],
input[type="password"],
input[type="email"],
input[type="number"] {
  height: 48px;
  line-height: 48px;
  padding: 0 20px;
  outline: none;
  font-size: 15px;
  color: #808080;
  max-width: 100%;
  width: 100%;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  display: block;
  font-weight: 400;
  opacity: 1;
  border-radius: 4px;
  border: none;
  -webkit-box-shadow: 0 1px 4px 0px rgb(0 0 0 / 12%);
  box-shadow: 0 1px 4px 0px rgb(0 0 0 / 12%);
}
.right-3 {
  right: 0.75rem;
}

.bottom-0 {
  bottom: 0px;
}
.right-0 {
  right: 0px;
}
.absolute {
  position: absolute;
}
.text-xl {
  font-size: 1.25rem;
  line-height: 1.75rem;
}
.h-full {
  height: 100%;
}
@media (max-width: 1024px) {
  .story-modal {
    min-height: 100vh;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    width: 100% !important;
    top: 0 !important;
  }
}
@media (max-width: 1024px) {
  .story-modal-media {
    border-radius: 0px !important;
    min-width: 0 !important;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
  }
}
@media (max-width: 1024px) {
  .story-modal-media-image {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    height: 40vh !important;
  }
}
@media (max-width: 1024px) {
  .comment-section {
    border-radius: 0px !important;
    border-bottom-left-radius: 9px !important;
    border-bottom-right-radius: 9px !important;
  }
}
@media (max-width: 1024px) {
  .dropdown-menu.show {
    width: 400px !important;
    max-height: 66px !important;
    opacity: 1 !important;
  }
}
@media (max-width: 1024px) {
  .pausedBtn {
    max-width: 95px !important;
    height: 95px !important;
    margin-left: 34px !important;
    position: absolute;
    top: -37px;
  }
}
@media (max-width: 1024px) {
  .playedBtn {
    max-width: 77px !important;
    height: 77px !important;
    margin-left: 48px !important;
    position: absolute !important;
    top: -29px !important;
  }
}

.-ml-2 {
  margin-left: -0.5rem;
}

/*
* SLIDERS AND ANIMATIONS
*/
.play-button {
  height: 45px;
}
input[type="range"] {
  margin: auto;
  -webkit-appearance: none;
  position: relative;
  overflow: hidden;
  width: 100%;
  cursor: pointer;
  outline: none;
  border-radius: 0; /* iOS */
  background: transparent;
}
input[type="range"]:focus {
  outline: none;
}
::-webkit-slider-runnable-track {
  /* this is the non played line */
  background: #a7a4a459;
}
/*
 * 1. Set to 0 width and remove border for a slider without a thumb
 */
::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 40px; /* 1 */
  height: 40px;
  background: #fff;
  box-shadow: -100vw 0 0 100vw #fff; /*cec1fa */
  /*border: none;  2px solid #999; */
  border-radius: 50% !important;
}
::-moz-range-track {
  height: 40px;
  background: #a7a4a4;
}
::-moz-range-thumb {
  background: #fff;
  height: 40px;
  width: 40px; /* 20px; */
  border: none; /* 3px solid #999; */
  border-radius: 50% !important;
  box-shadow: -100vw 0 0 100vw dodgerblue;
  box-sizing: border-box;
}
::-ms-fill-lower {
  background: dodgerblue;
}
::-ms-thumb {
  background: #fff;
  border: 2px solid #999;
  height: 40px;
  width: 20px;
  box-sizing: border-box;
}
::-ms-ticks-after {
  display: none;
}
::-ms-ticks-before {
  display: none;
}
::-ms-track {
  background: #a7a4a4;
  color: transparent;
  height: 40px;
  border: none;
}
::-ms-tooltip {
  display: none;
}
/**
* OVERRIDES
*/
.video-box.small .video-box-cover .video-box-cover-image {
  border-top-left-radius: 0px;
  border-bottom-left-radius: 0px !important;
  border-bottom-right-radius: 12px !important;
  border-top-right-radius: 12px !important;
}
.video-box .video-box-cover .video-box-cover-image {
  border-bottom-right-radius: 12px !important;
  border-top-right-radius: 12px !important;
}
.video-box.small .video-box-cover:after {
  border-radius: 0 !important;
}

/* AUDIO PROGRESS BAR */
input[type="range"] {
  -webkit-appearance: none;
  -moz-apperance: none;
  height: 3px;
  border-radius: 33px;
  position: absolute;
  bottom: 0%;
  width: 100%;
  left: 0;

  background-image: -webkit-gradient(
    linear,
    left top,
    right top,
    color-stop(15%, #3f249c),
    color-stop(15%, #f5d0cc)
  );

  background-image: -moz-linear-gradient(
    left center,
    #3f249c 0%,
    #3f249c 15%,
    #f5d0cc 15%,
    #f5d0cc 100%
  );
}

input[type="range"]::-moz-range-track {
  border: none;
  background: none;
  outline: none;
}

input[type="range"]:focus {
  outline: none;
  border: none;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none !important;
  /*background-color: #3f249c;
    height: 13px;
    width: 13px;
    border-radius: 50%;*/
}

input[type="range"]::-moz-range-thumb {
  -moz-appearance: none !important;
  /*background-color: #3f249c;
    border: none;
    height: 13px;
    width: 13px;
    border-radius: 50%;*/
}
/* */

#audio-player-root {
  width: 100%;
}

.pausedBtn {
  transition: 0.6s;
}

.playedBtn {
  transition: 0.6s;
}

/**
* Pulsating animation
*/

.heart {
  -webkit-transform: scale(1);
  -ms-transform: scale(1);
  -o-transform: scale(1);
  -moz-transform: scale(1);
  transform: scale(1);
  -webkit-transform-origin: center center;
  -moz-transform-origin: center center;
  -ms-transform-origin: center center;
  -o-transform-origin: center center;
  transition: all 6s;
}
.heart.css {
  -webkit-animation-delay: 6s;
  -moz-animation-delay: 6s;
  -ms-animation-delay: 6s;
  -o-animation-delay: 6s;
  animation-dely: 6s;
}
.heart.animated {
  -webkit-animation: 6000ms pulsate infinite alternate ease-in-out;
  -moz-animation: 6000ms pulsate infinite alternate ease-in-out;
  -ms-animation: 6000ms pulsate infinite alternate ease-in-out;
  -o-animation: 6000ms pulsate infinite alternate ease-in-out;
  animation: 6000ms pulsate infinite alternate ease-in-out;
}
.heart:before,
.heart:after {
  position: absolute;
  content: "";
  left: 50px;
  top: 0;
  width: 50px;
  height: 80px;
  background: red;
  -moz-border-radius: 50px 50px 0 0;
  border-radius: 50px 50px 0 0;
  -webkit-transform: rotate(-45deg);
  -moz-transform: rotate(-45deg);
  -ms-transform: rotate(-45deg);
  -o-transform: rotate(-45deg);
  transform: rotate(-45deg);
  -webkit-transform-origin: 0 100%;
  -moz-transform-origin: 0 100%;
  -ms-transform-origin: 0 100%;
  -o-transform-origin: 0 100%;
  transform-origin: 0 100%;
}
.heart:after {
  left: 0;
  -webkit-transform: rotate(45deg);
  -moz-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  -o-transform: rotate(45deg);
  transform: rotate(45deg);
  -webkit-transform-origin: 100% 100%;
  -moz-transform-origin: 100% 100%;
  -ms-transform-origin: 100% 100%;
  -o-transform-origin: 100% 100%;
  transform-origin: 100% 100%;
}
.heart.css:hover {
  -webkit-transform: scale(2);
  -moz-transform: scale(2);
  -ms-transform: scale(2);
  -o-transform: scale(2);
  transform: scale(2);
  -webkit-animation: "";
  -moz-animation: none;
  -ms-animation: "";
  -o-animation: "";
  animation: "";
}

@keyframes pulsate {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
  }
}
@-webkit-keyframes pulsate {
  0% {
    -webkit-transform: scale(1);
  }
  50% {
    -webkit-transform: scale(1.05);
  }
  100% {
    -webkit-transform: scale(1);
  }
}
@-moz-keyframes pulsate {
  0% {
    -moz-transform: scale(1);
  }
  50% {
    -moz-transform: scale(1.05);
  }
  100% {
    -moz-transform: scale(1);
  }
}
@-ms-keyframes pulsate {
  0% {
    -ms-transform: scale(1);
  }
  50% {
    -ms-transform: scale(1.05);
  }
  100% {
    -ms-transform: scale(1);
  }
}
@-o-keyframes pulsate {
  0% {
    -o-transform: scale(1);
  }
  50% {
    -o-transform: scale(1.05);
  }
  100% {
    -o-transform: scale(1);
  }
}
.user-status-c .user-status-title.medium {
  font-size: 1rem;
}
.user-status-c .user-status-title {
  color: #fff;
  font-size: 0.875rem;
  font-weight: 500;
  line-height: 1.4285714286em;
}
.user-status-c .user-status-title .bold {
  color: #fff;
  font-weight: 700;
}
.user-status-c .user-status-title.medium + .user-status-text {
  margin-top: 2px;
}
.user-status .user-status-text.small {
  font-size: 0.75rem;
}
.user-status-c .user-status-text {
  margin-top: 4px;
  color: #9aa4bf;
  font-size: 0.875rem;
  font-weight: 500;
}
.small,
small {
  font-size: 80%;
  font-weight: 400;
}
.user-status-c {
  min-height: 44px;
  padding: 2px 0 0 0 !important;
  position: relative;
}
.dropdown-menu.show {
  top: 10px !important;
  left: -28px !important;
  width: 378px;
  max-height: 66px !important;
  opacity: 1 !important;
}
.dropdown .dropdown-menu {
  -webkit-transition: all 0.2s ease;
  -moz-transition: all 0.2s ease;
  -ms-transition: all 0.2s ease;
  -o-transition: all 0.2s ease;
  transition: all 0.2s ease;

  max-height: 66px;
  display: block;
  overflow: hidden;
  opacity: 0;
  width: 378px;
}
@media (max-width: 1024px) {
  .story-modal .story-content {
    height: 38vh;
  }
}
</style>

<template>
  <div>
    <div
      class="uk-modal-container uk-modal uk-open transition-animated"
      id="story-modal"
      style="display: block; z-index: 1000000 !important"
    >
      <div
        class="uk-modal-dialog story-modal"
        style="border-radius: 12px; top: 100px; background: #1d2333 !important; padding: 15px"
      >
        <button
          class="uk-modal-close-default lg:-mt-9 lg:-mr-9 -mt-5 -mr-5 shadow-lg bg-white rounded-full p-4 transition dark:bg-gray-600 dark:text-white uk-icon uk-close"
          type="button"
          style="
            background: #2c3346 !important;
            border-radius: 12px !important;
            padding: 12px !important;
          "
          @click="_sharePostDialog_Close()"
        >
          <svg
            width="14"
            height="14"
            viewBox="0 0 14 14"
            xmlns="http://www.w3.org/2000/svg"
            data-svg="close-icon"
          >
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
          </svg>
        </button>

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h6 style="color: #fff; padding: 10px 0">Share this post</h6>
                    <div class="form"
                        style="margin-top: 15px;padding: 10px;
                        background: #1e2538;
                        border-radius: 9px;"
                    >

                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input small mid-textarea">
                                    <textarea style="padding: 12px; color: #fff"
                                    id="post-share-description"
                                    name="post_share_description"
                                    placeholder="Write a little description about the post..."
                                    v-model="post_share_description"
                                    ></textarea>
                                    <div class="dropleft emoji-invoker" >
                                        <div class="dropdown-toggle" data-toggle="dropdown">
                                            <svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0h24v24H0z" fill="none"/>
                                                <path fill="#fff" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                            </svg>
                                        </div>
                                        <ul class="dropdown-menu" @click="_dropdownMenuClicked($event)">
                                            <vemojipicker  @select="selectEmoji" />
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row split">
                            <div class="form-item centered">
                            <label
                                class="form-title"
                                for="share-track-privacy-visibility"
                                >Who can see this post?</label
                            >
                            </div>
                            <div class="form-item">
                            <div class="form-select">
                                <select style="color: #fff !important;
                                    padding: 10px;
                                    font-size: 14px;"
                                    id="post-share-privacy-visibility"
                                    name="post_share_privacy_visibility"
                                    v-model="post_share_privacy"
                                >
                                <option value="1" style="color: #fff">Only Me</option>
                                <option value="2" style="color: #fff">Members Only</option>
                                <option value="3" selected style="color: #fff">Everyone (Public)</option>
                                </select>
                                <svg class="form-select-icon icon-small-arrow">
                                <use xlink:href="#svg-small-arrow"></use>
                                </svg>
                            </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input small mid-textarea">
                                    <audio-controls
                                    v-if="post_details != null"
                                    url="https://file-examples-com.github.io/uploads/2017/11/file_example_MP3_5MG.mp3"
                                    playerid="audio-player"
                                    albumart=""
                                    :post_details="post_details"
                                    view_mode="share_mode"
                                    >
                                    </audio-controls>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p style="width: 28% !important; float: right !important; height: 34px !important; line-height: 34px !important; margin-top: 14px !important"
                        class="button secondary full"
                        @click="
                        _sharePrompt(
                            'Share post',
                            'Are you sure you want to share this post?',
                            'question',
                            post_details
                        )
                        "
                    >
                        Share now
                    </p>
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
import Utils from "../../../utilities/Utils";
import Toast from "../../../utilities/Toast";

import { VEmojiPicker } from 'v-emoji-picker';

export default {
    name: "share-dialog",
    components: {
        'vemojipicker': VEmojiPicker
    },
    props: ["post_details", "view_mode", "app_user"],
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

        post_share_privacy: 3,
        post_share_description: "",
        };
    },
    mounted() {},
    methods: {
        _dropdownMenuClicked(e) {
            e.stopPropagation();
        },
        selectEmoji(emoji) {
            this.post_share_description += emoji.data;
        },
        musicfeedsharepost(postObject) {
        //postID, sourceUserID, sourcePostID
        const self = this;
        const f = new FormData();
        const d = new Date();
        f.append("post_id", postObject.id);
        f.append("shared_by", this.app_user.id);
        f.append("description", this.post_share_description);
        f.append("privacy", this.post_share_privacy);
        f.append("source_user_id", postObject.source_user_id);
        f.append("source_post_id", postObject.source_post_id);
        f.append("track_id", postObject.track_id);
        f.append("is_album", postObject.is_album);
        this.axios
            .post(Utils._getWeb(this.const.musicfeedsharepost), f, {
            headers: "",
            })
            .then((response) => {
            const status = response.data.status;

            if (status == 1) {
                const data = response.data.data;

                this.$emit("reflect-shared-post", data[0]);

                Toast._success(
                self,
                "Post shared",
                "You have successfully shared a post!"
                );
            } else {
                Toast._error(
                self,
                "Cannot connect to the server",
                "Please check your network connectivity. If error still persist, contact support@harmelo.com."
                );
            }
            })
            .catch((err) => {
            Toast._error(
                self,
                "Cannot connect to the server",
                "Please check your network connectivity. If error still persist, contact support@harmelo.com."
            );
            });
        },
        _sharePrompt(head, body, type, postObject) {
        //postID, sourceUserID, sourcePostID
        const self = this;
        this.$swal(Utils._swalV1(head, body, true, type)).then((result) => {
            if (result.value) self.musicfeedsharepost(postObject);
            //postID, sourceUserID, sourcePostID
            else if (result.dismiss === "cancel");
        });
        },
        _getImageSourceV2(src) {
        return "../../../../assets/" + src;
        },
        _sharePostDialog_Close() {
        this.$emit("close-details-modal");
        },
    },
};
</script>

<style scoped>
.emoji-invoker {
    position: absolute;
    top: -.6rem;
    right: 0.7rem;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s;
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
  background: rgba(0, 0, 0, 0.6);
  opacity: 0;
  transition: opacity 0.15s linear;
}
.uk-modal-container .uk-modal-dialog {
  width: 520px;
}
.uk-open > .uk-modal-dialog {
  opacity: 1;
  transform: translateY(0);
}
.story-modal {
  max-width: 990px !important;
  min-height: 600px;
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
  height: 70vh;
}
.p-4 {
  padding: 1rem;
}
[data-simplebar] {
  position: relative;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  -webkit-box-pack: start;
  -ms-flex-pack: start;
  justify-content: flex-start;
  -ms-flex-line-pack: start;
  align-content: flex-start;
  -webkit-box-align: start;
  -ms-flex-align: start;
  align-items: flex-start;
  width: inherit;
  height: inherit;
  max-width: inherit;
  max-height: inherit;
  height: 100%;
}
.simplebar-wrapper {
  overflow: hidden;
  width: inherit;
  height: inherit;
  max-width: inherit;
  max-height: inherit;
}
.simplebar-height-auto-observer-wrapper {
  -webkit-box-sizing: inherit !important;
  box-sizing: inherit !important;
  height: 100%;
  width: inherit;
  max-width: 1px;
  position: relative;
  float: left;
  max-height: 1px;
  overflow: hidden;
  z-index: -1;
  padding: 0;
  margin: 0;
  pointer-events: none;
  -webkit-box-flex: inherit;
  -ms-flex-positive: inherit;
  flex-grow: inherit;
  -ms-flex-negative: 0;
  flex-shrink: 0;
  -ms-flex-preferred-size: 0;
  flex-basis: 0;
}
.simplebar-height-auto-observer {
  -webkit-box-sizing: inherit;
  box-sizing: inherit;
  display: block;
  opacity: 0;
  position: absolute;
  top: 0;
  left: 0;
  height: 1000%;
  width: 1000%;
  min-height: 1px;
  min-width: 1px;
  overflow: hidden;
  pointer-events: none;
  z-index: -1;
}
.simplebar-mask {
  direction: inherit;
  position: absolute;
  overflow: hidden;
  padding: 0;
  margin: 0;
  left: 0;
  top: 0;
  bottom: 0;
  right: 0;
  width: auto !important;
  height: auto !important;
  z-index: 0;
}
.simplebar-offset {
  direction: inherit !important;
  -webkit-box-sizing: inherit !important;
  box-sizing: inherit !important;
  resize: none !important;
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  padding: 0;
  margin: 0;
  -webkit-overflow-scrolling: touch;
}
.simplebar-content {
  direction: inherit;
  -webkit-box-sizing: border-box !important;
  box-sizing: border-box !important;
  position: relative;
  display: block;
  height: 100%;
  width: auto;
  visibility: visible;
  overflow: scroll;
  max-width: 100%;
  max-height: 100%;
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
.simplebar-placeholder {
  max-height: 100%;
  max-width: 100%;
  width: 100%;
  pointer-events: none;
}
.simplebar-track.simplebar-horizontal {
  left: 0;
  height: 11px;
}
.simplebar-track {
  z-index: 1;
  position: absolute;
  right: 0;
  bottom: 0;
  pointer-events: none;
}
.simplebar-track.simplebar-horizontal .simplebar-scrollbar {
  right: auto;
  left: 0;
  top: 2px;
  height: 7px;
  min-height: 0;
  min-width: 10px;
  width: auto;
}
.simplebar-scrollbar {
  position: absolute;
  right: 0px;
  width: 5px;
  min-height: 10px;
}
.simplebar-track.simplebar-vertical {
  top: 0;
  width: 10px;
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
    min-height: 95vh;
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
    border-top-left-radius: 9px !important;
    border-top-right-radius: 9px !important;
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
  transition: .6s;
}

.playedBtn {
  transition: .6s;
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
</style>
